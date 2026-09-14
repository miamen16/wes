<?php
/**
 * Block: Page Hero.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bg      = get_field( 'bg' ) ?: 'white';
$eyebrow = get_field( 'eyebrow' );
$title   = get_field( 'title' );
$intro   = get_field( 'intro' );
$anchor  = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="page-hero page-hero--<?php echo esc_attr( $bg ); ?>"<?php echo $anchor; // phpcs:ignore ?>>
	<div class="container page-hero__inner">
		<?php if ( $eyebrow ) : ?><p class="page-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
		<h1 class="page-hero__title"><?php echo esc_html( $title ?: 'Page title' ); ?></h1>
		<?php if ( $intro ) : ?><p class="page-hero__intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
	</div>
</section>
