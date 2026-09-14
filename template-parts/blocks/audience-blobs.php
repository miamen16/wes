<?php
/**
 * Block: Audience Blobs.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_field( 'items' ) ?: array();
$pos   = array(
	array( 'x' => 93,  'y' => 20,  'w' => 281, 'h' => 257, 'iconw' => 135 ),
	array( 'x' => 479, 'y' => 0,   'w' => 296, 'h' => 269, 'iconw' => 125 ),
	array( 'x' => 238, 'y' => 273, 'w' => 366, 'h' => 284, 'iconw' => 103 ),
);
?>
<div class="section block-blobs">
	<div class="container">
		<div class="teach__cluster">
			<span class="tcircle" style="left:78px;  top:304px; width:127px; height:127px;"></span>
			<span class="tcircle" style="left:0;     top:200px; width:78px;  height:78px;"></span>
			<span class="tcircle" style="left:403px; top:174px; width:59px;  height:59px;"></span>
			<span class="tcircle" style="left:638px; top:304px; width:100px; height:100px;"></span>
			<span class="tcircle" style="left:793px; top:260px; width:45px;  height:45px;"></span>
			<?php foreach ( $items as $i => $aud ) : $p = $pos[ $i ] ?? $pos[0]; ?>
				<a class="aud aud--<?php echo esc_attr( $aud['blob'] ?? 'teal' ); ?>" href="<?php echo esc_url( $aud['link'] ?: '#' ); ?>"
					style="left:<?php echo (int) $p['x']; ?>px; top:<?php echo (int) $p['y']; ?>px; width:<?php echo (int) $p['w']; ?>px; height:<?php echo (int) $p['h']; ?>px;">
					<?php if ( ! empty( $aud['icon'] ) ) : ?>
						<img class="aud__icon" src="<?php echo esc_url( $aud['icon'] ); ?>" alt="" aria-hidden="true" style="width:<?php echo (int) $p['iconw']; ?>px;">
					<?php endif; ?>
					<span class="aud__label"><?php echo nl2br( esc_html( $aud['label'] ?? '' ) ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</div>
