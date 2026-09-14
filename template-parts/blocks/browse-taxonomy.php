<?php
/**
 * Block: Browse by Taxonomy — Accordion layout displaying taxonomy terms and their related CPT posts.
 * Uses the exact same CSS classes for unified styling.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading       = get_field( 'heading' );
$lead          = get_field( 'lead' );
$chosen_tax    = get_field( 'target_taxonomy' ); // حقل ACF يرجع اسم الـ Taxonomy مثل 'theme'
$chosen_cpt    = get_field( 'target_post_type' ) ?: 'resource'; // حقل ACF يرجع اسم الـ CPT

// متغيرات ترتيب الـ Taxonomy والتصنيفات
$taxonomy_order = get_field( 'taxonomy_order' ) ?: 'name';

// متغيرات ترتيب المقالات والـ CPT داخل الأكورديون
$post_order     = get_field( 'post_order' ) ?: 'title';
$post_dir       = get_field( 'post_order_dir' ) ?: 'ASC';

// جلب التيرمز (Terms) الخاصة بالتاكسونومي المختارة بناءً على الترتيب المحدد
$terms = array();
if ( ! empty( $chosen_tax ) && taxonomy_exists( $chosen_tax ) ) {
	$terms = get_terms( array(
		'taxonomy'   => $chosen_tax,
		'hide_empty' => true,
		'orderby'    => $taxonomy_order,
		'order'      => 'ASC',
	) );
}
?>
<div class="section block-theme-index block-taxonomy-accordion">
	<div class="container">
		
		<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
			<div class="tindex__card">
				
				<?php if ( $heading ) : ?><h2 class="tindex__heading" style="margin-top: 0;"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="tindex__lead tindex-inside"><?php echo esc_html( $lead ); ?></p><?php endif; ?>

				<ul class="tindex">
					<?php foreach ( $terms as $term ) : 
						// جلب البوستات التابعة لكل Term بناءً على متغيرات الترتيب المحددة
						$posts_query = new WP_Query( array(
							'post_type'      => $chosen_cpt,
							'posts_per_page' => -1,
							'orderby'        => $post_order,
							'order'          => $post_dir,
							'tax_query'      => array(
								array(
									'taxonomy' => $chosen_tax,
									'field'    => 'term_id',
									'terms'    => $term->term_id,
								),
							),
						) );
						?>
						<li class="tindex__row-container" style="border-bottom: 1px solid #eee; list-style: none;">
							
							<div class="tindex__row js-tax-accordion-toggle">
								<span class="tindex__name"><?php echo esc_html( $term->name ); ?></span>
								
								<span class="tindex__tags">
									<?php echo ! empty( $term->description ) ? esc_html( $term->description ) : ''; ?>
								</span>
								
								<span class="tindex__chev" aria-hidden="true" style="transition: transform 0.3s ease; transform: rotate(180deg);">
									<svg width="16" height="10" viewBox="0 0 16 10" fill="none"><path d="M1.5 1.5l6.5 6 6.5-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</span>
							</div>

							<div class="tindex__content js-tax-accordion-content" ">
								<?php if ( $posts_query->have_posts() ) : ?>
									<ul class="tindex__posts-list" style="margin: 0; padding: 0; list-style: none;">
										<?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); 
											
											$current_post_id = get_the_ID();

											$custom_link = get_field( 'link', $current_post_id ); 
											$author      = get_field( 'custom_author', $current_post_id );
											$year        = get_field( 'year', $current_post_id );

											$has_link    = ! empty( $custom_link['url'] );
											$post_url    = $has_link ? $custom_link['url'] : '';
											$link_target = ! empty( $custom_link['target'] ) ? $custom_link['target'] : '_blank';
											?>
											<li style="list-style: none; margin-bottom: 12px;">
												
												<?php if ( $has_link ) : ?>
													<a href="<?php echo esc_url( $post_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" style="text-decoration: none; color: #333; font-weight: 500; display: inline-block;">
												<?php else : ?>
													<span style="color: #333; font-weight: 500; display: inline-block; cursor: default;">
												<?php endif; ?>

														<span class="res-title"><?php the_title(); ?></span>
														
														<?php if ( $author ) : ?>
															<span class="res-author" > - <?php echo esc_html( $author ); ?></span>
														<?php endif; ?>
														
														<?php if ( $year ) : ?>
															<span class="res-year">, <?php echo esc_html( $year ); ?></span>
														<?php endif; ?>

												<?php if ( $has_link ) : ?>
													</a>
												<?php else : ?>
													</span>
												<?php endif; ?>

											</li>
										<?php endwhile; wp_reset_postdata(); ?>
									</ul>
								<?php else : ?>
									<p style="margin: 0; font-size: 13px; color: #999;"><?php esc_html_e( 'No items found here.', 'wes' ); ?></p>
								<?php endif; ?>
							</div>

						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php 
add_action( 'wp_footer', function() { ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	const toggles = document.querySelectorAll('.js-tax-accordion-toggle');

	toggles.forEach(toggle => {
		toggle.addEventListener('click', function () {
			const container = this.closest('.tindex__row-container');
			const content = container.querySelector('.js-tax-accordion-content');
			const chevron = this.querySelector('.tindex__chev');

			const isOpen = container.classList.contains('is-open');

			// قفل جميع الأكورديونز الأخرى (One at a time)
			document.querySelectorAll('.tindex__row-container').forEach(item => {
				if (item !== container) {
					item.classList.remove('is-open');
					const panel = item.querySelector('.js-tax-accordion-content');
					const icon = item.querySelector('.tindex__chev');
					if (panel) panel.style.display = 'none';
					if (icon) icon.style.transform = 'rotate(180deg)'; // السهم الطبيعي للأسفل
				}
			});

			// التحكم في العنصر الحالي الضغط عليه
			if (!isOpen) {
				container.classList.add('is-open');
				content.style.display = 'block';
				chevron.style.transform = 'rotate(0deg)'; // يلف لفوق عند الفتح
			} else {
				container.classList.remove('is-open');
				content.style.display = 'none';
				chevron.style.transform = 'rotate(180deg)';
			}
		});
	});
});
</script>
<?php }, 100 ); ?>