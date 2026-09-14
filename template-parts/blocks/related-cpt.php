<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ---- Get ACF fields ----
$heading          = get_field( 'heading' );
$subtitle         = get_field( 'subtitle' );
$bg               = get_field( 'bg' ) ?: 'none';
$target_post_types = get_field( 'target_post_types' );   // NEW: array of slugs
$posts_limit      = get_field( 'posts_limit' ) ? intval( get_field( 'posts_limit' ) ) : 3;
$order_setting    = get_field( 'orderby' ) ?: 'date_desc';

// ---- Include / Exclude (NEW: arrays from Select2) ----
$include_posts = get_field( 'include_posts' );
$include_array = ! empty( $include_posts ) ? array_map( 'intval', (array) $include_posts ) : array();

$exclude_posts = get_field( 'exclude_posts' );
$exclude_array = ! empty( $exclude_posts ) ? array_map( 'intval', (array) $exclude_posts ) : array();

// Auto-exclude current post
if ( is_single() || is_page() ) {
    $current_id = get_queried_object_id();
    if ( $current_id && ! in_array( $current_id, $exclude_array ) ) {
        $exclude_array[] = $current_id;
    }
}

// ---- Fallback if target_post_types is empty ----
if ( empty( $target_post_types ) ) {
    $target_post_types = array( 'howto', 'checklist', 'explainer' );
}

$all_posts = array();

// ---- Query ----
if ( 'cpt_input_order' === $order_setting ) {
    $posts_fetched = 0;
    foreach ( $target_post_types as $current_post_type ) {
        if ( $posts_fetched >= $posts_limit ) break;
        $current_limit = $posts_limit - $posts_fetched;
        $args = array(
            'post_type'      => $current_post_type,
            'posts_per_page' => $current_limit,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        if ( ! empty( $include_array ) ) $args['post__in'] = $include_array;
        if ( ! empty( $exclude_array ) ) $args['post__not_in'] = $exclude_array;
        $sub_query = new WP_Query( $args );
        if ( $sub_query->have_posts() ) {
            while ( $sub_query->have_posts() ) {
                $sub_query->the_post();
                $all_posts[] = get_post();
                $posts_fetched++;
            }
        }
        wp_reset_postdata();
    }
} else {
    $orderby = 'date';
    $order   = 'DESC';
    if ( 'date_asc' === $order_setting ) {
        $orderby = 'date';
        $order   = 'ASC';
    } elseif ( 'title_asc' === $order_setting ) {
        $orderby = 'title';
        $order   = 'ASC';
    }
    $args = array(
        'post_type'      => $target_post_types,
        'posts_per_page' => $posts_limit,
        'post_status'    => 'publish',
        'orderby'        => $orderby,
        'order'          => $order,
    );
    if ( ! empty( $include_array ) ) $args['post__in'] = $include_array;
    if ( ! empty( $exclude_array ) ) $args['post__not_in'] = $exclude_array;
    $main_query = new WP_Query( $args );
    if ( $main_query->have_posts() ) {
        while ( $main_query->have_posts() ) {
            $main_query->the_post();
            $all_posts[] = get_post();
        }
    }
    wp_reset_postdata();
}
?>
<!-- Display results -->
<div class="section block-related<?php echo 'cream' === $bg ? ' block-related--cream' : ''; ?>">
    <div class="container">
        <?php if ( $heading ) : ?><h2 class="related__heading"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
        <?php if ( $subtitle ) : ?><p class="related__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
        <div class="related__list">
            <?php if ( ! empty( $all_posts ) ) : 
                foreach ( $all_posts as $single_post ) : 
                    $post_type_slug = $single_post->post_type;
                    $category_name  = wes_get_badge_label( $post_type_slug );
                    $cat_color      = '';
                    if ( $post_type_slug === 'explainer' ) {
                        $cat_color = '#FFAE00';
                    } elseif ( $post_type_slug === 'checklist' ) {
                        $cat_color = '#009BA6';
                    }
                    $cat_style      = $cat_color ? 'style="color: ' . $cat_color . ' !important; font-weight: bold;"' : '';
                    $permalink      = get_permalink( $single_post->ID );
                    $title          = $single_post->post_title;
                    $icon_size      = 30;
                    ?>
                    <a class="related__item" href="<?php echo esc_url( $permalink ); ?>" target="_blank" rel="noopener noreferrer" style="display: grid; grid-template-columns: 1fr auto; align-items: start; gap: 15px; text-decoration: none; width: 100%;">
                        <div class="related__text-content" style="text-align: left;">
                            <span class="related__cat" <?php echo $cat_style; ?>><?php echo esc_html( $category_name ); ?></span>
                            <span class="related__sep">–</span>
                            <span class="related__title"><?php echo esc_html( $title ); ?></span>
                        </div>
                        <div class="related__ext-wrapper" style="display: inline-flex; align-items: center; justify-content: center; width: <?php echo $icon_size; ?>px !important; height: <?php echo $icon_size; ?>px !important; flex-shrink: 0; margin-top: 4px;">
                            <svg class="related__ext" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" viewBox="0 0 36 36" fill="none" stroke="#ED6708" stroke-width="2.7" aria-hidden="true" style="width: <?php echo $icon_size; ?>px !important; height: <?php echo $icon_size; ?>px !important;">
                                <path d="M14.5889 19.5726C15.3046 20.5295 16.2178 21.3212 17.2665 21.8941C18.3151 22.4671 19.4747 22.8078 20.6666 22.8931C21.8585 22.9785 23.0548 22.8065 24.1744 22.3889C25.294 21.9712 26.3107 21.3177 27.1555 20.4726L32.1555 15.4726C33.6735 13.9009 34.5135 11.7959 34.4945 9.6109C34.4755 7.42592 33.5991 5.33582 32.054 3.79075C30.509 2.24569 28.4188 1.36928 26.2339 1.35029C24.0489 1.3313 21.9439 2.17126 20.3722 3.68924L17.5055 6.53924" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21.2555 16.2392C20.5398 15.2823 19.6266 14.4905 18.578 13.9176C17.5293 13.3447 16.3697 13.004 15.1778 12.9186C13.9859 12.8333 12.7896 13.0052 11.67 13.4229C10.5504 13.8405 9.53368 14.4941 8.68888 15.3392L3.68887 20.3392C2.17089 21.9109 1.33094 24.0159 1.34992 26.2009C1.36891 28.3858 2.24532 30.4759 3.79039 32.021C5.33545 33.5661 7.42556 34.4425 9.61053 34.4615C11.7955 34.4805 13.9005 33.6405 15.4722 32.1225L18.3222 29.2725" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </a>
                <?php endforeach; 
            else : ?>
                <?php if ( is_admin() ) : ?>
                    <div class="acf-admin-notice" style="padding: 20px; background: #fff; border-left: 4px solid #ffb900; margin-block: 15px; color: #333;">
                        <strong>Related CPT Block:</strong> No published posts found matching your criteria for post types <code><?php echo implode( ', ', (array) $target_post_types ); ?></code>.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>