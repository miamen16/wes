<?php
/**
 * Block: Step Cards (coloured numbered grid).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_field( 'items' ) ?: array();
?>
<div class="section block-step-cards">
	<div class="container">
		<div class="step-cards">
			<?php foreach ( $items as $i => $s ) : ?>
				<article class="step-card step-card--<?php echo esc_attr( $s['color'] ?? 'orange' ); ?>">
					<span class="step-card__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<div class="step-card__body">
						<h3 class="step-card__label"><?php echo esc_html( $s['label'] ?? '' ); ?></h3>
						<h4 class="step-card__title"><?php echo esc_html( $s['title'] ?? '' ); ?></h4>
						<?php foreach ( preg_split( '/\n+/', (string) ( $s['text'] ?? '' ) ) as $para ) : ?>
							<?php if ( trim( $para ) !== '' ) : ?><p class="step-card__text"><?php echo esc_html( trim( $para ) ); ?></p><?php endif; ?>
						<?php endforeach; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</div>
