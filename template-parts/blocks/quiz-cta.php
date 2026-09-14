<?php
/**
 * Block: Quiz CTA (cream section, peach card: text left, illustration right).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_field( 'heading' );
$lead    = get_field( 'lead' );
$img     = get_field( 'image' );
$link    = get_field( 'link' );
?>
<section class="section block-quiz block-quiz--cream">
	<div class="container">
		<div class="quizcta">
			<div class="quizcta__text">
				<h2 class="quizcta__title"><?php echo esc_html( $heading ?: 'Is your climate changing?' ); ?></h2>
				<?php if ( $lead ) : ?><p class="quizcta__lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</div>
			<?php if ( $img ) : ?>
				<a class="quizcta__media" href="<?php echo esc_url( $link ?: '#' ); ?>" aria-label="<?php esc_attr_e( 'Take the quiz', 'wes' ); ?>">
					<img src="<?php echo esc_url( $img ); ?>" alt="" decoding="async">
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
