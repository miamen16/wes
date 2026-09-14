<?php
/**
 * Block: Section Heading (optional eyebrow + heading + lead; optional cream band).
 * Lead field uses WYSIWYG for rich text.
 * ID/Anchor from editor added to .section__head container.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get the anchor/ID from block attributes
$block_id = $block['anchor'] ?? '';

$align   = get_field( 'align' ) ?: 'left';
$eyebrow = get_field( 'eyebrow' );
$h       = get_field( 'heading' );
$lead    = get_field( 'lead' );
$bg      = get_field( 'bg' );

$c  = ( 'center' === $align ) ? ' is-center' : '';
$c .= ( 'cream' === $bg ) ? ' block-section-heading--cream' : '';

// Build id attribute if exists
$id_attr = ! empty( $block_id ) ? ' id="' . esc_attr( $block_id ) . '"' : '';
?>

<div class="section block-section-heading<?php echo esc_attr( $c ); ?>">
    <div class="containerr">
        <div class="section__head"<?php echo $id_attr; ?>>
            <?php if ( $eyebrow ) : ?>
                <p class="section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
            <?php endif; ?>

            <h2 class="section__title"><?php echo esc_html( $h ?: 'Section heading' ); ?></h2>

            <?php if ( $lead ) : ?>
                <div class="section__lead">
                    <?php echo wp_kses_post( $lead ); // Allow safe HTML ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>