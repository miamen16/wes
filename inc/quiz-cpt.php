<?php
/**
 * Quiz Custom Post Type + ACF data model + JSON importer.
 *
 * Each language is a separate Quiz post. Polylang owns the translation
 * relationship; ACF stores only the content for the current post language.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Quiz CPT.
 */
function wes_register_quiz_cpt() {
	register_post_type(
		'quiz',
		array(
			'labels' => array(
				'name'          => 'Quizzes',
				'singular_name' => 'Quiz',
				'add_new'       => 'Add New',
				'add_new_item'  => 'Add New Quiz',
				'edit_item'     => 'Edit Quiz',
				'new_item'      => 'New Quiz',
				'view_item'     => 'View Quiz',
				'search_items'  => 'Search Quizzes',
				'not_found'     => 'No quizzes found',
				'all_items'     => 'All Quizzes',
				'menu_name'     => 'Quizzes',
			),
			'public'             => true,
			'has_archive'        => true,
			'menu_icon'          => 'dashicons-forms',
			'menu_position'      => 25,
			'rewrite'            => array(
				'slug'       => 'quizzes',
				'with_front' => false,
			),
			'supports'           => array( 'title', 'thumbnail', 'revisions' ),
			'show_in_rest'       => true,
			'show_in_nav_menus'  => true,
			'publicly_queryable' => true,
		)
	);
}
add_action( 'init', 'wes_register_quiz_cpt', 5 );

/**
 * Make Quiz available to Polylang's post-type settings.
 *
 * @param array $post_types Registered post types.
 * @param bool  $is_settings Whether this is the settings list.
 * @return array
 */
function wes_quiz_polylang_post_types( $post_types, $is_settings ) {
	if ( $is_settings ) {
		$post_types['quiz'] = true;
	}
	return $post_types;
}
add_filter( 'pll_get_post_types', 'wes_quiz_polylang_post_types', 10, 2 );

/**
 * Register Quiz ACF fields.
 */
function wes_register_quiz_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$question_option = array(
		array(
			'key'       => 'field_wes_quiz_option_text',
			'label'     => 'Option text',
			'name'      => 'text',
			'type'      => 'textarea',
			'rows'      => 2,
			'new_lines' => '',
		),
		array(
			'key'       => 'field_wes_quiz_option_feedback',
			'label'     => 'Feedback',
			'name'      => 'feedback',
			'type'      => 'textarea',
			'rows'      => 2,
			'new_lines' => '',
		),
	);

	$question = array(
		array(
			'key'           => 'field_wes_quiz_question_multi',
			'label'         => 'Multiple answers',
			'name'          => 'multi',
			'type'          => 'true_false',
			'ui'            => 1,
			'ui_on_text'    => 'Yes',
			'ui_off_text'   => 'No',
			'default_value' => 0,
		),
		array(
			'key'       => 'field_wes_quiz_question_text',
			'label'     => 'Question text',
			'name'      => 'text',
			'type'      => 'textarea',
			'rows'      => 3,
			'new_lines' => '',
		),
		array(
			'key'          => 'field_wes_quiz_question_options',
			'label'        => 'Options',
			'name'         => 'options',
			'type'         => 'repeater',
			'layout'       => 'block',
			'button_label' => 'Add Option',
			'min'          => 1,
			'sub_fields'   => $question_option,
		),
	);

	$profile_item = array(
		array(
			'key'   => 'field_wes_quiz_item_cat',
			'label' => 'Category',
			'name'  => 'cat',
			'type'  => 'text',
		),
		array(
			'key'   => 'field_wes_quiz_item_title',
			'label' => 'Title',
			'name'  => 'title',
			'type'  => 'text',
		),
		array(
			'key'          => 'field_wes_quiz_item_link',
			'label'        => 'Link',
			'name'         => 'link',
			'type'         => 'text',
			'instructions' => 'Use an internal path such as /explainers/example/ or a full external URL.',
		),
	);

	$profile_group = array(
		array(
			'key'   => 'field_wes_quiz_group_heading',
			'label' => 'Heading',
			'name'  => 'heading',
			'type'  => 'text',
		),
		array(
			'key'          => 'field_wes_quiz_group_items',
			'label'        => 'Items',
			'name'         => 'items',
			'type'         => 'repeater',
			'layout'       => 'table',
			'button_label' => 'Add Item',
			'sub_fields'   => $profile_item,
		),
	);

	$profile = array(
		array(
			'key'      => 'field_wes_quiz_profile_key',
			'label'    => 'Key',
			'name'     => 'key',
			'type'     => 'select',
			'choices'  => array(
				'A' => 'A',
				'B' => 'B',
				'C' => 'C',
			),
			'required' => 1,
		),
		array(
			'key'   => 'field_wes_quiz_profile_title',
			'label' => 'Title',
			'name'  => 'title',
			'type'  => 'text',
		),
		array(
			'key'       => 'field_wes_quiz_profile_lead',
			'label'     => 'Lead',
			'name'      => 'lead',
			'type'      => 'textarea',
			'rows'      => 3,
			'new_lines' => '',
		),
		array(
			'key'       => 'field_wes_quiz_profile_body',
			'label'     => 'Body',
			'name'      => 'body',
			'type'      => 'textarea',
			'rows'      => 5,
			'new_lines' => '',
		),
		array(
			'key'          => 'field_wes_quiz_profile_groups',
			'label'        => 'Groups',
			'name'         => 'groups',
			'type'         => 'repeater',
			'layout'       => 'block',
			'button_label' => 'Add Group',
			'sub_fields'   => $profile_group,
		),
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_wes_quiz',
			'title'  => 'Quiz Content',
			'fields' => array(
				array( 'key' => 'field_wes_quiz_intro_tab', 'label' => 'Intro', 'type' => 'tab' ),
				array( 'key' => 'field_wes_quiz_intro_eyebrow', 'label' => 'Eyebrow', 'name' => 'quiz_intro_eyebrow', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_intro_title', 'label' => 'Title', 'name' => 'quiz_intro_title', 'type' => 'text', 'required' => 1 ),
				array( 'key' => 'field_wes_quiz_intro_lead', 'label' => 'Lead', 'name' => 'quiz_intro_lead', 'type' => 'textarea', 'rows' => 4, 'new_lines' => '' ),
				array( 'key' => 'field_wes_quiz_intro_note', 'label' => 'Note', 'name' => 'quiz_intro_note', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '' ),
				array( 'key' => 'field_wes_quiz_intro_start', 'label' => 'Start button text', 'name' => 'quiz_intro_start', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_ui_tab', 'label' => 'UI Labels', 'type' => 'tab' ),
				array( 'key' => 'field_wes_quiz_ui_progress', 'label' => 'Progress', 'name' => 'quiz_ui_progress', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_ui_multi', 'label' => 'Multiple-answer helper', 'name' => 'quiz_ui_multi', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_ui_single', 'label' => 'Single-answer helper', 'name' => 'quiz_ui_single', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_ui_next', 'label' => 'Next', 'name' => 'quiz_ui_next', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_ui_back', 'label' => 'Back', 'name' => 'quiz_ui_back', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_ui_results', 'label' => 'Results', 'name' => 'quiz_ui_results', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_ui_retake', 'label' => 'Retake', 'name' => 'quiz_ui_retake', 'type' => 'text' ),
				array( 'key' => 'field_wes_quiz_questions_tab', 'label' => 'Questions', 'type' => 'tab' ),
				array( 'key' => 'field_wes_quiz_questions', 'label' => 'Questions', 'name' => 'quiz_questions', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add Question', 'sub_fields' => $question ),
				array( 'key' => 'field_wes_quiz_profiles_tab', 'label' => 'Profiles', 'type' => 'tab' ),
				array( 'key' => 'field_wes_quiz_profiles', 'label' => 'Profiles', 'name' => 'quiz_profiles', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add Profile', 'min' => 1, 'max' => 3, 'sub_fields' => $profile ),
			),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'quiz' ) ) ),
			'position' => 'normal',
			'menu_order' => 0,
			'style' => 'default',
			'active' => true,
		)
	);
}
add_action( 'acf/init', 'wes_register_quiz_fields', 20 );

/**
 * Add a one-click importer for the existing quiz.json.
 */
function wes_quiz_import_admin_notice() {
	if ( ! current_user_can( 'edit_posts' ) || ! post_type_exists( 'quiz' ) ) {
		return;
	}
	if ( get_option( 'wes_quiz_json_imported' ) ) {
		return;
	}
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=wes_import_quiz_json' ), 'wes_import_quiz_json' );
	echo '<div class="notice notice-info"><p><strong>WES Quiz:</strong> The existing <code>quiz.json</code> can be imported into the new Quiz posts.</p><p><a class="button button-primary" href="' . esc_url( $url ) . '">Import quiz.json</a></p></div>';
}
add_action( 'admin_notices', 'wes_quiz_import_admin_notice' );

/**
 * Validate one language payload before importing it.
 *
 * @param array  $data Language payload.
 * @param string $lang Language slug.
 * @return array{errors: array, warnings: array, questions: int, profiles: int}
 */
function wes_validate_quiz_language( $data, $lang ) {
	$errors   = array();
	$warnings = array();
	$required = array( 'intro', 'ui', 'questions', 'profiles' );

	foreach ( $required as $key ) {
		if ( ! isset( $data[ $key ] ) || ! is_array( $data[ $key ] ) ) {
			$errors[] = sprintf( '%s: missing or invalid %s.', strtoupper( $lang ), $key );
		}
	}

	if ( ! empty( $errors ) ) {
		return array(
			'errors'    => $errors,
			'warnings'  => $warnings,
			'questions' => 0,
			'profiles'  => 0,
		);
	}

	$intro_keys = array( 'eyebrow', 'title', 'lead', 'note', 'start' );
	$ui_keys    = array( 'progress', 'multi', 'single', 'next', 'back', 'results', 'retake' );

	foreach ( $intro_keys as $key ) {
		if ( ! array_key_exists( $key, $data['intro'] ) ) {
			$errors[] = sprintf( '%s: intro.%s is missing.', strtoupper( $lang ), $key );
		}
	}
	foreach ( $ui_keys as $key ) {
		if ( ! array_key_exists( $key, $data['ui'] ) ) {
			$errors[] = sprintf( '%s: ui.%s is missing.', strtoupper( $lang ), $key );
		}
	}

	$question_count = count( $data['questions'] );
	if ( 5 !== $question_count ) {
		$warnings[] = sprintf( '%s: expected 5 questions, found %d.', strtoupper( $lang ), $question_count );
	}

	foreach ( $data['questions'] as $qi => $question ) {
		if ( ! is_array( $question ) ) {
			$errors[] = sprintf( '%s: question %d is invalid.', strtoupper( $lang ), $qi + 1 );
			continue;
		}
		foreach ( array( 'multi', 'text', 'options' ) as $key ) {
			if ( ! array_key_exists( $key, $question ) ) {
				$errors[] = sprintf( '%s: question %d is missing %s.', strtoupper( $lang ), $qi + 1, $key );
			}
		}
		if ( isset( $question['options'] ) && is_array( $question['options'] ) ) {
			if ( 5 !== count( $question['options'] ) ) {
				$warnings[] = sprintf( '%s: question %d has %d options; expected 5.', strtoupper( $lang ), $qi + 1, count( $question['options'] ) );
			}
			foreach ( $question['options'] as $oi => $option ) {
				if ( ! is_array( $option ) ) {
					$errors[] = sprintf( '%s: question %d option %d is invalid.', strtoupper( $lang ), $qi + 1, $oi + 1 );
					continue;
				}
				foreach ( array( 'text', 'feedback' ) as $key ) {
					if ( ! array_key_exists( $key, $option ) ) {
						$errors[] = sprintf( '%s: question %d option %d is missing %s.', strtoupper( $lang ), $qi + 1, $oi + 1, $key );
					}
				}
			}
		}
	}

	$profile_count = count( $data['profiles'] );
	if ( 3 !== $profile_count ) {
		$warnings[] = sprintf( '%s: expected 3 profiles, found %d.', strtoupper( $lang ), $profile_count );
	}

	$profile_keys = array();
	foreach ( $data['profiles'] as $pi => $profile ) {
		if ( ! is_array( $profile ) ) {
			$errors[] = sprintf( '%s: profile %d is invalid.', strtoupper( $lang ), $pi + 1 );
			continue;
		}
		foreach ( array( 'key', 'title', 'lead', 'body', 'groups' ) as $key ) {
			if ( ! array_key_exists( $key, $profile ) ) {
				$errors[] = sprintf( '%s: profile %d is missing %s.', strtoupper( $lang ), $pi + 1, $key );
			}
		}
		if ( isset( $profile['key'] ) ) {
			$profile_keys[] = (string) $profile['key'];
		}
		if ( isset( $profile['groups'] ) && is_array( $profile['groups'] ) ) {
			foreach ( $profile['groups'] as $gi => $group ) {
				if ( ! is_array( $group ) ) {
					$errors[] = sprintf( '%s: profile %d group %d is invalid.', strtoupper( $lang ), $pi + 1, $gi + 1 );
					continue;
				}
				foreach ( array( 'heading', 'items' ) as $key ) {
					if ( ! array_key_exists( $key, $group ) ) {
						$errors[] = sprintf( '%s: profile %d group %d is missing %s.', strtoupper( $lang ), $pi + 1, $gi + 1, $key );
					}
				}
				if ( isset( $group['items'] ) && is_array( $group['items'] ) ) {
					foreach ( $group['items'] as $ii => $item ) {
						if ( ! is_array( $item ) ) {
							$errors[] = sprintf( '%s: profile %d group %d item %d is invalid.', strtoupper( $lang ), $pi + 1, $gi + 1, $ii + 1 );
							continue;
						}
						foreach ( array( 'cat', 'title', 'link' ) as $key ) {
							if ( ! array_key_exists( $key, $item ) ) {
								$errors[] = sprintf( '%s: profile %d group %d item %d is missing %s.', strtoupper( $lang ), $pi + 1, $gi + 1, $ii + 1, $key );
							}
						}
					}
				}
			}
		}
	}

	$expected_keys = array( 'A', 'B', 'C' );
	if ( count( array_unique( $profile_keys ) ) !== 3 || array_diff( $expected_keys, $profile_keys ) ) {
		$errors[] = sprintf( '%s: profiles must contain unique A, B and C keys.', strtoupper( $lang ) );
	}

	return array(
		'errors'    => $errors,
		'warnings'  => $warnings,
		'questions' => $question_count,
		'profiles'  => $profile_count,
	);
}

/**
 * Import one language's ACF fields.
 *
 * @param int   $post_id Quiz post ID.
 * @param array $data Language payload.
 * @return void
 */
function wes_import_quiz_language_fields( $post_id, $data ) {
	$intro = $data['intro'] ?? array();
	$ui    = $data['ui'] ?? array();

	$fields = array(
		'quiz_intro_eyebrow' => $intro['eyebrow'] ?? '',
		'quiz_intro_title'   => $intro['title'] ?? '',
		'quiz_intro_lead'    => $intro['lead'] ?? '',
		'quiz_intro_note'    => $intro['note'] ?? '',
		'quiz_intro_start'   => $intro['start'] ?? '',
		'quiz_ui_progress'   => $ui['progress'] ?? '',
		'quiz_ui_multi'      => $ui['multi'] ?? '',
		'quiz_ui_single'     => $ui['single'] ?? '',
		'quiz_ui_next'       => $ui['next'] ?? '',
		'quiz_ui_back'       => $ui['back'] ?? '',
		'quiz_ui_results'    => $ui['results'] ?? '',
		'quiz_ui_retake'     => $ui['retake'] ?? '',
	);

	foreach ( $fields as $name => $value ) {
		update_field( $name, is_scalar( $value ) ? (string) $value : '', $post_id );
	}

	$questions = array();
	foreach ( (array) ( $data['questions'] ?? array() ) as $question ) {
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
	update_field( 'quiz_questions', $questions, $post_id );

	$profiles = array();
	foreach ( (array) ( $data['profiles'] ?? array() ) as $profile ) {
		$groups = array();
		foreach ( (array) ( $profile['groups'] ?? array() ) as $group ) {
			$items = array();
			foreach ( (array) ( $group['items'] ?? array() ) as $item ) {
				$items[] = array(
					'cat'   => (string) ( $item['cat'] ?? '' ),
					'title' => (string) ( $item['title'] ?? '' ),
					'link'  => (string) ( $item['link'] ?? '' ),
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
		);
	}
	update_field( 'quiz_profiles', $profiles, $post_id );
}

/**
 * Render an importer result notice after redirect.
 */
function wes_quiz_import_result_notice() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	$report = get_transient( 'wes_quiz_import_report' );
	if ( false === $report || ! is_array( $report ) ) {
		return;
	}
	delete_transient( 'wes_quiz_import_report' );

	$class = ! empty( $report['errors'] ) ? 'notice-error' : 'notice-success';
	echo '<div class="notice ' . esc_attr( $class ) . '"><p><strong>WES Quiz import:</strong></p><ul style="list-style:disc;margin-left:20px;">';
	foreach ( (array) ( $report['success'] ?? array() ) as $message ) {
		echo '<li>' . esc_html( $message ) . '</li>';
	}
	foreach ( (array) ( $report['warnings'] ?? array() ) as $message ) {
		echo '<li><strong>Warning:</strong> ' . esc_html( $message ) . '</li>';
	}
	foreach ( (array) ( $report['errors'] ?? array() ) as $message ) {
		echo '<li><strong>Error:</strong> ' . esc_html( $message ) . '</li>';
	}
	echo '</ul></div>';
}
add_action( 'admin_notices', 'wes_quiz_import_result_notice', 20 );

/**
 * Import all three languages from the existing JSON file.
 */
function wes_import_quiz_json() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You are not allowed to import quizzes.', 'wes' ) );
	}
	check_admin_referer( 'wes_import_quiz_json' );

	$file = get_template_directory() . '/assets/data/quiz.json';
	$raw  = file_exists( $file ) ? wp_json_file_decode( $file, array( 'associative' => true ) ) : null;
	$langs = array( 'en', 'ar', 'fr' );
	$report = array(
		'success'  => array(),
		'warnings' => array(),
		'errors'   => array(),
	);

	if ( empty( $raw ) || ! is_array( $raw ) ) {
		$report['errors'][] = 'The quiz JSON file could not be read or decoded.';
		set_transient( 'wes_quiz_import_report', $report, MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'edit.php?post_type=quiz' ) );
		exit;
	}

	$created = array();

	foreach ( $langs as $lang ) {
		if ( empty( $raw[ $lang ] ) || ! is_array( $raw[ $lang ] ) ) {
			$report['errors'][] = sprintf( '%s: language data is missing.', strtoupper( $lang ) );
			continue;
		}

		$validation = wes_validate_quiz_language( $raw[ $lang ], $lang );
		$report['warnings'] = array_merge( $report['warnings'], $validation['warnings'] );
		if ( ! empty( $validation['errors'] ) ) {
			$report['errors'] = array_merge( $report['errors'], $validation['errors'] );
			continue;
		}

		$existing = get_posts(
			array(
				'post_type'      => 'quiz',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_wes_quiz_import_lang',
				'meta_value'     => $lang,
			)
		);

		$post_id = ! empty( $existing ) ? (int) $existing[0] : wp_insert_post(
			array(
				'post_type'   => 'quiz',
				'post_status' => 'publish',
				'post_title'  => isset( $raw[ $lang ]['intro']['title'] ) ? wp_strip_all_tags( $raw[ $lang ]['intro']['title'] ) : 'Climate Quiz',
				'meta_input'  => array( '_wes_quiz_import_lang' => $lang ),
			)
		);

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			$report['errors'][] = sprintf( '%s: could not create or find the Quiz post.', strtoupper( $lang ) );
			continue;
		}

		wp_update_post(
			array(
			'ID'         => $post_id,
			'post_title' => isset( $raw[ $lang ]['intro']['title'] ) ? wp_strip_all_tags( $raw[ $lang ]['intro']['title'] ) : 'Climate Quiz',
			)
		);
		update_post_meta( $post_id, '_wes_quiz_import_lang', $lang );
		wes_import_quiz_language_fields( $post_id, $raw[ $lang ] );

		if ( function_exists( 'pll_set_post_language' ) ) {
			pll_set_post_language( $post_id, $lang );
		} else {
			$report['warnings'][] = sprintf( '%s: Polylang is not active; language was not assigned.', strtoupper( $lang ) );
		}

		$created[ $lang ] = $post_id;
		$report['success'][] = sprintf(
			'%s imported: %d questions, %d profiles. Post ID %d.',
			strtoupper( $lang ),
			$validation['questions'],
			$validation['profiles'],
			$post_id
		);
	}

	if ( count( $created ) >= 2 && function_exists( 'pll_save_post_translations' ) ) {
		pll_save_post_translations( $created );
		$report['success'][] = 'Polylang translation relationships were saved.';
	} elseif ( ! empty( $created ) && ! function_exists( 'pll_save_post_translations' ) ) {
		$report['warnings'][] = 'Polylang translation relationships could not be saved because Polylang is not active.';
	}

	if ( ! empty( $created ) && empty( $report['errors'] ) ) {
		update_option( 'wes_quiz_json_imported', 1, false );
	}

	set_transient( 'wes_quiz_import_report', $report, MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'edit.php?post_type=quiz' ) );
	exit;
}
add_action( 'admin_post_wes_import_quiz_json', 'wes_import_quiz_json' );
