<?php
/**
 * Block: Illustrated Checklist (numbered rows with illustration).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_field( 'items' ) ?: array();
?>
<div class="section block-checklist-rows">
	<div class="container">
		<ol class="clist">
			<?php foreach ( $items as $i => $it ) : ?>
				<li class="clist__row">
					<?php if ( ! empty( $it['image'] ) ) : ?>
						<div class="clist__media"><img src="<?php echo esc_url( $it['image'] ); ?>" alt="<?php echo esc_attr( $it['title'] ?? '' ); ?>" decoding="async"></div>
					<?php endif; ?>
					<span class="clist__num clist__num--<?php echo esc_attr( $it['color'] ?? 'teal' ); ?>"><?php echo (int) $i + 1; ?></span>
					<div class="clist__body">
						<h3 class="clist__title"><?php echo esc_html( $it['title'] ?? '' ); ?></h3>
						<p class="clist__text"><?php echo esc_html( $it['text'] ?? '' ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</div>
