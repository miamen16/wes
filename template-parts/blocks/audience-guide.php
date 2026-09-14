<?php
/**
 * Block: Audience Quick Guide — tabs + 2-column academic reference list.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_field( 'heading' );
$lead    = get_field( 'lead' );
$tabs    = get_field( 'tabs' ) ?: array();
$cards   = get_field( 'cards' ) ?: array();
$uid     = 'ag-' . wp_unique_id();
$ext     = '<svg class="ag-card__ext" viewBox="0 0 24 24" width="33" height="33" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<div class="section block-audience-guide">
	<div class="container">
		<?php if ( $heading ) : ?><h2 class="ag__heading"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		<?php if ( $lead ) : ?><p class="ag__lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>

		<?php if ( $tabs ) : ?>
			<!-- الـ Tabs الأصلية بدون أي تعديل في الكلاسات أو البيانات -->
			<div class="ag__tabs" role="tablist" data-ag-tabs>
				<?php foreach ( $tabs as $i => $t ) : ?>
					<button type="button" class="ag__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab"
						aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						data-ag-tab="<?php echo esc_attr( $i ); ?>" aria-controls="<?php echo esc_attr( "$uid-$i" ); ?>">
						<?php echo esc_html( $t['label'] ?? '' ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<!-- الـ Select الخاص بالموبايل (تمت إضافته هنا) -->
			<div class="ag__mobile-select-wrapper">
				<select class="ag__mobile-select" data-ag-select aria-label="Select Category">
					<?php foreach ( $tabs as $i => $t ) : ?>
						<option value="<?php echo esc_attr( $i ); ?>" <?php selected( 0, $i ); ?>>
							<?php echo esc_html( $t['label'] ?? '' ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

		<?php
		foreach ( $tabs as $i => $t ) :
			$tab_cards = array_filter( $cards, function ( $c ) use ( $i ) { return (int) ( $c['tab'] ?? 0 ) === $i; } );
			?>
			<div class="ag__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" id="<?php echo esc_attr( "$uid-$i" ); ?>"
				role="tabpanel" data-ag-panel="<?php echo esc_attr( $i ); ?>"<?php echo 0 === $i ? '' : ' hidden'; ?>>
				<?php if ( $tab_cards ) : ?>
					<div class="ag__refs">
						<?php foreach ( $tab_cards as $c ) : ?>
						<?php $link = $c['link'] ?? null; ?>

						<<?php echo ! empty( $link['url'] ) ? 'a' : 'article'; ?>
							<?php if ( ! empty( $link['url'] ) ) : ?>
								href="<?php echo esc_url( $link['url'] ); ?>"
								target="<?php echo esc_attr( $link['target'] ?: '_self' ); ?>"
								<?php if ( '_blank' === ( $link['target'] ?? '' ) ) : ?>
									rel="noopener noreferrer"
									<?php endif; ?>
							<?php endif; ?>
							class="ag-card"
						>
							<?php if ( ! empty( $c['cat'] ) ) : ?>
								<p class="ag-card__cat"><?php echo esc_html( $c['cat'] ); ?></p>
							<?php endif; ?>
							
							<h3 class="ag-card__title">
								<?php echo esc_html( $c['title'] ?? '' ); ?>
								<?php echo $ext; // phpcs:ignore ?>
							</h3>
							
							<?php if ( ! empty( $c['text'] ) ) : ?>
								<p class="ag-card__text"><?php echo esc_html( $c['text'] ); ?></p>
							<?php endif; ?>
						</<?php echo ! empty( $link['url'] ) ? 'a' : 'article'; ?>>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="ag__empty"><?php esc_html_e( 'References coming soon.', 'wes' ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>