<?php
/**
 * Block: How-to Steps.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$h     = get_field( 'heading' );
$items = get_field( 'items' ) ?: array();
?>
<div class="section block-steps">
	<div class="container">
		<?php if ( $h ) : ?><h2 class="section__title"><?php echo esc_html( $h ); ?></h2><?php endif; ?>
		<ol class="steps">
			<?php foreach ( $items as $i => $s ) : ?>
				<li class="steps__item">
					<span class="steps__num"><?php echo (int) $i + 1; ?></span>
					<div class="steps__body">
						<h3 class="steps__title"><?php echo esc_html( $s['title'] ?? '' ); ?></h3>
						<?php if ( ! empty( $s['text'] ) ) : ?><p class="steps__text"><?php echo esc_html( $s['text'] ); ?></p><?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</div>
