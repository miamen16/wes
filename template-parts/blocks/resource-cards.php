<?php
/**
 * Block: Resource Cards.
 * Rich variant (Figma): 2-col cards with publishing org, brief overview, language
 * flags, tag chips and an external-link icon. Falls back to the legacy dense
 * text-card grid when only cat/title/text/meta are supplied.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_field( 'items' ) ?: array();
$lang  = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$L     = array(
	'org'   => array( 'en' => 'Publishing Organization:', 'ar' => 'الجهة الناشرة:', 'fr' => 'Organisme de publication :' ),
	'brief' => array( 'en' => 'Brief Overview:', 'ar' => 'نظرة عامة موجزة:', 'fr' => 'Bref aperçu :' ),
);
$lo    = $L['org'][ $lang ] ?? $L['org']['en'];
$lb    = $L['brief'][ $lang ] ?? $L['brief']['en'];

// Is this the rich (Figma) shape?
$is_rich = false;
foreach ( $items as $c ) {
	if ( ! empty( $c['org'] ) || ! empty( $c['overview'] ) || ! empty( $c['flags'] ) ) {
		$is_rich = true;
		break;
	}
}

$ext = '<svg class="rc2__ext" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>';

if ( ! $is_rich ) :
	?>
	<div class="section block-resource-cards">
		<div class="container">
			<div class="rcards">
				<?php foreach ( $items as $c ) : ?>
					<article class="rcard">
						<?php if ( ! empty( $c['cat'] ) ) : ?><p class="rcard__cat"><?php echo esc_html( $c['cat'] ); ?></p><?php endif; ?>
						<h3 class="rcard__title"><?php echo esc_html( $c['title'] ?? '' ); ?></h3>
						<?php if ( ! empty( $c['text'] ) ) : ?><p class="rcard__text"><?php echo esc_html( $c['text'] ); ?></p><?php endif; ?>
						<?php if ( ! empty( $c['meta'] ) ) : ?><p class="rcard__meta"><?php echo esc_html( $c['meta'] ); ?></p><?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
	return;
endif;
?>
<div class="section block-resource-cards block-resource-cards--rich">
	<div class="container">
		<div class="rcards2">
			<?php
			foreach ( $items as $c ) :
				$flags = array_filter( array_map( 'trim', explode( '|', (string) ( $c['flags'] ?? '' ) ) ) );
				$tags  = array_filter( array_map( 'trim', explode( '|', (string) ( $c['tags'] ?? '' ) ) ) );
				$link  = $c['link'] ?? '';

				// Map the language code to your specific SVG filenames
				$flag_map = array(
					'en' => 'gb.svg',
					'ar' => 'eg.svg',
					'fr' => 'fr.svg',
					'es' => 'es.svg',
				);
				?>
				<article class="rc2">
					<?php if ( $link ) : ?><a class="rc2__link" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $c['title'] ?? '' ); ?>"><?php echo $ext; // phpcs:ignore ?></a><?php else : ?><span class="rc2__link"><?php echo $ext; // phpcs:ignore ?></span><?php endif; ?>
					<h3 class="rc2__title"><?php echo esc_html( $c['title'] ?? '' ); ?></h3>
					<?php if ( ! empty( $c['org'] ) ) : ?>
						<p class="rc2__meta"><span class="rc2__label"><?php echo esc_html( $lo ); ?></span> <?php echo esc_html( $c['org'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $c['overview'] ) ) : ?>
						<p class="rc2__meta"><span class="rc2__label"><?php echo esc_html( $lb ); ?></span> <?php echo esc_html( $c['overview'] ); ?></p>
					<?php endif; ?>
					<div class="rc2__foot">
						<?php if ( $flags ) : ?>
							<span class="rc2__flags">
								<?php 
								foreach ( $flags as $f ) : 
									// Lowercase the flag code to ensure a clean match in our map
									$f_clean = strtolower( $f );
									
									// Fallback to the code itself + .svg if it's not in our explicit map
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
						<?php if ( $tags ) : ?>
							<span class="rc2__tags">
								<?php foreach ( $tags as $t ) : ?><span class="rc2__tag"><?php echo esc_html( $t ); ?></span><?php endforeach; ?>
							</span>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php $cta = get_field( 'cta' ); if ( $cta ) : ?>
			<div class="rc2__cta"><a class="btn btn--orange" href="<?php echo esc_url( get_field( 'cta_link' ) ?: '#' ); ?>"><?php echo esc_html( $cta ); ?>
				<svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true"><path d="M1.5 1.5l6 6-6 6" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a></div>
		<?php endif; ?>
	</div>
</div>