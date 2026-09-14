<?php
/**
 * Block: Featured CPT — Highlighted resource card pulling data from a selected resource_card post.
 * Uses the same styling as Featured Resource block.
 * Background color is configurable on the card itself.
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
$eyebrow_override = get_field( 'eyebrow' );
$bg_color = get_field( 'bg_color' ) ?: '#def1f3';

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

$link_field = get_field( 'link', $post_id );
$link       = '';
if ( ! empty( $link_field ) ) {
    if ( is_array( $link_field ) && ! empty( $link_field['url'] ) ) {
        $link = $link_field['url'];
    } elseif ( is_string( $link_field ) && ! empty( $link_field ) ) {
        $link = $link_field;
    }
}
if ( empty( $link ) ) {
    $link = get_permalink( $post_id );
}


// Custom meta fields
$org       = get_post_meta( $post_id, 'org', true );
$overview  = get_post_meta( $post_id, 'overview', true );
$flags_raw = get_post_meta( $post_id, 'flags', true );

// Featured image (large for featured block)
$image_id = get_post_thumbnail_id( $post_id );
$img_src  = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';

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
// 4. Language labels
// --------------------------------------------------------------
$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$L = array(
    'eyebrow' => array( 'en' => 'Featured Resource', 'ar' => 'مورد مميّز', 'fr' => 'Ressource à la une' ),
    'org'     => array( 'en' => 'Publishing Organization:', 'ar' => 'الجهة الناشرة:', 'fr' => 'Organisme de publication :' ),
    'brief'   => array( 'en' => 'Brief Overview:', 'ar' => 'نظرة عامة موجزة:', 'fr' => 'Bref aperçu :' ),
);

// Use override if provided, otherwise use translated default
if ( empty( $eyebrow_override ) ) {
    $eyebrow = $L['eyebrow'][ $lang ] ?? $L['eyebrow']['en'];
} else {
    $eyebrow = $eyebrow_override;
}

$lo = $L['org'][ $lang ] ?? $L['org']['en'];
$lb = $L['brief'][ $lang ] ?? $L['brief']['en'];

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

<div class="section block-featured-resource">
    <div class="container">
        <article class="fresource" style="background-color: <?php echo esc_attr( $bg_color ); ?>;">
            <div class="fresource__body">
                <?php if ( $eyebrow ) : ?>
                    <p class="fresource__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
                <?php endif; ?>

                <h3 class="fresource__title">
                    <a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $title ); ?></a>
                </h3>

                <?php if ( $org ) : ?>
                    <p class="fresource__meta">
                        <span class="rc2__label"><?php echo esc_html( $lo ); ?></span>
                        <?php echo esc_html( $org ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( $overview ) : ?>
                    <p class="fresource__meta">
                        <span class="rc2__label"><?php echo esc_html( $lb ); ?></span>
                        <?php echo esc_html( $overview ); ?>
                    </p>
                <?php endif; ?>

                <div class="rc2__foot">
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
            </div>

            <?php if ( $img_src ) : ?>
                <div class="fresource__media">
                    <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                    <a class="fresource__link" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $title ); ?>"></a>
                </div>
            <?php endif; ?>
        </article>
    </div>
</div>