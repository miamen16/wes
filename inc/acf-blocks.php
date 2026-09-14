<?php
/**
 * ACF Gutenberg blocks — the WES component library for flexible page building.
 *
 * Each block reuses the same markup/CSS as the Home sections, so pages built in the
 * block editor are pixel-consistent with the design. Render templates live in
 * template-parts/blocks/.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Custom block category. */
add_filter( 'block_categories_all', function ( $cats ) {
	array_unshift( $cats, array(
		'slug'  => 'wes',
		'title' => 'WES',
		'icon'  => null,
	) );
	return $cats;
} );

/* Register blocks. */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}
	$blocks = array(
		array( 'page-hero', 'Page Hero', 'Inner-page hero with eyebrow, title and intro.', 'cover-image' ),
		array( 'section-heading', 'Section Heading', 'Heading + lead, left or centered.', 'heading' ),
		array( 'prose', 'Rich Text', 'Brand-styled body copy / article content.', 'editor-paragraph' ),
		array( 'cards', 'Content Cards', 'Grid/carousel of image + title cards.', 'grid-view' ),
		array( 'cta-banner', 'CTA Banner', 'Coloured rounded banner with text, button and image.', 'megaphone' ),
		array( 'steps', 'How-to Steps', 'Numbered step-by-step list.', 'editor-ol' ),
		array( 'checklist', 'Checklist', 'Checkable list of items.', 'yes-alt' ),
		array( 'download', 'Download Button', 'PDF / file download call-to-action.', 'download' ),
		array( 'action-cards', 'Action Cards', 'Cards with category badge + optional "5" medallion.', 'images-alt2' ),
		array( 'quiz-cta', 'Quiz CTA', 'Centered quiz call-to-action with illustration.', 'forms' ),
		array( 'audience-blobs', 'Audience Blobs', 'Organic colour blobs with icon + label.', 'marker' ),
		array( 'org-directory', 'Organisation Directory', 'Filters + two-column organisation list.', 'list-view' ),
		array( 'map-banner', 'Map Banner', 'Teal banner with centered text + map image.', 'location-alt' ),
		array( 'lesson-hero', 'Lesson Hero', 'Hero with eyebrow, title, intro + side illustration.', 'align-pull-right' ),
		array( 'custom-hero', 'Custom Hero', 'Simplified hero banner with gold wave layout.', 'admin-appearance' ),
		array( 'step-cards', 'Step Cards', 'Coloured numbered step cards in a grid.', 'screenoptions' ),
		array( 'info-card', 'Info Card', 'Soft rounded card: heading, text + small image.', 'info' ),
		array( 'related-links', 'Related Links', 'Labelled links with external-link icons.', 'admin-links' ),
		array( 'related-cpt', 'Related CPT', 'Labelled Custom Post Type links with custom icons.', 'admin-links' ),
		array( 'checklist-rows', 'Illustrated Checklist', 'Numbered rows with illustration + title + text.', 'editor-ul' ),
		array( 'callout', 'Callout Banner', 'Full-width coloured statement bar.', 'format-quote' ),
		array( 'media-text', 'Media + Text', 'Illustration beside a paragraph (image left or right).', 'align-left' ),
		array( 'quiz-app', 'Interactive Quiz', 'The "Is your climate changing?" multi-step quiz.', 'forms' ),
		array( 'principles', 'Principles List', 'Numbered accordion-style principle rows.', 'editor-ol' ),
		array( 'resource-cards', 'Resource Cards', 'Dense grid of text resource cards (category + title + text).', 'grid-view' ),
		array( 'resource-cards-dynamic', 'Resource Cards Dynamic', 'Dense grid of text resource cards (category + title + text).', 'grid-view' ),
		array( 'audience-guide', 'Audience Quick Guide', 'Tabbed guide with audience reference cards.', 'id-alt' ),
		array( 'audience-guide-cpt', 'Audience Quick Guide Dyinamic', 'Tabbed guide with audience reference cards.', 'id-alt' ),
		array( 'theme-index', 'Browse by Theme', 'Theme rows with tag pills.', 'tag' ),
		array( 'browse-taxonomy', 'Browse by Taxonomy', 'Accordion layout displaying taxonomy terms and posts.', 'category' ),
		array( 'tools-cards', 'Tools Cards', 'Two-column white tool cards: source + title + body + button.', 'book' ),
		array( 'featured-resource', 'Featured Resource', 'Highlighted resource card with illustration.', 'star-filled' ),
		array( 'featured-cpt', 'Featured CPT', 'Highlighted resource card with illustration.', 'star-filled' ),
		array( 'begin-cards', 'Begin Cards', 'Illustrated category cards (circle image + org + overview).', 'images-alt2' ),
		array( 'begin-cards-new', 'Begin Cards New', 'Illustrated category cards (circle image + org + overview).', 'images-alt2' ),
		array( 'card1', 'Card 1', 'Single illustrated category card (circle image + org + overview).', 'images-alt2' ),
		array( 'resource-library', 'Resource Library', 'Filter tabs + compact resource rows.', 'list-view' ),
	);
	foreach ( $blocks as $b ) {
		acf_register_block_type( array(
			'name'            => 'wes-' . $b[0],
			'title'           => $b[1],
			'description'     => $b[2],
			'category'        => 'wes',
			'icon'            => $b[3],
			'keywords'        => array( 'wes', $b[0] ),
			'render_template' => 'template-parts/blocks/' . $b[0] . '.php',
			'mode'            => 'preview',
			'supports'        => array( 'align' => false, 'mode' => true, 'jsx' => true, 'anchor' => true ),
			'enqueue_assets'  => function () {
				// Editor preview pulls the same front-end CSS.
				$uri = get_template_directory_uri();
				$dir = get_template_directory();
				wp_enqueue_style( 'wes-tokens', $uri . '/assets/css/tokens.css', array(), filemtime( "$dir/assets/css/tokens.css" ) );
				wp_enqueue_style( 'wes-main', $uri . '/assets/css/main.css', array( 'wes-tokens' ), filemtime( "$dir/assets/css/main.css" ) );
			},
		) );
	}
} );


/* Block field groups. */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'      => 'group_block_page_hero',
		'title'    => 'Page Hero',
		'fields'   => array(
			wes_acf_text( 'field_ph_eyebrow', 'Eyebrow / category', 'eyebrow' ),
			wes_acf_text( 'field_ph_title', 'Title', 'title' ),
			wes_acf_textarea( 'field_ph_intro', 'Intro', 'intro' ),
			array( 'key' => 'field_ph_bg', 'label' => 'Background', 'name' => 'bg', 'type' => 'select', 'choices' => array( 'white' => 'White', 'cream' => 'Cream', 'teal' => 'Teal gradient' ), 'default_value' => 'white' ),
		),
		'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-page-hero' ) ) ),
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_block_section_heading',
		'title'    => 'Section Heading',
		'fields'   => array(
			wes_acf_text( 'field_sh_eyebrow', 'Eyebrow', 'eyebrow' ),
			wes_acf_text( 'field_sh_heading', 'Heading', 'heading' ),
			wes_acf_textarea( 'field_sh_lead', 'Lead', 'lead' ),
			array( 'key' => 'field_sh_align', 'label' => 'Alignment', 'name' => 'align', 'type' => 'button_group', 'choices' => array( 'left' => 'Left', 'center' => 'Center' ), 'default_value' => 'left' ),
			array( 'key' => 'field_sh_bg', 'label' => 'Background', 'name' => 'bg', 'type' => 'button_group', 'choices' => array( '' => 'None', 'cream' => 'Cream' ), 'default_value' => '' ),
			array(
				'key'           => 'field_sh_lead_wysiwyg',
				'label'         => 'Lead',
				'name'          => 'lead',
				'type'          => 'wysiwyg',
				'media_upload'  => 0,
				'tabs'          => 'visual',
				'toolbar'       => 'basic',
				'delay'         => 0,
			),
		),
		'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-section-heading' ) ) ),
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_block_prose',
		'title'    => 'Rich Text',
		'fields'   => array(
			array( 'key' => 'field_prose_body', 'label' => 'Content', 'name' => 'body', 'type' => 'wysiwyg', 'media_upload' => 1, 'tabs' => 'all' ),
		),
		'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-prose' ) ) ),
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_block_cards',
		'title'    => 'Content Cards',
		'fields'   => array(
			array( 'key' => 'field_cards_layout', 'label' => 'Layout', 'name' => 'layout', 'type' => 'button_group', 'choices' => array( 'carousel' => 'Carousel', 'grid' => 'Grid' ), 'default_value' => 'grid' ),
			array(
				'key' => 'field_cards_items', 'label' => 'Cards', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add card',
				'sub_fields' => array(
					wes_acf_image( 'field_card_img', 'Image', 'image' ),
					wes_acf_text( 'field_card_strong', 'Title (bold)', 'title_strong' ),
					wes_acf_text( 'field_card_rest', 'Title (rest)', 'title_rest' ),
					wes_acf_text( 'field_card_link', 'Link', 'link' ),
					wes_acf_textarea( 'field_card_back', 'Flip-back text', 'back' ),
					array(
						'key' => 'field_card_related_repeater', 'label' => 'Flip-back related principles', 'name' => 'related', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add Related Principle', 'collapsed' => 'field_related_text',
						'sub_fields' => array(
							array( 'key' => 'field_related_number', 'label' => 'Number', 'name' => 'number', 'type' => 'text', 'default_value' => '', 'placeholder' => 'e.g., 1, 2, 3, or I, II, III', 'instructions' => 'Optional: Display a number or icon before the text' ),
							array( 'key' => 'field_related_text', 'label' => 'Text', 'name' => 'text', 'type' => 'text', 'required' => 1, 'placeholder' => 'Principle name', 'instructions' => 'This will be used as the collapsible title' ),
							array( 'key' => 'field_related_link', 'label' => 'Link (optional)', 'name' => 'link', 'type' => 'url', 'placeholder' => 'https://example.com/principle', 'instructions' => 'Optional: Add a URL to make this principle clickable' ),
						),
					),
					array( 'key' => 'field_card_related_old', 'label' => 'Flip-back related (old format - deprecated)', 'name' => 'related_old', 'type' => 'textarea', 'instructions' => 'Deprecated: Use the repeater above instead', 'wrapper' => array( 'width' => '', 'class' => 'acf-hidden', 'id' => '' ) ),
				),
			),
		),
		'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-cards' ) ) ),
	) );

	acf_add_local_field_group( array(
		'key' => 'group_block_tools_cards', 'title' => 'Tools Cards',
		'fields' => array(
			array( 'key' => 'field_tc_items', 'label' => 'Tool cards', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add tool',
				'sub_fields' => array(
					array( 'key' => 'field_tc_img', 'label' => 'Illustration', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
					wes_acf_text( 'field_tc_source', 'Source (e.g. UNEP · 2020)', 'source' ), wes_acf_text( 'field_tc_title', 'Title', 'title' ), wes_acf_textarea( 'field_tc_body', 'Body', 'body' ), wes_acf_text( 'field_tc_button', 'Button label', 'button' ), wes_acf_text( 'field_tc_link', 'Link', 'link' ),
				),
			),
		),
		'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-tools-cards' ) ) ),
	) );

	acf_add_local_field_group( array(
		'key' => 'group_block_cta', 'title' => 'CTA Banner',
		'fields' => array( wes_acf_text( 'field_cta_title', 'Title', 'title' ), wes_acf_textarea( 'field_cta_text', 'Text', 'text' ), wes_acf_text( 'field_cta_btn', 'Button label', 'btn' ), wes_acf_text( 'field_cta_link', 'Button link', 'link' ), wes_acf_image( 'field_cta_img', 'Image', 'image' ), array( 'key' => 'field_cta_color', 'label' => 'Colour', 'name' => 'color', 'type' => 'select', 'choices' => array( 'teal' => 'Light teal', 'cream' => 'Cream', 'green' => 'Light green', 'orange' => 'Light orange' ), 'default_value' => 'teal' ) ),
		'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-cta-banner' ) ) ),
	) );

	acf_add_local_field_group( array( 'key' => 'group_block_steps', 'title' => 'How-to Steps', 'fields' => array( wes_acf_text( 'field_steps_heading', 'Heading', 'heading' ), array( 'key' => 'field_steps_items', 'label' => 'Steps', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add step', 'sub_fields' => array( wes_acf_text( 'field_step_title', 'Step title', 'title' ), wes_acf_textarea( 'field_step_text', 'Step description', 'text' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-steps' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_checklist', 'title' => 'Checklist', 'fields' => array( wes_acf_text( 'field_cl_heading', 'Heading', 'heading' ), array( 'key' => 'field_cl_items', 'label' => 'Items', 'name' => 'items', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add item', 'sub_fields' => array( wes_acf_text( 'field_cl_item', 'Item', 'item' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-checklist' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_download', 'title' => 'Download Button', 'fields' => array( wes_acf_text( 'field_dl_label', 'Label', 'label' ), array( 'key' => 'field_dl_file', 'label' => 'File', 'name' => 'file', 'type' => 'file', 'return_format' => 'url' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-download' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_action_cards', 'title' => 'Action Cards', 'fields' => array( array( 'key' => 'field_ac_items', 'label' => 'Cards', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add card', 'sub_fields' => array( wes_acf_image( 'field_ac_img', 'Image', 'image' ), array( 'key' => 'field_ac_cat', 'label' => 'Category', 'name' => 'category', 'type' => 'select', 'choices' => array( 'howto' => 'How-to guide', 'checklist' => '5-step checklist' ) ), wes_acf_text( 'field_ac_label', 'Badge label', 'label' ), wes_acf_text( 'field_ac_title', 'Title', 'title' ), wes_acf_text( 'field_ac_link', 'Link', 'link' ), array( 'key' => 'field_ac_med', 'label' => 'Show "5" medallion', 'name' => 'medallion', 'type' => 'true_false', 'ui' => 1 ), array( 'key' => 'field_ac_is_new', 'label' => 'Mark as NEW', 'name' => 'is_new', 'type' => 'true_false', 'ui' => 1, 'instructions' => 'Show a "NEW" badge on this card.' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-action-cards' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_quiz', 'title' => 'Quiz CTA', 'fields' => array( wes_acf_text( 'field_qz_heading', 'Heading', 'heading' ), wes_acf_textarea( 'field_qz_lead', 'Lead', 'lead' ), wes_acf_text( 'field_qz_note', 'Note', 'note' ), wes_acf_image( 'field_qz_img', 'Illustration', 'image' ), wes_acf_text( 'field_qz_link', 'Link', 'link' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-quiz-cta' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_blobs', 'title' => 'Audience Blobs', 'fields' => array( array( 'key' => 'field_blob_items', 'label' => 'Blobs', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'max' => 3, 'button_label' => 'Add blob', 'sub_fields' => array( array( 'key' => 'field_blob_color', 'label' => 'Colour', 'name' => 'blob', 'type' => 'select', 'choices' => array( 'teal' => 'Teal', 'green' => 'Green', 'gold' => 'Gold' ) ), wes_acf_image( 'field_blob_icon', 'Icon', 'icon' ), wes_acf_textarea( 'field_blob_label', 'Label', 'label', 2 ), wes_acf_text( 'field_blob_link', 'Link', 'link' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-audience-blobs' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_org', 'title' => 'Organisation Directory', 'fields' => array( wes_acf_text( 'field_od_heading', 'Heading', 'heading' ), wes_acf_textarea( 'field_od_subtitle', 'Subtitle', 'subtitle' ), wes_acf_textarea( 'field_od_body', 'Body', 'body', 4 ), array( 'key' => 'field_od_filters', 'label' => 'Filters', 'name' => 'filters', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add filter', 'sub_fields' => array( wes_acf_text( 'field_od_flabel', 'Label', 'label' ), array( 'key' => 'field_od_ficon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => array( 'map' => 'Map', 'folder' => 'Folder', 'people' => 'People' ) ) ) ), array( 'key' => 'field_od_orgs', 'label' => 'Organisations', 'name' => 'orgs', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add organisation', 'sub_fields' => array( wes_acf_text( 'field_od_oname', 'Name', 'name' ), wes_acf_text( 'field_od_olink', 'Link', 'link' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-org-directory' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_map', 'title' => 'Map Banner', 'fields' => array( wes_acf_textarea( 'field_mb_banner', 'Banner text', 'banner', 2 ), wes_acf_image( 'field_mb_map', 'Map image', 'map' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-map-banner' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_lesson_hero', 'title' => 'Lesson Hero', 'fields' => array( wes_acf_text( 'field_lh_eyebrow', 'Eyebrow', 'eyebrow' ), wes_acf_text( 'field_lh_sub_eyebrow', 'Sub Eyebrow Description', 'sub_eyebrow' ), wes_acf_text( 'field_lh_title', 'Title', 'title' ), wes_acf_textarea( 'field_lh_intro', 'Intro', 'intro' ), wes_acf_image( 'field_lh_image', 'Illustration', 'image' ), wes_acf_text( 'field_lh_alt', 'Illustration alt text (SEO)', 'alt' ), wes_acf_text( 'field_lh_cta1', 'Button 1 label', 'cta1_label' ), wes_acf_text( 'field_lh_cta1l', 'Button 1 link', 'cta1_link' ), wes_acf_text( 'field_lh_cta2', 'Button 2 label', 'cta2_label' ), wes_acf_text( 'field_lh_cta2l', 'Button 2 link', 'cta2_link' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-lesson-hero' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_custom_hero', 'title' => 'Custom Hero', 'fields' => array( wes_acf_text( 'field_ch_eyebrow', 'Eyebrow', 'eyebrow' ), wes_acf_text( 'field_ch_sub_eyebrow', 'Sub Eyebrow Description', 'sub_eyebrow' ), wes_acf_text( 'field_ch_title', 'Title', 'title' ), wes_acf_textarea( 'field_ch_intro', 'Intro', 'intro' ), wes_acf_image( 'field_ch_image', 'Illustration', 'image' ), wes_acf_image( 'field_ch_emblem_image', 'Custom Emblem Image', 'emblem_image' ), wes_acf_text( 'field_ch_alt', 'Illustration alt text (SEO)', 'alt' ), wes_acf_text( 'field_ch_cta1', 'Button 1 label', 'cta1_label' ), wes_acf_text( 'field_ch_cta1l', 'Button 1 link', 'cta1_link' ), wes_acf_text( 'field_ch_cta2', 'Button 2 label', 'cta2_label' ), wes_acf_text( 'field_ch_cta2l', 'Button 2 link', 'cta2_link' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-custom-hero' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_step_cards', 'title' => 'Step Cards', 'fields' => array( array( 'key' => 'field_sc_items', 'label' => 'Steps', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add step', 'sub_fields' => array( wes_acf_text( 'field_sc_label', 'Step label', 'label' ), wes_acf_text( 'field_sc_title', 'Title', 'title' ), wes_acf_textarea( 'field_sc_text', 'Description', 'text', 4 ), array( 'key' => 'field_sc_color', 'label' => 'Colour', 'name' => 'color', 'type' => 'select', 'choices' => array( 'orange' => 'Orange', 'teal' => 'Teal', 'gold' => 'Gold', 'green' => 'Green', 'blue' => 'Blue' ) ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-step-cards' ) ) ) ) );

	acf_add_local_field_group( array(
		'key'      => 'group_block_info_card',
		'title'    => 'Info Card',
		'fields'   => array(
			wes_acf_text( 'field_ic_heading', 'Heading', 'heading' ),
			array( 'key' => 'field_ic_text', 'label' => 'Text', 'name' => 'text', 'type' => 'wysiwyg', 'default_value' => '', 'toolbar' => 'basic', 'media_upload' => 1, 'delay' => 1, 'tabs' => 'visual,text' ),
			wes_acf_image( 'field_ic_image', 'Image', 'image' ),
			wes_acf_text( 'field_ic_alt', 'Image alt text (SEO)', 'alt' ),
			array( 'key' => 'field_ic_color', 'label' => 'Colour', 'name' => 'color', 'type' => 'select', 'choices' => array( 'teal' => 'Light teal', 'cream' => 'Cream', 'green' => 'Light green' ), 'default_value' => 'teal' ),
			array( 'key' => 'field_ic_color_picker', 'label' => 'Background Colour', 'name' => 'color_picker', 'type' => 'color_picker', 'default_value' => '#008080' ),
		),
		'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-info-card' ) ) ),
	) );

	acf_add_local_field_group( array( 'key' => 'group_block_related', 'title' => 'Related Links', 'fields' => array( wes_acf_text( 'field_rl_heading', 'Heading', 'heading' ), wes_acf_text( 'field_rl_subtitle', 'Subtitle', 'subtitle' ), array( 'key' => 'field_rl_bg', 'label' => 'Background', 'name' => 'bg', 'type' => 'select', 'choices' => array( 'none' => 'None', 'cream' => 'Cream' ), 'default_value' => 'none' ), array( 'key' => 'field_rl_items', 'label' => 'Links', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add link', 'sub_fields' => array( wes_acf_text( 'field_rl_cat', 'Category label', 'category' ), wes_acf_text( 'field_rl_title', 'Title', 'title' ), wes_acf_text( 'field_rl_link', 'Link', 'link' ), array( 'key' => 'field_rl_custom_svg', 'label' => 'Upload SVG Icon', 'name' => 'custom_svg', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'thumbnail' ), array( 'key' => 'field_rl_icon_size', 'label' => 'Icon Size (px)', 'name' => 'icon_size', 'type' => 'number', 'default_value' => 30, 'min' => 10, 'max' => 60 ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-related-links' ) ) ) ) );

	acf_add_local_field_group( array(
		'key' => 'group_block_related_cpt', 'title' => 'Related CPT',
		'fields' => array(
			wes_acf_text( 'field_rcpt_heading', 'Heading', 'heading' ), wes_acf_text( 'field_rcpt_subtitle', 'Subtitle', 'subtitle' ),
			array( 'key' => 'field_rcpt_bg', 'label' => 'Background', 'name' => 'bg', 'type' => 'select', 'choices' => array( 'none' => 'None', 'cream' => 'Cream' ), 'default_value' => 'none' ),
			array( 'key' => 'field_rcpt_target_post_types', 'label' => 'Target Post Types', 'name' => 'target_post_types', 'type' => 'select', 'choices' => array( 'howto' => 'How-to Guides', 'checklist' => 'Checklists', 'explainer' => 'Explainers', 'resource' => 'Resource Books', 'resource_card' => 'Resource Cards', 'post' => 'Posts', 'page' => 'Pages' ), 'multiple' => 1, 'ui' => 1, 'ajax' => 0, 'return_format' => 'value', 'default_value' => array( 'howto', 'checklist', 'explainer' ), 'instructions' => 'Select one or more post types to pull from. Order here affects the "Custom Post Type Input Order" option.' ),
			array( 'key' => 'field_rcpt_posts_limit', 'label' => 'Number of Items to Show', 'name' => 'posts_limit', 'type' => 'number', 'default_value' => 3, 'min' => 1, 'max' => 20 ),
			array( 'key' => 'field_rb_orderby', 'label' => 'Order By', 'name' => 'orderby', 'type' => 'select', 'choices' => array( 'date_desc' => 'Date (Newest First)', 'date_asc' => 'Date (Oldest First)', 'title_asc' => 'Title (Alphabetical)', 'cpt_input_order' => 'Custom Post Type Input Order (As written)' ), 'default_value' => 'date_desc' ),
			array( 'key' => 'field_rcpt_include_posts', 'label' => 'Specific Posts to Include', 'name' => 'include_posts', 'type' => 'post_object', 'post_type' => array( 'howto', 'checklist', 'explainer', 'resource', 'resource_card', 'post', 'page' ), 'field_type' => 'select', 'multiple' => 1, 'ui' => 1, 'ajax' => 1, 'return_format' => 'id', 'instructions' => 'Select specific posts to include (overrides the query). Leave empty to show all matching posts.' ),
			array( 'key' => 'field_rcpt_exclude_posts', 'label' => 'Specific Posts to Exclude', 'name' => 'exclude_posts', 'type' => 'post_object', 'post_type' => array( 'howto', 'checklist', 'explainer', 'resource', 'resource_card', 'post', 'page' ), 'field_type' => 'select', 'multiple' => 1, 'ui' => 1, 'ajax' => 1, 'return_format' => 'id', 'instructions' => 'Select posts to exclude from the results.' ),
		),
		'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-related-cpt' ) ) ),
	) );

	acf_add_local_field_group( array( 'key' => 'group_block_callout', 'title' => 'Callout Banner', 'fields' => array( wes_acf_textarea( 'field_co_text', 'Statement', 'text', 2 ), array( 'key' => 'field_co_custom_quote', 'label' => 'Custom Quote Icon (PNG)', 'name' => 'custom_quote_img', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'thumbnail' ), array( 'key' => 'field_co_color', 'label' => 'Custom Background Colour', 'name' => 'color', 'type' => 'color_picker', 'default_value' => '#008080' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-callout' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_media_text', 'title' => 'Media + Text', 'fields' => array( wes_acf_image( 'field_mt_image', 'Image', 'image' ), wes_acf_text( 'field_mt_alt', 'Image alt text (SEO)', 'alt' ), array( 'key' => 'field_mt_body', 'label' => 'Text', 'name' => 'body', 'type' => 'wysiwyg', 'media_upload' => 0, 'tabs' => 'visual' ), array( 'key' => 'field_mt_pos', 'label' => 'Image position', 'name' => 'position', 'type' => 'button_group', 'choices' => array( 'left' => 'Left', 'right' => 'Right' ), 'default_value' => 'left' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-media-text' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_checklist_rows', 'title' => 'Illustrated Checklist', 'fields' => array( array( 'key' => 'field_clr_items', 'label' => 'Rows', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add row', 'sub_fields' => array( wes_acf_image( 'field_clr_img', 'Illustration', 'image' ), array( 'key' => 'field_clr_color', 'label' => 'Number colour', 'name' => 'color', 'type' => 'select', 'choices' => array( 'teal' => 'Teal', 'green' => 'Green', 'blue' => 'Blue', 'gold' => 'Gold', 'orange' => 'Orange' ) ), wes_acf_text( 'field_clr_title', 'Title', 'title' ), wes_acf_textarea( 'field_clr_text', 'Description', 'text', 3 ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-checklist-rows' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_principles', 'title' => 'Principles List', 'fields' => array( array( 'key' => 'field_pr_items', 'label' => 'Principles', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add principle', 'sub_fields' => array( wes_acf_text( 'field_pr_title', 'Title', 'title' ), wes_acf_text( 'field_pr_tags', 'Tag keywords (· separated)', 'tags' ), wes_acf_text( 'field_pr_color', 'Badge colour (teal|gold|orange|green)', 'color' ), array( 'key' => 'field_pr_image', 'label' => 'Panel Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium' ), wes_acf_textarea( 'field_pr_intro', 'Intro paragraph', 'intro', 3 ), wes_acf_textarea( 'field_pr_do', 'What to do (one per line)', 'do' ), wes_acf_textarea( 'field_pr_avoid', 'What to avoid (one per line)', 'avoid' ), wes_acf_textarea( 'field_pr_practice', 'In practice', 'practice', 3 ), wes_acf_textarea( 'field_pr_practice_crossed', 'In Practice - Crossed Text (Strikethrough)', 'practice_crossed', 2 ), wes_acf_textarea( 'field_pr_practice_normal', 'In Practice - Normal Text', 'practice_normal', 2 ), wes_acf_textarea( 'field_pr_quick_check', 'Quick check', 'quick_check', 2 ), wes_acf_textarea( 'field_pr_why', 'Why this works', 'why', 3 ), wes_acf_textarea( 'field_pr_why_part_2', 'Why this works - Part 2', 'why_part_2', 3 ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-principles' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_resource_cards', 'title' => 'Resource Cards', 'fields' => array( array( 'key' => 'field_rc_items', 'label' => 'Cards', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add card', 'sub_fields' => array( wes_acf_text( 'field_rc_cat', 'Category label', 'cat' ), wes_acf_text( 'field_rc_title', 'Title', 'title' ), wes_acf_textarea( 'field_rc_text', 'Description', 'text', 3 ), wes_acf_text( 'field_rc_meta', 'Meta (e.g. reading time)', 'meta' ), wes_acf_text( 'field_rc_org', 'Publishing organization', 'org' ), wes_acf_textarea( 'field_rc_overview', 'Brief overview', 'overview', 3 ), wes_acf_text( 'field_rc_flags', 'Flags (pipe-separated, e.g. en|fr)', 'flags' ), wes_acf_text( 'field_rc_tags', 'Tags (pipe-separated)', 'tags' ), wes_acf_text( 'field_rc_link', 'Link', 'link' ) ) ), wes_acf_text( 'field_rc_cta', 'CTA label (optional, after grid)', 'cta' ), wes_acf_text( 'field_rc_ctalink', 'CTA link', 'cta_link' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-resource-cards' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_resource_cards_dynamic', 'title' => 'Resource Cards Dynamic Settings', 'fields' => array( array( 'key' => 'field_rc_dyn_source', 'label' => 'Resource Source', 'name' => 'resource_source', 'type' => 'true_false', 'ui' => 1, 'ui_on_text' => 'Featured Only', 'ui_off_text' => 'All Resources', 'default_value' => 0, 'instructions' => 'Show featured resources only, or all resources.' ), array( 'key' => 'field_rc_dyn_posts_per_page', 'label' => 'Posts Per Page', 'name' => 'posts_per_page', 'type' => 'number', 'default_value' => 9, 'min' => -1, 'instructions' => 'Number of cards per page. Enter -1 for unlimited per page.' ), array( 'key' => 'field_rc_dyn_max_posts', 'label' => 'Max Posts (Total)', 'name' => 'max_posts', 'type' => 'number', 'default_value' => -1, 'min' => -1, 'instructions' => 'Maximum total cards across all pages. Enter -1 for unlimited.' ), array( 'key' => 'field_rc_dyn_action_types', 'label' => 'Filter by Action Type', 'name' => 'action_types', 'type' => 'taxonomy', 'taxonomy' => 'resource_action_type', 'field_type' => 'select', 'multiple' => 1, 'allow_null' => 0, 'return_format' => 'slug', 'instructions' => 'Select one or more action types to filter the cards.' ), array( 'key' => 'field_rc_dyn_audiences', 'label' => 'Filter by Target Audience', 'name' => 'audiences', 'type' => 'taxonomy', 'taxonomy' => 'resource_audience', 'field_type' => 'select', 'multiple' => 1, 'allow_null' => 0, 'return_format' => 'slug', 'instructions' => 'Select one or more audiences to filter the cards.' ), array( 'key' => 'field_rc_dyn_bg_color', 'label' => 'Background Color', 'name' => 'bg_color', 'type' => 'color_picker', 'default_value' => '#ffffff', 'instructions' => 'Select a background color for the cards section.' ), array( 'key' => 'field_rc_dyn_cta', 'label' => 'CTA Button Text', 'name' => 'cta', 'type' => 'text', 'instructions' => 'Text for the call-to-action button at the bottom.' ), array( 'key' => 'field_rc_dyn_cta_link', 'label' => 'CTA Button Link', 'name' => 'cta_link', 'type' => 'url', 'instructions' => 'Link for the CTA button.' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-resource-cards-dynamic' ) ) ), 'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'instruction_placement' => 'label', 'active' => true ) );

	acf_add_local_field_group( array( 'key' => 'group_block_featured_resource', 'title' => 'Featured Resource', 'fields' => array( wes_acf_text( 'field_fr_eyebrow', 'Eyebrow', 'eyebrow' ), wes_acf_text( 'field_fr_title', 'Title', 'title' ), wes_acf_text( 'field_fr_org', 'Publishing organization', 'org' ), wes_acf_textarea( 'field_fr_overview', 'Brief overview', 'overview', 4 ), wes_acf_text( 'field_fr_flags', 'Flags (pipe-separated)', 'flags' ), wes_acf_text( 'field_fr_tags', 'Tags (pipe-separated)', 'tags' ), array( 'key' => 'field_fr_image', 'label' => 'Illustration', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ), wes_acf_text( 'field_fr_link', 'Link', 'link' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-featured-resource' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_featured_cpt', 'title' => 'Featured CPT Settings', 'fields' => array( array( 'key' => 'field_featured_select_post', 'label' => 'Select Resource Card', 'name' => 'select_post', 'type' => 'post_object', 'post_type' => array( 'resource_card' ), 'return_format' => 'object', 'multiple' => 0, 'allow_null' => 0, 'instructions' => 'Choose a resource card to feature.' ), array( 'key' => 'field_featured_eyebrow', 'label' => 'Eyebrow Text (Optional)', 'name' => 'eyebrow', 'type' => 'text', 'instructions' => 'Leave empty to use "Featured Resource" (translated).', 'default_value' => '' ), array( 'key' => 'field_featured_bg_color', 'label' => 'Background Color', 'name' => 'bg_color', 'type' => 'color_picker', 'default_value' => '#ffffff', 'instructions' => 'Select a background color for the featured section.' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-featured-cpt' ) ) ), 'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'instruction_placement' => 'label', 'active' => true ) );

	acf_add_local_field_group( array( 'key' => 'group_block_begin_cards', 'title' => 'Begin Cards', 'fields' => array( array( 'key' => 'field_bc_items', 'label' => 'Cards', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add card', 'sub_fields' => array( array( 'key' => 'field_bc_image', 'label' => 'Illustration', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ), wes_acf_text( 'field_bc_category', 'Category eyebrow', 'category' ), wes_acf_text( 'field_bc_title', 'Title', 'title' ), wes_acf_text( 'field_bc_org', 'Publishing organization', 'org' ), wes_acf_textarea( 'field_bc_overview', 'Brief overview', 'overview', 3 ), wes_acf_text( 'field_bc_flags', 'Flags (pipe-separated)', 'flags' ), wes_acf_text( 'field_bc_link', 'Link', 'link' ) ) ), wes_acf_text( 'field_bc_cta', 'CTA label (optional, after grid)', 'cta' ), wes_acf_text( 'field_bc_ctalink', 'CTA link', 'cta_link' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-begin-cards' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_begin_cards_new', 'title' => 'Begin Cards New Settings', 'fields' => array( array( 'key' => 'field_bcn_select_post_1', 'label' => 'Select Resource Card 1', 'name' => 'select_post_1', 'type' => 'post_object', 'post_type' => array( 'resource_card' ), 'return_format' => 'object', 'multiple' => 0, 'allow_null' => 1, 'instructions' => 'Choose the first resource card.' ), array( 'key' => 'field_bcn_select_post_2', 'label' => 'Select Resource Card 2', 'name' => 'select_post_2', 'type' => 'post_object', 'post_type' => array( 'resource_card' ), 'return_format' => 'object', 'multiple' => 0, 'allow_null' => 1, 'instructions' => 'Choose the second resource card (optional).' ), array( 'key' => 'field_bcn_select_post_3', 'label' => 'Select Resource Card 3', 'name' => 'select_post_3', 'type' => 'post_object', 'post_type' => array( 'resource_card' ), 'return_format' => 'object', 'multiple' => 0, 'allow_null' => 1, 'instructions' => 'Choose the third resource card (optional).' ), array( 'key' => 'field_bcn_bg_color', 'label' => 'Background Color', 'name' => 'bg_color', 'type' => 'color_picker', 'default_value' => '#ffffff', 'instructions' => 'Select a background color for the cards section.' ), array( 'key' => 'field_bcn_cta', 'label' => 'CTA Button Text', 'name' => 'cta', 'type' => 'text', 'instructions' => 'Optional call-to-action button below the cards.' ), array( 'key' => 'field_bcn_cta_link', 'label' => 'CTA Button Link', 'name' => 'cta_link', 'type' => 'url', 'instructions' => 'URL for the CTA button.' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-begin-cards-new' ) ) ), 'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'instruction_placement' => 'label', 'active' => true ) );

	acf_add_local_field_group( array( 'key' => 'group_block_card1', 'title' => 'Card 1 Settings', 'fields' => array( array( 'key' => 'field_card1_select_post', 'label' => 'Select Resource Card', 'name' => 'select_post', 'type' => 'post_object', 'post_type' => array( 'resource_card' ), 'return_format' => 'object', 'multiple' => 0, 'allow_null' => 0, 'instructions' => 'Choose a resource card post to display.' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-card1' ) ) ), 'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'instruction_placement' => 'label', 'active' => true ) );

	acf_add_local_field_group( array( 'key' => 'group_block_resource_library', 'title' => 'Resource Library', 'fields' => array( wes_acf_text( 'field_rlib_heading', 'Heading', 'heading' ), wes_acf_textarea( 'field_rlib_lead', 'Lead', 'lead', 2 ), array( 'key' => 'field_rlib_tabs', 'label' => 'Tabs', 'name' => 'tabs', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add tab', 'sub_fields' => array( wes_acf_text( 'field_rlib_tlabel', 'Tab label', 'label' ) ) ), array( 'key' => 'field_rlib_rows', 'label' => 'Rows', 'name' => 'rows', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add row', 'sub_fields' => array( wes_acf_text( 'field_rlib_rtitle', 'Title', 'title' ), wes_acf_text( 'field_rlib_rorg', 'Publishing organization', 'org' ), wes_acf_text( 'field_rlib_rtags', 'Tags (pipe-separated)', 'tags' ), wes_acf_text( 'field_rlib_rlink', 'Link', 'link' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-resource-library' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_audience_guide', 'title' => 'Audience Quick Guide', 'fields' => array( wes_acf_text( 'field_ag_heading', 'Heading', 'heading' ), wes_acf_textarea( 'field_ag_lead', 'Lead', 'lead', 2 ), array( 'key' => 'field_ag_tabs', 'label' => 'Tabs', 'name' => 'tabs', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add tab', 'sub_fields' => array( wes_acf_text( 'field_ag_tlabel', 'Tab label', 'label' ) ) ), array( 'key' => 'field_ag_cards', 'label' => 'Cards', 'name' => 'cards', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add card', 'collapsed' => 'field_ag_ctitle', 'sub_fields' => array( array( 'key' => 'field_ag_ctab', 'label' => 'Tab index (0-based)', 'name' => 'tab', 'type' => 'number', 'default_value' => 0 ), wes_acf_text( 'field_ag_ccat', 'Category label', 'cat' ), wes_acf_text( 'field_ag_ctitle', 'Title', 'title' ), wes_acf_textarea( 'field_ag_ctext', 'Description', 'text', 3 ), array( 'key' => 'field_ag_clink', 'label' => 'Link', 'name' => 'link', 'type' => 'link', 'return_format' => 'array' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-audience-guide' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_audience_guide_cpt', 'title' => 'Audience Quick Guide', 'fields' => array( wes_acf_text( 'field_ag_cpt_heading', 'Heading', 'heading' ), wes_acf_textarea( 'field_ag_cpt_lead', 'Lead', 'lead', 2 ), array( 'key' => 'field_ag_cpt_tabs', 'label' => 'Tabs', 'name' => 'tabs', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add tab', 'sub_fields' => array( wes_acf_text( 'field_ag_cpt_tlabel', 'Tab label', 'label' ) ) ), array( 'key' => 'field_ag_cpt_cards', 'label' => 'Cards', 'name' => 'cards', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add card', 'collapsed' => 'field_ag_cpt_ctitle', 'sub_fields' => array( array( 'key' => 'field_ag_cpt_ctab', 'label' => 'Tab index (0-based)', 'name' => 'tab', 'type' => 'number', 'default_value' => 0 ), wes_acf_text( 'field_ag_cpt_ccat', 'Category label', 'cat' ), wes_acf_text( 'field_ag_cpt_ctitle', 'Title', 'title' ), wes_acf_textarea( 'field_ag_cpt_ctext', 'Description', 'text', 3 ), array( 'key' => 'field_ag_cpt_clink', 'label' => 'Link', 'name' => 'link', 'type' => 'link', 'return_format' => 'array' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-audience-guide-cpt' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_theme_index', 'title' => 'Browse by Theme', 'fields' => array( wes_acf_text( 'field_ti_heading', 'Heading', 'heading' ), wes_acf_textarea( 'field_ti_lead', 'Lead', 'lead', 2 ), array( 'key' => 'field_ti_items', 'label' => 'Themes', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add theme', 'sub_fields' => array( wes_acf_text( 'field_ti_name', 'Theme name', 'name' ), wes_acf_text( 'field_ti_tags', 'Tags (comma-separated)', 'tags' ) ) ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-theme-index' ) ) ) ) );

	acf_add_local_field_group( array( 'key' => 'group_block_browse_taxonomy', 'title' => 'Browse by Taxonomy', 'fields' => array( wes_acf_text( 'field_bt_heading', 'Heading', 'heading' ), wes_acf_textarea( 'field_bt_lead', 'Lead', 'lead', 2 ), array( 'key' => 'field_bt_target_taxonomy', 'label' => 'Target Taxonomy', 'name' => 'target_taxonomy', 'type' => 'select', 'choices' => array( 'theme' => 'Themes', 'topic' => 'Topics' ), 'default_value' => 'theme', 'allow_null' => 0, 'multiple' => 0, 'ui' => 1 ), array( 'key' => 'field_bt_target_post_type', 'label' => 'Target Post Type', 'name' => 'target_post_type', 'type' => 'select', 'choices' => array( 'resource' => 'Resources', 'explainer' => 'Explainers', 'howto' => 'How-to Guides', 'checklist' => 'Checklists' ), 'default_value' => 'resource', 'allow_null' => 0, 'multiple' => 0, 'ui' => 1 ), array( 'key' => 'field_order_tax', 'label' => 'Taxonomy Order', 'name' => 'taxonomy_order', 'type' => 'select', 'choices' => array( 'name' => 'Name', 'count' => 'Count', 'id' => 'ID', 'menu_order' => 'Manual Order' ), 'default_value' => 'name' ), array( 'key' => 'field_order_posts', 'label' => 'Resource Order', 'name' => 'post_order', 'type' => 'select', 'choices' => array( 'title' => 'Title', 'date' => 'Publish Date', 'menu_order' => 'Menu Order', 'rand' => 'Random' ), 'default_value' => 'title' ), array( 'key' => 'field_post_order_dir', 'label' => 'Order Direction', 'name' => 'post_order_dir', 'type' => 'button_group', 'choices' => array( 'ASC' => 'ASC', 'DESC' => 'DESC' ), 'default_value' => 'ASC' ) ), 'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/wes-browse-taxonomy' ) ) ) ) );

} );
