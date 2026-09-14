<?php
/**
 * Block: Begin Cards New — illustrated cards pulling data from selected resource_card posts.
 * Mobile: carousel/slider with 1 card visible.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// --------------------------------------------------------------
// 1. Get selected posts from ACF
// --------------------------------------------------------------
$post_1   = get_field( 'select_post_1' );
$post_2   = get_field( 'select_post_2' );
$post_3   = get_field( 'select_post_3' );
$bg_color = get_field( 'bg_color' ) ?: '#ffffff';
$cta      = get_field( 'cta' );
$cta_link = get_field( 'cta_link' );

// Build array of selected posts (skip empty)
$selected_posts = array_filter( array( $post_1, $post_2, $post_3 ) );

// If no posts selected, show message
if ( empty( $selected_posts ) ) {
    echo '<p class="resource-cards__empty">' . esc_html__( 'Please select at least one resource card in the block settings.', 'wes' ) . '</p>';
    return;
}

// --------------------------------------------------------------
// 2. Language label for "Publishing Organization"
// --------------------------------------------------------------
$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$lo   = array(
    'en' => 'Publishing Organization:',
    'ar' => 'الجهة الناشرة:',
    'fr' => 'Organisme de publication :',
)[ $lang ] ?? 'Publishing Organization:';

// --------------------------------------------------------------
// 3. Flag mapping
// --------------------------------------------------------------
$flag_map = array(
    'en' => 'gb.svg',
    'ar' => 'eg.svg',
    'fr' => 'fr.svg',
    'es' => 'es.svg',
);

// --------------------------------------------------------------
// 4. Determine card count class (2 or 3)
// --------------------------------------------------------------
$count = count( $selected_posts );
$grid_class = ( $count === 2 ) ? 'bcards--two' : 'bcards--three';
$block_id = 'begin-cards-' . uniqid();
?>

<div class="section block-begin-cards" style="background-color: <?php echo esc_attr( $bg_color ); ?>;">
    <div class="container">
        <!-- Carousel Container -->
        <div class="bcards-carousel" id="<?php echo esc_attr( $block_id ); ?>">
            <div class="bcards-carousel__track-wrapper">
                <div class="bcards-carousel__track">
                    <?php foreach ( $selected_posts as $post_obj ) : 
                        $post_id = $post_obj->ID;
                        $title   = get_the_title( $post_id );

                        // *** USE ACF LINK FIELD IF AVAILABLE, ELSE PERMALINK ***
                        $link_val = get_field( 'link', $post_id );
                        $link_url = '';
                        $link_target = '';
                        if ( ! empty( $link_val ) ) {
                            if ( is_array( $link_val ) && isset( $link_val['url'] ) && ! empty( $link_val['url'] ) ) {
                                $link_url = $link_val['url'];
                                $link_target = ! empty( $link_val['target'] ) ? $link_val['target'] : '';
                            } elseif ( is_string( $link_val ) && ! empty( $link_val ) ) {
                                $link_url = $link_val;
                            }
                        }
                        $link = ! empty( $link_url ) ? $link_url : get_permalink( $post_id );

                        // Custom meta
                        $org       = get_post_meta( $post_id, 'org', true );
                        $overview  = get_post_meta( $post_id, 'overview', true );
                        $flags_raw = get_post_meta( $post_id, 'flags', true );

                        // Featured image (circular)
                        $image_id = get_post_thumbnail_id( $post_id );
                        $img_src  = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';

                        // Category (first term from resource_action_type)
                        $cat_terms = get_the_terms( $post_id, 'resource_action_type' );
                        $category  = '';
                        if ( $cat_terms && ! is_wp_error( $cat_terms ) ) {
                            $category = $cat_terms[0]->name;
                        }

                        // Process flags (pipe-separated)
                        $flags = array();
                        if ( ! empty( $flags_raw ) ) {
                            if ( is_string( $flags_raw ) ) {
                                $flags = array_filter( array_map( 'trim', explode( '|', $flags_raw ) ) );
                            } elseif ( is_array( $flags_raw ) ) {
                                $flags = $flags_raw;
                            }
                        }

                        // Get tags from 'post_tag' taxonomy
                        $tag_terms = get_the_terms( $post_id, 'post_tag' );
                        $tags = array();
                        if ( $tag_terms && ! is_wp_error( $tag_terms ) ) {
                            $tags = wp_list_pluck( $tag_terms, 'name' );
                        }
                    ?>
                        <div class="bcards-carousel__slide">
                            <article class="bcard">
                                <?php if ( $img_src ) : ?>
                                    <span class="bcard__media">
                                        <img src="<?php echo esc_url( $img_src ); ?>" alt="" loading="lazy" />
                                    </span>
                                <?php endif; ?>

                                <?php if ( ! empty( $category ) ) : ?>
                                    <p class="bcard__cat"><?php echo esc_html( $category ); ?></p>
                                <?php endif; ?>

                                <h3 class="bcard__title">
                                    <a href="<?php echo esc_url( $link ); ?>" <?php echo ( ! empty( $link_url ) ) ? 'target="' . esc_attr( $link_target ?: '_blank' ) . '" rel="noopener noreferrer"' : ''; ?>>
                                        <?php echo esc_html( $title ); ?>
                                    </a>
                                </h3>

                                <?php if ( ! empty( $org ) ) : ?>
                                    <p class="bcard__meta">
                                        <span class="rc2__label"><?php echo esc_html( $lo ); ?></span>
                                        <?php echo esc_html( $org ); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ( ! empty( $overview ) ) : ?>
                                    <p class="bcard__text"><?php echo esc_html( $overview ); ?></p>
                                <?php endif; ?>

                                <!-- Tags & Flags together -->
                                <?php if ( ! empty( $flags ) || ! empty( $tags ) ) : ?>
                                    <div class="bcard__footer rc2__tags-wrap">
                                        <?php if ( ! empty( $flags ) ) : ?>
                                                <?php foreach ( $flags as $f ) : 
                                                    $f_clean = strtolower( trim( $f ) );
                                                    if ( empty( $f_clean ) ) continue;
                                                    $flag_img = $flag_map[ $f_clean ] ?? $f_clean . '.svg';
                                                    $flag_src = function_exists( 'wes_img' ) ? wes_img( $flag_img ) : get_template_directory_uri() . '/assets/img/' . $flag_img;
                                                ?>
                                                    <img class="flagchip-img flagchip-img--<?php echo esc_attr( $f_clean ); ?>" 
                                                         src="<?php echo esc_url( $flag_src ); ?>" 
                                                         alt="<?php echo esc_attr( strtoupper( $f_clean ) ); ?> flag" 
                                                         width="30" 
                                                         height="30" />
                                                <?php endforeach; ?>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $tags ) ) : ?>
                                                <?php foreach ( $tags as $t ) : ?>
                                                    <span class="rc2__tag"><?php echo esc_html( $t ); ?></span>
                                                <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

             <!--Navigation Controls -->
            <?php if ( $count > 1 ) : ?>
                <!--<button class="bcards-carousel__prev" aria-label="Previous slide">‹</button>-->
                <!--<button class="bcards-carousel__next" aria-label="Next slide">›</button>-->
                <div class="bcards-carousel__dots"></div>
            <?php endif; ?>
        </div>

        <!-- Original Grid (hidden on mobile, shown on desktop) -->
        <div class="bcards <?php echo esc_attr( $grid_class ); ?>">
            <?php foreach ( $selected_posts as $post_obj ) : 
                $post_id = $post_obj->ID;
                $title   = get_the_title( $post_id );

                // *** USE ACF LINK FIELD IF AVAILABLE, ELSE PERMALINK ***
                $link_val = get_field( 'link', $post_id );
                $link_url = '';
                $link_target = '';
                if ( ! empty( $link_val ) ) {
                    if ( is_array( $link_val ) && isset( $link_val['url'] ) && ! empty( $link_val['url'] ) ) {
                        $link_url = $link_val['url'];
                        $link_target = ! empty( $link_val['target'] ) ? $link_val['target'] : '';
                    } elseif ( is_string( $link_val ) && ! empty( $link_val ) ) {
                        $link_url = $link_val;
                    }
                }
                $link = ! empty( $link_url ) ? $link_url : get_permalink( $post_id );

                // Custom meta
                $org       = get_post_meta( $post_id, 'org', true );
                $overview  = get_post_meta( $post_id, 'overview', true );
                $flags_raw = get_post_meta( $post_id, 'flags', true );

                // Featured image (circular)
                $image_id = get_post_thumbnail_id( $post_id );
                $img_src  = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';

                // Category (first term from resource_action_type)
                $cat_terms = get_the_terms( $post_id, 'resource_action_type' );
                $category  = '';
                if ( $cat_terms && ! is_wp_error( $cat_terms ) ) {
                    $category = $cat_terms[0]->name;
                }

                // Process flags (pipe-separated)
                $flags = array();
                if ( ! empty( $flags_raw ) ) {
                    if ( is_string( $flags_raw ) ) {
                        $flags = array_filter( array_map( 'trim', explode( '|', $flags_raw ) ) );
                    } elseif ( is_array( $flags_raw ) ) {
                        $flags = $flags_raw;
                    }
                }

                // Get tags from 'post_tag' taxonomy
                $tag_terms = get_the_terms( $post_id, 'post_tag' );
                $tags = array();
                if ( $tag_terms && ! is_wp_error( $tag_terms ) ) {
                    $tags = wp_list_pluck( $tag_terms, 'name' );
                }
            ?>
                <article class="bcard">
                    <?php if ( $img_src ) : ?>
                        <span class="bcard__media">
                            <img src="<?php echo esc_url( $img_src ); ?>" alt="" loading="lazy" />
                        </span>
                    <?php endif; ?>

                    <?php if ( ! empty( $category ) ) : ?>
                        <p class="bcard__cat"><?php echo esc_html( $category ); ?></p>
                    <?php endif; ?>

                    <h3 class="bcard__title">
                        <a href="<?php echo esc_url( $link ); ?>" <?php echo ( ! empty( $link_url ) ) ? 'target="' . esc_attr( $link_target ?: '_blank' ) . '" rel="noopener noreferrer"' : ''; ?>>
                            <?php echo esc_html( $title ); ?>
                        </a>
                    </h3>

                    <?php if ( ! empty( $org ) ) : ?>
                        <p class="bcard__meta">
                            <span class="rc2__label"><?php echo esc_html( $lo ); ?></span>
                            <?php echo esc_html( $org ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ( ! empty( $overview ) ) : ?>
                        <p class="bcard__text"><?php echo esc_html( $overview ); ?></p>
                    <?php endif; ?>

                    <!-- Tags & Flags together -->
                    <?php if ( ! empty( $flags ) || ! empty( $tags ) ) : ?>
                        <div class="bcard__footer rc2__tags-wrap">
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
                                             width="30" 
                                             height="30" />
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>

                            <?php if ( ! empty( $tags ) ) : ?>
                                <span class="rc2__tags">
                                    <?php foreach ( $tags as $t ) : ?>
                                        <span class="rc2__tag"><?php echo esc_html( $t ); ?></span>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- CTA (optional) -->
        <?php if ( $cta ) : ?>
            <div class="rc2__cta">
                <a class="btn btn--orange" href="<?php echo esc_url( $cta_link ?: '#' ); ?>">
                    <?php echo esc_html( $cta ); ?>
                    <svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true">
                        <path d="M1.5 1.5l6 6-6 6" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>