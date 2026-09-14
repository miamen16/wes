<?php
/**
 * Block: Tools Cards — two columns on cream: illustration, title, source eyebrow,
 * body, orange button. (Matches the Communicating Climate "Tools you can use today".)
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_field( 'items' ) ?: array();
?>
<div class="section block-tools-cards">
	<div class="container">
		<div class="tools-cards">
			<?php
			foreach ( $items as $t ) :
				$img = $t['image'] ?? 0;
				$src = is_array( $img ) ? ( $img['url'] ?? '' ) : ( is_numeric( $img ) && $img ? wp_get_attachment_image_url( $img, 'medium' ) : $img );
				?>
				<article class="tcard">
					<?php if ( $src ) : ?><span class="tcard__media"><img src="<?php echo esc_url( $src ); ?>" alt="" loading="lazy" /></span><?php endif; ?>
					<h3 class="tcard__title"><?php echo esc_html( $t['title'] ?? '' ); ?></h3>
					<?php if ( ! empty( $t['source'] ) ) : ?><p class="tcard__source"><?php echo esc_html( $t['source'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $t['body'] ) ) : ?><p class="tcard__body"><?php echo esc_html( $t['body'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $t['button'] ) ) : ?>
						<a class="btn btn--orange" href="<?php echo esc_url( $t['link'] ?: '#' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $t['button'] ); ?>
							<svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true"><path d="M1.5 1.5l6 6-6 6" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</a>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</div>
