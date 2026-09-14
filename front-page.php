<?php
/**
 * Front page (Home) — content from ACF (Home Page field group) with Figma defaults.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- ===== Hero + Mediterranean band share a clip wrapper so the wave can blend across ===== -->
<div class="hero-med">
<!-- ============================= HERO ============================= -->
<section class="hero" id="top">
	<div class="container hero__inner">
		<img class="hero__graphic" src="<?php echo esc_url( wes_img( 'hero-graphic.svg' ) ); ?>" alt="" aria-hidden="true">
		<div class="hero__content">
			<img class="hero__logo" src="<?php echo esc_url( wes_field( 'hero_logo' ) ); ?>" alt="<?php esc_attr_e( 'Our Climate, Our Community', 'wes' ); ?>" width="106" height="135">
			<h1 class="hero__title"><?php echo nl2br( esc_html( wes_field( 'hero_title' ) ) ); ?></h1>
			<p class="hero__desc"><?php echo esc_html( wes_field( 'hero_subtitle' ) ); ?></p>
		</div>
	</div>
</section>

<!-- ===================== MEDITERRANEAN (teal map) ===================== -->
<section class="med" id="mediterranean-climate">
	<div class="container med__inner">
		<p class="med__banner"><?php echo esc_html( wes_field( 'med_banner' ) ); ?></p>
		<?php if ( wes_field( 'med_media_type' ) === 'video' ) : ?>
            <div class="med__video" data-video-play>
				<video class="med__map" playsinline width="1039" preload="metadata">
                    <source src="<?php echo esc_url( wes_field( 'med_video' ) ); ?>" type="video/mp4">
                </video>
                <button class="med__playbtn" aria-label="<?php esc_attr_e( 'Play video', 'wes' ); ?>">
					<svg width="64" height="64" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
				</button>
            </div>
        <?php else : ?>
		<img class="med__map" src="<?php echo esc_url( wes_field( 'med_map' ) ); ?>" alt="<?php esc_attr_e( 'Map of the Mediterranean dotted with cultural landmarks', 'wes' ); ?>" width="1039" height="514">
		<?php endif; ?>
		<a class="med__scroll" href="#understandingFP" aria-label="<?php esc_attr_e( 'Scroll to content', 'wes' ); ?>">
			<svg width="50" height="50" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4v15m0 0l-6-6m6 6l6-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</a>
	</div>
</section>
</div><!-- /.hero-med -->

<!-- ===================== UNDERSTANDING (explainer cards) ===================== -->
<section class="section section--cream understanding" id="understandingFP">
	<div class="container">
		<div class="section__head">
			<h2 class="section__title"><?php echo esc_html( wes_field( 'und_heading' ) ); ?></h2>
			<p class="section__lead"><?php echo esc_html( wes_field( 'und_lead' ) ); ?></p>
		</div>
        <div class="seg-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Content type', 'wes' ); ?>">
        	<button class="seg-tab is-active" role="tab" aria-selected="true" type="button" data-filter="explainer">
        		<?php echo esc_html( wes_field( 'und_tab1' ) ?: 'All Explainers' ); ?>
        	</button>
        	
        	<button class="seg-tab" role="tab" aria-selected="false" type="button" data-filter="infographic">
        		<?php echo esc_html( wes_field( 'und_tab2' ) ?: 'Infographics' ); ?>
        	</button>
        </div>
	</div>
	
    <div class="carousel" data-carousel>
    	<div class="carousel__track">
    	<?php
    		$explainer_args = array(
    			'post_type'      => array( 'explainer', 'infographic' ),
    			'posts_per_page' => -1,
    			'post_status'    => 'publish',
    		);
    
    		$explainer_query = new WP_Query( $explainer_args );
    
    		if ( $explainer_query->have_posts() ) :
    			while ( $explainer_query->have_posts() ) : $explainer_query->the_post(); 
    				
    				$card_id  = get_the_ID();
    				$card_cpt = get_post_type( $card_id );
    				
    				$title_strong = get_post_meta( $card_id, '_wes_title_strong', true ) ?: get_the_title();
    				$title_rest   = get_post_meta( $card_id, '_wes_title_rest', true );
    				$card_image   = get_the_post_thumbnail_url( $card_id, 'large' );
    				$card_link    = get_permalink();
    				?>
    				
    				<article class="ecard" data-type="<?php echo esc_attr( $card_cpt ); ?>">
    					<a class="ecard__link" href="<?php echo esc_url( $card_link ); ?>">
    				    <?php wes_new_badge( $card_id ); ?>
    						<div class="ecard__media">
    							<img src="<?php echo esc_url( $card_image ); ?>" alt="<?php echo esc_attr( $title_strong ); ?>" decoding="async">
    						</div>
    						<h3 class="ecard__title">
    							<span class="ecard__title-strong"><?php echo esc_html( $title_strong ); ?></span> 
    							<?php echo esc_html( $title_rest ); ?>
    						</h3>
    					</a>
    				</article>
    
    			<?php 
    			endwhile;
    			wp_reset_postdata();
    		endif; 
    		?>
    	</div>
    </div>

	<div class="carousel__dots" role="tablist" aria-label="<?php esc_attr_e( 'Slides', 'wes' ); ?>"></div>
</section>

<!-- ===================== WHAT YOU CAN DO ===================== -->
<section class="section what-you-can-do" id="take-actionFP">
	<div class="container">
		<div class="section__head">
			<h2 class="section__title"><?php echo esc_html( wes_field( 'wycd_heading' ) ); ?></h2>
			<p class="section__lead"><?php echo esc_html( wes_field( 'wycd_lead' ) ); ?></p>
		</div>
		<!--///////////////////////////////-------------------->
		<div class="seg-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Action type', 'wes' ); ?>">
			<button class="seg-tab is-active" role="tab" aria-selected="true" type="button" data-filter="howto">
				<?php echo esc_html( wes_field( 'wycd_tab1' ) ?: 'How-to Guides' ); ?>
			</button>
			<button class="seg-tab" role="tab" aria-selected="false" type="button" data-filter="checklist">
				<?php echo esc_html( wes_field( 'wycd_tab2' ) ?: 'Checklists' ); ?>
			</button>
		</div>		

		<div class="carousel" data-carousel>
			<div class="carousel__track">
				<?php
				// 1. Query both 'howto' and 'checklist' custom post types
				$action_args = array(
					'post_type'      => array( 'howto', 'checklist' ),
					'posts_per_page' => -1, // Brings all posts so you can scroll through them
					'post_status'    => 'publish',
				// 	'suppress_filters' => true,
				);

				$action_query = new WP_Query( $action_args );

				if ( $action_query->have_posts() ) :
					while ( $action_query->have_posts() ) : $action_query->the_post(); 
						
						$card_id    = get_the_ID();
						$card_cpt   = get_post_type( $card_id ); // 'howto' or 'checklist'
						$card_link  = get_permalink();
						$card_title = get_the_title();
						$card_image = get_the_post_thumbnail_url( $card_id, 'large' ) ?: 'https://via.placeholder.com/400x300';
						
						// Dynamic Badge configurations based on CPT
						$icon_file  = ( 'checklist' === $card_cpt ) ? 'icon-checklist.svg' : 'icon-howto.svg';
						
						// Get CPT object to automatically grab the Singular Label
						$badge_label = wes_get_badge_label( $card_cpt );

						$has_medallion = get_post_meta( $card_id, '_wes_has_medallion', true );
						?>
						
						<article class="action-card js-action-card" data-type="<?php echo esc_attr( $card_cpt ); ?>">
							<a class="action-card__link" href="<?php echo esc_url( $card_link ); ?>">
						    <?php wes_new_badge( $card_id ); ?>
								<div class="action-card__media">
									<?php if ( ! empty( $has_medallion ) ) : ?>
										<span class="action-card__medallion">
											<img src="<?php echo esc_url( wes_img( 'badge-5-medallion.png' ) ); ?>" alt="" aria-hidden="true" width="108" height="138">
										</span>
									<?php endif; ?>
									<img class="action-card__img" src="<?php echo esc_url( $card_image ); ?>" alt="<?php echo esc_attr( $card_title ); ?>" decoding="async">
								</div>
								<div class="action-card__meta">
									<span class="badge badge--<?php echo esc_attr( $card_cpt ); ?>">
										<img src="<?php echo esc_url( wes_img( $icon_file ) ); ?>" alt="" aria-hidden="true" width="24" height="24">
										<?php echo esc_html( $badge_label ); ?>
									</span>
									<h3 class="action-card__title"><?php echo esc_html( $card_title ); ?></h3>
								</div>
							</a>
						</article>

					<?php 
					endwhile;
					wp_reset_postdata();
				else :
					echo '<p>No items found.</p>';
				endif; 
				?>
			</div>
		</div>
		<div class="carousel__dots" role="tablist" aria-label="<?php esc_attr_e( 'Slides', 'wes' ); ?>"></div>
		<!--///////////////////////////////-------------------->
	</div>
</section>

<!-- ===================== QUIZ CTA ===================== -->
<section class="section section--sand quiz" id="quiz">
	<div class="container">
		<div class="quiz__head">
			<h2 class="section__title section__title--center"><?php echo esc_html( wes_field( 'quiz_heading' ) ); ?></h2>
			<p class="quiz__lead"><?php echo nl2br( esc_html( wes_field( 'quiz_lead' ) ) ); ?></p>
			<p class="quiz__note"><?php echo esc_html( wes_field( 'quiz_note' ) ); ?></p>
		</div>
		<a class="quiz__cta" href="<?php echo esc_url( wes_field( 'quiz_link' ) ); ?>" aria-label="<?php esc_attr_e( 'Take the quiz', 'wes' ); ?>">
			<img src="<?php echo esc_url( wes_field( 'quiz_image' ) ); ?>" alt="" width="556" height="547">
		</a>
	</div>
</section>

<!-- ===================== TEACH & FACILITATE ===================== -->
<section class="section teach" id="teachFP">
	<div class="container">
		<div class="teach__head">
			<h2 class="section__title section__title--center"><?php echo esc_html( wes_field( 'teach_heading' ) ); ?></h2>
			<p class="teach__lead"><?php echo esc_html( wes_field( 'teach_lead' ) ); ?></p>
		</div>
		<p class="teach__body"><?php echo esc_html( wes_field( 'teach_body' ) ); ?> <a class="readmore" href="<?php echo esc_url( wes_field( 'teach_readmore' ) ); ?>"><?php echo esc_html( wes_field( 'teach_readmore_label' ) ); ?></a></p>

		<div class="teach__cluster" id="teach__cluster" aria-label="<?php esc_attr_e( 'Audiences', 'wes' ); ?>">
			<span class="tcircle" style="left:78px;  top:304px; width:127px; height:127px;"></span>
			<span class="tcircle" style="left:0;     top:200px; width:78px;  height:78px;"></span>
			<span class="tcircle" style="left:403px; top:174px; width:59px;  height:59px;"></span>
			<span class="tcircle" style="left:638px; top:304px; width:100px; height:100px;"></span>
			<span class="tcircle" style="left:793px; top:260px; width:45px;  height:45px;"></span>

			<?php
			// Fixed layout presets per audience slot (positions are design, not editable).
			$aud_pos = array(
				array( 'x' => 93,  'y' => 20,  'w' => 281, 'h' => 257, 'iconw' => 135 ),
				array( 'x' => 479, 'y' => 0,   'w' => 296, 'h' => 269, 'iconw' => 125 ),
				array( 'x' => 238, 'y' => 273, 'w' => 366, 'h' => 284, 'iconw' => 103 ),
			);
			foreach ( (array) wes_field( 'teach_audiences' ) as $i => $aud ) :
				$p = $aud_pos[ $i ] ?? $aud_pos[0];
				?>
				<a class="aud aud--<?php echo esc_attr( $aud['blob'] ?? 'teal' ); ?>" href="<?php echo esc_url( $aud['link'] ?? '#' ); ?>"
					style="left:<?php echo (int) $p['x']; ?>px; top:<?php echo (int) $p['y']; ?>px; width:<?php echo (int) $p['w']; ?>px; height:<?php echo (int) $p['h']; ?>px;">
					<img class="aud__icon" src="<?php echo esc_url( $aud['icon'] ); ?>" alt="" aria-hidden="true" style="width:<?php echo (int) $p['iconw']; ?>px;">
					<span class="aud__label"><?php echo nl2br( esc_html( $aud['label'] ?? '' ) ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ===================== COMMUNICATING CLIMATE ===================== -->
<section class="section comms-section" id="communicating">
	<div class="container">
		<div class="comms">
			<div class="comms__text">
				<h2 class="comms__title"><?php echo esc_html( wes_field( 'comms_title' ) ); ?></h2>
				<p class="comms__lead"><?php echo esc_html( wes_field( 'comms_lead' ) ); ?></p>
				<a class="btn btn--orange" href="<?php echo esc_url( wes_field( 'comms_link' ) ); ?>">
					<?php echo esc_html( wes_field( 'comms_btn' ) ); ?>
					<svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true"><path d="M1.5 1.5l6 6-6 6" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</a>
			</div>
			<img class="comms__img" src="<?php echo esc_url( wes_field( 'comms_image' ) ); ?>" alt="<?php esc_attr_e( 'A journalist interviewing a climate expert', 'wes' ); ?>" width="488" height="399" decoding="async">
		</div>
	</div>
</section>

<!-- ===================== GET INVOLVED ===================== -->
<section class="section get-involved" id="get-involved">
	<div class="container">
		<div class="section__head section__head--gi">
			<h2 class="section__title"><?php echo esc_html( wes_field( 'gi_heading' ) ); ?></h2>
			<p class="gi__subtitle"><?php echo esc_html( wes_field( 'gi_subtitle' ) ); ?></p>
			<p class="gi__body"><?php echo esc_html( wes_field( 'gi_body' ) ); ?></p>
		</div>

		<div class="filters">
			<?php
			// Get current language (Polylang)
			$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
			$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
	$default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : '';

			// Translated "All …" labels
			$all_labels = array(
				'get_involved_topic' => array(
					'en' => 'All Topics',
					'fr' => 'Tous les thèmes',
					'ar' => 'جميع المواضيع',
				),
				'get_involved_country' => array(
					'en' => 'All Countries',
					'fr' => 'Tous les pays',
					'ar' => 'جميع البلدان',
				),
				'stakeholder' => array(
					'en' => 'All Stakeholders',
					'fr' => 'Tous les acteurs',
					'ar' => 'جميع أصحاب المصلحة',
				),
			);

			// Icon map
			$gi_icons = array(
				'map'    => '<svg class="filter__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#009BA6" stroke-width="1.6" aria-hidden="true"><path d="M9 4.5l6 2 4.5-2v13l-4.5 2-6-2-4.5 2v-13z M9 4.5v13 M15 6.5v13" stroke-linejoin="round" stroke-linecap="round"/></svg>',
				'folder' => '<svg class="filter__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#009BA6" stroke-width="1.6" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-linejoin="round"/><path d="M3 10h18" stroke-linecap="round"/></svg>',
				'people' => '<svg class="filter__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#009BA6" stroke-width="1.6" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0" stroke-linecap="round"/><path d="M16 6a3 3 0 0 1 0 6 M16.5 14.5a5.5 5.5 0 0 1 4 4.5" stroke-linecap="round"/></svg>',
			);
			$gi_icon_keys = array( 'map', 'folder', 'people' );
			$gi_dropdowns = array(
				'get_involved_topic'   => 'Topic',
				'get_involved_country' => 'Country',
				'stakeholder'          => 'Stakeholder',
			);
			$gi_i = 0;
			foreach ( $gi_dropdowns as $tax => $label ) :
				$terms = get_terms( array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
					'parent'     => 0,
					'lang'       => $default_lang,
				) );
				?>
				<span class="filter-wrap"><?php echo $gi_icons[ $gi_icon_keys[ $gi_i % 3 ] ]; // phpcs:ignore ?>
                <select class="filter" name="<?php echo esc_attr( $tax ); ?>">
                    <option value=""><?php echo esc_html( $all_labels[ $tax ][ $lang ] ?? $all_labels[ $tax ]['en'] ); ?></option>
                    <?php foreach ( $terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( wes_get_term_display_name( $term, $current_lang ) ); ?></option>
                    <?php endforeach; ?>
                </select>
				</span>
			<?php $gi_i++; endforeach; ?>
		</div>

		<div id="gi-org-grid" class="org-grid">
			<?php
			$gi_query_args = array(
				'post_type'      => 'get_involved',
				'posts_per_page' => 6,
				'paged'          => 1,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC',
			);
        	if ( $default_lang ) {
        		$gi_query_args['lang'] = $default_lang;
        	}
        	$gi_query = new WP_Query( $gi_query_args );
			if ( $gi_query->have_posts() ) :
				while ( $gi_query->have_posts() ) :
					$gi_query->the_post();
					$display_id = wes_get_display_post_id( get_the_ID(), $current_lang );
			        $url = get_field( 'external_url', $display_id ) ?: '#';
					?>
					<a class="org" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="org__name"><?php echo esc_html( get_the_title( $display_id ) ); ?></span>
						<svg class="org__ext" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</a>
				<?php endwhile;
			else :
				// Translated "No results" message
				$no_results = array(
					'en' => 'No resources found.',
					'fr' => 'Aucune ressource trouvée.',
					'ar' => 'لم يتم العثور على موارد.',
				);
				echo '<p class="gi__no-results">' . esc_html( $no_results[ $lang ] ?? $no_results['en'] ) . '</p>';
			endif;
			wp_reset_postdata();
			?>
		</div>
		<div class="gi-pagination" id="gi-pagination">
			<?php wes_gi_pagination( $gi_query ); ?>
		</div>
	</div>
</section>

<?php
get_footer();
