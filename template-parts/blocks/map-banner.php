<?php
/**
 * Block: Map Banner (teal Mediterranean section).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$banner     = get_field( 'banner' );
$map        = get_field( 'map' );
$media_type = get_field( 'media_type' );
$video      = get_field( 'video' );
?>
<section class="med block-map">
	<div class="container med__inner">
		<?php if ( $banner ) : ?><p class="med__banner"><?php echo esc_html( $banner ); ?></p><?php endif; ?>
		<?php if ( $media_type === 'video' && $video ) : ?>
			<div class="med__video" data-video-play>
				<video class="med__map" playsinline width="1039" preload="metadata">
					<source src="<?php echo esc_url( $video ); ?>" type="video/mp4">
				</video>
				<button class="med__playbtn" aria-label="<?php esc_attr_e( 'Play video', 'wes' ); ?>">
					<svg width="64" height="64" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
				</button>
			</div>
		<?php elseif ( $map ) : ?>
			<img class="med__map" src="<?php echo esc_url( $map ); ?>" alt="" width="1039" height="514">
		<?php endif; ?>
	</div>
</section>
