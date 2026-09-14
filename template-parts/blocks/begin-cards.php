<?php
/**
 * Block: Begin Cards — illustrated category cards (circular image + category
 * eyebrow + title + org + overview + flags). Used by "Not sure where to begin?".
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_field( 'items' ) ?: array();
$lang  = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$lo    = array( 'en' => 'Publishing Organization:', 'ar' => 'الجهة الناشرة:', 'fr' => 'Organisme de publication :' )[ $lang ] ?? 'Publishing Organization:';
?>
<div class="section block-begin-cards">
	<div class="container">
		<div class="bcards bcards--<?php echo count( $items ) === 2 ? 'two' : 'three'; ?>">
			<?php
			foreach ( $items as $c ) :
				$flags   = array_filter( array_map( 'trim', explode( '|', (string) ( $c['flags'] ?? '' ) ) ) );
				$image   = $c['image'] ?? 0;
				$img_src = is_array( $image ) ? ( $image['url'] ?? '' ) : ( is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'medium' ) : $image );
				$link    = $c['link'] ?? '';

				// نفس خريطة الأعلام اللي عملناها في البلوك الأول
				$flag_map = array(
					'en' => 'gb.svg',
					'ar' => 'eg.svg',
					'fr' => 'fr.svg',
					'es' => 'es.svg',
				);
				?>
				<article class="bcard">
					<?php if ( $img_src ) : ?><span class="bcard__media"><img src="<?php echo esc_url( $img_src ); ?>" alt="" loading="lazy" /></span><?php endif; ?>
					<?php if ( ! empty( $c['category'] ) ) : ?><p class="bcard__cat"><?php echo esc_html( $c['category'] ); ?></p><?php endif; ?>
					<h3 class="bcard__title"><?php echo $link ? '<a href="' . esc_url( $link ) . '">' . esc_html( $c['title'] ?? '' ) . '</a>' : esc_html( $c['title'] ?? '' ); ?></h3>
					<?php if ( ! empty( $c['org'] ) ) : ?><p class="bcard__meta"><span class="rc2__label"><?php echo esc_html( $lo ); ?></span> <?php echo esc_html( $c['org'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $c['overview'] ) ) : ?><p class="bcard__text"><?php echo esc_html( $c['overview'] ); ?></p><?php endif; ?>
					
					<?php if ( $flags ) : ?>
						<span class="rc2__flags">
							<?php 
							foreach ( $flags as $f ) : 
								$f_clean = strtolower( $f );
								$flag_img = $flag_map[ $f_clean ] ?? $f_clean . '.svg'; 
								?>
								<img class="flagchip-img flagchip-img--<?php echo esc_attr( $f_clean ); ?>" 
									 src="<?php echo esc_url( wes_img( $flag_img ) ); ?>" 
									 alt="<?php echo esc_attr( strtoupper( $f_clean ) ); ?> flag" 
									 width="30" 
									 height="30" />
							<?php endforeach; ?>
						</span>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
		<?php $cta = get_field( 'cta' ); if ( $cta ) : ?>
			<div class="rc2__cta"><a class="btn btn--orange" href="<?php echo esc_url( get_field( 'cta_link' ) ?: '#' ); ?>"><?php echo esc_html( $cta ); ?>
				<svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true"><path d="M1.5 1.5l6 6-6 6" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a></div>
		<?php endif; ?>
	</div>
</div>