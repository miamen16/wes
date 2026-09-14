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

$wes_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$wes_quiz = function_exists( 'wes_quiz_data' ) ? wes_quiz_data( $wes_lang ) : null;

if ( empty( $wes_quiz ) ) {
	echo '<div class="block-empty container">Quiz content unavailable.</div>';
	return;
}
?>
<div class="quiz-app" data-quiz="<?php echo esc_attr( wp_json_encode( $wes_quiz, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ); ?>">
	<noscript><p class="container" style="padding:40px var(--gutter);"><?php echo esc_html( $wes_quiz['intro']['lead'] ?? '' ); ?></p></noscript>
</div>
