<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading      = get_field( 'heading' );
$img          = get_field( 'image' );
$alt          = get_field( 'alt' );
$chosen_color = get_field( 'color' ) ?: '#ffffff';
?>
<div class="section block-info-card">
	<div class="container">
		<div class="info-card" style="background-color: <?php echo esc_attr( $chosen_color ); ?>;">
			<div class="info-card__text">
				<?php if ( $heading ) : ?>
					<h2 class="info-card__heading"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				
				<?php if ( get_field( 'text' ) ) : ?>
					<div class="info-card__body">
						<?php 
						// the_field() automatically escapes content appropriately for WYSIWYG
						the_field( 'text' ); 
						?>
					</div>
				<?php endif; ?>
			</div>
			
			<?php if ( $img ) : ?>
				<img class="info-card__img" src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" decoding="async">
			<?php endif; ?>
		</div>
	</div>
</div>