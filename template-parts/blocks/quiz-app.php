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
 * Gutenberg/ACF must not depend on the server-side quiz data layer while the
 * block is being edited. The ACF field itself is the editor UI; the actual
 * quiz payload is only needed on the front end.
 *
 * This also prevents an invalid/missing Quiz translation, incomplete ACF
 * fields, or another data-layer issue from causing the editor block to vanish
 * immediately after selecting a Quiz post.
 */
if ( is_admin() ) {
	?>
	<div class="quiz-app quiz-app--editor-placeholder">
		<div class="container">
			<strong><?php echo esc_html__( 'Interactive Quiz', 'wes' ); ?></strong>
			<?php if ( $wes_quiz_id ) : ?>
				<p><?php echo esc_html__( 'Quiz selected. The quiz will use the selected Quiz content on the front end.', 'wes' ); ?></p>
			<?php else : ?>
				<p><?php echo esc_html__( 'Select a Quiz in the block settings to configure this block.', 'wes' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return;
}

$wes_quiz = function_exists( 'wes_quiz_data' ) ? wes_quiz_data( $wes_lang, $wes_quiz_id ) : null;

/* Never render an empty/invalid front-end quiz payload. */
if ( ! is_array( $wes_quiz ) ) {
	return;
}
?>
<div class="quiz-app" data-quiz="<?php echo esc_attr( wp_json_encode( $wes_quiz, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ); ?>">
	<noscript><p class="container" style="padding:40px var(--gutter);"><?php echo esc_html( $wes_quiz['intro']['lead'] ?? '' ); ?></p></noscript>
</div>
