<?php
/**
 * WES theme functions.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WES_VERSION', '0.1.0' );

/**
 * Theme setup.
 */
function wes_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'wes' ),
			'footer'  => __( 'Footer Menu', 'wes' ),
		)
	);
}
add_action( 'after_setup_theme', 'wes_setup' );

/**
 * Enqueue styles and scripts.
 */
function wes_assets() {
	$theme_uri = get_template_directory_uri();
	$ver       = WES_VERSION;

	wp_enqueue_style(
		'wes-fonts',
		'https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&family=Roboto+Condensed:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	$dir = get_template_directory();
	$cv  = file_exists( "$dir/assets/css/tokens.css" ) ? filemtime( "$dir/assets/css/tokens.css" ) : $ver;
	$mv  = file_exists( "$dir/assets/css/main.css" ) ? filemtime( "$dir/assets/css/main.css" ) : $ver;
	$rt  = file_exists( "$dir/assets/css/rtl.css" ) ? filemtime( "$dir/assets/css/rtl.css" ) : $ver;
	$jv  = file_exists( "$dir/assets/js/main.js" ) ? filemtime( "$dir/assets/js/main.js" ) : $ver;

	wp_enqueue_style( 'wes-tokens', $theme_uri . '/assets/css/tokens.css', array(), $cv );
	wp_enqueue_style( 'wes-main', $theme_uri . '/assets/css/main.css', array( 'wes-tokens' ), $mv );
	wp_enqueue_style( 'wes-rtl', $theme_uri . '/assets/css/rtl.css', array(), $rt );
	wp_enqueue_script( 'wes-main', $theme_uri . '/assets/js/main.js', array(), $jv, true );

	if ( file_exists( "$dir/assets/js/quiz.js" ) ) {
		wp_enqueue_script( 'wes-quiz', $theme_uri . '/assets/js/quiz.js', array(), filemtime( "$dir/assets/js/quiz.js" ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'wes_assets' );

/**
 * Helper: theme image URL.
 *
 * @param string $file Image filename.
 * @return string
 */
function wes_img( $file ) {
	return get_template_directory_uri() . '/assets/img/' . ltrim( $file, '/' );
}

require_once get_template_directory() . '/inc/wes-content.php';
require_once get_template_directory() . '/inc/acf-register.php';
require_once get_template_directory() . '/inc/acf-blocks.php';
require_once get_template_directory() . '/inc/cpt.php';
require_once get_template_directory() . '/inc/quiz.php';

add_filter( 'redirect_canonical', function ( $redirect ) {
	return is_front_page() ? false : $redirect;
} );

add_filter( 'page_link', function ( $link, $post_id ) {
	if ( ! function_exists( 'PLL' ) || ! function_exists( 'pll_home_url' ) ) {
		return $link;
	}
	foreach ( PLL()->model->get_languages_list() as $l ) {
		if ( isset( $l->page_on_front ) && (int) $l->page_on_front === (int) $post_id ) {
			return pll_home_url( $l->slug );
		}
	}
	return $link;
}, 10, 2 );

add_filter( 'acf/load_field/name=selected_tags_select', function ( $field ) {
	$taxonomy = get_field( 'dynamic_taxonomy' ) ?: 'resource_action_type';
	$terms    = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		)
	);
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		$choices = array();
		foreach ( $terms as $term ) {
			$choices[ $term->slug ] = $term->name;
		}
		$field['choices'] = $choices;
	}
	return $field;
} );

// Resource Cards AJAX.
add_action( 'wp_ajax_load_resource_cards_page', 'wes_rc_ajax_paginate' );
add_action( 'wp_ajax_nopriv_load_resource_cards_page', 'wes_rc_ajax_paginate' );
function wes_rc_ajax_paginate() {
	$page = max( 1, intval( $_POST['page'] ?? 1 ) );
	set_query_var( 'resource_page', $page );

	global $wes_rc_ajax_filters;
	$wes_rc_ajax_filters = array(
		'posts_per_page'  => max( 1, intval( $_POST['posts_per_page'] ?? 9 ) ),
		'max_posts'       => intval( $_POST['max_posts'] ?? -1 ),
		'action_types'    => array_filter( array_map( 'sanitize_text_field', explode( ',', wp_unslash( $_POST['action_types'] ?? '' ) ) ) ),
		'audiences'       => array_filter( array_map( 'sanitize_text_field', explode( ',', wp_unslash( $_POST['audiences'] ?? '' ) ) ) ),
		'cta'             => sanitize_text_field( wp_unslash( $_POST['cta'] ?? '' ) ),
		'cta_link'        => esc_url_raw( wp_unslash( $_POST['cta_link'] ?? '' ) ),
		'resource_source' => ! empty( $_POST['resource_source'] ) ? '1' : '',
	);

	get_template_part( 'template-parts/blocks/resource-cards-dynamic' );
	wp_die();
}

add_action( 'wp_enqueue_scripts', 'wes_enqueue_resource_cards_ajax' );
function wes_enqueue_resource_cards_ajax() {
	$script = get_template_directory() . '/assets/js/resource-cards-ajax.js';
	if ( has_block( 'acf/wes-resource-cards-dynamic' ) && file_exists( $script ) ) {
		wp_enqueue_script(
			'wes-resource-cards-ajax',
			get_template_directory_uri() . '/assets/js/resource-cards-ajax.js',
			array( 'jquery' ),
			filemtime( $script ),
			true
		);
		wp_localize_script( 'wes-resource-cards-ajax', 'wes_rc_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
}

// Resource Library AJAX.
add_action( 'wp_ajax_wes_drl_filter', 'wes_ajax_drl_filter' );
add_action( 'wp_ajax_nopriv_wes_drl_filter', 'wes_ajax_drl_filter' );
function wes_ajax_drl_filter() {
	$cpt            = isset( $_POST['cpt'] ) ? sanitize_key( wp_unslash( $_POST['cpt'] ) ) : '';
	$tax            = isset( $_POST['tax'] ) ? sanitize_key( wp_unslash( $_POST['tax'] ) ) : '';
	$filter         = isset( $_POST['filter'] ) ? sanitize_text_field( wp_unslash( $_POST['filter'] ) ) : '*';
	$limit          = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : -1;
	$posts_per_page = isset( $_POST['posts_per_page'] ) ? max( 1, intval( $_POST['posts_per_page'] ) ) : 4;
	$orderby        = isset( $_POST['orderby'] ) ? sanitize_key( wp_unslash( $_POST['orderby'] ) ) : 'date';
	$order          = isset( $_POST['order'] ) && 'ASC' === strtoupper( sanitize_text_field( wp_unslash( $_POST['order'] ) ) ) ? 'ASC' : 'DESC';
	$page           = isset( $_POST['page'] ) ? max( 1, intval( $_POST['page'] ) ) : 1;
	$show_flags     = isset( $_POST['show_flags'] ) ? filter_var( wp_unslash( $_POST['show_flags'] ), FILTER_VALIDATE_BOOLEAN ) : true;
	$selected_terms = isset( $_POST['selected_terms'] ) ? array_map( 'sanitize_title', (array) wp_unslash( $_POST['selected_terms'] ) ) : array();
	$selected_terms = array_filter( $selected_terms );

	// The previous implementation referenced $posts_per_page and $layout_style
	// without defining them, causing PHP warnings and broken AJAX rendering.
	// Keep the layout from the request when supplied; otherwise the block template
	// can use its normal default.
	$layout_style = isset( $_POST['layout_style'] ) ? sanitize_key( wp_unslash( $_POST['layout_style'] ) ) : '';

	global $wes_drl_ajax_settings;
	$wes_drl_ajax_settings = array(
		'cpt'            => $cpt,
		'tax'            => $tax,
		'limit'          => $limit,
		'posts_per_page' => $posts_per_page,
		'orderby'        => $orderby,
		'order'          => $order,
		'page'           => $page,
		'show_flags'     => $show_flags,
		'selected_terms' => $selected_terms,
		'layout_style'   => $layout_style,
	);

	$_REQUEST['ajax']   = '1';
	$_REQUEST['filter'] = $filter;

	$template_path = get_template_directory() . '/template-parts/blocks/resource-library-cpt.php';
	if ( file_exists( $template_path ) ) {
		include $template_path;
	} else {
		wp_die( 'Template not found.' );
	}

	wp_die();
}

add_action( 'wp_enqueue_scripts', 'wes_enqueue_resource_library_ajax' );
function wes_enqueue_resource_library_ajax() {
	$script = get_template_directory() . '/assets/js/resource-library-ajax.js';
	if ( ( has_block( 'acf/resource-library-cpt' ) || has_block( 'acf/wes-resource-library-cpt' ) ) && file_exists( $script ) ) {
		wp_enqueue_script(
			'wes-resource-library-ajax',
			get_template_directory_uri() . '/assets/js/resource-library-ajax.js',
			array( 'jquery' ),
			filemtime( $script ),
			true
		);
		wp_localize_script( 'wes-resource-library-ajax', 'wes_drl_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
}

add_action( 'init', function () {
	if ( function_exists( 'pll_register_string' ) ) {
		pll_register_string( 'related_principles_heading', 'Related principles', 'WES' );
	}
} );

/**
 * Get the best post ID to display: translated version if available, otherwise original.
 *
 * @param int    $post_id Post ID.
 * @param string $lang Language slug.
 * @return int
 */
function wes_get_display_post_id( $post_id, $lang = '' ) {
	if ( ! $lang ) {
		$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
	}
	if ( function_exists( 'pll_get_post' ) && $lang ) {
		$translated_id = pll_get_post( $post_id, $lang );
		if ( $translated_id ) {
			return $translated_id;
		}
	}
	return $post_id;
}

/**
 * Get translated term name if translation exists, otherwise original.
 *
 * @param WP_Term $term Term object.
 * @param string  $lang Language slug.
 * @return string
 */
function wes_get_term_display_name( $term, $lang = '' ) {
	if ( ! $lang ) {
		$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
	}
	if ( function_exists( 'pll_get_term' ) && $lang ) {
		$translated_term_id = pll_get_term( $term->term_id, $lang );
		if ( $translated_term_id ) {
			$translated_term = get_term( $translated_term_id );
			if ( $translated_term && ! is_wp_error( $translated_term ) ) {
				return $translated_term->name;
			}
		}
	}
	return $term->name;
}

// Get Involved AJAX.
add_action( 'wp_ajax_wes_get_involved_filter', 'wes_gi_ajax_filter' );
add_action( 'wp_ajax_nopriv_wes_get_involved_filter', 'wes_gi_ajax_filter' );
function wes_gi_ajax_filter() {
	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
	$default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : '';
	$tax_query    = array( 'relation' => 'AND' );
	$taxonomies   = array( 'get_involved_topic', 'get_involved_country', 'stakeholder' );

	foreach ( $taxonomies as $tax ) {
		if ( ! empty( $_POST[ $tax ] ) ) {
			$tax_query[] = array(
				'taxonomy' => $tax,
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_POST[ $tax ] ) ),
			);
		}
	}

	$paged      = isset( $_POST['page'] ) ? max( 1, intval( $_POST['page'] ) ) : 1;
	$query_args = array(
		'post_type'      => 'get_involved',
		'posts_per_page' => 6,
		'paged'          => $paged,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
		'tax_query'      => count( $tax_query ) > 1 ? $tax_query : array(),
	);
	if ( $default_lang ) {
		$query_args['lang'] = $default_lang;
	}

	$query = new WP_Query( $query_args );
	ob_start();
	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();
			$display_id = wes_get_display_post_id( get_the_ID(), $current_lang );
			$url        = get_field( 'external_url', $display_id ) ?: '#';
			?>
			<a class="org" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
				<span class="org__name"><?php echo esc_html( get_the_title( $display_id ) ); ?></span>
				<svg class="org__ext" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</a>
			<?php
		endwhile;
	else :
		echo '<p class="gi__no-results">No resources found.</p>';
	endif;
	wp_reset_postdata();
	$html = ob_get_clean();

	ob_start();
	wes_gi_pagination( $query );
	$pagination = ob_get_clean();

	wp_send_json(
		array(
			'html'       => $html,
			'pagination' => $pagination,
		)
	);
}

function wes_gi_pagination( $query ) {
	$total   = (int) $query->max_num_pages;
	$current = max( 1, (int) $query->get( 'paged' ) );
	if ( $total <= 1 ) {
		return;
	}
	?>
	<nav class="gi-pagination__nav">
		<?php if ( $current > 1 ) : ?>
			<button class="gi-page" data-page="<?php echo esc_attr( $current - 1 ); ?>">Previous</button>
		<?php endif; ?>
		<?php
		$pages = array( 1 );
		for ( $i = $current - 2; $i <= $current + 2; $i++ ) {
			if ( $i > 1 && $i < $total ) {
				$pages[] = $i;
			}
		}
		if ( $total > 1 ) {
			$pages[] = $total;
		}
		$pages         = array_unique( $pages );
		sort( $pages );
		$previous_page = 0;
		foreach ( $pages as $page ) :
			if ( $previous_page && $page > $previous_page + 1 ) :
				?><span class="gi-page-dots">...</span><?php
			endif;
			?>
			<button class="gi-page<?php echo $page === $current ? ' current' : ''; ?>" data-page="<?php echo esc_attr( $page ); ?>"><?php echo esc_html( $page ); ?></button>
			<?php
			$previous_page = $page;
		endforeach;
		?>
		<?php if ( $current < $total ) : ?>
			<button class="gi-page" data-page="<?php echo esc_attr( $current + 1 ); ?>">Next</button>
		<?php endif; ?>
	</nav>
	<?php
}

add_action( 'wp_enqueue_scripts', function () {
	$script = get_template_directory() . '/assets/js/get-involved-ajax.js';
	if ( is_front_page() && file_exists( $script ) ) {
		wp_enqueue_script(
			'wes-get-involved-ajax',
			get_template_directory_uri() . '/assets/js/get-involved-ajax.js',
			array( 'jquery' ),
			filemtime( $script ),
			true
		);
		wp_localize_script( 'wes-get-involved-ajax', 'wes_gi_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
} );
