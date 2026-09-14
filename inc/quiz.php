<?php
/**
 * Interactive quiz data layer.
 *
 * Quiz content is stored in the multilingual Quiz CPT/ACF fields. The legacy
 * assets/data/quiz.json remains as a safe fallback while the migration is
 * being completed.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve an EN-form internal link to the current language's permalink.
 *
 * @param string $link Internal or external link.
 * @param string $lang Language slug.
 * @return string
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
		if ( $pg ) {
			$target = (int) $pg->ID;
		}
	} elseif ( count( $parts ) === 2 && isset( $pt_by_archive[ $parts[0] ] ) ) {
		$f = get_posts(
			array(
				'name'        => $parts[1],
				'post_type'   => $pt_by_archive[ $parts[0] ],
				'post_status' => 'publish',
				'numberposts' => 1,
			)
		);
		if ( $f ) {
			$target = (int) $f[0]->ID;
		}
	}
	if ( $target ) {
		$tr = function_exists( 'pll_get_post' ) ? (int) pll_get_post( $target, $lang ) : $target;
		if ( $tr ) {
			return get_permalink( $tr );
		}
		return get_permalink( $target );
	}
	return ( 'en' === $lang ) ? $link : home_url( '/' . $lang . $link );
}

/**
 * Profile lean per question/option (index-based; option order is fixed).
 * A = start by understanding, B = ready to act, C = ready to mobilise.
 * Q1/Q2 are observational (no weight); Q5 is decisive (×2).
 *
 * @param int $qi Question index.
 * @param int $oi Option index.
 * @return array
 */
function wes_quiz_weight( $qi, $oi ) {
	$lean = array(
		2 => array( 'B', 'B', 'B', 'A', 'A' ),
		3 => array( 'A', 'A', 'B', 'C', 'A' ),
		4 => array( 'A', 'B', 'B', 'C', 'A' ),
	);
	$mult = array( 4 => 2 );
	$w    = array( 'A' => 0, 'B' => 0, 'C' => 0 );
	if ( isset( $lean[ $qi ][ $oi ] ) ) {
		$w[ $lean[ $qi ][ $oi ] ] += isset( $mult[ $qi ] ) ? $mult[ $qi ] : 1;
	}
	return $w;
}

/**
 * Build the payload from one Quiz CPT post.
 *
 * @param int    $post_id Quiz post ID.
 * @param string $lang Language slug.
 * @return array|null
 */
function wes_quiz_cpt_data( $post_id, $lang = 'en' ) {
	if ( ! $post_id || 'quiz' !== get_post_type( $post_id ) || ! function_exists( 'get_field' ) ) {
		return null;
	}

	$questions = array();
	foreach ( (array) get_field( 'quiz_questions', $post_id ) as $question ) {
		$options = array();
		foreach ( (array) ( $question['options'] ?? array() ) as $option ) {
			$options[] = array(
				'text'     => (string) ( $option['text'] ?? '' ),
				'feedback' => (string) ( $option['feedback'] ?? '' ),
			);
		}
		$questions[] = array(
			'multi'   => ! empty( $question['multi'] ),
			'text'    => (string) ( $question['text'] ?? '' ),
			'options' => $options,
		);
	}

	$profiles = array();
	foreach ( (array) get_field( 'quiz_profiles', $post_id ) as $profile ) {
		$groups = array();
		foreach ( (array) ( $profile['groups'] ?? array() ) as $group ) {
			$items = array();
			foreach ( (array) ( $group['items'] ?? array() ) as $item ) {
				$items[] = array(
					'cat'   => (string) ( $item['cat'] ?? '' ),
					'title' => (string) ( $item['title'] ?? '' ),
					'link'  => wes_quiz_link( (string) ( $item['link'] ?? '' ), $lang ),
				);
			}
			$groups[] = array(
				'heading' => (string) ( $group['heading'] ?? '' ),
				'items'   => $items,
			);
		}
		$profiles[] = array(
			'key'    => (string) ( $profile['key'] ?? '' ),
			'title'  => (string) ( $profile['title'] ?? '' ),
			'lead'   => (string) ( $profile['lead'] ?? '' ),
			'body'   => (string) ( $profile['body'] ?? '' ),
			'groups' => $groups,
			'image'  => wes_img( 'climate-quiz-results-illustration.png' ),
			'alt'    => '',
		);
	}

	$rec = array(
		'en' => 'Recommended resources',
		'ar' => 'موارد مقترحة',
		'fr' => 'Ressources recommandées',
	);

	return array(
		'intro' => array(
			'eyebrow' => (string) get_field( 'quiz_intro_eyebrow', $post_id ),
			'title'   => (string) get_field( 'quiz_intro_title', $post_id ),
			'lead'    => (string) get_field( 'quiz_intro_lead', $post_id ),
			'note'    => (string) get_field( 'quiz_intro_note', $post_id ),
			'start'   => (string) get_field( 'quiz_intro_start', $post_id ),
			'image'   => wes_img( 'climate-quiz-start-illustration.png' ),
			'alt'     => '',
		),
		'ui' => array(
			'progress'    => (string) get_field( 'quiz_ui_progress', $post_id ),
			'multi'       => (string) get_field( 'quiz_ui_multi', $post_id ),
			'single'      => (string) get_field( 'quiz_ui_single', $post_id ),
			'next'        => (string) get_field( 'quiz_ui_next', $post_id ),
			'back'        => (string) get_field( 'quiz_ui_back', $post_id ),
			'results'     => (string) get_field( 'quiz_ui_results', $post_id ),
			'retake'      => (string) get_field( 'quiz_ui_retake', $post_id ),
			'recommended' => $rec[ $lang ] ?? $rec['en'],
		),
		'questions' => $questions,
		'profiles'  => $profiles,
	);
}

/**
 * Build the full quiz payload for a language.
 *
 * The current language's Quiz CPT post is preferred. The legacy JSON remains
 * a fallback so the site keeps working before the importer has been run.
 *
 * @param string $lang Language slug.
 * @return array|null
 */
function wes_quiz_data( $lang = 'en' ) {
	$lang = in_array( $lang, array( 'en', 'ar', 'fr' ), true ) ? $lang : 'en';

	// Prefer the Quiz CPT for the requested Polylang language.
	$quiz_id = 0;
	$query_args = array(
		'post_type'      => 'quiz',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	);
	if ( function_exists( 'pll_get_post_language' ) ) {
		$query_args['lang'] = $lang;
	}
	$quiz_ids = get_posts( $query_args );
	if ( ! empty( $quiz_ids ) ) {
		$quiz_id = (int) $quiz_ids[0];
	}

	if ( $quiz_id ) {
		$data = wes_quiz_cpt_data( $quiz_id, $lang );
		if ( ! empty( $data['questions'] ) && ! empty( $data['profiles'] ) ) {
			foreach ( $data['questions'] as $qi => &$question ) {
				$question['help'] = ! empty( $question['multi'] ) ? ( $data['ui']['multi'] ?? '' ) : ( $data['ui']['single'] ?? '' );
				foreach ( $question['options'] as $oi => &$option ) {
					$option['w'] = wes_quiz_weight( $qi, $oi );
				}
				unset( $option );
			}
			unset( $question );
			return $data;
		}
	}

	// Legacy fallback during migration.
	static $raw = null;
	if ( null === $raw ) {
		$file = get_template_directory() . '/assets/data/quiz.json';
		$raw  = file_exists( $file ) ? json_decode( file_get_contents( $file ), true ) : array();
	}
	if ( empty( $raw[ $lang ] ) ) {
		return null;
	}
	$d = $raw[ $lang ];
	$d['intro']['image'] = wes_img( 'climate-quiz-start-illustration.png' );
	$d['intro']['alt']   = '';
	$rec = array( 'en' => 'Recommended resources', 'ar' => 'موارد مقترحة', 'fr' => 'Ressources recommandées' );
	$d['ui']['recommended'] = $rec[ $lang ] ?? $rec['en'];
	foreach ( $d['questions'] as $qi => &$q ) {
		$q['help'] = ! empty( $q['multi'] ) ? ( $d['ui']['multi'] ?? '' ) : ( $d['ui']['single'] ?? '' );
		foreach ( $q['options'] as $oi => &$opt ) {
			$opt['w'] = wes_quiz_weight( $qi, $oi );
		}
		unset( $opt );
	}
	unset( $q );
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
