<?php
/**
 * Fallback template.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="container" style="padding-block:80px;">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			the_title( '<h1>', '</h1>' );
			the_content();
		endwhile;
	else :
		echo '<p>' . esc_html__( 'Nothing here yet.', 'wes' ) . '</p>';
	endif;
	?>
</div>
<?php
get_footer();
