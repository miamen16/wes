<?php
/**
 * Interactive Quiz block field configuration.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Quiz selector used by the Interactive Quiz block.
 */
function wes_register_quiz_block_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_block_quiz_app',
			'title'    => 'Interactive Quiz Settings',
			'fields'   => array(
				array(
					'key'           => 'field_quiz_app_post',
					'label'         => 'Quiz',
					'name'          => 'quiz_post',
					'type'          => 'post_object',
					'post_type'     => array( 'quiz' ),
					'field_type'    => 'select',
					'multiple'      => 0,
					'ui'            => 1,
					'ajax'          => 1,
					'return_format' => 'id',
					'allow_null'    => 0,
					'required'      => 1,
					'instructions'  => 'Select the Quiz post to display. Its Polylang translation will be loaded automatically for the current page language.',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/wes-quiz-app',
					),
				),
			),
			'position' => 'normal',
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'wes_register_quiz_block_fields', 25 );
