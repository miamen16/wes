<?php
/**
 * Block: Featured Resource — a highlighted resource card with illustration.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = get_field( 'eyebrow' );
$title    = get_field( 'title' );
$org      = get_field( 'org' );
$overview = get_field( 'overview' );
$flags    = array_filter( array_map( 'trim', explode( '|', (string) get_field( 'flags' ) ) ) );
$tags     = array_filter( array_map( 'trim', explode( '|', (string) get_field( 'tags' ) ) ) );
$image    = get_field( 'image' );
$link     = get_field( 'link' );

$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$L    = array(
	'eyebrow' => array( 'en' => 'Featured Resource', 'ar' => 'مورد مميّز', 'fr' => 'Ressource à la une' ),
	'org'     => array( 'en' => 'Publishing Organization:', 'ar' => 'الجهة الناشرة:', 'fr' => 'Organisme de publication :' ),
	'brief'   => array( 'en' => 'Brief Overview:', 'ar' => 'نظرة عامة موجزة:', 'fr' => 'Bref aperçu :' ),
);
$eyebrow = $eyebrow ?: ( $L['eyebrow'][ $lang ] ?? $L['eyebrow']['en'] );
$lo      = $L['org'][ $lang ] ?? $L['org']['en'];
$lb      = $L['brief'][ $lang ] ?? $L['brief']['en'];
$img_src = is_array( $image ) ? ( $image['url'] ?? '' ) : ( is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'large' ) : $image );

// خريطة تحويل اختصارات اللغات إلى ملفات الـ SVG المطلوبة
$flag_map = array(
	'en' => 'gb.svg',
	'ar' => 'eg.svg',
	'fr' => 'fr.svg',
	'es' => 'es.svg',
);
?>
<div class="section block-featured-resource">
	<div class="container">
		<article class="fresource">
			<div class="fresource__body">
				<?php if ( $eyebrow ) : ?><p class="fresource__eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<h3 class="fresource__title"><?php echo esc_html( $title ); ?></h3>
				<?php if ( $org ) : ?><p class="fresource__meta"><span class="rc2__label"><?php echo esc_html( $lo ); ?></span> <?php echo esc_html( $org ); ?></p><?php endif; ?>
				<?php if ( $overview ) : ?><p class="fresource__meta"><span class="rc2__label"><?php echo esc_html( $lb ); ?></span> <?php echo esc_html( $overview ); ?></p><?php endif; ?>
				<div class="rc2__foot">
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
					
					<?php if ( $tags ) : ?><span class="rc2__tags"><?php foreach ( $tags as $t ) : ?><span class="rc2__tag"><?php echo esc_html( $t ); ?></span><?php endforeach; ?></span><?php endif; ?>
				</div>
			</div>
			<?php if ( $img_src ) : ?>
				<div class="fresource__media">
					<img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
					<?php if ( $link ) : ?><a class="fresource__link" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>"></a><?php endif; ?>
				</div>
			<?php endif; ?>
		</article>
	</div>
</div>