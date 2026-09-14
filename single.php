<?php
/**
 * Single template — article CPTs (explainer / howto / checklist) render their
 * block content full-width, exactly like the page template (the lesson-hero
 * block provides the hero, the rest provide the body).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
