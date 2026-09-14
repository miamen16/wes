<?php
/**
 * WES content layer: default (Figma) content + field helpers.
 *
 * Defaults serve two purposes:
 *  1. Seeded into ACF fields on the Home page / options (so editors see real content).
 *  2. Fallback in templates via wes_field()/wes_rows() if a field is empty.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global (site-wide) default content — header, footer, social.
 *
 * @return array
 */
function wes_site_defaults() {
	return array(
		'primary_nav' => array(
			array( 'label' => 'About', 'url' => '#about' ),
			array( 'label' => 'Mediterranean Climate', 'url' => '#mediterranean-climate' ),
			array( 'label' => 'Take Action', 'url' => '#take-action' ),
			array( 'label' => 'Quiz', 'url' => '#quiz' ),
			array( 'label' => 'Teach & Facilitate', 'url' => '#teach' ),
			array( 'label' => 'Communicating Climate', 'url' => '#communicating' ),
			array( 'label' => 'Get Involved', 'url' => '#get-involved' ),
		),
		'lang_label'      => 'English',
		'stay_title'      => 'Stay connected',
		'stay_desc'       => 'Follow Our Climate - Our Community on social media for stories, updates, practical tips, and inspiration from across the Mediterranean.',
		'stay_social'     => array(
			array( 'network' => 'facebook', 'url' => '#' ),
			array( 'network' => 'instagram', 'url' => '#' ),
			array( 'network' => 'tiktok', 'url' => '#' ),
			array( 'network' => 'linkedin', 'url' => '#' ),
		),
		'footer_logos'    => array(
			array( 'image' => wes_img( 'funder-1.png' ), 'alt' => 'WES-BCA', 'size' => 'small' ),
			array( 'image' => wes_img( 'funder-2.png' ), 'alt' => 'Funded by the European Union', 'size' => 'small' ),
			array( 'image' => wes_img( 'funder-3.png' ), 'alt' => 'Partner organisations', 'size' => 'wide' ),
		),
		'footer_funding'  => 'This website is funded by the European Union. Its contents are the sole responsibility of the Our Climate - Our Community campaign and do not necessarily reflect the views of the European Union.',
		'footer_links'    => array(
			array( 'label' => 'About', 'url' => '#' ),
			array( 'label' => 'Contact', 'url' => '#' ),
			array( 'label' => 'Privacy Policy', 'url' => '#' ),
			array( 'label' => 'Cookie policy', 'url' => '#' ),
		),
		'footer_copyright' => '© Copyright WES-BCA 2024‑2027. All Rights Reserved', // non-breaking hyphen keeps the year intact
		'about_heading'    => 'About',
		'about_content'    => '<p><strong>Our Climate – Our Community is a regional campaign that aims to strengthen climate awareness and understanding across the Mediterranean with a particular emphasis on the communities of its southern and eastern shores.</strong></p><p>Through science, education, inclusive storytelling, and by connecting people, we transform ideas into action for a more resilient region. The campaign empowers women, youth, and communities to protect what we share: our climate and our Mediterranean.</p><p>We work hand in hand with civil society actors, ministries responsible for environment, climate, water, and education, EU Delegations in partner countries, regional partners, intergovernmental organisations, and other projects active across the region.</p><p>The campaign is funded by the European Union through the <a href="#">Water and Environment Support - Biodiversity and Climate Action (WES-BCA) in the Neighbourhood South region</a> project and will run until November 2027. It promotes the implementation of the <a href="#">Pact for the Mediterranean</a>, launched in November 2025, a flagship European Union initiative aimed at strengthening cooperation with its Southern Mediterranean partners.</p><p>The campaign also supports the implementation of the <a href="#">Mediterranean Strategy on Education for Sustainable Development (MSESD)</a> and its Action Plan, under the guidance of the Mediterranean Committee on Education for Sustainable Development (MCESD).</p>',
	);
}

/**
 * Home page default content.
 *
 * @return array
 */
function wes_home_defaults() {
	return array(
		// Hero.
		'hero_logo'     => wes_img( 'logo.svg' ),
		'hero_title'    => "Our Climate-\nOur Community",
		'hero_subtitle' => 'What climate change really means for life around the Mediterranean — and what we can do about it, together',

		// Mediterranean.
		'med_banner'    => 'The Mediterranean is our shared home — a cradle of civilizations and a climate hotspot.',
		'med_map'       => wes_img( 'mediterranean-map.png' ),

		// Understanding.
		'und_heading'   => 'Understanding Climate Change in the Mediterranean',
		'und_lead'      => 'Clear, accessible explanations of how climate change is reshaping water, food, nature, and daily life — and how we can respond.',
		'und_tab1'      => 'Explainers',
		'und_tab2'      => 'Infographics',
		'und_cards'     => array(
			array( 'image' => wes_img( 'card-living-with-heat.png' ), 'title_strong' => 'Living with Heat:', 'title_rest' => 'How Mediterranean Communities Adapt to Heatwaves', 'link' => '#' ),
			array( 'image' => wes_img( 'card-what-is-climate-change.png' ), 'title_strong' => 'What Is Climate Change', 'title_rest' => 'and What Does It Mean for the Mediterranean?', 'link' => '#' ),
			array( 'image' => wes_img( 'card-plastic.png' ), 'title_strong' => 'Plastic Isn’t Just Pollution:', 'title_rest' => 'It’s a Climate Problem', 'link' => '#' ),
			array( 'image' => wes_img( 'card-water-food.png' ), 'title_strong' => 'Water, Food, Climate and Nature:', 'title_rest' => 'What’s Changing in the Mediterranean', 'link' => '#' ),
		),

		// What You Can Do.
		'wycd_heading'  => 'What You Can Do – What We Can Do Together',
		'wycd_lead'     => 'Simple ways to act — individually and together',
		'wycd_tab1'     => 'How-to guides',
		'wycd_tab2'     => '5-step checklists',
		'wycd_cards'    => array(
			array( 'image' => wes_img( 'card-food-waste.png' ), 'category' => 'howto', 'label' => 'How-to guide', 'title' => 'Reduce Food Waste at Home', 'link' => '#', 'medallion' => false ),
			array( 'image' => wes_img( 'card-save-water.png' ), 'category' => 'checklist', 'label' => '5-step checklist', 'title' => 'Save Water at Home', 'link' => '#', 'medallion' => true ),
			array( 'image' => wes_img( 'card-single-use-plastics.png' ), 'category' => 'howto', 'label' => 'How-to guide', 'title' => 'Reduce Your Single-Use Plastics', 'link' => '#', 'medallion' => false ),
		),

		// Quiz.
		'quiz_heading'  => 'Is Your Climate Changing?',
		'quiz_lead'     => "Find out how climate change may be showing up where you live — and what you can do about it.\n5 questions · 30 seconds",
		'quiz_note'     => 'No right or wrong answers',
		'quiz_image'    => wes_img( 'quiz-illustration.png' ),
		'quiz_link'     => '#',

		// Teach & Facilitate.
		'teach_heading' => 'Teach & Facilitate',
		'teach_lead'    => 'Ready-to-use activities and tools for educators working with young people across the Mediterranean',
		'teach_body'    => 'Climate and sustainability education in the Mediterranean is built on solid foundations. As one of the world’s climate hotspots, our region has invested early and consistently in preparing..',
		'teach_readmore' => '#',
		'teach_readmore_label' => 'Read More',
		'teach_audiences' => array(
			array( 'blob' => 'teal', 'icon' => wes_img( 'icon-book.png' ), 'label' => "For School\nTeachers", 'link' => '#', 'x' => 93, 'y' => 20, 'w' => 281, 'h' => 257, 'iconw' => 135 ),
			array( 'blob' => 'green', 'icon' => wes_img( 'icon-laptop.png' ), 'label' => "For Youth\nLeaders", 'link' => '#', 'x' => 479, 'y' => 0, 'w' => 296, 'h' => 269, 'iconw' => 125 ),
			array( 'blob' => 'gold', 'icon' => wes_img( 'icon-easel.png' ), 'label' => "For Non-Formal Educators,\nand Facilitators", 'link' => '#', 'x' => 238, 'y' => 273, 'w' => 366, 'h' => 284, 'iconw' => 103 ),
		),

		// Communicating.
		'comms_title'   => 'Communicating Climate Effectively',
		'comms_lead'    => 'Evidence-based guidance for journalists and communicators grounded in the latest climate communication research.',
		'comms_btn'     => 'Explore the Toolkit',
		'comms_link'    => '#',
		'comms_image'   => wes_img( 'comms-illustration.png' ),

		// Get Involved.
		'gi_heading'    => 'Get Involved',
		'gi_subtitle'   => 'Connect with organizations working on climate action and community initiatives across the Mediterranean.',
		'gi_body'       => 'Use the filters to discover civil society groups, networks and initiatives active in your country on topics that matter to you – from climate action and community empowerment to education, youth engagement, tourism and cultural heritage – and find ways to connect or take part locally.',
		'gi_filters'    => array(
			array( 'label' => 'Egypt', 'icon' => 'map' ),
			array( 'label' => 'Climate Change', 'icon' => 'folder' ),
			array( 'label' => 'Civil Society Organisations', 'icon' => 'people' ),
		),
		'gi_orgs'       => array(
			array( 'name' => 'Green Future Initiative', 'link' => '#' ),
			array( 'name' => 'Youth for Civic Engagement', 'link' => '#' ),
			array( 'name' => 'Women Forward Network', 'link' => '#' ),
			array( 'name' => 'Culture and Heritage Collective', 'link' => '#' ),
		),
	);
}

/**
 * Get a Home field value, falling back to the Figma default.
 *
 * @param string $name Field name.
 * @return mixed
 */
function wes_field( $name ) {
	$val = function_exists( 'get_field' ) ? get_field( $name ) : null;
	if ( $val === null || $val === '' || $val === false || ( is_array( $val ) && empty( $val ) ) ) {
		$defaults = wes_home_defaults();
		return isset( $defaults[ $name ] ) ? $defaults[ $name ] : '';
	}
	return $val;
}

/**
 * Inline social SVG (fill = brand blue) for a given network.
 *
 * @param string $net Network slug.
 * @return string SVG markup.
 */
function wes_social_icon( $net ) {
	$icons = array(
		'facebook'  => '<path d="M13.5 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.5-1.5h1.7V3.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.1H7.2V13h2.6v8z"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2"/>',
		'tiktok'    => '<path d="M16.5 3c.3 2 1.6 3.6 3.5 3.9v2.6c-1.3 0-2.5-.4-3.5-1v5.9a5.7 5.7 0 1 1-5.7-5.7c.3 0 .6 0 .9.1v2.7a3 3 0 1 0 2.1 2.9V3z"/>',
		'linkedin'  => '<path d="M6.94 7.5a1.94 1.94 0 1 1 0-3.88 1.94 1.94 0 0 1 0 3.88zM5.2 9h3.5v11H5.2zM10.6 9h3.35v1.5h.05c.47-.85 1.6-1.75 3.3-1.75 3.5 0 4.15 2.3 4.15 5.3V20h-3.5v-4.9c0-1.17-.02-2.67-1.63-2.67-1.63 0-1.88 1.27-1.88 2.58V20h-3.5z"/>',
		'x'         => '<path d="M17.5 3h3l-6.6 7.5L21.7 21h-5.9l-4.2-5.4L6.6 21H3.5l7-8L2.7 3h6l3.8 5 4.9-5zM16.4 19.2h1.6L7.7 4.7H6z"/>',
		'youtube'   => '<path d="M21.6 7.2c-.2-.9-.9-1.6-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4c-.9.2-1.6.9-1.8 1.8C2 8.8 2 12 2 12s0 3.2.4 4.8c.2.9.9 1.6 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4c.9-.2 1.6-.9 1.8-1.8.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3z"/>',
	);
	$path = $icons[ $net ] ?? $icons['facebook'];
	return '<svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">' . $path . '</svg>';
}

/**
 * Per-language ACF options post_id (Polylang). Falls back to 'option' if Polylang inactive.
 *
 * @return string
 */
function wes_options_pid() {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );
		if ( ! $lang && function_exists( 'pll_default_language' ) ) {
			$lang = pll_default_language( 'slug' );
		}
		if ( $lang ) {
			return 'options_' . $lang;
		}
	}
	return 'option';
}

/**
 * Get an options (site-wide) field value, falling back to the default.
 *
 * @param string $name Field name.
 * @return mixed
 */
function wes_opt( $name ) {
	$val = function_exists( 'get_field' ) ? get_field( $name, wes_options_pid() ) : null;
	if ( $val === null || $val === '' || $val === false || ( is_array( $val ) && empty( $val ) ) ) {
		$defaults = wes_site_defaults();
		return isset( $defaults[ $name ] ) ? $defaults[ $name ] : '';
	}
	return $val;
}
