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
 * Keep the Interactive Quiz block in ACF edit mode while configuring it.
 *
 * The block is registered centrally in inc/acf-blocks.php. This filter only
 * changes this block's editor mode so Gutenberg does not depend on the
 * server-rendered preview before the required Quiz field has been saved.
 *
 * ACF applies the acf/register_block_type_args filter with the block
 * arguments array only, so the block name must be read from the arguments.
 *
 * @param array $args Block registration arguments.
 * @return array
 */
function wes_quiz_block_editor_args( $args ) {
	$name = isset( $args['name'] ) ? (string) $args['name'] : '';

	if ( ! in_array( $name, array( 'wes-quiz-app', 'acf/wes-quiz-app' ), true ) ) {
		return $args;
	}

	$args['mode'] = 'edit';

	return $args;
}
add_filter( 'acf/register_block_type_args', 'wes_quiz_block_editor_args', 20 );

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
