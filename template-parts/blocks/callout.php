<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text             = get_field( 'text' );
$chosen_color     = get_field( 'color' ) ?: '#008080';
$custom_quote_url = get_field( 'custom_quote_img' );

if ( empty( $custom_quote_url ) ) {
	$custom_quote_url = 'https://wes.aura.llc/wp-content/uploads/2026/06/boxicons_quote-right-filled.png';
}
?>
<div class="block-callout">
	<div class="container">
		<div class="callout" style="background-color: <?php echo esc_attr( $chosen_color ); ?>; display: flex; align-items: center; gap: 30px; padding: 30px 30px 30px 50px; border-radius: 16px;">
			<img class="callout__quote" src="<?php echo esc_url( $custom_quote_url ); ?>" alt="Quote" style="width: 50px; height: auto; flex-shrink: 0; object-fit: contain;">
			<p class="callout__text" style="margin: 0; flex-grow: 1;">
				<?php echo esc_html( $text ?: 'Key statement' ); ?>
			</p>
		</div>
	</div>
</div>