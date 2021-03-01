<?php
/**
 * Configuration for Themify Customizer
 * Created by themify
 * @since 1.0.0
 */

/**
 * Add new section
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */

/**
 * Add customizer controls
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function themify_theme_customizer_definition( $args ) {
	global $themify_customizer;
	$args = array(

		// Accordion Start ---------------------------
		'start_body_acc' => $themify_customizer->accordion_start( __( 'Body', 'themify' ) ),
		
		// Styling key name. Includes any string depicting the styling, for example 'body' and a suffix
		// specifying the type of control, for example '_background'
		'body_background' => array(
			'setting' => array( // Optional. Default setting/value to save.
				'transport' => 'postMessage', // Live update (postMessage) or reload (refresh) the page.
			),
			'control' => array(
				'type'    => 'Themify_Background_Control', // Type of the control to render.
				'label'   => __( 'Body Background', 'themify' ), // Visible name of the control.
				'show_label' => true, // Whether to show the control name or not. Defaults to true.
				'section' => 'themify_options', // Optional section ID where the control will be added.
			),
			'selector' => 'body', // CSS Selector to apply styling.
			'prop' => 'background', // Styling to apply, can be a CSS property or a custom set of properties.
		),

		'body_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Body Font', 'themify' ),
			),
			'selector' => 'body',
			'prop' => 'font',
		),

		'body_text_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
			),
			'selector' => 'body',
			'prop' => 'color',
		),

		'body_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
				'label'   => __( 'Body Link', 'themify' ),
			),
			'selector' => 'a',
			'prop' => 'font',
		),

		'body_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
			),
			'selector' => 'a',
			'prop' => 'color',
		),

		'body_link_hover_font' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
				'label'   => __( 'Body Link Hover', 'themify' ),
			),
			'selector' => 'a:hover',
			'prop' => 'font',
		),

		'body_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
			),
			'selector' => 'a:hover',
			'prop' => 'color',
		),
		
		'end_body_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_accent_acc' => $themify_customizer->accordion_start( __( 'Accent Styling', 'themify' ) ),
		
		// Accent Styles
		'scheme_font_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Accent Styling', 'themify' ),
				'color_label' => __( 'Accent Font Color', 'themify' ),
			),
			'selector' => '.transparent-header #headerwrap,#headerwrap,#footerwrap,.transparent-header #headerwrap.fixed-header,#headerwrap.fixed-header',
			'prop' => 'color',
		),

		'scheme_general_link_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label' => __( 'General Link Color', 'themify' ),
			),
			'selector' => 'a, .post-nav .arrow',
			'prop' => 'color',
		),

		'scheme_general_font_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label' => __( 'General Font Color', 'themify' ),
			),
			'selector' => 'body,h1,h2,h3,h4,h5,h6',
			'prop' => 'color',
		),

		'scheme_link_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label' => __( 'Accent Link Color', 'themify' ),
			),
			'selector' => '.transparent-header #headerwrap a,#headerwrap a,#footerwrap a,input[type=reset], input[type=submit], button, .woocommerce #content input.button, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #content input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,.module-buttons .module-buttons-item .ui.builder_button,.transparent-header #headerwrap.fixed-header a, #headerwrap.fixed-header a',
			'prop' => 'color',
		),

		'scheme_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label' => __( 'Accent Background Color', 'themify' ),
			),
			'selector' => '.transparent-header #headerwrap.fixed-header,#headerwrap.fixed-header,#headerwrap,#footerwrap,input[type=reset], input[type=submit], button, .woocommerce #content input.button, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #content input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,.module-buttons .module-buttons-item .builder_button',
			'prop' => 'background',
		),

		'end_accent_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_headings_acc' => $themify_customizer->accordion_start( __( 'Headings', 'themify' ) ),

		'heading1_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Heading 1 Font', 'themify' ),
			),
			'selector' => 'h1',
			'prop' => 'font',
		),
		'heading1_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Heading 1 Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'h1',
			'prop' => 'color',
		),

		'heading2_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Heading 2 Font', 'themify' ),
			),
			'selector' => 'h2',
			'prop' => 'font',
		),
		'heading2_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Heading 2 Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'h2',
			'prop' => 'color',
		),

		'heading3_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Heading 3 Font', 'themify' ),
			),
			'selector' => 'h3',
			'prop' => 'font',
		),
		'heading3_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Heading 3 Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'h3',
			'prop' => 'color',
		),

		'heading4_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Heading 4 Font', 'themify' ),
			),
			'selector' => 'h4',
			'prop' => 'font',
		),
		'heading4_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Heading 4 Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'h4',
			'prop' => 'color',
		),

		'heading5_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Heading 5 Font', 'themify' ),
			),
			'selector' => 'h5',
			'prop' => 'font',
		),
		'heading5_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Heading 5 Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'h5',
			'prop' => 'color',
		),

		'heading6_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Heading 6 Font', 'themify' ),
			),
			'selector' => 'h6',
			'prop' => 'font',
		),
		'heading6_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Heading 6 Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'h6',
			'prop' => 'color',
		),

		'end_headings_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_header_acc' => $themify_customizer->accordion_start( __( 'Header', 'themify' ) ),

		'headerwrap_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Header Wrap', 'themify' ),
			),
			'selector' => '.header-classic #headerwrap, #headerwrap',
			'prop' => 'background',
		),

		'headerwrap_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Header Wrap Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#headerwrap',
			'prop' => 'border',
		),

		'headerwrap_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Header Wrap Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#headerwrap',
			'prop' => 'padding',
		),

		'headerwrap_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Header Wrap Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#headerwrap',
			'prop' => 'margin',
		),

		'header_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Header Font', 'themify' ),
			),
			'selector' => '#header',
			'prop' => 'font',
		),

		'header_text_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Header Font Color', 'themify' ),
				'show_label' => false,
			),
		'selector' => '#header',
			'prop' => 'color',
		),

		'header_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
				'label'   => __( 'Header Link', 'themify' ),
			),
			'selector' => '#headerwrap a',
			'prop' => 'font',
		),

		'header_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Header Link Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#headerwrap a',
			'prop' => 'color',
		),

		'header_link_hover_font' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
				'label'   => __( 'Header Link Hover', 'themify' ),
			),
			'selector' => '#headerwrap a:hover',
			'prop' => 'font',
		),

		'header_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
			),
			'selector' => '#headerwrap a:hover',
			'prop' => 'color',
		),

		'header_top_widgets_background' => array(
			'control' => array(
				'label'   => __( 'Header Top Widgets', 'themify' ),
				'type'    => 'Themify_Background_Control',
				'active_callback' => 'themify_is_header_top_widgets',
			),
			'selector' => '.header-top-widgets .header-widget-full',
			'prop' => 'background',
		),

		'header_top_widgets_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'active_callback' => 'themify_is_header_top_widgets',
			),
			'selector' => '.header-top-widgets .header-widget-full',
			'prop' => 'font',
		),

		'header_top_widgets_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Link Color', 'themify' ),
				'active_callback' => 'themify_is_header_top_widgets',
			),
			'selector' => '.header-top-widgets .header-widget-full a',
			'prop' => 'color',
		),

		'header_top_widgets_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'active_callback' => 'themify_is_header_top_widgets',
			),
			'selector' => '.header-top-widgets .header-widget-full',
			'prop' => 'color',
		),

		'header_navigation_bar_background' => array(
			'control' => array(
				'label'   => __( 'Navigation Bar', 'themify' ),
				'type'    => 'Themify_Color_Control',
				'color_label' => __( 'Background Color', 'themify' ),
				'active_callback' => 'themify_is_navigation_bar',
			),
			'selector' => 'body:not(.mobile_menu_active).header-classic .navbar-wrapper, body:not(.mobile_menu_active).header-magazine .navbar-wrapper',
			'prop' => 'background',
		),

		'end_header_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------
		
		// Accordion Start ---------------------------
		'start_sticky_header_acc' => $themify_customizer->accordion_start( __( 'Sticky Header', 'themify' ) ),

		'sticky_headerwrap_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Sticky Header Wrap', 'themify' ),
			),
			'selector' => '#pagewrap #headerwrap.fixed-header, .transparent-header #pagewrap #headerwrap.fixed-header',
			'prop' => 'background',
		),

		'sticky_header_imageselect' => array(
			'control' => array(
				'type'    => 'Themify_Image_Control',
				'label'   => __( 'Sticky Header Logo', 'themify' ),
				'image_options' => array(
					'show_size_fields' => true,
					'image_label' => ''
				)
			),
			'selector' => '#headerwrap.fixed-header #site-logo',
			'prop' => 'logo',
		),

		'sticky_header_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Sticky Header Font', 'themify' ),
			),
			'selector' => '#headerwrap.fixed-header #header, #headerwrap.fixed-header #site-description',
			'prop' => 'font',
		),

		'sticky_header_font_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Sticky Header Font Color', 'themify' ),
				'show_label' => false,
			),
		'selector' => '#headerwrap.fixed-header #header',
			'prop' => 'color',
		),

		'sticky_header_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
				'label'   => __( 'Sticky Header Link', 'themify' ),
			),
			'selector' => 'body:not(.mobile_menu_active) #headerwrap.fixed-header #header a',
			'prop' => 'font',
		),

		'sticky_header_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Sticky Header Link Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'body:not(.mobile_menu_active) #headerwrap.fixed-header #header a',
			'prop' => 'color',
		),

		'sticky_header_link_hover_font' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
				'label'   => __( 'Sticky Header Link Hover', 'themify' ),
			),
			'selector' => 'body:not(.mobile_menu_active) #headerwrap.fixed-header #header a:hover',
			'prop' => 'font',
		),

		'sticky_header_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
			),
			'selector' => 'body:not(.mobile_menu_active) #headerwrap.fixed-header #header a:hover',
			'prop' => 'color',
		),

		'end_sticky_header_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_titletagline_acc' => $themify_customizer->accordion_start( __( 'Site Logo &amp; Tagline', 'themify' ) ),

		// This element is not CSS, but markup written by site_logo()
		'site-logo_image' => array(
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'type'    => 'Themify_Logo_Control',
				'label'   => __( 'Site Logo', 'themify' ),
			),
			'selector' => 'body[class*="themify"] #site-logo a',
			'prop' => 'logo',
		),

		'site-logo_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Site Logo Margin', 'themify' ),
			),
			'selector' => '#site-logo',
			'prop' => 'margin',
		),

		// This element is not CSS, but markup written by site_description()
		'site-tagline' => array(
			'control' => array(
				'type'    => 'Themify_Tagline_Control',
				'label'   => __( 'Site Tagline', 'themify' ),
			),
			'selector' => '#site-description',
			'prop' => 'tagline',
		),

		'site-tagline_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Site Tagline Margin', 'themify' ),
			),
			'selector' => '#site-description',
			'prop' => 'margin',
		),

		'end_titletagline_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_nav_acc' => $themify_customizer->accordion_start( __( 'Main Navigation', 'themify' ) ),


		'main_nav_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Menu Link', 'themify' ),
			),
			'selector' => '#main-nav a',
			'prop' => 'font',
		),

		'main_nav_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Link Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav a, #main-nav > li > a, .transparent-header #main-nav a',
			'prop' => 'color',
		),

		'main_nav_link_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Menu Link Background', 'themify' ),
				'show_label' => false,
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#main-nav-wrap #main-nav a',
			'prop' => 'background',
		),

		'main_nav_link_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Menu Link Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav-wrap #main-nav a',
			'prop' => 'border',
		),

		'main_nav_link_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Menu Link Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav a',
			'prop' => 'padding',
		),

		'main_nav_link_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Menu Link Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav a',
			'prop' => 'margin',
		),

		'main_nav_link_hover_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Menu Link Hover', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#main-nav-wrap #main-nav a:hover',
			'prop' => 'background',
		),

		'main_nav_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Link Hover Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav-wrap #main-nav a:hover, #main-nav-wrap #main-nav > li > a:hover',
			'prop' => 'color',
		),

		'main_nav_link_active_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Menu Active Link', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#main-nav .current_page_item a, #main-nav .current-menu-item a',
			'prop' => 'background',
		),

		'main_nav_link_active_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Active Link Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav-wrap #main-nav .current_page_item a, #main-nav .current-menu-item a, #main-nav > .current_page_item > a, #main-nav > .current-menu-item > a',
			'prop' => 'color',
		),

		'main_nav_link_active_hover_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Menu Active Link Hover', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#main-nav-wrap #main-nav .current_page_item a:hover,  #main-nav .current-menu-item a:hover',
			'prop' => 'background',
		),

		'main_nav_link_active_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Active Link Hover Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav-wrap #main-nav .current_page_item a:hover,  #main-nav .current-menu-item a:hover',
			'prop' => 'color',
		),

		'end_nav_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------
		
		// Mobile Menu Options
		'start_mobile_menu_acc' => $themify_customizer->accordion_start( __( 'Mobile Menu', 'themify' ) ),
		'mobile_menu_panel_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Mobile Menu Panel', 'themify' ),
				'show_label' => true,
			),
			'selector' => '.mobile-menu-visible.header-slide-out .sidemenu-on, .mobile_menu_active.header-leftpane #headerwrap, .mobile_menu_active.header-rightpane #headerwrap, .mobile_menu_active #headerwrap .sidemenu-on',
			'prop' => 'background',
			'global' => true,
		),
		'mobile_menu_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Color', 'themify' )
			),
			'selector' => '.mobile-menu-visible.header-slide-out .sidemenu-on, .mobile_menu_active.header-leftpane #headerwrap, .mobile_menu_active.header-rightpane #headerwrap, .mobile_menu_active #headerwrap .sidemenu-on',
			'prop' => 'color',
			'global' => true,
		),
		'mobile_menu_panel_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Panel Link Color', 'themify' )
			),
			'selector' => '.mobile-menu-visible.header-slide-out .sidemenu-on a, .mobile_menu_active.header-leftpane #headerwrap a, .mobile_menu_active.header-rightpane #headerwrap a, .mobile_menu_active #headerwrap .sidemenu.sidemenu-on a',
			'prop' => 'color',
			'global' => true,
		),
		'mobile_menu_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Mobile Menu Link', 'themify' )
			),
			'selector' => '.mobile_menu_active #headerwrap .sidemenu-on nav li a',
			'prop' => 'font',
			'global' => true,
		),
		'mobile_menu_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Link Color', 'themify' )
			),
			'selector' => '.mobile_menu_active #headerwrap .sidemenu-on a, .mobile_menu_active #headerwrap #main-nav a, .mobile_menu_active #headerwrap #main-nav ul a',
			'prop' => 'color',
			'global' => true,
		),
		'mobile_menu_hover_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Mobile Menu Link Hover', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.mobile_menu_active #headerwrap .sidemenu-on a:hover, .mobile_menu_active #headerwrap #main-nav a:hover, .mobile_menu_active #headerwrap #main-nav ul a:hover',
			'prop' => 'background',
			'global' => true,
		),
		'mobile_menu_hover_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Mobile Menu Link Hover Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '.mobile_menu_active #headerwrap .sidemenu-on a:hover, .mobile_menu_active #headerwrap #main-nav a:hover, .mobile_menu_active #headerwrap #main-nav ul a:hover',
			'prop' => 'color',
			'global' => true,
		),
		'mobile_menu_icon_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Mobile Menu Icon', 'themify' ),
				'color_label'   => __( 'Background Color', 'themify' ),
				'show_label' => true,
			),
			'selector' => '.mobile_menu_active.header-leftpane #pagewrap #headerwrap .header-icons #menu-icon, .mobile_menu_active.header-rightpane #pagewrap #headerwrap .header-icons #menu-icon, body:not(.header-leftpane):not(.header-rightpane).mobile_menu_active #menu-icon, .mobile_menu_active.header-horizontal #headerwrap #menu-icon',
			'prop' => 'background',
			'global' => true,
		),
		'mobile_menu_icon_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.header-leftpane #menu-icon, .header-rightpane #menu-icon, .header-slide-out .menu-icon,  body:not(.header-leftpane):not(.header-rightpane).mobile_menu_active #menu-icon',
			'prop' => 'padding',
		),
		'mobile_menu_icon_height' => array(
			'control' => array(
				'type'  => 'Themify_Height_Control',
			),
			'selector' => '#headerwrap .menu-icon-inner',
			'prop' => 'height',
		),
		'mobile_menu_icon_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
			),
			'selector' => '#headerwrap .menu-icon-inner',
			'prop' => 'width',
		),
		'mobile_menu_icon_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Color', 'themify' )
			),
			'selector' => '.mobile_menu_active.header-leftpane #pagewrap #headerwrap .header-icons #menu-icon, .mobile_menu_active.header-rightpane #pagewrap #headerwrap .header-icons #menu-icon, #headerwrap #menu-icon, .mobile_menu_active.header-horizontal #headerwrap #menu-icon',
			'prop' => 'color',
			'global' => true,
		),
		
		'end_mobile_menu_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_shop_acc' => $themify_customizer->accordion_start( __( 'Shop', 'themify' ), 'themify_options', 'themify_is_woocommerce_active' ),
		
		'shop_product_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Product Title', 'themify' ),
			),
			'selector' => '.woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product h3, .woocommerce div.product .product_title, .wc-products .product h3',
			'prop' => 'font',
		),
		'shop_product_title_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Product Title Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '.woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product h3, .woocommerce div.product .product_title, .wc-products .product h3 a',
			'prop' => 'color',
		),
		'shop_product_price_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Price', 'themify' ),
			),
			'selector' => '.woocommerce ul.products li.product .price',
			'prop' => 'font',
		),
		'shop_product_price_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Product Price Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '.woocommerce ul.products li.product .price',
			'prop' => 'color',
		),
		'shop_add_to_cart_button_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Add to Cart Button', 'themify' ),
			),
			'selector' => '.woocommerce .wc-products .product .add_to_cart_button, .woocommerce ul.products li.product .add_to_cart_button, .woocommerce #content input.button, .woocommerce #respond input#submit, .woocommerce #content input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce-page #content input.button, .woocommerce-page #respond input#submit, .woocommerce-page #content input.button.alt, .woocommerce-page #respond input#submit.alt, .woocommerce-page a.button, .woocommerce-page button.button, .woocommerce-page input.button, .woocommerce-page a.button.alt, .woocommerce-page button.button.alt, .woocommerce-page input.button.alt',
			'prop' => 'font',
		),
		'shop_add_to_cart_button_background_hover' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Background Color Hover', 'themify' ),
				'label'   => __( 'Add to Cart Button Background Color Hover', 'themify' ),
				'show_label' => false,
			),
			'selector' => '.woocommerce .wc-products .product .add_to_cart_button:hover, .woocommerce ul.products li.product .add_to_cart_button:hover, .woocommerce #content input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce #content input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page #content input.button:hover, .woocommerce-page #respond input#submit:hover, .woocommerce-page #content input.button.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce-page a.button:hover, .woocommerce-page button.button:hover, .woocommerce-page input.button:hover, .woocommerce-page a.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce ul.products li.product .add_to_cart_button:hover, .woocommerce ul.products li.product .button[data-product_id]:hover',
			'prop' => 'background',
		),
		'shop_add_to_cart_button_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Background Color', 'themify' ),
				'label'   => __( 'Add to Cart Button Background Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '.woocommerce .wc-products .product .add_to_cart_button, .woocommerce ul.products li.product .add_to_cart_button, .woocommerce #content input.button, .woocommerce #respond input#submit, .woocommerce #content input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce-page #content input.button, .woocommerce-page #respond input#submit, .woocommerce-page #content input.button.alt, .woocommerce-page #respond input#submit.alt, .woocommerce-page a.button, .woocommerce-page button.button, .woocommerce-page input.button, .woocommerce-page a.button.alt, .woocommerce-page button.button.alt, .woocommerce-page input.button.alt, .woocommerce ul.products li.product .add_to_cart_button, .woocommerce ul.products li.product .button[data-product_id]',
			'prop' => 'background',
		),
		'shop_add_to_cart_button_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Add to Cart Button Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '.woocommerce .wc-products .product .add_to_cart_button, .woocommerce ul.products li.product .add_to_cart_button, .woocommerce #content input.button, .woocommerce #respond input#submit, .woocommerce #content input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce-page #content input.button, .woocommerce-page #respond input#submit, .woocommerce-page #content input.button.alt, .woocommerce-page #respond input#submit.alt, .woocommerce-page a.button, .woocommerce-page button.button, .woocommerce-page input.button, .woocommerce-page a.button.alt, .woocommerce-page button.button.alt, .woocommerce-page input.button.alt',
			'prop' => 'border',
		),
		'shop_sale_tag_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Sale Tag', 'themify' ),
			),
			'selector' => '.woocommerce span.onsale, .woocommerce-page span.onsale, .woocommerce ul.products li.product .onsale, .woocommerce-page ul.products li.product .onsale',
			'prop' => 'font',
		),
		'shop_sale_tag_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Sale Tag Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '.woocommerce span.onsale, .woocommerce-page span.onsale, .woocommerce ul.products li.product .onsale, .woocommerce-page ul.products li.product .onsale',
			'prop' => 'color',
		),
		'shop_sale_tag_background_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Background Color', 'themify' ),
				'label'   => __( 'Sale Tag Background Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '.woocommerce span.onsale, .woocommerce-page span.onsale, .woocommerce ul.products li.product .onsale, .woocommerce-page ul.products li.product .onsale',
			'prop' => 'background',
		),
		
		'end_shop_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_post_acc' => $themify_customizer->accordion_start( __( 'Post', 'themify' ) ),

		// Post Title .post-title

		'post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Post Title', 'themify' ),
			),
			'selector' => '.post-title',
			'prop' => 'font',
		),

		'post_title_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
			),
			'selector' => '.post-title, .post-title a',
			'prop' => 'color',
		),


		'post_title_hover_font' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
				'label'   => __( 'Post Title Hover', 'themify' ),
			),
			'selector' => '.post-title a:hover',
			'prop' => 'font',
		),

		'post_title_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
			),
			'selector' => '.post-title a:hover',
			'prop' => 'color',
		),

		'single_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Single Post Title', 'themify' ),
			),
			'selector' => '.single-post .entry-title',
			'prop' => 'font',
		),

		'grid4_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Grid4 Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.grid4 .post-title',
			'prop' => 'font',
		),

		'grid3_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Grid3 Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.grid3 .post-title',
			'prop' => 'font',
		),

		'grid2_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Grid2 Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.grid2 .post-title',
			'prop' => 'font',
		),

		'grid2_thumb_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Grid2 Thumb Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.grid2-thumb .post-title',
			'prop' => 'font',
		),

		'list_thumb_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'List Thumb Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.list-thumb-image .post-title',
			'prop' => 'font',
		),

		'single_post_featured_area' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Single Post Featured Area', 'themify' ),
			),
			'selector' => '.single.single-post .featured-area',
			'prop' => 'background',
		),

		'end_post_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_page_title_acc' => $themify_customizer->accordion_start( __( 'Page Title', 'themify' ) ),

		// Page Title .page-title

		'page_title_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Page Title', 'themify' ),
			),
			'selector' => '.page-title',
			'prop' => 'color',
		),

		'page_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '.page-title',
			'prop' => 'font',
		),

		'end_page_title_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_module_title_acc' => $themify_customizer->accordion_start( __( 'Module Title', 'themify' ) ),

		// Module Title .module-title

		'module_title_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Module Title', 'themify' ),
			),
			'selector' => '.module-title',
			'prop' => 'color',
		),

		'module_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '.module-title',
			'prop' => 'font',
		),

		'end_module_title_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_sidebar_acc' => $themify_customizer->accordion_start( __( 'Sidebar', 'themify' ) ),

		// Sidebar Font #sidebar

		'sidebar_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Sidebar Font', 'themify' ),
			),
			'selector' => '#sidebar, #sidebar-alt',
			'prop' => 'color',
		),

		'sidebar_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '#sidebar, #sidebar-alt',
			'prop' => 'font',
		),

		// Sidebar Link #sidebar a

		'sidebar_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Sidebar Link', 'themify' ),
			),
			'selector' => '#sidebar a, #sidebar-alt a',
			'prop' => 'color',
		),

		'sidebar_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
			),
			'selector' => '#sidebar a, #sidebar-alt a',
			'prop' => 'font',
		),

		// Sidebar Link Hover #sidebar a:hover

		'sidebar_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Sidebar Link Hover', 'themify' ),
			),
			'selector' => '#sidebar a:hover, #sidebar-alt a:hover',
			'prop' => 'color',
		),

		'sidebar_link_hover_font' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
			),
			'selector' => '#sidebar a:hover, #sidebar-alt a:hover',
			'prop' => 'font',
		),

		// Sidebar Widget Title #sidebar .widgettitle

		'sidebar_widget_title_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Sidebar Widget Title', 'themify' ),
			),
			'selector' => '#sidebar .widgettitle, #sidebar-alt .widgettitle',
			'prop' => 'color',
		),

		'sidebar_widget_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '#sidebar .widgettitle, #sidebar-alt .widgettitle',
			'prop' => 'font',
		),

		'end_sidebar_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_footer_acc' => $themify_customizer->accordion_start( __( 'Footer', 'themify' ) ),

		// Footer Wrap #footerwrap

		'footerwrap_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Footer Wrap', 'themify' ),
			),
			'selector' => '#footerwrap',
			'prop' => 'background',
		),

		'footerwrap_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '#footerwrap',
			'prop' => 'border',
		),

		'footerwrap_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '#footerwrap',
			'prop' => 'padding',
		),

		'footerwrap_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '#footerwrap',
			'prop' => 'margin',
		),

		'footer-logo_image' => array(
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'type'    => 'Themify_Logo_Control',
				'label'   => __( 'Footer Logo', 'themify' ),
			),
			'selector' => '#footer-logo a',
			'prop' => 'logo',
		),

		// Footer Font #footer

		'footer_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Footer Font', 'themify' ),
			),
			'selector' => '#footer',
			'prop' => 'color',
		),

		'footer_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '#footer, #footer-logo, .footer-nav a, .footer-text',
			'prop' => 'font',
		),

		// Footer Link #footer a

		'footer_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Footer Link', 'themify' ),
			),
			'selector' => '#footer a',
			'prop' => 'color',
		),

		'footer_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
			),
			'selector' => '#footer a',
			'prop' => 'font',
		),

		// Footer Link #footer a:hover

		'footer_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Footer Link Hover', 'themify' ),
			),
			'selector' => '#footer a:hover',
			'prop' => 'color',
		),

		'footer_link_hover_font' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
			),
			'selector' => '#footer a:hover',
			'prop' => 'font',
		),
		

		// Footer Widget Font #footer .footer-widgets

		'footer_widget_text_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Footer Widget Font', 'themify' ),
			),
			'selector' => '#footer .footer-widgets',
			'prop' => 'color',
		),

		'footer_widget_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '#footer .footer-widgets',
			'prop' => 'font',
		),

		// Footer Widget Title #footer .footer-widgets .widgettitle

		'footer_widget_title_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Footer Widget Title', 'themify' ),
			),
			'selector' => '#footer .footer-widgets .widgettitle',
			'prop' => 'color',
		),

		'footer_widget_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '#footer .footer-widgets .widgettitle',
			'prop' => 'font',
		),

		// Back to Top Button .back-top a
		'back_top_arrow_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Back to Top Button', 'themify' ),
			),
			'selector' => '#footerwrap .back-top a:before, #pagewrap .back-top.back-top-float',
			'prop' => 'color',
		),
		'back_top_background_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'color_label'   => __( 'Background Color', 'themify' ),
			),
			'selector' => '#pagewrap .back-top.back-top-float, .full-section-scrolling #footerwrap .back-top .arrow-up a, .footer-horizontal-left #footerwrap .back-top .arrow-up a, .footer-horizontal-right #footerwrap .back-top .arrow-up a, .footer-left-col #footerwrap .back-top .arrow-up a, .footer-right-col #footerwrap .back-top .arrow-up a, #footer .back-top .arrow-up a',
			'prop' => 'background',
		),

		'end_footer_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------*/

		// Accordion Start ---------------------------
		'start_customcss_acc' => $themify_customizer->accordion_start( __( 'Custom CSS', 'themify' ) ),

		// This element is not CSS, but markup written by themify_custom_css()
		'customcss' => array(
			'control' => array(
				'type'    => 'Themify_CustomCSS_Control',
				'label'   => __( 'Custom CSS', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'customcss',
			'prop' => 'customcss',
		),

		'end_customcss_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

	);
	return $args;
}
add_filter( 'themify_customizer_settings', 'themify_theme_customizer_definition' );