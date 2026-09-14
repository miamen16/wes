<?php
/**
 * Block: Rich Text (prose).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$body = get_field( 'body' );
?>
<div class="block-prose">
	<div class="container">
		<div class="prose">
			<?php echo $body ? wp_kses_post( $body ) : '<p class="prose__placeholder">Add your content…</p>'; // phpcs:ignore ?>
		</div>
	</div>
</div>
