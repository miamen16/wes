<?php
/**
 * ACF: options page + code-defined field groups (Site Settings + Home Page).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---- Options page for global (header/footer) content ---- */
add_action( 'acf/init', function () {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( array(
			'page_title' => 'Site Settings',
			'menu_title' => 'Site Settings',
			'menu_slug'  => 'wes-site-settings',
			'capability' => 'edit_theme_options',
			'position'   => 2,
			'icon_url'   => 'dashicons-admin-customizer',
			'redirect'   => false,
			// Store/load options per active language (Polylang). Switch language in the
			// admin bar to edit each language's globals.
			'post_id'    => wes_options_pid(),
		) );
	}
} );

/* Small helpers to keep field arrays terse. */
function wes_acf_text( $key, $label, $name, $extra = array() ) {
	return array_merge( array( 'key' => $key, 'label' => $label, 'name' => $name, 'type' => 'text' ), $extra );
}
function wes_acf_textarea( $key, $label, $name, $rows = 3 ) {
	return array( 'key' => $key, 'label' => $label, 'name' => $name, 'type' => 'textarea', 'rows' => $rows, 'new_lines' => '' );
}
function wes_acf_image( $key, $label, $name ) {
	return array( 'key' => $key, 'label' => $label, 'name' => $name, 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium' );
}
function wes_acf_tab( $key, $label ) {
	return array( 'key' => $key, 'label' => $label, 'type' => 'tab', 'placement' => 'top' );
}

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/* =====================================================================
	 * SITE SETTINGS (options page)
	 * ===================================================================== */
	acf_add_local_field_group( array(
		'key'      => 'group_wes_site',
		'title'    => 'Site Settings',
		'fields'   => array(
			wes_acf_tab( 'tab_nav', 'Navigation' ),
			array(
				'key' => 'field_primary_nav', 'label' => 'Primary menu', 'name' => 'primary_nav', 'type' => 'repeater',
				'button_label' => 'Add menu item', 'layout' => 'table',
				'sub_fields' => array(
					wes_acf_text( 'field_nav_label', 'Label', 'label' ),
					wes_acf_text( 'field_nav_url', 'URL', 'url' ),
				),
			),
			wes_acf_text( 'field_lang_label', 'Language label', 'lang_label' ),

			wes_acf_tab( 'tab_stay', 'Stay Connected' ),
			wes_acf_text( 'field_stay_title', 'Title', 'stay_title' ),
			wes_acf_text( 'field_stay_newsletter_shortcode', 'Newsletter Shortcode', 'newsletter_shortcode' ),
			wes_acf_textarea( 'field_stay_desc', 'Description', 'stay_desc' ),
			array(
				'key' => 'field_stay_social', 'label' => 'Social links', 'name' => 'stay_social', 'type' => 'repeater',
				'button_label' => 'Add social link', 'layout' => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_social_net', 'label' => 'Network', 'name' => 'network', 'type' => 'select',
						'choices' => array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'linkedin' => 'LinkedIn', 'x' => 'X / Twitter', 'youtube' => 'YouTube' ) ),
					wes_acf_text( 'field_social_url', 'URL', 'url' ),
				),
			),

			wes_acf_tab( 'tab_footer', 'Footer' ),
			array(
				'key' => 'field_footer_logos', 'label' => 'Funder / partner logos', 'name' => 'footer_logos', 'type' => 'repeater',
				'button_label' => 'Add logo', 'layout' => 'block',
				'sub_fields' => array(
					wes_acf_image( 'field_logo_img', 'Logo', 'image' ),
					wes_acf_text( 'field_logo_alt', 'Alt text', 'alt' ),
					array( 'key' => 'field_logo_size', 'label' => 'Size', 'name' => 'size', 'type' => 'select', 'choices' => array( 'small' => 'Standard', 'wide' => 'Wide' ), 'default_value' => 'small' ),
				),
			),
			wes_acf_textarea( 'field_footer_funding', 'Funding statement', 'footer_funding', 4 ),
			array(
				'key' => 'field_footer_links', 'label' => 'Legal links', 'name' => 'footer_links', 'type' => 'repeater',
				'button_label' => 'Add link', 'layout' => 'table',
				'sub_fields' => array(
					wes_acf_text( 'field_flink_label', 'Label', 'label' ),
					wes_acf_text( 'field_flink_url', 'URL', 'url' ),
				),
			),
			wes_acf_text( 'field_footer_copyright', 'Copyright', 'footer_copyright' ),

			wes_acf_tab( 'tab_about', 'About modal' ),
			wes_acf_text( 'field_about_heading', 'Heading', 'about_heading' ),
			array( 'key' => 'field_about_content', 'label' => 'Content', 'name' => 'about_content', 'type' => 'wysiwyg', 'media_upload' => 0, 'tabs' => 'visual' ),
		),
		'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'wes-site-settings' ) ) ),
	) );

	/* =====================================================================
	 * HOME PAGE
	 * ===================================================================== */
	$und_card = array(
		wes_acf_image( 'field_und_img', 'Illustration', 'image' ),
		wes_acf_text( 'field_und_strong', 'Title (bold line)', 'title_strong' ),
		wes_acf_text( 'field_und_rest', 'Title (rest)', 'title_rest' ),
		wes_acf_text( 'field_und_link', 'Link', 'link' ),
	);
	$wycd_card = array(
		wes_acf_image( 'field_wycd_img', 'Illustration', 'image' ),
		array( 'key' => 'field_wycd_cat', 'label' => 'Category', 'name' => 'category', 'type' => 'select', 'choices' => array( 'howto' => 'How-to guide', 'checklist' => '5-step checklist' ) ),
		wes_acf_text( 'field_wycd_label', 'Badge label', 'label' ),
		wes_acf_text( 'field_wycd_title', 'Title', 'title' ),
		wes_acf_text( 'field_wycd_link', 'Link', 'link' ),
		array( 'key' => 'field_wycd_med', 'label' => 'Show "5" medallion', 'name' => 'medallion', 'type' => 'true_false', 'ui' => 1 ),
	);
	$audience = array(
		array( 'key' => 'field_aud_blob', 'label' => 'Blob colour', 'name' => 'blob', 'type' => 'select', 'choices' => array( 'teal' => 'Teal', 'green' => 'Green', 'gold' => 'Gold' ) ),
		wes_acf_image( 'field_aud_icon', 'Icon', 'icon' ),
		wes_acf_textarea( 'field_aud_label', 'Label', 'label', 2 ),
		wes_acf_text( 'field_aud_link', 'Link', 'link' ),
	);
	$gi_filter = array(
		wes_acf_text( 'field_filter_label', 'Label', 'label' ),
		array( 'key' => 'field_filter_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => array( 'map' => 'Map', 'folder' => 'Folder', 'people' => 'People' ) ),
	);
	$gi_org = array(
		wes_acf_text( 'field_org_name', 'Name', 'name' ),
		wes_acf_text( 'field_org_link', 'Link', 'link' ),
	);

	acf_add_local_field_group( array(
		'key'      => 'group_wes_home',
		'title'    => 'Home Page',
		'fields'   => array(
			wes_acf_tab( 'tab_hero', 'Hero' ),
			wes_acf_image( 'field_hero_logo', 'Logo', 'hero_logo' ),
			wes_acf_textarea( 'field_hero_title', 'Title', 'hero_title', 2 ),
			wes_acf_textarea( 'field_hero_subtitle', 'Subtitle', 'hero_subtitle', 3 ),

			wes_acf_tab( 'tab_med', 'Mediterranean' ),
			wes_acf_textarea( 'field_med_banner', 'Banner text', 'med_banner', 2 ),
			array(
                'key'          => 'field_med_media_type',
                'label'        => 'Media Type',
                'name'         => 'med_media_type',
                'type'         => 'button_group',
                'choices'      => array(
                    'image' => 'Image',
                    'video' => 'Video',
                ),
                'default_value' => 'image',
            ),
            wes_acf_image( 'field_med_map', 'Image', 'med_map' ),
            array(
                'key'           => 'field_med_video',
                'label'         => 'Video file',
                'name'          => 'med_video',
                'type'          => 'file',
                'return_format' => 'url',
                'mime_types'    => 'mp4,webm,ogg',
            ),

			wes_acf_tab( 'tab_und', 'Understanding' ),
			wes_acf_text( 'field_und_heading', 'Heading', 'und_heading' ),
			wes_acf_textarea( 'field_und_lead', 'Lead', 'und_lead' ),
			wes_acf_text( 'field_und_tab1', 'Tab 1 label', 'und_tab1' ),
			wes_acf_text( 'field_und_tab2', 'Tab 2 label', 'und_tab2' ),
			array( 'key' => 'field_und_cards', 'label' => 'Explainer cards', 'name' => 'und_cards', 'type' => 'repeater', 'button_label' => 'Add card', 'layout' => 'block', 'sub_fields' => $und_card ),

			wes_acf_tab( 'tab_wycd', 'What You Can Do' ),
			wes_acf_text( 'field_wycd_heading', 'Heading', 'wycd_heading' ),
			wes_acf_textarea( 'field_wycd_lead', 'Lead', 'wycd_lead' ),
			wes_acf_text( 'field_wycd_tab1', 'Tab 1 label', 'wycd_tab1' ),
			wes_acf_text( 'field_wycd_tab2', 'Tab 2 label', 'wycd_tab2' ),
			array( 'key' => 'field_wycd_cards', 'label' => 'Action cards', 'name' => 'wycd_cards', 'type' => 'repeater', 'button_label' => 'Add card', 'layout' => 'block', 'sub_fields' => $wycd_card ),

			wes_acf_tab( 'tab_quiz', 'Quiz' ),
			wes_acf_text( 'field_quiz_heading', 'Heading', 'quiz_heading' ),
			wes_acf_textarea( 'field_quiz_lead', 'Lead', 'quiz_lead' ),
			wes_acf_text( 'field_quiz_note', 'Note', 'quiz_note' ),
			wes_acf_image( 'field_quiz_image', 'Illustration', 'quiz_image' ),
			wes_acf_text( 'field_quiz_link', 'Quiz link', 'quiz_link' ),

			wes_acf_tab( 'tab_teach', 'Teach & Facilitate' ),
			wes_acf_text( 'field_teach_heading', 'Heading', 'teach_heading' ),
			wes_acf_textarea( 'field_teach_lead', 'Lead', 'teach_lead' ),
			wes_acf_textarea( 'field_teach_body', 'Body', 'teach_body', 3 ),
			wes_acf_text( 'field_teach_readmore', 'Read More link', 'teach_readmore' ),
			wes_acf_text( 'field_teach_rm_label', 'Read More label', 'teach_readmore_label' ),
			array( 'key' => 'field_teach_aud', 'label' => 'Audiences', 'name' => 'teach_audiences', 'type' => 'repeater', 'button_label' => 'Add audience', 'layout' => 'block', 'max' => 3, 'sub_fields' => $audience ),

			wes_acf_tab( 'tab_comms', 'Communicating' ),
			wes_acf_text( 'field_comms_title', 'Title', 'comms_title' ),
			wes_acf_textarea( 'field_comms_lead', 'Lead', 'comms_lead' ),
			wes_acf_text( 'field_comms_btn', 'Button text', 'comms_btn' ),
			wes_acf_text( 'field_comms_link', 'Button link', 'comms_link' ),
			wes_acf_image( 'field_comms_image', 'Illustration', 'comms_image' ),

			wes_acf_tab( 'tab_gi', 'Get Involved' ),
			wes_acf_text( 'field_gi_heading', 'Heading', 'gi_heading' ),
			wes_acf_textarea( 'field_gi_subtitle', 'Subtitle', 'gi_subtitle' ),
			wes_acf_textarea( 'field_gi_body', 'Body', 'gi_body', 4 ),
			array( 'key' => 'field_gi_filters', 'label' => 'Filters', 'name' => 'gi_filters', 'type' => 'repeater', 'button_label' => 'Add filter', 'layout' => 'table', 'sub_fields' => $gi_filter ),
			array( 'key' => 'field_gi_orgs', 'label' => 'Organisations', 'name' => 'gi_orgs', 'type' => 'repeater', 'button_label' => 'Add organisation', 'layout' => 'table', 'sub_fields' => $gi_org ),
		),
		'location' => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
		'menu_order' => 0,
		'position'   => 'normal',
		'style'      => 'default',
	) );
} );
