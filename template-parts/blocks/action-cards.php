<?php
/**
 * Block: Action Cards (badge + optional medallion).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_field( 'items' ) ?: array();
?>
<div class="section block-action-cards">
	<div class="container">
		<div class="action-grid">
			<?php
			foreach ( $items as $a ) :
				$cat  = $a['category'] ?? 'howto';
				$icon = ( 'checklist' === $cat ) ? 'icon-checklist.svg' : 'icon-howto.svg';
				?>
				<article class="action-card">
					<a class="action-card__link" href="<?php echo esc_url( $a['link'] ?: '#' ); ?>">
						<div class="action-card__media">
							<?php if ( ! empty( $a['medallion'] ) ) : ?>
								<span class="action-card__medallion"><img src="<?php echo esc_url( wes_img( 'badge-5-medallion.png' ) ); ?>" alt="" aria-hidden="true" width="108" height="138"></span>
							<?php endif; ?>
							<?php if ( ! empty( $a['image'] ) ) : ?>
								<img class="action-card__img" src="<?php echo esc_url( $a['image'] ); ?>" alt="<?php echo esc_attr( $a['title'] ?? '' ); ?>" decoding="async">
							<?php endif; ?>
						</div>
						<div class="action-card__meta">
							<span class="badge badge--<?php echo esc_attr( $cat ); ?>">
								<img src="<?php echo esc_url( wes_img( $icon ) ); ?>" alt="" aria-hidden="true" width="24" height="24">
								<?php echo esc_html( $a['label'] ?? '' ); ?>
							</span>
							<h3 class="action-card__title"><?php echo esc_html( $a['title'] ?? '' ); ?></h3>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</div>
