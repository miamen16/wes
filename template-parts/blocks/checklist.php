<?php
/**
 * Block: Checklist.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$h     = get_field( 'heading' );
$items = get_field( 'items' ) ?: array();
?>
<div class="section block-checklist">
	<div class="container">
		<?php if ( $h ) : ?><h2 class="section__title"><?php echo esc_html( $h ); ?></h2><?php endif; ?>
		<ul class="checklist">
			<?php foreach ( $items as $it ) : ?>
				<li class="checklist__item">
					<svg class="checklist__check" width="26" height="26" viewBox="0 0 26 26" fill="none" aria-hidden="true"><circle cx="13" cy="13" r="13" fill="#A0D081"/><path d="M7 13.5l4 4 8-8.5" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<span><?php echo esc_html( $it['item'] ?? '' ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
