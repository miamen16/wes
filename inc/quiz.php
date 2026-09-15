<?php
/**
 * Interactive quiz data layer.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/quiz-cpt.php';
require_once get_template_directory() . '/inc/quiz-block.php';

/**
 * Ensure the Quiz CPT is exposed to Polylang and enabled for translations.
 *
 * Polylang expects the post type slug as the array value. Using a boolean
 * here can prevent the CPT from being rendered correctly in the settings UI.
 *
 * @param array $post_types Registered post types.
 * @param bool  $is_settings Whether Polylang is building its settings list.
 * @return array
 */
function wes_quiz_polylang_post_types_final( $post_types, $is_settings ) {
	$post_types['quiz'] = 'quiz';
	return $post_types;
}
add_filter( 'pll_get_post_types', 'wes_quiz_polylang_post_types_final', 99, 2 );

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
		$f = get_posts( array( 'name' => $parts[1], 'post_type' => $pt_by_archive[ $parts[0] ], 'post_status' => 'publish', 'numberposts' => 1 ) );
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
			$groups[] = array( 'heading' => (string) ( $group['heading'] ?? '' ), 'items' => $items );
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
	$rec = array( 'en' => 'Recommended resources', 'ar' => 'موارد مقترحة', 'fr' => 'Ressources recommandées' );
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
 * Get quiz data for a language, optionally starting from a specific Quiz post.
 *
 * A selected Quiz post is authoritative. Legacy JSON is used only when there
 * is no valid Quiz post selected.
 *
 * @param string $lang    Language slug.
 * @param int    $quiz_id Selected Quiz post ID.
 * @return array|null
 */
function wes_quiz_data( $lang = 'en', $quiz_id = 0 ) {
	$lang    = in_array( $lang, array( 'en', 'ar', 'fr' ), true ) ? $lang : 'en';
	$quiz_id = absint( $quiz_id );

	if ( $quiz_id && 'quiz' === get_post_type( $quiz_id ) ) {
		if ( function_exists( 'pll_get_post' ) ) {
			$translated_id = (int) pll_get_post( $quiz_id, $lang );
			if ( $translated_id && 'quiz' === get_post_type( $translated_id ) ) {
				$quiz_id = $translated_id;
			}
		}

		$data = wes_quiz_cpt_data( $quiz_id, $lang );
		if ( is_array( $data ) ) {
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

		return null;
	}

	// Legacy JSON fallback is used only when no valid Quiz post was selected.
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
