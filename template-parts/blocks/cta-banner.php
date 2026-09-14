<?php
/**
 * Block: CTA Banner (reuses the Communicating-section layout).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = get_field( 'title' );
$text  = get_field( 'text' );
$btn   = get_field( 'btn' );
$link  = get_field( 'link' );
$img   = get_field( 'image' );
$color = get_field( 'color' ) ?: 'teal';
?>
<div class="section comms-section block-cta">
	<div class="container">
		<div class="comms comms--<?php echo esc_attr( $color ); ?>">
			<div class="comms__text">
				<h2 class="comms__title"><?php echo esc_html( $title ?: 'Call to action' ); ?></h2>
				<?php if ( $text ) : ?><p class="comms__lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>
				<?php if ( $btn ) : ?>
					<a class="btn btn--orange" href="<?php echo esc_url( $link ?: '#' ); ?>"><?php echo esc_html( $btn ); ?>
						<svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true"><path d="M1.5 1.5l6 6-6 6" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</a>
				<?php endif; ?>
			</div>
			<?php if ( $img ) : ?><img class="comms__img" src="<?php echo esc_url( $img ); ?>" alt="" decoding="async"><?php endif; ?>
		</div>
	</div>
</div>
