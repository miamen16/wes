<?php
/**
 * Block: Browse by Theme — rows (title + tag keywords + chevron) inside a white card.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_field( 'heading' );
$lead    = get_field( 'lead' );
$items   = get_field( 'items' ) ?: array();
?>
<div class="section block-theme-index">
	<div class="container">
		<?php if ( $heading ) : ?><h2 class="tindex__heading"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		<?php if ( $lead ) : ?><p class="tindex__lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
		<div class="tindex__card">
			<ul class="tindex">
				<?php foreach ( $items as $row ) : ?>
					<li class="tindex__row">
						<span class="tindex__name"><?php echo esc_html( $row['name'] ?? '' ); ?></span>
						<span class="tindex__tags"><?php echo esc_html( $row['tags'] ?? '' ); ?></span>
						<span class="tindex__chev" aria-hidden="true"><svg width="16" height="10" viewBox="0 0 16 10" fill="none"><path d="M1.5 1.5l6.5 6 6.5-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
