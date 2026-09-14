<?php
/**
 * Footer: "Stay connected" band + funder logos + legal row (from Site Settings options).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main><!-- .site-main -->

<!-- ===================== STAY CONNECTED ===================== -->
<section class="stay-connected">
	<div class="container stay__inner">
		<div class="stay__text">
			<h2 class="stay__title"><?php echo esc_html( wes_opt( 'stay_title' ) ); ?></h2>
			<p class="stay__desc"><?php echo esc_html( wes_opt( 'stay_desc' ) ); ?></p>
		</div>
			<div class="stay-mailster">

		<div class="stay__social">
			<?php foreach ( (array) wes_opt( 'stay_social' ) as $s ) : ?>
				<a class="social" href="<?php echo esc_url( $s['url'] ?? '#' ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $s['network'] ?? '' ) ); ?>">
					<?php echo wes_social_icon( $s['network'] ?? 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			<?php endforeach; ?>
		</div>
						<div class="mailster">
                       <?php echo do_shortcode( wes_opt( 'newsletter_shortcode' ) ); ?>
				</div>
		</div>
	</div>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="site-footer">
	<div class="container">
		<div class="footer__logos">
			<?php
			$logo_class = array( 'small' => 'footer__logo--1', 'wide' => 'footer__logo--3' );
			foreach ( (array) wes_opt( 'footer_logos' ) as $i => $logo ) :
				$cls = ( 'wide' === ( $logo['size'] ?? 'small' ) ) ? 'footer__logo--3' : ( 1 === $i ? 'footer__logo--2' : 'footer__logo--1' );
				?>
				<img src="<?php echo esc_url( $logo['image'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ?? '' ); ?>" class="footer__logo <?php echo esc_attr( $cls ); ?>" loading="lazy">
			<?php endforeach; ?>
		</div>
		<hr class="footer__divider">
		<div class="footer__legal">
			<p class="footer__funding"><?php echo esc_html( wes_opt( 'footer_funding' ) ); ?></p>
			<nav class="footer__links" aria-label="<?php esc_attr_e( 'Legal', 'wes' ); ?>">
				<?php foreach ( (array) wes_opt( 'footer_links' ) as $link ) : ?>
					<a href="<?php echo esc_url( $link['url'] ?? '#' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $link['label'] ?? '' ); ?></a>
				<?php endforeach; ?>
			</nav>
			<p class="footer__copy"><?php echo esc_html( wes_opt( 'footer_copyright' ) ); ?></p>
		</div>
	</div>
</footer>

<!-- ===================== ABOUT MODAL ===================== -->
<div class="wes-modal" id="about" hidden>
	<div class="wes-modal__overlay" data-modal-close></div>
	<div class="wes-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="about-title">
		<button class="wes-modal__close" type="button" data-modal-close aria-label="<?php esc_attr_e( 'Close', 'wes' ); ?>">&times;</button>
		<h2 class="wes-modal__title" id="about-title"><?php echo esc_html( wes_opt( 'about_heading' ) ); ?></h2>
		<div class="wes-modal__body"><?php echo wp_kses_post( wes_opt( 'about_content' ) ); ?></div>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>