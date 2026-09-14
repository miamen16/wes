<?php
/**
 * Block: Organisation Directory.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading  = get_field( 'heading' );
$subtitle = get_field( 'subtitle' );
$body     = get_field( 'body' );
$filters  = get_field( 'filters' ) ?: array();
$orgs     = get_field( 'orgs' ) ?: array();

$filter_icons = array(
	'map'    => '<svg class="filter__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#009BA6" stroke-width="1.6" aria-hidden="true"><path d="M9 4.5l6 2 4.5-2v13l-4.5 2-6-2-4.5 2v-13z M9 4.5v13 M15 6.5v13" stroke-linejoin="round" stroke-linecap="round"/></svg>',
	'folder' => '<svg class="filter__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#009BA6" stroke-width="1.6" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-linejoin="round"/><path d="M3 10h18" stroke-linecap="round"/></svg>',
	'people' => '<svg class="filter__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#009BA6" stroke-width="1.6" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0" stroke-linecap="round"/><path d="M16 6a3 3 0 0 1 0 6 M16.5 14.5a5.5 5.5 0 0 1 4 4.5" stroke-linecap="round"/></svg>',
);
$caret = '<svg class="filter__caret" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true"><path d="M1 1.5l5 5 5-5" stroke="#1A4A85" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<section class="section get-involved block-org">
	<div class="container">
		<div class="section__head section__head--gi">
			<h2 class="section__title"><?php echo esc_html( $heading ?: 'Get Involved' ); ?></h2>
			<?php if ( $subtitle ) : ?><p class="gi__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
			<?php if ( $body ) : ?><p class="gi__body"><?php echo esc_html( $body ); ?></p><?php endif; ?>
		</div>
		<?php if ( $filters ) : ?>
			<div class="filters">
				<?php foreach ( $filters as $f ) : $ic = $filter_icons[ $f['icon'] ?? 'map' ] ?? $filter_icons['map']; ?>
					<button class="filter" type="button"><?php echo $ic; // phpcs:ignore ?> <?php echo esc_html( $f['label'] ?? '' ); ?> <?php echo $caret; // phpcs:ignore ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( $orgs ) : ?>
			<div class="org-grid">
				<?php foreach ( $orgs as $org ) : ?>
					<a class="org" href="<?php echo esc_url( $org['link'] ?: '#' ); ?>">
						<span class="org__name"><?php echo esc_html( $org['name'] ?? '' ); ?></span>
						<svg class="org__ext" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ED6708" stroke-width="2" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
