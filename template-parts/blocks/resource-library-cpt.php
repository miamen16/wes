<?php
/**
 * Block: Resource Library New – two layouts (List + Cards)
 * Uses Select2 only when needed (ACF field 'layout_style' = 'list' or 'cards').
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =============================================================
// LANGUAGE DETECTION & TRANSLATIONS
// =============================================================
$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';

$translations = array(
    'en' => array(
        'All'    => 'All',
        'Others' => 'Others',
    ),
    'ar' => array(
        'All'    => 'الكل',
        'Others' => 'آخرى',
    ),
    'fr' => array(
        'All'    => 'Toutes les ressources',
        'Others' => 'Autres',
    ),
);

$all_label    = $translations[ $lang ]['All'] ?? 'All';
$others_label = $translations[ $lang ]['Others'] ?? 'Others';

// =============================================================
// FLAG HELPERS
// =============================================================

if ( ! function_exists( 'wes_drl_get_flags' ) ) {
    function wes_drl_get_flags( $post_id ) {
        $flags_raw = get_post_meta( $post_id, 'flags', true );
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
        return $flags;
    }
}

// =============================================================
// RENDER FUNCTIONS – list & cards
// =============================================================
if ( ! function_exists( 'wes_drl_render_rows' ) ) {
        function wes_drl_render_rows( $cpt, $tax, $filter, $posts_limit, $posts_per_page, $orderby, $order, $selected_terms = array(), $paged = 1, $show_flags = false ) {
        $all_rows = array();
        $args = array(
            'post_type'      => $cpt,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => $orderby,
            'order'          => $order,
        );
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();

                $link_val = get_field( 'link', $post_id );
                $link_url = '';
                if ( ! empty( $link_val ) ) {
                    if ( is_array( $link_val ) && isset( $link_val['url'] ) && ! empty( $link_val['url'] ) ) {
                        $link_url = $link_val['url'];
                    } elseif ( is_string( $link_val ) && ! empty( $link_val ) ) {
                        $link_url = $link_val;
                    }
                }

                $post_terms = wp_get_post_terms( $post_id, $tax );
                $tag_names = array();
                $tag_slugs = array();
                if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {
                    foreach ( $post_terms as $pt ) {
                        $tag_names[] = $pt->name;
                        $tag_slugs[] = $pt->slug;
                    }
                }

                $include = false;
                if ( $filter === '*' ) {
                    $include = true;
                } elseif ( $filter === '__others__' ) {
                    $has_selected = ! empty( array_intersect( $tag_slugs, $selected_terms ) );
                    $include = ! $has_selected;
                } else {
                    $include = in_array( $filter, $tag_slugs );
                }

                if ( ! $include ) {
                    continue;
                }

                $org = get_field( 'overview', $post_id );
                $all_rows[] = array(
                    'title'    => get_the_title(),
                    'org'      => $org,
                    'tags'     => $tag_names,
                    'tagslugs' => $tag_slugs,
                    'link'     => $link_url,
                    'flags'    => wes_drl_get_flags( $post_id ),
                );
            }
            wp_reset_postdata();
        }

        // Apply max total limit
        if ( $posts_limit > 0 ) {
            $all_rows = array_slice( $all_rows, 0, $posts_limit );
        }

        $total_rows = count( $all_rows );
        if ( $posts_per_page > 0 ) {
            $offset      = ( $paged - 1 ) * $posts_per_page;
            $rows        = array_slice( $all_rows, $offset, $posts_per_page );
            $total_pages = (int) ceil( $total_rows / $posts_per_page );
        } else {
            $rows        = $all_rows;
            $total_pages = 1;
        }

        if ( empty( $rows ) ) {
            echo '<p class="rlib__empty">' . esc_html__( 'No resources found for this filter.', 'wes' ) . '</p>';
            return;
        }

        // External link icon
        $ext_icon = '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        $slug = function( $s ) {
            return sanitize_title( $s );
        };

        echo '<ul class="rlib">';
        foreach ( $rows as $r ) {
            $tagslugs = implode( ' ', array_map( $slug, $r['tags'] ) );
            echo '<li class="rlib__row" data-tags="' . esc_attr( $tagslugs ) . '">';
            echo '<div class="rlib__main">';
            echo '<h3 class="rlib__title">' . esc_html( $r['title'] ) . '</h3>';
            if ( ! empty( $r['org'] ) ) {
                echo '<p class="rlib__org">' . esc_html( $r['org'] ) . '</p>';
            }
            if ( ! empty( $r['tags'] ) || ( $show_flags && ! empty( $r['flags'] ) ) ) {
                echo '<span class="rc2__tags">';
                if ( $show_flags && ! empty( $r['flags'] ) ) {
                    $wes_flag_map = array( 'en' => 'gb.svg', 'ar' => 'eg.svg', 'fr' => 'fr.svg', 'es' => 'es.svg' );
                    foreach ( $r['flags'] as $f ) {
                        $f_clean = strtolower( trim( $f ) );
                        if ( empty( $f_clean ) ) continue;
                        $flag_img = $wes_flag_map[ $f_clean ] ?? $f_clean . '.svg';
                        $flag_src = function_exists( 'wes_img' ) ? wes_img( $flag_img ) : get_template_directory_uri() . '/assets/img/' . $flag_img;
                        echo '<img class="flagchip-img flagchip-img--' . esc_attr( $f_clean ) . '" src="' . esc_url( $flag_src ) . '" alt="' . esc_attr( strtoupper( $f_clean ) ) . ' flag" width="30" height="30" />';
                    }
                }
                foreach ( $r['tags'] as $t ) {
                    echo '<span class="rc2__tag">' . esc_html( $t ) . '</span>';
                }
                echo '</span>';
            }
            echo '</div>';
            if ( ! empty( $r['link'] ) ) {
                echo '<a class="rlib__ext" href="' . esc_url( $r['link'] ) . '" aria-label="' . esc_attr( $r['title'] ) . '">' . $ext_icon . '</a>';
            }
            echo '</li>';
        }
        echo '</ul>';

        if ( $total_pages > 1 ) {
            $arabic_numerals = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
            $english_digits  = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
            $to_arabic = strpos( get_locale(), 'ar' ) === 0;
            echo '<nav class="rlib__pagination" data-rlib-uid="' . esc_attr( $cpt . '-' . $tax ) . '">';
            if ( $paged > 1 ) {
                echo '<button class="rlib__page-btn rlib__page-prev" data-page="' . ( $paged - 1 ) . '" aria-label="' . esc_attr__( 'Previous page', 'wes' ) . '">&lsaquo;</button>';
            }
            for ( $i = 1; $i <= $total_pages; $i++ ) {
                $active = $i === $paged ? ' is-active' : '';
                $page_label = $to_arabic ? str_replace( $english_digits, $arabic_numerals, (string) $i ) : $i;
                echo '<button class="rlib__page-btn' . $active . '" data-page="' . $i . '">' . $page_label . '</button>';
            }
            if ( $paged < $total_pages ) {
                echo '<button class="rlib__page-btn rlib__page-next" data-page="' . ( $paged + 1 ) . '" aria-label="' . esc_attr__( 'Next page', 'wes' ) . '">&rsaquo;</button>';
            }
            echo '</nav>';
        }
    }
}

if ( ! function_exists( 'wes_drl_render_cards' ) ) {
    function wes_drl_render_cards( $cpt, $tax, $filter, $posts_limit, $posts_per_page, $orderby, $order, $selected_terms = array(), $paged = 1, $show_flags = false ) {
        $all_rows = array();
        $args = array(
            'post_type'      => $cpt,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => $orderby,
            'order'          => $order,
        );
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();

                $link_val = get_field( 'link', $post_id );
                $link_url = '';
                if ( ! empty( $link_val ) ) {
                    if ( is_array( $link_val ) && isset( $link_val['url'] ) && ! empty( $link_val['url'] ) ) {
                        $link_url = $link_val['url'];
                    } elseif ( is_string( $link_val ) && ! empty( $link_val ) ) {
                        $link_url = $link_val;
                    }
                }

                $post_terms = wp_get_post_terms( $post_id, $tax );
                $tag_names = array();
                $tag_slugs = array();
                if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {
                    foreach ( $post_terms as $pt ) {
                        $tag_names[] = $pt->name;
                        $tag_slugs[] = $pt->slug;
                    }
                }

                $include = false;
                if ( $filter === '*' ) {
                    $include = true;
                } elseif ( $filter === '__others__' ) {
                    $has_selected = ! empty( array_intersect( $tag_slugs, $selected_terms ) );
                    $include = ! $has_selected;
                } else {
                    $include = in_array( $filter, $tag_slugs );
                }

                if ( ! $include ) {
                    continue;
                }

                $org = get_field( 'overview', $post_id );
                $all_rows[] = array(
                    'title'    => get_the_title(),
                    'org'      => $org,
                    'tags'     => $tag_names,
                    'link'     => $link_url,
                    'flags'    => wes_drl_get_flags( $post_id ),
                );
            }
            wp_reset_postdata();
        }

        // Apply max total limit
        if ( $posts_limit > 0 ) {
            $all_rows = array_slice( $all_rows, 0, $posts_limit );
        }

        $total_rows = count( $all_rows );
        if ( $posts_per_page > 0 ) {
            $offset      = ( $paged - 1 ) * $posts_per_page;
            $rows        = array_slice( $all_rows, $offset, $posts_per_page );
            $total_pages = (int) ceil( $total_rows / $posts_per_page );
        } else {
            $rows        = $all_rows;
            $total_pages = 1;
        }

        if ( empty( $rows ) ) {
            echo '<p class="rlib__empty">' . esc_html__( 'No resources found for this filter.', 'wes' ) . '</p>';
            return;
        }

        $ext_icon = '<svg class="ag-card__ext" viewBox="0 0 24 24" width="33" height="33" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        echo '<div class="ag__refs">';
        foreach ( $rows as $r ) {
            $tag_list = ! empty( $r['tags'] ) ? esc_html( implode( ', ', $r['tags'] ) ) : '';
            echo '<' . ( ! empty( $r['link'] ) ? 'a' : 'article' ) . ' class="ag-card"';
            if ( ! empty( $r['link'] ) ) {
                echo ' href="' . esc_url( $r['link'] ) . '" target="_blank" rel="noopener noreferrer"';
            }
            echo '>';
            if ( ! empty( $r['tags'] ) ) {
                echo '<p class="ag-card__cat">' . esc_html( $r['tags'][0] ) . '</p>';
            }
            echo '<h3 class="ag-card__title">';
            echo esc_html( $r['title'] );
            echo $ext_icon;
            echo '</h3>';
            if ( ! empty( $r['tags'] ) || ( $show_flags && ! empty( $r['flags'] ) ) ) {
                echo '<span class="rc2__tags">';
                if ( $show_flags && ! empty( $r['flags'] ) ) {
                    $wes_flag_map = array( 'en' => 'gb.svg', 'ar' => 'eg.svg', 'fr' => 'fr.svg', 'es' => 'es.svg' );
                    foreach ( $r['flags'] as $f ) {
                        $f_clean = strtolower( trim( $f ) );
                        if ( empty( $f_clean ) ) continue;
                        $flag_img = $wes_flag_map[ $f_clean ] ?? $f_clean . '.svg';
                        $flag_src = function_exists( 'wes_img' ) ? wes_img( $flag_img ) : get_template_directory_uri() . '/assets/img/' . $flag_img;
                        echo '<img class="flagchip-img flagchip-img--' . esc_attr( $f_clean ) . '" src="' . esc_url( $flag_src ) . '" alt="' . esc_attr( strtoupper( $f_clean ) ) . ' flag" width="30" height="30" />';
                    }
                }
                foreach ( $r['tags'] as $t ) {
                    echo '<span class="rc2__tag">' . esc_html( $t ) . '</span>';
                }
                echo '</span>';
            }
            if ( ! empty( $r['org'] ) ) {
                echo '<p class="ag-card__text">' . esc_html( $r['org'] ) . '</p>';
            }
            echo '</' . ( ! empty( $r['link'] ) ? 'a' : 'article' ) . '>';
        }
        echo '</div>';

        if ( $total_pages > 1 ) {
            $arabic_numerals = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
            $english_digits  = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
            $to_arabic = strpos( get_locale(), 'ar' ) === 0;
            echo '<nav class="rlib__pagination" data-rlib-uid="' . esc_attr( $cpt . '-' . $tax ) . '">';
            if ( $paged > 1 ) {
                echo '<button class="rlib__page-btn rlib__page-prev" data-page="' . ( $paged - 1 ) . '" aria-label="' . esc_attr__( 'Previous page', 'wes' ) . '">&lsaquo;</button>';
            }
            for ( $i = 1; $i <= $total_pages; $i++ ) {
                $active = $i === $paged ? ' is-active' : '';
                $page_label = $to_arabic ? str_replace( $english_digits, $arabic_numerals, (string) $i ) : $i;
                echo '<button class="rlib__page-btn' . $active . '" data-page="' . $i . '">' . $page_label . '</button>';
            }
            if ( $paged < $total_pages ) {
                echo '<button class="rlib__page-btn rlib__page-next" data-page="' . ( $paged + 1 ) . '" aria-label="' . esc_attr__( 'Next page', 'wes' ) . '">&rsaquo;</button>';
            }
            echo '</nav>';
        }
    }
}

// =============================================================
// GET BLOCK SETTINGS – with AJAX override
// =============================================================
global $wes_drl_ajax_settings;

if ( isset( $_REQUEST['ajax'] ) && $_REQUEST['ajax'] === '1' && ! empty( $wes_drl_ajax_settings ) ) {
    // AJAX MODE
    $cpt            = $wes_drl_ajax_settings['cpt'];
    $tax            = $wes_drl_ajax_settings['tax'];
    $posts_limit    = $wes_drl_ajax_settings['limit'];
    $posts_per_page = isset( $wes_drl_ajax_settings['posts_per_page'] ) ? intval( $wes_drl_ajax_settings['posts_per_page'] ) : 8;
    $post_orderby   = $wes_drl_ajax_settings['orderby'];
    $post_order     = $wes_drl_ajax_settings['order'];
    $selected_terms = isset( $wes_drl_ajax_settings['selected_terms'] ) ? $wes_drl_ajax_settings['selected_terms'] : array();
    $tax_orderby    = isset( $wes_drl_ajax_settings['tax_orderby'] ) ? $wes_drl_ajax_settings['tax_orderby'] : 'name';
    $tax_order      = isset( $wes_drl_ajax_settings['tax_order'] ) ? $wes_drl_ajax_settings['tax_order'] : 'ASC';
    $layout_style   = isset( $wes_drl_ajax_settings['layout_style'] ) ? $wes_drl_ajax_settings['layout_style'] : 'list';
    $paged          = isset( $wes_drl_ajax_settings['page'] ) ? intval( $wes_drl_ajax_settings['page'] ) : 1;
    $show_flags     = isset( $wes_drl_ajax_settings['show_flags'] ) ? filter_var( $wes_drl_ajax_settings['show_flags'], FILTER_VALIDATE_BOOLEAN ) : false;
} else {
    // NORMAL MODE
    $eyebrow      = get_field( 'eyebrow' );
    $heading      = get_field( 'heading' );
    $lead         = get_field( 'lead' );
    $cpt          = get_field( 'dynamic_post_type' );
    $tax          = get_field( 'dynamic_taxonomy' );
    $posts_limit  = get_field( 'posts_limit' ) ?: -1;
    $posts_per_page = get_field( 'posts_per_page' ) ?: 4;
    $post_orderby = get_field( 'post_orderby' ) ?: 'date';
    $post_order   = get_field( 'post_order' ) ?: 'DESC';
    $tax_orderby  = get_field( 'tax_orderby' ) ?: 'name';
    $tax_order    = get_field( 'tax_order' ) ?: 'ASC';
    $layout_style = get_field( 'layout_style' ) ?: 'list';
    $show_flags   = get_field( 'show_flags' ) ?: true;
    $paged        = isset( $_GET['rlib_page'] ) ? max( 1, intval( $_GET['rlib_page'] ) ) : 1;

    $selected_terms = get_field( 'selected_tags_select' );
    if ( ! is_array( $selected_terms ) ) {
        $selected_terms = array();
    }
}

$uid = 'drl-' . wp_unique_id();

// =============================================================
// BUILD TABS
// =============================================================
$tab_items = array();

if ( ! isset( $_REQUEST['ajax'] ) || $_REQUEST['ajax'] !== '1' ) {
    $tab_items[] = array(
        'label'  => $all_label,
        'filter' => '*',
    );

    if ( $cpt && $tax ) {
        if ( ! empty( $selected_terms ) ) {
            $all_terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => true ) );
            $term_map = array();
            if ( ! is_wp_error( $all_terms ) && ! empty( $all_terms ) ) {
                foreach ( $all_terms as $term ) {
                    $term_map[ $term->slug ] = $term->name;
                }
            }
            foreach ( $selected_terms as $slug ) {
                if ( isset( $term_map[ $slug ] ) ) {
                    $tab_items[] = array( 'label' => $term_map[ $slug ], 'filter' => $slug );
                } else {
                    $tab_items[] = array( 'label' => ucfirst( str_replace( '-', ' ', $slug ) ), 'filter' => $slug );
                }
            }
            $tab_items[] = array(
                'label'  => $others_label,
                'filter' => '__others__',
            );
        } else {
            $all_terms = get_terms( array(
                'taxonomy' => $tax,
                'hide_empty' => true,
                'orderby' => $tax_orderby,
                'order' => $tax_order,
            ) );
            if ( ! is_wp_error( $all_terms ) && ! empty( $all_terms ) ) {
                $other_term = null;
                $other_index = -1;
                foreach ( $all_terms as $index => $term ) {
                    $name = strtolower( trim( $term->name ) );
                    if ( $name === 'other' || $name === 'others' || $name === 'autre' || $name === 'autres' || str_contains( $name, 'أخرى' ) ) {
                        $other_term = $term;
                        $other_index = $index;
                        break;
                    }
                }
                if ( $other_term !== null ) {
                    unset( $all_terms[ $other_index ] );
                    $all_terms = array_values( $all_terms );
                }
                foreach ( $all_terms as $term ) {
                    $tab_items[] = array( 'label' => $term->name, 'filter' => $term->slug );
                }
                if ( $other_term !== null ) {
                    $tab_items[] = array(
                        'label'  => $other_term->name,
                        'filter' => $other_term->slug,
                    );
                }
            }
        }
    }
}

// =============================================================
// AJAX REQUEST – output only list or cards fragment
// =============================================================
if ( isset( $_REQUEST['ajax'] ) && $_REQUEST['ajax'] === '1' ) {
    $filter = isset( $_REQUEST['filter'] ) ? sanitize_text_field( $_REQUEST['filter'] ) : '*';
    if ( $layout_style === 'cards' ) {
        wes_drl_render_cards( $cpt, $tax, $filter, $posts_limit, $posts_per_page, $post_orderby, $post_order, $selected_terms, $paged, $show_flags );
    } else {
        wes_drl_render_rows( $cpt, $tax, $filter, $posts_limit, $posts_per_page, $post_orderby, $post_order, $selected_terms, $paged, $show_flags );
    }
    exit;
}

// =============================================================
// NORMAL PAGE LOAD – full block
// =============================================================
?>
<div class="section block-resource-library"
     data-cpt="<?php echo esc_attr( $cpt ); ?>"
     data-tax="<?php echo esc_attr( $tax ); ?>"
     data-limit="<?php echo esc_attr( $posts_limit ); ?>"
     data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
     data-orderby="<?php echo esc_attr( $post_orderby ); ?>"
     data-order="<?php echo esc_attr( $post_order ); ?>"
     data-tax-orderby="<?php echo esc_attr( $tax_orderby ); ?>"
     data-tax-order="<?php echo esc_attr( $tax_order ); ?>"
     data-selected-terms="<?php echo esc_attr( implode( ',', $selected_terms ) ); ?>"
     data-layout="<?php echo esc_attr( $layout_style ); ?>"
     data-show-flags="<?php echo $show_flags ? '1' : ''; ?>"
     data-uid="<?php echo esc_attr( $uid ); ?>">
    <div class="container">

        <?php if ( $eyebrow ) : ?>
            <p class="section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
        <?php endif; ?>
        <?php if ( $heading ) : ?>
            <h2 class="rlib__heading"><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>
        <?php if ( $lead ) : ?>
            <p class="rlib__lead"><?php echo esc_html( $lead ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $tab_items ) && count( $tab_items ) > 1 ) : ?>
            <!-- Mobile select -->
            <div class="rlib__select-wrap">
                <select class="rlib__select" data-rlib-select="<?php echo esc_attr( $uid ); ?>" aria-label="<?php esc_attr_e( 'Filter resources', 'wes' ); ?>">
                    <?php foreach ( $tab_items as $i => $tab ) : ?>
                        <option value="<?php echo esc_attr( $tab['filter'] ); ?>" <?php selected( $i, 0 ); ?>>
                            <?php echo esc_html( $tab['label'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Desktop tabs -->
            <div class="seg-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Content filter', 'wes' ); ?>" data-rlib="<?php echo esc_attr( $uid ); ?>">
                <?php foreach ( $tab_items as $i => $tab ) : ?>
                    <button
                        type="button"
                        class="seg-tab<?php echo $i === 0 ? ' is-active' : ''; ?>"
                        role="tab"
                        aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                        data-filter="<?php echo esc_attr( $tab['filter'] ); ?>">
                        <?php echo esc_html( $tab['label'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Container for dynamic content -->
        <div class="rlib-list-wrap" data-rlib-list="<?php echo esc_attr( $uid ); ?>">
            <?php
            if ( $layout_style === 'cards' ) {
                wes_drl_render_cards( $cpt, $tax, '*', $posts_limit, $posts_per_page, $post_orderby, $post_order, $selected_terms, $paged, $show_flags );
            } else {
                wes_drl_render_rows( $cpt, $tax, '*', $posts_limit, $posts_per_page, $post_orderby, $post_order, $selected_terms, $paged, $show_flags );
            }
            ?>
        </div>
    </div>
</div>