<?php
/**
 * Custom post types + taxonomy for the repeatable article types.
 *
 * Explainers, How-to Guides and Checklists each have many articles; the single
 * design we built becomes the single template, and each type gets an archive.
 * A shared "Topic" taxonomy powers archive filtering.
 *
 * @package WES
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** The article CPTs: post_type => [singular, plural, rewrite slug, dashicon]. */
function wes_article_types() {
	return array(
		'explainer' => array( 'Explainer', 'Explainers', 'explainers', 'dashicons-book-alt' ),
		'infographic' => array( 'Infographic', 'Infographics', 'infographics', 'dashicons-chart-bar' ),
		'howto'     => array( 'How-to Guide', 'How-to Guides', 'how-to-guides', 'dashicons-list-view' ),
		'checklist' => array( 'Checklist', 'Checklists', 'checklists', 'dashicons-yes-alt' ),
		'get_involved' => array( 'Get Involved Resource', 'Get Involved Resources', 'get-involved', 'dashicons-groups' ),
	);
}

/**
 * Get the translated badge label for action cards (How-to / Checklist).
 */
function wes_get_badge_label( $post_type ) {
    // Default to English if Polylang isn't active
    $current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    
    // Static translation map (easy to add more languages)
    $translations = array(
        'en' => array(
            'howto'     => 'How-to Guide',
            'checklist' => 'Checklist',
            'explainer' => 'Explainer',
            'infographic' => 'Infographic',
            'get_involved' => 'Get Involved',
            'post'      => 'Article',
            'page'      => 'Page',   
        ),
        'fr' => array(
            'howto'     => 'Guide pratique',
            'checklist' => 'Checklist en 5 étapes',
            'explainer' => 'Décryptage',
            'infographic' => 'Infographie',
            'get_involved' => 'Participer',
            'post'      => 'Article',
            'page'      => 'Page',
        ),
        'ar' => array(
            'howto'     => 'دليل إرشادي',
            'checklist' => 'قائمة من ٥ خطوات',
            'explainer' => 'شرح',
            'infographic' => 'إنفوغرافيك',
            'get_involved' => 'شارك معنا', // example
            'post'      => 'مقالة',
            'page'      => 'صفحة',
        ),
    );
    
    // Return the translated label, or fallback to uppercase post type name
    return $translations[ $current_lang ][ $post_type ] ?? ucfirst( $post_type );
}

/**
 * Check if a post should display the "NEW" badge.
 *
 * Logic:
 * Manual only: returns true when _wes_is_new meta is "1".
 * New posts default to checked (see meta box default_value).
 */
function wes_is_post_new( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return false;
	}
	return get_post_meta( $post_id, '_wes_is_new', true ) === '1';
}

/**
 * Output the NEW badge markup if the post is new.
 */
function wes_new_badge( $post_id = null ) {
	if ( ! wes_is_post_new( $post_id ) ) {
		return;
	}
	$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$labels = array(
		'en' => 'NEW',
		'ar' => 'جديد',
		'fr' => 'NOUVEAU',
	);
	$text = $labels[ $lang ] ?? 'NEW';
	echo '<div class="new-badge"><span class="new-badge__dot">⬤</span><span class="new-badge__text">' . esc_html( $text ) . '</span></div>';
}

/**
 * Register the "Is New?" meta box for article CPTs.
 */
add_action( 'add_meta_boxes', function () {
	$types = array( 'explainer', 'infographic', 'howto', 'checklist' );
	foreach ( $types as $type ) {
		add_meta_box(
			'wes_is_new',
			__( 'New Badge', 'wes' ),
			function ( $post ) {
				$value = get_post_meta( $post->ID, '_wes_is_new', true );
				wp_nonce_field( 'wes_is_new_nonce', 'wes_is_new_nonce_field' );
                // Default to checked for new posts (empty meta = new post).
                $checked = $value === '1' || $value === '';
				?>
				<label>
					<input type="checkbox" name="_wes_is_new" value="1" <?php checked( $checked ); ?>>
					<?php esc_html_e( 'Mark this post as "NEW"', 'wes' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Uncheck to remove the NEW badge from this post.', 'wes' ); ?></p>
				<?php
			},
			$type,
			'side',
			'low'
		);
	}
} );

add_action( 'save_post', function ( $post_id ) {
	if ( ! isset( $_POST['wes_is_new_nonce_field'] ) || ! wp_verify_nonce( $_POST['wes_is_new_nonce_field'], 'wes_is_new_nonce' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$types = array( 'explainer', 'infographic', 'howto', 'checklist' );
	if ( ! in_array( get_post_type( $post_id ), $types, true ) ) {
		return;
	}
	if ( isset( $_POST['_wes_is_new'] ) ) {
		update_post_meta( $post_id, '_wes_is_new', '1' );
	} else {
		update_post_meta( $post_id, '_wes_is_new', '0' );
	}
} );


add_action( 'init', function () {
	// 1. Register Core Article CPTs
	foreach ( wes_article_types() as $type => $cfg ) {
		list( $singular, $plural, $slug, $icon ) = $cfg;
		register_post_type( $type, array(
			'labels'       => array(
				'name'               => $plural,
				'singular_name'      => $singular,
				'add_new_item'       => 'Add New ' . $singular,
				'edit_item'          => 'Edit ' . $singular,
				'new_item'           => 'New ' . $singular,
				'view_item'          => 'View ' . $singular,
				'search_items'       => 'Search ' . $plural,
				'not_found'          => 'No ' . strtolower( $plural ) . ' found',
				'all_items'          => 'All ' . $plural,
				'menu_name'          => $plural,
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => $icon,
			'rewrite'      => array( 'slug' => $slug, 'with_front' => false ),
			'supports' => ( $type === 'get_involved' ) 
                ? array( 'title', 'thumbnail', 'custom-fields' )   // بدون محرر
                : array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes' ),
			'show_in_rest' => true,
			'menu_position'=> 22,
		) );
	}

	// ---- Shared Topic for article types (explainer, infographic, howto, checklist) ----
	register_taxonomy( 'topic', array( 'explainer', 'infographic', 'howto', 'checklist' ), array(
		'labels'            => array(
			'name'          => 'Topics',
			'singular_name' => 'Topic',
			'menu_name'     => 'Topics',
			'all_items'     => 'All Topics',
			'add_new_item'  => 'Add New Topic',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'topic', 'with_front' => false ),
	) );

	/* -------------------------------------------------------------------------
	 * RESOURCES: Resources CPT + Theme Taxonomy (Super Simple Layout)
	 * ------------------------------------------------------------------------- */
	
	register_post_type( 'resource', array(
		'labels'       => array(
			'name'               => 'Resource Books',
			'singular_name'      => 'Resource Book',
			'add_new_item'       => 'Add New Book',
			'edit_item'          => 'Edit Book',
			'all_items'          => 'All Resource Books',
			'menu_name'          => 'Resource Books',
		),
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-media-document',
		'rewrite'      => array( 'slug' => 'resources', 'with_front' => false ),
		'supports'     => array( 'title', 'thumbnail' , 'editor' ),
		'show_in_rest' => false,
		'menu_position'=> 23,
	) );

	register_taxonomy( 'theme', 'resource', array(
		'labels'            => array(
			'name'          => 'Themes',
			'singular_name' => 'Theme',
			'menu_name'     => 'Themes',
			'all_items'     => 'All Themes',
			'add_new_item'  => 'Add New Theme',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => false,
		'rewrite'           => array( 'slug' => 'theme', 'with_front' => false ),
	) );
	
	// Target Audiences Taxonomy for Resource Books
    register_taxonomy( 'target_audience', 'resource', array(
    	'labels'            => array(
    		'name'              => 'Target Book Audiences',
    		'singular_name'     => 'Target Book Audience',
    		'menu_name'         => 'Target Book Audiences',
    		'all_items'         => 'All Audiences',
    		'add_new_item'      => 'Add New Audience',
    		'edit_item'         => 'Edit Audience',
    		'update_item'       => 'Update Audience',
    		'search_items'      => 'Search Audiences',
    		'not_found'         => 'No audiences found',
    		'back_to_items'     => 'Back to audiences',
    		'choose_from_most_used' => 'Choose from most used audiences',
    	),
    	'public'            => true,
    	'hierarchical'      => true,
    	'show_admin_column' => true,
    	'show_in_rest'      => false,
    	'rewrite'           => array( 'slug' => 'audience', 'with_front' => false ),
    ) );
    
    // Types for Resource Books
    register_taxonomy( 'r-type', 'resource', array(
    	'labels'            => array(
    		'name'              => 'Types',
    		'singular_name'     => 'Type',
    		'menu_name'         => 'Type',
    		'all_items'         => 'All Types',
    		'add_new_item'      => 'Add New type',
    		'edit_item'         => 'Edit type',
    		'update_item'       => 'Update type',
    		'search_items'      => 'Search type',
    		'not_found'         => 'No type found',
    		'back_to_items'     => 'Back to types',
    		'choose_from_most_used' => 'Choose from most used types',
    	),
    	'public'            => true,
    	'hierarchical'      => true,
    	'show_admin_column' => true,
    	'show_in_rest'      => false,
    	'rewrite'           => array( 'slug' => 'resource-type', 'with_front' => false ),
    ) );

	/* -------------------------------------------------------------------------
	 * RESOURCE CARDS CPT + Taxonomies
	 * ------------------------------------------------------------------------- */
	
	register_post_type( 'resource_card', array(
		'labels'       => array(
			'name'               => 'Resource',
			'singular_name'      => 'Resource',
			'add_new_item'       => 'Add New Resource',
			'edit_item'          => 'Edit Resource',
			'all_items'          => 'All Resource',
			'menu_name'          => 'Resource',
		),
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-grid-view',
		'rewrite'      => array( 'slug' => 'resource-cards', 'with_front' => false ),
		'supports'     => array( 'title', 'thumbnail' ),
		'taxonomies'   => array( 'post_tag' ),
		'show_in_rest' => true,
		'menu_position'=> 24,
	) );

	register_taxonomy( 'resource_action_type', 'resource_card', array(
		'labels'            => array(
			'name'          => 'Action Types',
			'singular_name' => 'Action Type',
			'menu_name'     => 'Action Types',
			'all_items'     => 'All Action Types',
			'add_new_item'  => 'Add New Action Type',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'resource-action-type', 'with_front' => false ),
	) );

	register_taxonomy( 'resource_audience', 'resource_card', array(
		'labels'            => array(
			'name'          => 'Target Audience',
			'singular_name' => 'Audience',
			'menu_name'     => 'Target Audience',
			'all_items'     => 'All Audiences',
			'add_new_item'  => 'Add New Audience',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'resource-audience', 'with_front' => false ),
	) );

	register_taxonomy( 'resource_country', 'resource_card', array(
		'labels'            => array(
			'name'          => 'Countries',
			'singular_name' => 'Country',
			'menu_name'     => 'Countries',
			'all_items'     => 'All Countries',
			'add_new_item'  => 'Add New Country',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'resource-country', 'with_front' => false ),
	) );

	/* -------------------------------------------------------------------------
	 * GET INVOLVED RESOURCES – Dedicated Taxonomies (separate from others)
	 * ------------------------------------------------------------------------- */
	
	// 1. Topic specific to Get Involved
	register_taxonomy( 'get_involved_topic', 'get_involved', array(
		'labels'            => array(
			'name'              => 'Topics (Get Involved)',
			'singular_name'     => 'Topic',
			'menu_name'         => 'Topics',
			'all_items'         => 'All Topics',
			'add_new_item'      => 'Add New Topic',
			'edit_item'         => 'Edit Topic',
			'update_item'       => 'Update Topic',
			'search_items'      => 'Search Topics',
			'not_found'         => 'No topics found',
			'back_to_items'     => 'Back to topics',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'gi-topic', 'with_front' => false ),
	) );

	// 2. Country specific to Get Involved
	register_taxonomy( 'get_involved_country', 'get_involved', array(
		'labels'            => array(
			'name'              => 'Countries (Get Involved)',
			'singular_name'     => 'Country',
			'menu_name'         => 'Countries',
			'all_items'         => 'All Countries',
			'add_new_item'      => 'Add New Country',
			'edit_item'         => 'Edit Country',
			'update_item'       => 'Update Country',
			'search_items'      => 'Search Countries',
			'not_found'         => 'No countries found',
			'back_to_items'     => 'Back to countries',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'gi-country', 'with_front' => false ),
	) );

	// 3. Stakeholder (already planned)
	register_taxonomy( 'stakeholder', 'get_involved', array(
		'labels'            => array(
			'name'              => 'Stakeholders',
			'singular_name'     => 'Stakeholder',
			'menu_name'         => 'Stakeholders',
			'all_items'         => 'All Stakeholders',
			'add_new_item'      => 'Add New Stakeholder',
			'edit_item'         => 'Edit Stakeholder',
			'update_item'       => 'Update Stakeholder',
			'search_items'      => 'Search Stakeholders',
			'not_found'         => 'No stakeholders found',
			'back_to_items'     => 'Back to stakeholders',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'stakeholder', 'with_front' => false ),
	) );

} );


/* -------------------------------------------------------------------------
 * Register ACF Fields Programmatically (unchanged)
 * ------------------------------------------------------------------------- */

add_action( 'acf/init', function() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		
		// 1. جروب حقول الـ Resource Books القديمة
		acf_add_local_field_group( array(
			'key' => 'group_wes_resource_settings',
			'title' => 'Resource Settings',
			'fields' => array(
				array(
					'key' => 'field_wes_custom_author',
					'label' => 'Custom Author',
					'name' => 'custom_author',
					'type' => 'text',
					'required' => 0,
					'placeholder' => 'Enter author name...',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key' => 'field_wes_resource_year',
					'label' => 'Year',
					'name' => 'year',
					'type' => 'text',
					'required' => 0,
					'placeholder' => 'e.g. 2026',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key' => 'field_ag_clink',
					'label' => 'Link',
					'name' => 'link',
					'type' => 'link',
					'return_format' => 'array',
					'required' => 0,
					'wrapper' => array( 'width' => '34' ),
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'resource',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'active' => true,
		) );

		// 2. جروب الحقول الخاص بـ Resource Cards (إضافة حقل الـ Flags كـ Checkboxes)
		acf_add_local_field_group( array(
			'key' => 'group_wes_resource_card_settings',
			'title' => 'Resource Card Extra Data',
			'fields' => array(
				array(
					'key' => 'field_rc_org',
					'label' => 'Publishing Organization',
					'name' => 'org',
					'type' => 'text',
					'required' => 0,
					'placeholder' => 'Enter publishing organization...',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key' => 'field_rc_link_object',
					'label' => 'Resource Link',
					'name' => 'link',
					'type' => 'link',
					'return_format' => 'array',
					'required' => 0,
					'wrapper' => array( 'width' => '50' ),
				),
				array(
            		'key' => 'field_rc_featured',
            		'label' => 'Featured',
            		'name' => 'featured',
            		'type' => 'true_false',
            		'message' => 'Mark as Featured',
            		'default_value' => 0,
            		'ui' => 1,
            		'wrapper' => array( 'width' => '50' ),
            	),
				array(
					'key' => 'field_rc_overview',
					'label' => 'Brief Overview',
					'name' => 'overview',
					'type' => 'textarea',
					'required' => 0,
					'rows' => 3,
					'placeholder' => 'Enter brief overview...',
				),
				array(
					'key' => 'field_rc_note',
					'label' => 'Note',
					'name' => 'r-note',
					'type' => 'textarea',
					'required' => 0,
					'rows' => 2,
					'placeholder' => 'Enter Note here...',
				),
				array(
					'key' => 'field_rc_flags_checkbox',
					'label' => 'Flags (Languages)',
					'name' => 'flags',
					'type' => 'checkbox',
					'choices' => array(
						'en' => 'English',
						'ar' => 'Arabic',
						'fr' => 'French',
						'es' => 'Spanish',
					),
					'allow_custom' => 0,
					'save_custom' => 0,
					'default_value' => array(),
					'layout' => 'horizontal',
					'toggle' => 0,
					'return_format' => 'value',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'resource_card',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'standard',
			'label_placement' => 'top',
			'active' => true,
		) );

	}
} );

/* -------------------------------------------------------------------------
 * META BOXES: Core Article Settings (Sidebar Only) – applies to all article types including get_involved
 * ------------------------------------------------------------------------- */

add_action( 'add_meta_boxes', function() {
	foreach ( array_keys( wes_article_types() ) as $screen ) {
		add_meta_box(
			'wes_article_meta_box',
			'Article Settings',
			'wes_render_article_meta_html',
			$screen,
			'side',
			'default'
		);
	}
} );

function wes_render_article_meta_html( $post ) {
	$strong_title = get_post_meta( $post->ID, '_wes_title_strong', true );
	$title_rest   = get_post_meta( $post->ID, '_wes_title_rest', true );
	
	wp_nonce_field( 'wes_save_article_meta', 'wes_article_meta_nonce' );
	?>
	<p style="margin-bottom: 12px;">
		<label for="wes_title_strong" style="display:block; font-weight:bold; margin-bottom:4px;">Title Strong:</label>
		<input type="text" id="wes_title_strong" name="wes_title_strong" value="<?php echo esc_attr( $strong_title ); ?>" class="widefat">
	</p>
	<p style="margin-bottom: 5px;">
		<label for="wes_title_rest" style="display:block; font-weight:bold; margin-bottom:4px;">Title Rest:</label>
		<input type="text" id="wes_title_rest" name="wes_title_rest" value="<?php echo esc_attr( $title_rest ); ?>" class="widefat">
	</p>
	<?php
}

add_action( 'save_post', function( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['wes_article_meta_nonce'] ) && wp_verify_nonce( $_POST['wes_article_meta_nonce'], 'wes_save_article_meta' ) ) {
		if ( isset( $_POST['wes_title_strong'] ) ) {
			update_post_meta( $post_id, '_wes_title_strong', sanitize_text_field( $_POST['wes_title_strong'] ) );
		}
		if ( isset( $_POST['wes_title_rest'] ) ) {
			update_post_meta( $post_id, '_wes_title_rest', sanitize_text_field( $_POST['wes_title_rest'] ) );
		}
	}
} );

/* -------------------------------------------------------------------------
 * POLYLANG SUPPORT
 * ------------------------------------------------------------------------- */

/* ربط الـ CPTs بالـ Polylang */
add_filter( 'pll_get_post_types', function ( $types ) {
	foreach ( array_keys( wes_article_types() ) as $t ) {
		$types[ $t ] = $t;
	}
	$types['resource']      = 'resource';
	$types['resource_card'] = 'resource_card';
	return $types;
} );

/* ربط التصنيفات بالـ Polylang (بما فيها الجديدة) */
add_filter( 'pll_get_taxonomies', function ( $tax ) {
	$tax['topic']                = 'topic';
	$tax['theme']                = 'theme';
	$tax['resource_action_type'] = 'resource_action_type';
	$tax['resource_audience']    = 'resource_audience';
	$tax['resource_country']     = 'resource_country';
	// Get Involved specific taxonomies
	$tax['get_involved_topic']   = 'get_involved_topic';
	$tax['get_involved_country'] = 'get_involved_country';
	$tax['stakeholder']          = 'stakeholder';
	return $tax;
} );

/* -------------------------------------------------------------------------
 * ARCHIVE FILTERING (pre_get_posts)
 * ------------------------------------------------------------------------- */

add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	
	// 1. Article types (explainer, howto, etc.) using shared 'topic'
	if ( $q->is_post_type_archive( array( 'explainer', 'infographic', 'howto', 'checklist' ) ) && ! empty( $_GET['topic'] ) ) {
		$q->set( 'tax_query', array( array(
			'taxonomy' => 'topic',
			'field'    => 'slug',
			'terms'    => sanitize_title( wp_unslash( $_GET['topic'] ) ),
		) ) );
	}
	
	// 2. Resource Books using 'theme'
	if ( $q->is_post_type_archive( 'resource' ) && ! empty( $_GET['theme'] ) ) {
		$q->set( 'tax_query', array( array(
			'taxonomy' => 'theme',
			'field'    => 'slug',
			'terms'    => sanitize_title( wp_unslash( $_GET['theme'] ) ),
		) ) );
	}
	
	// 3. Resource Cards using its own taxonomies
	if ( $q->is_post_type_archive( 'resource_card' ) ) {
		$tax_query = array('relation' => 'AND');
		
		if ( ! empty( $_GET['action_type'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'resource_action_type',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['action_type'] ) ),
			);
		}
		if ( ! empty( $_GET['audience'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'resource_audience',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['audience'] ) ),
			);
		}
		if ( ! empty( $_GET['country'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'resource_country',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['country'] ) ),
			);
		}
		if ( count( $tax_query ) > 1 ) {
			$q->set( 'tax_query', $tax_query );
		}
	}
	
	// 4. Get Involved Resources using its own dedicated taxonomies
	if ( $q->is_post_type_archive( 'get_involved' ) ) {
		$tax_query = array('relation' => 'AND');
		
		if ( ! empty( $_GET['gi_topic'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'get_involved_topic',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['gi_topic'] ) ),
			);
		}
		if ( ! empty( $_GET['gi_country'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'get_involved_country',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['gi_country'] ) ),
			);
		}
		if ( ! empty( $_GET['stakeholder'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'stakeholder',
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET['stakeholder'] ) ),
			);
		}
		if ( count( $tax_query ) > 1 ) {
			$q->set( 'tax_query', $tax_query );
		}
	}
} );


/***************************************************************************/
/* ACF BLOCK REGISTRATION (unchanged) */
add_action( 'acf/init', function() {
	
	if ( function_exists( 'acf_register_block_type' ) ) {
		acf_register_block_type( array(
			'name'            => 'resource-library-cpt',
			'title'           => __( 'Resource Library (Dynamic)' ),
			'description'     => __( 'A dynamic block to filter and display custom post types.' ),
			'render_template' => 'template-parts/blocks/resource-library-cpt.php',
			'category'        => 'formatting',
			'icon'            => 'filter',
			'keywords'        => array( 'resource', 'library', 'cpt', 'dynamic' ),
		) );
	}

	if ( function_exists( 'acf_add_local_field_group' ) ) {
		acf_add_local_field_group( array(
            'key' => 'group_dynamic_resource_library',
            'title' => 'Resource Library Settings (New)',
            'fields' => array(
                array(
                    'key' => 'field_rln_style',
                    'label' => 'Style',
                    'name' => 'layout_style',
                    'type' => 'select',
                    'choices' => array(
                        'list' => 'List',
                        'cards'      => 'Cards',
                    ),
                ),
                array(
                    'key' => 'field_rln_show_flags',
                    'label' => 'Show Flags',
                    'name' => 'show_flags',
                    'type' => 'true_false',
                    'instructions' => 'Show language flags on each resource.',
                    'ui' => 1,
                    'default_value' => 0,
                ),
                array(
                    'key' => 'field_rln_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_rln_heading',
                    'label' => 'Heading',
                    'name' => 'heading',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_rln_lead',
                    'label' => 'Lead Text',
                    'name' => 'lead',
                    'type' => 'textarea',
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_rln_post_type',
                    'label' => 'Select Post Type',
                    'name' => 'dynamic_post_type',
                    'type' => 'select',
                    'choices' => array(
                        'resource_card' => 'Resource Cards',
                        'resource'      => 'Resource Books',
                        'explainer'     => 'Explainers',
                        'infographic'   => 'Infographics',
                        'howto'         => 'How-to Guides',
                        'checklist'     => 'Checklists',
                        'get_involved'  => 'Get Involved Resources', // added
                    ),
                ),
                array(
                    'key' => 'field_rln_taxonomy',
                    'label' => 'Select Taxonomy (For Tabs)',
                    'name' => 'dynamic_taxonomy',
                    'type' => 'select',
                    'choices' => array(
                        'post_tag'             => 'Tags (Default WP Tags)',
                        'resource_action_type' => 'Action Types',
                        'resource_audience'    => 'Target Audience',
                        'target_audience'      => 'Target Book Audience',
                        'topic'                => 'Topics',
                        'theme'                => 'Themes',
                        'get_involved_topic'   => 'GI Topics',      // new
                        'get_involved_country' => 'GI Countries',   // new
                        'stakeholder'          => 'Stakeholders',   // new
                    ),
                ),
                array(
                    'key' => 'field_rln_selected_tags_select',
                    'label' => 'Selected Tags (for tabs)',
                    'name' => 'selected_tags_select',
                    'type' => 'select',
                    'choices' => array(),
                    'multiple' => 1,
                    'ui' => 1,
                    'ajax' => 0,
                    'return_format' => 'value',
                    'instructions' => 'Select the tags you want to show as tabs. The "Others" tab will show all remaining posts.',
                ),
                array(
                    'key' => 'field_rln_posts_limit',
                    'label' => 'Max Posts',
                    'name' => 'posts_limit',
                    'type' => 'number',
                    'instructions' => 'Maximum total posts to show. Leave empty or enter -1 to show all.',
                    'default_value' => -1,
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_rln_posts_per_page',
                    'label' => 'Posts Per Page',
                    'name' => 'posts_per_page',
                    'type' => 'number',
                    'instructions' => 'Number of posts per page when pagination is active. Leave empty or enter -1 to show all at once (no pagination).',
                    'default_value' => 4,
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_rln_post_orderby',
                    'label' => 'Order Posts By',
                    'name' => 'post_orderby',
                    'type' => 'select',
                    'choices' => array(
                        'date'       => 'Date Published',
                        'title'      => 'Title (Alphabetical)',
                        'menu_order' => 'Menu Order (Custom Order)',
                        'rand'       => 'Random',
                    ),
                    'default_value' => 'date',
                    'wrapper' => array( 'width' => '33' ),
                ),
                array(
                    'key' => 'field_rln_post_order',
                    'label' => 'Posts Order Direction',
                    'name' => 'post_order',
                    'type' => 'select',
                    'choices' => array(
                        'DESC' => 'Descending (Z-A / Newest First)',
                        'ASC'  => 'Ascending (A-Z / Oldest First)',
                    ),
                    'default_value' => 'DESC',
                    'wrapper' => array( 'width' => '33' ),
                ),
                array(
                    'key' => 'field_rln_tax_orderby',
                    'label' => 'Order Tabs By',
                    'name' => 'tax_orderby',
                    'type' => 'select',
                    'choices' => array(
                        'name'  => 'Name (Alphabetical)',
                        'id'    => 'Term ID',
                        'count' => 'Post Count (Most used first)',
                        'menu_order' => 'Menu Order (Custom Order)',
                    ),
                    'default_value' => 'name',
                ),
                array(
                    'key' => 'field_rln_tax_order',
                    'label' => 'Tabs Order Direction',
                    'name' => 'tax_order',
                    'type' => 'select',
                    'choices' => array(
                        'ASC'  => 'Ascending (A-Z)',
                        'DESC' => 'Descending (Z-A)',
                    ),
                    'default_value' => 'ASC',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/resource-library-cpt',
                    ),
                ),
            ),
            'active' => true,
        ) );
	}
	// 3. جروب حقول Get Involved Resources (رابط خارجي فقط)
    acf_add_local_field_group( array(
    	'key' => 'group_get_involved_settings',
    	'title' => 'Get Involved Settings',
    	'fields' => array(
    		array(
    			'key' => 'field_gi_external_url',
    			'label' => 'External URL',
    			'name' => 'external_url',
    			'type' => 'url',       // أو استخدم 'link' لو عايز title و target
    			'required' => 0,
    			'placeholder' => 'https://example.com',
    			'wrapper' => array( 'width' => '100' ),
    		),
    	),
    	'location' => array(
    		array(
    			array(
    				'param' => 'post_type',
    				'operator' => '==',
    				'value' => 'get_involved',
    			),
    		),
    	),
    	'menu_order' => 0,
    	'position' => 'normal',
    	'style' => 'standard',
    	'label_placement' => 'top',
    	'active' => true,
    ) );
} );

/* -------------------------------------------------------------------------
 * ADMIN COLUMNS: Resource Cards – Add Featured, Remove Country
 * ------------------------------------------------------------------------- */

add_filter( 'manage_resource_card_posts_columns', function ( $columns ) {
	unset( $columns['taxonomy-resource_country'] );
	$columns['featured'] = 'Featured';
	return $columns;
} );

add_action( 'manage_resource_card_posts_custom_column', function ( $column, $post_id ) {
	if ( 'featured' === $column ) {
		$featured = get_post_meta( $post_id, 'featured', true );
		echo $featured ? '<span style="color:#2ecc71;font-size:18px;">&#9679;</span>' : '<span style="color:#ccc;font-size:18px;">&#9679;</span>';
	}
}, 10, 2 );