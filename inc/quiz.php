<?php
/**
 * Interactive quiz data layer.
 * Loads the trilingual quiz content (assets/data/quiz.json), resolves
 * recommendation links to the current language, and attaches the profile-
 * scoring weights used by quiz.js.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve an EN-form internal link to the current language's permalink.
 * (Article slugs differ per language: -ar / -fr suffix.)
 */
function wes_quiz_link( $link, $lang ) {
	if ( ! is_string( $link ) || $link === '' || $link[0] !== '/' ) {
		return $link;
	}
	$pt_by_archive = array( 'explainers' => 'explainer', 'how-to-guides' => 'howto', 'checklists' => 'checklist' );
	$path   = trim( (string) wp_parse_url( $link, PHP_URL_PATH ), '/' );
	$parts  = $path === '' ? array() : explode( '/', $path );
	$target = 0;
	if ( count( $parts ) === 1 ) {
		$pg = get_page_by_path( $parts[0] );
		if ( $pg ) { $target = (int) $pg->ID; }
	} elseif ( count( $parts ) === 2 && isset( $pt_by_archive[ $parts[0] ] ) ) {
		$f = get_posts( array( 'name' => $parts[1], 'post_type' => $pt_by_archive[ $parts[0] ], 'post_status' => 'publish', 'numberposts' => 1 ) );
		if ( $f ) { $target = (int) $f[0]->ID; }
	}
	if ( $target ) {
		$tr = function_exists( 'pll_get_post' ) ? (int) pll_get_post( $target, $lang ) : $target;
		if ( $tr ) { return get_permalink( $tr ); }
		return get_permalink( $target );
	}
	return ( 'en' === $lang ) ? $link : home_url( '/' . $lang . $link );
}

/**
 * Profile lean per question/option (index-based; option order is fixed in quiz.json).
 * A = start by understanding, B = ready to act, C = ready to mobilise.
 * Q1/Q2 are observational (no weight); Q5 is decisive (×2).
 */
function wes_quiz_weight( $qi, $oi ) {
	$lean = array(
		2 => array( 'B', 'B', 'B', 'A', 'A' ), // Q3 — changed habits?
		3 => array( 'A', 'A', 'B', 'C', 'A' ), // Q4 — first reaction?
		4 => array( 'A', 'B', 'B', 'C', 'A' ), // Q5 — most helpful?
	);
	$mult = array( 4 => 2 );
	$w    = array( 'A' => 0, 'B' => 0, 'C' => 0 );
	if ( isset( $lean[ $qi ][ $oi ] ) ) {
		$w[ $lean[ $qi ][ $oi ] ] += isset( $mult[ $qi ] ) ? $mult[ $qi ] : 1;
	}
	return $w;
}

/**
 * Build the full quiz payload for a language (en|ar|fr).
 *
 * @return array|null
 */
function wes_quiz_data( $lang = 'en' ) {
	static $raw = null;
	if ( null === $raw ) {
		$file = get_template_directory() . '/assets/data/quiz.json';
		$raw  = file_exists( $file ) ? json_decode( file_get_contents( $file ), true ) : array();
	}
	if ( ! in_array( $lang, array( 'en', 'ar', 'fr' ), true ) || empty( $raw[ $lang ] ) ) {
		$lang = 'en';
	}
	$d = $raw[ $lang ];
	if ( empty( $d ) ) { return null; }

	// Illustrations (shared, language-neutral).
	$d['intro']['image'] = wes_img( 'climate-quiz-start-illustration.png' );
	$d['intro']['alt']   = '';

	// "Recommended resources" label (not in the source content).
	$rec = array( 'en' => 'Recommended resources', 'ar' => 'موارد مقترحة', 'fr' => 'Ressources recommandées' );
	$d['ui']['recommended'] = $rec[ $lang ] ?? $rec['en'];

	// Attach scoring weights per option.
	foreach ( $d['questions'] as $qi => &$q ) {
		$q['help'] = ! empty( $q['multi'] ) ? ( $d['ui']['multi'] ?? '' ) : ( $d['ui']['single'] ?? '' );
		foreach ( $q['options'] as $oi => &$opt ) {
			$opt['w'] = wes_quiz_weight( $qi, $oi );
		}
		unset( $opt );
	}
	unset( $q );

	// Resolve recommendation links + add the results illustration to each profile.
	foreach ( $d['profiles'] as &$p ) {
		$p['image'] = wes_img( 'climate-quiz-results-illustration.png' );
		$p['alt']   = '';
		foreach ( $p['groups'] as &$g ) {
			foreach ( $g['items'] as &$it ) {
				$it['link'] = wes_quiz_link( $it['link'], $lang );
			}
			unset( $it );
		}
		unset( $g );
	}
	unset( $p );

	return $d;
}
