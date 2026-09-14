<?php
/**
 * Block: Audience Quick Guide — Dynamic CPT Version
 * Tabbed guide with audience reference cards from custom post types.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get block settings
$eyebrow = get_field( 'eyebrow' );
$heading = get_field( 'heading' );
$lead    = get_field( 'lead' );
$link_text = get_field( 'link_text' );
$link_url  = get_field( 'link_url' );

// Get dynamic settings
$post_type      = get_field( 'dynamic_post_type' ) ?: 'audience_guide';
$taxonomy       = get_field( 'dynamic_taxonomy' ) ?: 'audience_type';
$selected_terms = get_field( 'selected_terms' ) ?: array();
$exclude_empty  = (bool) get_field( 'exclude_empty_tabs' );
$show_all_tab   = (bool) get_field( 'show_all_tab' );
$posts_limit    = get_field( 'posts_limit' ) ?: -1;
$post_orderby   = get_field( 'post_orderby' ) ?: 'menu_order';
$post_order     = get_field( 'post_order' ) ?: 'ASC';
$tax_orderby    = get_field( 'tax_orderby' ) ?: 'menu_order';
$tax_order      = get_field( 'tax_order' ) ?: 'ASC';
$category_field = get_field( 'category_field' ) ?: 'category';
$text_field     = get_field( 'text_field' ) ?: 'text';

// Unique ID for this block instance
$uid = 'ag-' . wp_unique_id();

// SVG icon
$ext = '<svg class="ag-card__ext" viewBox="0 0 24 24" width="33" height="33" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>';

// Build query args
$args = array(
	'post_type'      => $post_type,
	'posts_per_page' => $posts_limit,
	'orderby'        => $post_orderby,
	'order'          => $post_order,
	'post_status'    => 'publish',
);

// Get all posts
$all_posts = get_posts( $args );

// Build tabs data from taxonomy terms
$tabs_data = array();

// Add "All" tab if enabled
if ( $show_all_tab && ! empty( $all_posts ) ) {
	$tabs_data[] = array(
		'label' => 'All',
		'posts' => $all_posts,
	);
}

// Get taxonomy terms
$term_args = array(
	'taxonomy'   => $taxonomy,
	'orderby'    => $tax_orderby,
	'order'      => $tax_order,
	'hide_empty' => $exclude_empty,
);

if ( ! empty( $selected_terms ) ) {
	$term_args['include'] = $selected_terms;
}

$terms = get_terms( $term_args );

// Group posts by term
$term_posts_map = array();
foreach ( $all_posts as $post ) {
	$post_terms = wp_get_post_terms( $post->ID, $taxonomy );
	
	foreach ( $post_terms as $term ) {
		// Skip if term not in selected list
		if ( ! empty( $selected_terms ) && ! in_array( $term->term_id, (array) $selected_terms ) ) {
			continue;
		}
		
		if ( ! isset( $term_posts_map[ $term->term_id ] ) ) {
			$term_posts_map[ $term->term_id ] = array();
		}
		$term_posts_map[ $term->term_id ][] = $post;
	}
}

// Build tabs from terms
foreach ( $terms as $term ) {
	$posts = isset( $term_posts_map[ $term->term_id ] ) ? $term_posts_map[ $term->term_id ] : array();
	
	if ( empty( $posts ) && $exclude_empty ) {
		continue;
	}
	
	$tabs_data[] = array(
		'label' => $term->name,
		'posts' => $posts,
	);
}

// If no tabs found, show message
if ( empty( $tabs_data ) ) {
	?>
	<div class="section block-audience-guide">
		<div class="container">
			<p class="ag__empty"><?php esc_html_e( 'No audience guides available.', 'wes' ); ?></p>
		</div>
	</div>
	<?php
	return;
}
?>

<div class="section block-audience-guide">
	<div class="container">
		<?php if ( $eyebrow ) : ?>
			<p class="ag__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		
		<?php if ( $heading ) : ?>
			<h2 class="ag__heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		
		<?php if ( $lead ) : ?>
			<p class="ag__lead"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>

		<?php if ( count( $tabs_data ) > 1 ) : ?>
			<div class="ag__tabs" role="tablist" data-ag-tabs>
				<?php foreach ( $tabs_data as $i => $tab ) : ?>
					<button type="button" class="ag__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" 
						role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						data-ag-tab="<?php echo esc_attr( $i ); ?>" 
						aria-controls="<?php echo esc_attr( "$uid-$i" ); ?>">
						<?php echo esc_html( $tab['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="ag__mobile-select-wrapper">
				<select class="ag__mobile-select" data-ag-select aria-label="Select Category">
					<?php foreach ( $tabs_data as $i => $tab ) : ?>
						<option value="<?php echo esc_attr( $i ); ?>" <?php selected( 0, $i ); ?>>
							<?php echo esc_html( $tab['label'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

		<?php foreach ( $tabs_data as $i => $tab ) : ?>
			<div class="ag__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" 
				id="<?php echo esc_attr( "$uid-$i" ); ?>" 
				role="tabpanel" data-ag-panel="<?php echo esc_attr( $i ); ?>"
				<?php echo 0 === $i ? '' : ' hidden'; ?>>
				
				<?php if ( ! empty( $tab['posts'] ) ) : ?>
					<div class="ag__refs">
						<?php foreach ( $tab['posts'] as $post ) : 
							setup_postdata( $post );
							
							// Get ACF fields
							$card_cat = get_field( $category_field, $post->ID );
							$card_text = get_field( $text_field, $post->ID );
							
							// Fallback to post fields if ACF fields are empty
							if ( empty( $card_cat ) ) {
								$post_terms = wp_get_post_terms( $post->ID, $taxonomy );
								$card_cat = ! empty( $post_terms ) ? $post_terms[0]->name : '';
							}
							
							if ( empty( $card_text ) ) {
								$card_text = get_the_excerpt( $post->ID );
							}
							
							$link = array(
								'url'    => get_permalink( $post->ID ),
								'target' => '_self',
							);
							?>
							
							<a href="<?php echo esc_url( $link['url'] ); ?>" 
								target="<?php echo esc_attr( $link['target'] ?: '_self' ); ?>" 
								class="ag-card">
								
								<?php if ( ! empty( $card_cat ) ) : ?>
									<p class="ag-card__cat"><?php echo esc_html( $card_cat ); ?></p>
								<?php endif; ?>
								
								<h3 class="ag-card__title">
									<?php echo esc_html( get_the_title( $post->ID ) ); ?>
									<?php echo $ext; // phpcs:ignore ?>
								</h3>
								
								<?php if ( ! empty( $card_text ) ) : ?>
									<p class="ag-card__text"><?php echo esc_html( $card_text ); ?></p>
								<?php endif; ?>
							</a>
							
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				<?php else : ?>
					<p class="ag__empty"><?php esc_html_e( 'References coming soon.', 'wes' ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		
		<?php if ( ! empty( $link_text ) && ! empty( $link_url ) ) : ?>
			<div class="ag__footer">
				<a href="<?php echo esc_url( $link_url ); ?>" class="ag__view-all">
					<?php echo esc_html( $link_text ); ?>
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>