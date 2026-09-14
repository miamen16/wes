<?php
/**
 * Block: Custom Hero (Vertically Centered + Custom/Fallback Emblem).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow       = get_field( 'eyebrow' );
$sub_eyebrow   = get_field( 'sub_eyebrow' );
$title         = get_field( 'title' );
$intro         = get_field( 'intro' );
$img           = get_field( 'image' );
$alt           = get_field( 'alt' );
$cta1          = get_field( 'cta1_label' );
$cta2          = get_field( 'cta2_label' );
$custom_emblem = get_field( 'emblem_image' ); // حقل الصورة الجديد للـ Emblem

$decor = ! $img;

$eyebrow_html = '';
if ( $eyebrow ) {
	$parts        = array_map( 'trim', explode( '·', $eyebrow, 2 ) );
	$eyebrow_html = '<p class="lesson-hero__eyebrow"><span class="lesson-hero__eyebrow-lead">' . esc_html( $parts[0] ) . '</span>';
	if ( ! empty( $parts[1] ) ) {
		$eyebrow_html .= '<span class="lesson-hero__eyebrow-sub">' . esc_html( $parts[1] ) . '</span>';
	}
	$eyebrow_html .= '</p>';
}

if ( $sub_eyebrow ) {
	$eyebrow_html .= '<p class="lesson-hero__sub-eyebrow" style="margin-top: 5px; margin-bottom: 10px; font-size: 0.9rem; font-weight: normal; opacity: 0.85;">' . esc_html( $sub_eyebrow ) . '</p>';
}

// تحديد الصورة للـ Emblem: إما المرفوعة أو اللوجو الافتراضي
$emblem_src = $custom_emblem ? esc_url( $custom_emblem ) : esc_url( wes_img( 'logo.svg' ) );
?>
<section class="custom-hero lesson-hero<?php echo $decor ? ' lesson-hero--decor' : ''; ?>">
	<div class="container lesson-hero__inner">
		<div class="lesson-hero__text">
			
			<?php if ( $decor ) : ?>
				<!-- ضفنا ستايل محاذاة في المنتصف عمودياً هنا -->
				<div class="lesson-hero__head" style="display: flex; align-items: center;">
					<img class="lesson-hero__emblem2" src="<?php echo $emblem_src; ?>" alt="" width="106" height="135">
					<div class="lesson-hero__headings">
						<?php echo $eyebrow_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<h1 class="lesson-hero__title"><?php echo esc_html( $title ?: 'Hero title' ); ?></h1>
						<?php if ( $intro ) : ?><p class="lesson-hero__intro"><?php echo nl2br( esc_html( $intro ) ); ?></p><?php endif; ?>
					</div>
				</div>
			<?php else : ?>
				<?php echo $eyebrow_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h1 class="lesson-hero__title"><?php echo esc_html( $title ?: 'Hero title' ); ?></h1>
				<?php if ( $intro ) : ?><p class="lesson-hero__intro"><?php echo nl2br( esc_html( $intro ) ); ?></p><?php endif; ?>
			<?php endif; ?>
			
			<?php if ( $cta1 || $cta2 ) : ?>
				<div class="lesson-hero__cta">
					<?php if ( $cta1 ) : ?><a class="btn btn--outline" href="<?php echo esc_url( get_field( 'cta1_link' ) ?: '#' ); ?>"><?php echo esc_html( $cta1 ); ?></a><?php endif; ?>
					<?php if ( $cta2 ) : ?><a class="btn btn--orange" href="<?php echo esc_url( get_field( 'cta2_link' ) ?: '#' ); ?>"><?php echo esc_html( $cta2 ); ?></a><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $img ) : ?><img class="lesson-hero__img" src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="eager"><?php endif; ?>
	</div>
	
	<img class="lesson-hero__cream-wave" src="<?php echo esc_url( wes_img( 'brand-wave-gold.svg' ) ); ?>" alt="" aria-hidden="true">
</section>