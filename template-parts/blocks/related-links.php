<?php
/**
 * Block: Related Links.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading  = get_field( 'heading' );
$subtitle = get_field( 'subtitle' );
$items    = get_field( 'items' ) ?: array();
$bg       = get_field( 'bg' ) ?: 'none';
?>
<div class="section block-related<?php echo 'cream' === $bg ? ' block-related--cream' : ''; ?>">
	<div class="container">
		<?php if ( $heading ) : ?><h2 class="related__heading"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		<?php if ( $subtitle ) : ?><p class="related__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
		<div class="related__list">
			<?php foreach ( $items as $r ) : 
				$icon_size  = ! empty( $r['icon_size'] ) ? intval( $r['icon_size'] ) : 20;
				$png_url    = $r['custom_png'] ?? ''; // رابط الـ PNG اللي غيرناه الخطوة اللي فاتت
				?>
				
                <a class="related__item" href="<?php echo esc_url( $r['link'] ?: '#' ); ?>" style="display: grid; grid-template-columns: 1fr auto; align-items: start; gap: 15px; text-decoration: none; width: 100%;">
					
					<div class="related__text-content" style="text-align: left;">
						<span class="related__cat"><?php echo esc_html( $r['category'] ?? '' ); ?></span>
						<span class="related__sep">–</span>
						<span class="related__title"><?php echo esc_html( $r['title'] ?? '' ); ?></span>
					</div>
					
					<div class="related__ext-wrapper" style="display: inline-flex; align-items: center; justify-content: center; width: <?php echo $icon_size; ?>px !important; height: <?php echo $icon_size; ?>px !important; flex-shrink: 0; margin-top: 4px;">
						<?php if ( ! empty( $png_url ) ) : ?>
							<img src="<?php echo esc_url( $png_url ); ?>" 
								 alt=""
								 style="width: <?php echo $icon_size; ?>px !important; height: <?php echo $icon_size; ?>px !important; max-width: none !important; object-fit: contain;">
						<?php else : ?>
							<svg class="related__ext" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" viewBox="0 0 24 24" fill="none" stroke="#ED6708" stroke-width="2" aria-hidden="true" style="width: <?php echo $icon_size; ?>px !important; height: <?php echo $icon_size; ?>px !important;">
								<path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						<?php endif; ?>
					</div>

				</a>
			<?php endforeach; ?>
		</div>
	</div>
</div>