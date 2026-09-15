<?php
/**
 * Block: Interactive Quiz ("Is your climate changing?").
 * Trilingual data comes from wes_quiz_data(); the experience is driven by quiz.js.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wes_lang    = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$wes_quiz_id = function_exists( 'get_field' ) ? absint( get_field( 'quiz_post' ) ) : 0;

/*
 * In the block editor a newly inserted block can render before the ACF
 * selector has a saved value. Never return an empty render in that state;
 * Gutenberg/ACF needs a stable server-side preview while the block is being
 * configured.
 */
if ( ! $wes_quiz_id ) {
	?>
	<div class="quiz-app quiz-app--editor-placeholder">
		<div class="container">
			<strong><?php echo esc_html__( 'Interactive Quiz', 'wes' ); ?></strong>
			<p><?php echo esc_html__( 'Select a Quiz in the block settings to configure this block.', 'wes' ); ?></p>
		</div>
	</div>
	<?php
	return;
}

$wes_quiz = function_exists( 'wes_quiz_data' ) ? wes_quiz_data( $wes_lang, $wes_quiz_id ) : null;

/* A configured block must still render a stable editor placeholder if its
 * selected Quiz cannot currently provide data. Do not make Gutenberg lose
 * the block because the data layer is temporarily unavailable.
 */
if ( ! is_array( $wes_quiz ) ) {
	?>
	<div class="quiz-app quiz-app--editor-placeholder">
		<div class="container">
			<strong><?php echo esc_html__( 'Interactive Quiz', 'wes' ); ?></strong>
			<p><?php echo esc_html__( 'The selected Quiz could not be loaded. Please check the Quiz post and ACF fields.', 'wes' ); ?></p>
		</div>
	</div>
	<?php
	return;
}
?>
<div class="quiz-app" data-quiz="<?php echo esc_attr( wp_json_encode( $wes_quiz, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ); ?>">
	<noscript><p class="container" style="padding:40px var(--gutter);"><?php echo esc_html( $wes_quiz['intro']['lead'] ?? '' ); ?></p></noscript>
</div>
