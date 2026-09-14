<?php
/**
 * Block: Card 1 – Single illustrated card pulling data from a selected resource_card post.
 * Displays tags from the post_tag taxonomy.
 * Flags and tags are in the same flex container.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// --------------------------------------------------------------
// 1. Get ACF settings
// --------------------------------------------------------------
$selected_post = get_field( 'select_post' );

// If no post selected, show a message and exit
if ( ! $selected_post ) {
    echo '<p class="resource-cards__empty">' . esc_html__( 'Please select a resource card in the block settings.', 'wes' ) . '</p>';
    return;
}

// --------------------------------------------------------------
// 2. Extract data from the selected post
// --------------------------------------------------------------
$post_id = $selected_post->ID;
$title   = get_the_title( $post_id );
$excerpt = get_the_excerpt( $post_id );
$link    = get_permalink( $post_id );

// Custom meta fields
$org       = get_post_meta( $post_id, 'org', true );
$overview  = get_post_meta( $post_id, 'overview', true );
$flags_raw = get_post_meta( $post_id, 'flags', true );

// Featured image (circular)
$image_id = get_post_thumbnail_id( $post_id );
$img_src  = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';

// Category – first term from 'resource_action_type' as category eyebrow
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

// --------------------------------------------------------------
// 3. Get tags from 'post_tag' taxonomy
// --------------------------------------------------------------
$tag_terms = get_the_terms( $post_id, 'post_tag' );
$tags = array();
if ( $tag_terms && ! is_wp_error( $tag_terms ) ) {
    $tags = wp_list_pluck( $tag_terms, 'name' );
}

// --------------------------------------------------------------
// 4. Language label for "Publishing Organization"
// --------------------------------------------------------------
$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$lo   = array(
    'en' => 'Publishing Organization:',
    'ar' => 'الجهة الناشرة:',
    'fr' => 'Organisme de publication :',
)[ $lang ] ?? 'Publishing Organization:';

// --------------------------------------------------------------
// 5. Flag mapping
// --------------------------------------------------------------
$flag_map = array(
    'en' => 'gb.svg',
    'ar' => 'eg.svg',
    'fr' => 'fr.svg',
    'es' => 'es.svg',
);
?>

<div class="section block-begin-cards">
    <div class="container">
        <div class="bcards bcards--single">
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
                    <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
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

                <!-- Tags & Flags together in one flex container -->
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
    </div>
</div>