<?php
/**
 * Block: Media + Text.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image = get_field( 'image' );
$alt   = get_field( 'alt' );
$body  = get_field( 'body' );
$pos   = get_field( 'position' ) ?: 'left';
?>
<div class="block-media-text">
	<div class="container">
		<div class="media-text media-text--img-<?php echo esc_attr( $pos ); ?>">
			<?php if ( $image ) : ?><div class="media-text__media"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" decoding="async"></div><?php endif; ?>
			<div class="media-text__body"><?php echo $body ? wp_kses_post( $body ) : ''; // phpcs:ignore ?></div>
		</div>
	</div>
</div>
