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
				'menu_name'    => 'Quizzes',
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
 * Import all three languages from the existing JSON file.
 */
function wes_import_quiz_json() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You are not allowed to import quizzes.', 'wes' ) );
	}
	check_admin_referer( 'wes_import_quiz_json' );

	$file = get_template_directory() . '/assets/data/quiz.json';
	$raw  = file_exists( $file ) ? json_decode( file_get_contents( $file ), true ) : array();
	$langs = array( 'en', 'ar', 'fr' );

	if ( empty( $raw ) || ! is_array( $raw ) ) {
		wp_die( esc_html__( 'The quiz JSON file could not be read.', 'wes' ) );
	}

	$created = array();

	foreach ( $langs as $lang ) {
		if ( empty( $raw[ $lang ] ) ) {
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
			continue;
		}

		update_post_meta( $post_id, '_wes_quiz_import_lang', $lang );
		wes_import_quiz_language_fields( $post_id, $raw[ $lang ] );

		if ( function_exists( 'pll_set_post_language' ) ) {
			pll_set_post_language( $post_id, $lang );
		}

		$created[ $lang ] = $post_id;
	}

	if ( count( $created ) > 1 && function_exists( 'pll_save_post_translations' ) ) {
		pll_save_post_translations( $created );
	}

	update_option( 'wes_quiz_json_imported', current_time( 'mysql' ), false );
	wp_safe_redirect( admin_url( 'edit.php?post_type=quiz&wes_quiz_imported=1' ) );
	exit;
}
add_action( 'admin_post_wes_import_quiz_json', 'wes_import_quiz_json' );

/**
 * Save one language section into the ACF fields.
 *
 * @param int   $post_id Quiz post ID.
 * @param array $data Language data.
 * @return void
 */
function wes_import_quiz_language_fields( $post_id, $data ) {
	$intro = isset( $data['intro'] ) && is_array( $data['intro'] ) ? $data['intro'] : array();
	$ui    = isset( $data['ui'] ) && is_array( $data['ui'] ) ? $data['ui'] : array();

	$map = array(
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

	foreach ( $map as $field => $value ) {
		update_field( $field, $value, $post_id );
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
			'multi'   => ! empty( $question['multi'] ) ? 1 : 0,
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
 * Show importer result notice.
 */
add_action( 'admin_notices', function () {
	if ( isset( $_GET['wes_quiz_imported'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>Quiz JSON imported into the EN / AR / FR Quiz posts.</p></div>';
	}
} );
