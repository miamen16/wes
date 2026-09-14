<?php
/**
 * Block: Resource Cards – Dynamic with AJAX pagination (URL stays same).
 * Uses ACF fields: posts_per_page, action_types, audiences, cta, cta_link.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// --------------------------------------------------------------
// 1. Get editor settings from ACF (with AJAX override)
// --------------------------------------------------------------
$posts_per_page = (int) get_field( 'posts_per_page' ) ?: 9;
$max_posts      = (int) get_field( 'max_posts' ) ?: -1;
$action_types   = get_field( 'action_types' ) ?: array();
$audiences      = get_field( 'audiences' ) ?: array();
$bg_color       = get_field( 'bg_color' ) ?: '#ffffff';
$cta            = get_field( 'cta' );
$cta_link       = get_field( 'cta_link' );
$resource_source  = $wes_rc_ajax_filters['resource_source'] ?? $resource_source;

// Override with AJAX values if present (for pagination requests)
global $wes_rc_ajax_filters;
if ( isset( $wes_rc_ajax_filters ) && is_array( $wes_rc_ajax_filters ) ) {
    $posts_per_page = $wes_rc_ajax_filters['posts_per_page'] ?? $posts_per_page;
    $max_posts      = $wes_rc_ajax_filters['max_posts'] ?? $max_posts;
    $action_types   = $wes_rc_ajax_filters['action_types'] ?? $action_types;
    $audiences      = $wes_rc_ajax_filters['audiences'] ?? $audiences;
    $cta            = $wes_rc_ajax_filters['cta'] ?? $cta;
    $cta_link       = $wes_rc_ajax_filters['cta_link'] ?? $cta_link;
}

// Normalize taxonomy fields to slugs
$normalize_terms = function( $terms, $taxonomy ) {
    $slugs = array();
    $terms = (array) $terms;
    foreach ( $terms as $term ) {
        if ( is_numeric( $term ) ) {
            $t = get_term( $term, $taxonomy );
            if ( $t && ! is_wp_error( $t ) ) {
                $slugs[] = $t->slug;
            }
        } elseif ( is_object( $term ) && isset( $term->slug ) ) {
            $slugs[] = $term->slug;
        } elseif ( is_string( $term ) ) {
            $slugs[] = $term;
        }
    }
    return array_filter( $slugs );
};

$action_types = $normalize_terms( $action_types, 'resource_action_type' );
$audiences    = $normalize_terms( $audiences, 'resource_audience' );

// --------------------------------------------------------------
// 2. Pagination & effective limit
// --------------------------------------------------------------
$paged = get_query_var( 'resource_page' ) ? (int) get_query_var( 'resource_page' ) : 1;

if ( $max_posts > 0 ) {
    if ( $posts_per_page <= 0 ) {
        $effective_ppp = $max_posts;
    } else {
        $already_shown = ( $paged - 1 ) * $posts_per_page;
        if ( $already_shown >= $max_posts ) {
            $effective_ppp = -1;
        } else {
            $effective_ppp = min( $posts_per_page, $max_posts - $already_shown );
        }
    }
} else {
    $effective_ppp = $posts_per_page;
}

// --------------------------------------------------------------
// 3. Build WP_Query arguments
// --------------------------------------------------------------
$args = array(
    'post_type'      => 'resource_card',
    'post_status'    => 'publish',
    'posts_per_page' => $effective_ppp,
    'paged'          => $paged,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
);

if ( $resource_source ) {
    $args['meta_query'] = array(
        array(
            'key'   => 'featured',
            'value' => '1',
        ),
    );
}

if ( ! empty( $action_types ) ) {
    $args['tax_query'][] = array(
        'taxonomy' => 'resource_action_type',
        'field'    => 'slug',
        'terms'    => $action_types,
    );
}

if ( ! empty( $audiences ) ) {
    $args['tax_query'][] = array(
        'taxonomy' => 'resource_audience',
        'field'    => 'slug',
        'terms'    => $audiences,
    );
}

$query = new WP_Query( $args );

// If no posts, show a message and exit
if ( ! $query->have_posts() ) {
    echo '<p class="resource-cards__empty">' . esc_html__( 'No resource cards found.', 'wes' ) . '</p>';
    wp_reset_postdata();
    return;
}

// --------------------------------------------------------------
// 4. Cap display totals by max_posts
// --------------------------------------------------------------
$total_found = (int) $query->found_posts;
if ( $max_posts > 0 && $total_found > $max_posts ) {
    $total_found = $max_posts;
}
if ( $posts_per_page > 0 ) {
    $query->max_num_pages = (int) ceil( $total_found / $posts_per_page );
}

// --------------------------------------------------------------
// 5. Build items array
// --------------------------------------------------------------
$items = array();
while ( $query->have_posts() ) {
    $query->the_post();
    $post_id = get_the_ID();

    $title    = get_the_title();
    $excerpt  = get_the_excerpt();
    
    $link_field = get_field( 'link', $post_id );
    $link       = '';
    $link_raw   = get_post_meta( $post_id, 'link', true );
    if ( ! empty( $link_raw ) ) {
        if ( is_array( $link_raw ) && ! empty( $link_raw['url'] ) ) {
            $link = $link_raw['url'];
        } else {
            $unser = maybe_unserialize( $link_raw );
            if ( is_array( $unser ) && ! empty( $unser['url'] ) ) {
                $link = $unser['url'];
            }
        }
    }
    if ( empty( $link ) ) {
        $link = get_permalink();
    }


    $org       = get_post_meta( $post_id, 'org', true );
    $overview  = get_post_meta( $post_id, 'overview', true );
    $flags_raw = get_post_meta( $post_id, 'flags', true );

    // ----- Handle flags (robust) -----
    $flags = array();
    if ( ! empty( $flags_raw ) ) {
        if ( is_string( $flags_raw ) ) {
            if ( strpos( $flags_raw, 'a:' ) === 0 && strpos( $flags_raw, '{' ) !== false ) {
                $unserialized = @unserialize( $flags_raw );
                if ( is_array( $unserialized ) ) {
                    $flags_raw = $unserialized;
                } else {
                    $flags = array_filter( array_map( 'trim', explode( '|', $flags_raw ) ) );
                }
            } else {
                $flags = array_filter( array_map( 'trim', explode( '|', $flags_raw ) ) );
            }
        }
        if ( is_array( $flags_raw ) ) {
            $flat = array();
            foreach ( $flags_raw as $item ) {
                if ( is_string( $item ) ) {
                    $flat[] = trim( $item );
                } elseif ( is_array( $item ) && isset( $item['value'] ) ) {
                    $flat[] = trim( $item['value'] );
                } elseif ( is_array( $item ) ) {
                    foreach ( $item as $sub ) {
                        if ( is_string( $sub ) ) {
                            $flat[] = trim( $sub );
                        } elseif ( is_array( $sub ) && isset( $sub['value'] ) ) {
                            $flat[] = trim( $sub['value'] );
                        }
                    }
                }
            }
            $flags = array_filter( array_unique( $flat ) );
        }
    }
    if ( empty( $flags ) && ! empty( $flags_raw ) && is_string( $flags_raw ) ) {
        $check = @unserialize( $flags_raw );
        if ( is_array( $check ) ) {
            $flags = array_filter( array_map( 'trim', $check ) );
        }
    }

    // Tags from 'post_tag' taxonomy
    $tag_terms = get_the_terms( $post_id, 'post_tag' );
    $tags = '';
    if ( $tag_terms && ! is_wp_error( $tag_terms ) ) {
        $tags = implode( '|', wp_list_pluck( $tag_terms, 'name' ) );
    }

    // Category from first term of 'resource_action_type'
    $cat_terms = get_the_terms( $post_id, 'resource_action_type' );
    $cat = '';
    if ( $cat_terms && ! is_wp_error( $cat_terms ) ) {
        $cat = $cat_terms[0]->name;
    }

    $items[] = array(
        'title'    => $title,
        'text'     => $excerpt,
        'meta'     => '',
        'cat'      => $cat,
        'org'      => $org,
        'overview' => $overview,
        'flags'    => $flags,
        'tags'     => $tags,
        'link'     => $link,
    );
}
wp_reset_postdata();

// --------------------------------------------------------------
// 5. Language & Assets
// --------------------------------------------------------------
$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$L = array(
    'org'   => array( 'en' => 'Publishing Organization:', 'ar' => 'الجهة الناشرة:', 'fr' => 'Organisme de publication :' ),
    'brief' => array( 'en' => 'Brief Overview:', 'ar' => 'نظرة عامة موجزة:', 'fr' => 'Bref aperçu :' ),
);
$lo = $L['org'][ $lang ] ?? $L['org']['en'];
$lb = $L['brief'][ $lang ] ?? $L['brief']['en'];

$ext = '<svg class="rc2__ext" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$flag_map = array( 'en' => 'gb.svg', 'ar' => 'eg.svg', 'fr' => 'fr.svg', 'es' => 'es.svg' );

// Generate unique ID for this block instance
$block_id = 'rc-dyn-' . uniqid();
?>

<div class="section block-resource-cards block-resource-cards--rich" style="background-color: <?php echo esc_attr( $bg_color ); ?>;">
    <div class="container">
        <!-- CARDS WRAPPER – replaced by AJAX, includes pagination AND CTA -->
        <div id="<?php echo esc_attr( $block_id ); ?>" 
             class="rc-dynamic-wrapper" 
             data-block-id="<?php echo esc_attr( $block_id ); ?>"
             data-action-types="<?php echo esc_attr( implode( ',', $action_types ) ); ?>"
             data-audiences="<?php echo esc_attr( implode( ',', $audiences ) ); ?>"
             data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
             data-max-posts="<?php echo esc_attr( $max_posts ); ?>"
             data-cta="<?php echo esc_attr( $cta ); ?>"
             data-cta-link="<?php echo esc_attr( $cta_link ); ?>"
             data-resource-source="<?php echo esc_attr( $resource_source ? '1' : '' ); ?>">

            <div class="rcards2">
                <?php foreach ( $items as $c ) : 
                    $flags = is_array( $c['flags'] ) ? $c['flags'] : array();
                    $tags  = array_filter( array_map( 'trim', explode( '|', (string) ( $c['tags'] ?? '' ) ) ) );
                    $link  = $c['link'] ?? '';
                ?>
                    <article class="rc2">
                        <?php if ( $link ) : ?>
                            <a class="rc2__link" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $c['title'] ?? '' ); ?>"><?php echo $ext; ?></a>
                        <?php else : ?>
                            <span class="rc2__link"><?php echo $ext; ?></span>
                        <?php endif; ?>

                        <h3 class="rc2__title"><?php echo esc_html( $c['title'] ?? '' ); ?></h3>

                        <?php if ( ! empty( $c['org'] ) ) : ?>
                            <p class="rc2__meta"><span class="rc2__label"><?php echo esc_html( $lo ); ?></span> <?php echo esc_html( $c['org'] ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $c['overview'] ) ) : ?>
                            <p class="rc2__meta"><span class="rc2__label"><?php echo esc_html( $lb ); ?></span> <?php echo esc_html( $c['overview'] ); ?></p>
                        <?php endif; ?>

                        <div class="rc2__foot">
                            <?php if ( ! empty( $flags ) ) : ?>
                                <span class="rc2__flags">
                                    <?php foreach ( $flags as $f ) : 
                                        $f_clean = strtolower( trim( $f ) );
                                        if ( empty( $f_clean ) ) continue;
                                        $flag_img = $flag_map[ $f_clean ] ?? $f_clean . '.svg';
                                        $flag_src = function_exists( 'wes_img' ) ? wes_img( $flag_img ) : get_template_directory_uri() . '/assets/img/' . $flag_img;
                                    ?>
                                        <img class="flagchip-img flagchip-img--<?php echo esc_attr( $f_clean ); ?>" 
                                             src="<?php echo esc_url( $flag_src ); ?>" 
                                             alt="<?php echo esc_attr( strtoupper( $f_clean ) ); ?> flag" 
                                             width="30" height="30" />
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

            <!-- Pagination & CTA inside wrapper, both replaced together -->
            <div class="rc2-lower">

                <?php if ( $query->max_num_pages > 1 ) : ?>
                    <div class="rc-pagination rc-pagination--ajax" data-block-id="<?php echo esc_attr( $block_id ); ?>">
                        <?php
                        $pagination_html = paginate_links( array(
                            'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                            'format'    => '?resource_page=%#%',
                            'current'   => max( 1, $paged ),
                            'total'     => $query->max_num_pages,
                            'prev_next' => false,
                            'type'      => 'list',
                        ) );
                
                        // Detect Arabic language (any Arabic locale)
                        if ( strpos( get_locale(), 'ar' ) === 0 ) {
                            $arabic_numerals = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
                            $english_digits  = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
                            $pagination_html = preg_replace_callback(
                                '/>([^<]+)</',
                                function ( $m ) use ( $english_digits, $arabic_numerals ) {
                                    return '>' . str_replace( $english_digits, $arabic_numerals, $m[1] ) . '<';
                                },
                                $pagination_html
                            );
                        }
                
                        echo $pagination_html;
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ( $cta ) : ?>
                    <div class="rc2__cta rc2__cta--static">
                        <a class="btn btn--orange" href="<?php echo esc_url( $cta_link ?: '#' ); ?>">
                            <?php echo esc_html( $cta ); ?>
                            <svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true">
                                <path d="M1.5 1.5l6 6-6 6" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div><!-- END .rc-dynamic-wrapper -->
    </div><!-- END .container -->
</div><!-- END .section -->