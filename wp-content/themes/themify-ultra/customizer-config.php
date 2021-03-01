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

		'body_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'	  => __( 'Body Padding/Margin/Border', 'themify' ),
			),
			'selector' => 'body',
			'prop' => 'border',
		),

		'body_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
			),
			'selector' => 'body',
			'prop' => 'padding',
		),

		'body_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
			),
			'selector' => 'body',
			'prop' => 'margin',
		),

		'paragraph_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'	  => __( 'Paragraph Margin', 'themify' ),
			),
			'selector' => 'p',
			'prop' => 'margin',
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
			'selector' => '.transparent-header #headerwrap.fixed-header,#headerwrap.fixed-header,.transparent-header #headerwrap,#headerwrap,#footerwrap,input[type=reset], input[type=submit], button, .woocommerce #content input.button, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #content input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,.module-buttons .module-buttons-item .builder_button',
			'prop' => 'background',
		),

		'end_accent_acc' => $themify_customizer->accordion_end(),
		// Accordion End   ---------------------------

		// Accordion Start ---------------------------
		'start_layout_acc' => $themify_customizer->accordion_start( __( 'Layout Containers', 'themify' ) ),

		'pagewrap_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label' => __( 'Page Wrap', 'themify' ),
			),
			'selector' => '#pagewrap',
			'prop' => 'width',
		),

		'pagewrap_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
			),
			'selector' => '#pagewrap',
			'prop' => 'background',
		),

		'pagewrap_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '#pagewrap',
			'prop' => 'border',
		),

		'pagewrap_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
			),
			'selector' => '#pagewrap',
			'prop' => 'padding',
		),

		'pagewrap_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
			),
			'selector' => '#pagewrap',
			'prop' => 'margin',
		),

		'pagewidth_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label' => __( 'Page Width', 'themify' ),
			),
			'selector' => '.pagewidth',
			'prop' => 'width',
		),

		'middle_container_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Middle Container', 'themify' ),
			),
			'selector' => '#body',
			'prop' => 'background',
		),

		'middle_container_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Middle Container Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#body',
			'prop' => 'border',
		),

		'middle_container_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Middle Container Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#body',
			'prop' => 'padding',
		),

		'middle_container_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Middle Container Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#body',
			'prop' => 'margin',
		),

		'content_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label' => __( 'Content Container', 'themify' ),
			),
			'selector' => '#content',
			'prop' => 'width',
		),

		'content_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Content Background', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#content',
			'prop' => 'background',
		),

		'content_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Content Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#content',
			'prop' => 'border',
		),

		'content_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Content Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#content',
			'prop' => 'padding',
		),

		'content_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Content Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#content',
			'prop' => 'margin',
		),

		'sidebar_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label' => __( 'Sidebar Container', 'themify' ),
			),
			'selector' => '#sidebar',
			'prop' => 'width',
		),

		'sidebar_background' => array(
			'control' => array(
				'type'  => 'Themify_Background_Control',
				'label' => __( 'Sidebar Background', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#sidebar',
			'prop' => 'background',
		),

		'sidebar_border' => array(
			'control' => array(
				'type'  => 'Themify_Border_Control',
				'label' => __( 'Sidebar Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#sidebar',
			'prop' => 'border',
		),

		'sidebar_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
				'label' => __( 'Sidebar Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#sidebar',
			'prop' => 'padding',
		),

		'sidebar_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
				'label' => __( 'Sidebar Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#sidebar',
			'prop' => 'margin',
		),
		'duo_sidebar_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label' => __( 'Duo Sidebar Width', 'themify' ),
			),
			'selector' => '.sidebar2 #sidebar',
			'prop' => 'width',
		),
		'duo_sidebar_alt_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label'   => __( 'Duo Sidebar Alt Width', 'themify' ),
			),
			'selector' => '.sidebar2 #sidebar-alt',
			'prop' => 'width',
		),
		'duo_content_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label'   => __( 'Duo Sidebar Content Width', 'themify' ),
			),
			'selector' => '.sidebar2 #content',
			'prop' => 'width',
		),

		'end_layout_acc' => $themify_customizer->accordion_end(),
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
		'start_forms_acc' => $themify_customizer->accordion_start( __( 'Forms', 'themify' ) ),

		'form_fields_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Form Fields', 'themify' ),
			),
			'selector' => 'textarea, input[type=text], input[type=password], input[type=search], input[type=email], input[type=url], input[type=number], input[type=tel], input[type=date], input[type=datetime], input[type=datetime-local], input[type=month], input[type=time], input[type=week]',
			'prop' => 'background',
		),

		'form_fields_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Form Fields Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'textarea, input[type=text], input[type=password], input[type=search], input[type=email], input[type=url], input[type=number], input[type=tel], input[type=date], input[type=datetime], input[type=datetime-local], input[type=month], input[type=time], input[type=week]',
			'prop' => 'border',
		),

		'form_fields_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Form Fields Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'textarea, input[type=text], input[type=password], input[type=search], input[type=email], input[type=url], input[type=number], input[type=tel], input[type=date], input[type=datetime], input[type=datetime-local], input[type=month], input[type=time], input[type=week]',
			'prop' => 'padding',
		),

		'form_fields_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Form Fields', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'textarea, input[type=text], input[type=password], input[type=search], input[type=email], input[type=url], input[type=number], input[type=tel], input[type=date], input[type=datetime], input[type=datetime-local], input[type=month], input[type=time], input[type=week]',
			'prop' => 'font',
		),
		'form_fields_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Form Fields Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'textarea, input[type=text], input[type=password], input[type=search], input[type=email], input[type=url], input[type=number], input[type=tel], input[type=date], input[type=datetime], input[type=datetime-local], input[type=month], input[type=time], input[type=week]',
			'prop' => 'color',
		),

		'form_fields_focus_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Form Fields Focus', 'themify' ),
			),
			'selector' => 'textarea:focus, input[type=text]:focus, input[type=password]:focus, input[type=search]:focus, input[type=email]:focus, input[type=url]:focus, input[type=number]:focus, input[type=tel]:focus, input[type=date]:focus, input[type=datetime]:focus, input[type=datetime-local]:focus, input[type=month]:focus, input[type=time]:focus, input[type=week]:focus',
			'prop' => 'background',
		),

		'form_fields_focus_border' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Form Fields Focus Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'textarea:focus, input[type=text]:focus, input[type=password]:focus, input[type=search]:focus, input[type=email]:focus, input[type=url]:focus, input[type=number]:focus, input[type=tel]:focus, input[type=date]:focus, input[type=datetime]:focus, input[type=datetime-local]:focus, input[type=month]:focus, input[type=time]:focus, input[type=week]:focus',
			'prop' => 'border',
		),

		'form_fields_focus_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Form Fields Focus Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'textarea:focus, input[type=text]:focus, input[type=password]:focus, input[type=search]:focus, input[type=email]:focus, input[type=url]:focus, input[type=number]:focus, input[type=tel]:focus, input[type=date]:focus, input[type=datetime]:focus, input[type=datetime-local]:focus, input[type=month]:focus, input[type=time]:focus, input[type=week]:focus',
			'prop' => 'color',
		),

		'form_buttons_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Form Buttons', 'themify' ),
			),
			'selector' => 'input[type=reset], input[type=submit], button',
			'prop' => 'background',
		),

		'form_buttons_border' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Form Buttons Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'input[type=reset], input[type=submit], button',
			'prop' => 'border',
		),

		'form_buttons_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Form Buttons Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'input[type=reset], input[type=submit], button',
			'prop' => 'color',
		),

		'form_buttons_hover_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Form Buttons Hover', 'themify' ),
			),
			'selector' => 'input[type=reset]:hover, input[type=submit]:hover, button:hover',
			'prop' => 'background',
		),

		'form_buttons_hover_border' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Form Buttons Hover Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'input[type=reset]:hover, input[type=submit]:hover, button:hover',
			'prop' => 'border',
		),

		'form_buttons_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Form Buttons Hover Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => 'input[type=reset]:hover, input[type=submit]:hover, button:hover',
			'prop' => 'color',
		),

		'end_forms_acc' => $themify_customizer->accordion_end(),
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

		'headerinner_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Header Inner Background', 'themify' ),
			),
			'selector' => '#header',
			'prop' => 'background',
		),

		'headerinner_height' => array(
			'control' => array(
				'type'    => 'Themify_Height_Control',
				'label'   => __( 'Header Inner', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#header',
			'prop' => 'height',
		),

		'headerinner_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '#header',
			'prop' => 'border',
		),

		'headerinner_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Header Inner Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#header',
			'prop' => 'padding',
		),

		'headerinner_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Header Inner Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#header',
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

		'site-logo_position' => array(
			'control' => array(
				'type'    => 'Themify_Position_Control',
				'label'   => __( 'Site Logo Position', 'themify' ),
			),
			'selector' => '#site-logo',
			'prop' => 'position',
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

		'site-tagline_position' => array(
			'control' => array(
				'type'    => 'Themify_Position_Control',
				'label'   => __( 'Site Tagline Position', 'themify' ),
			),
			'selector' => '#site-description',
			'prop' => 'position',
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


		'main_nav_position' => array(
			'control' => array(
				'type'    => 'Themify_Position_Control',
				'label'   => __( 'Menu Container', 'themify' ),
			),
			'selector' => '#main-nav',
			'prop' => 'position',
		),

		'main_nav_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label' => __( 'Width', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav',
			'prop' => 'width',
		),

		'main_nav_height' => array(
			'control' => array(
				'type'  => 'Themify_Height_Control',
				'label' => __( 'Height', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav',
			'prop' => 'height',
		),

		'main_nav_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Menu Container Background', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav',
			'prop' => 'background',
		),

		'main_nav_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Menu Container Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav',
			'prop' => 'border',
		),

		'main_nav_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Menu Container Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav',
			'prop' => 'padding',
		),

		'main_nav_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Menu Container Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav',
			'prop' => 'margin',
		),

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
			'selector' => '#main-nav a',
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
			'selector' => '#main-nav-wrap #main-nav .current_page_item > a, #main-nav .current-menu-item > a',
			'prop' => 'background',
		),

		'main_nav_link_active_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Active Link Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav-wrap #main-nav .current_page_item > a, #main-nav .current-menu-item > a, #main-nav > .current_page_item > a, #main-nav > .current-menu-item > a',
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

		'main_nav_dropdown_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Dropdown Container', 'themify' ),
			),
			'selector' => '#main-nav-wrap #main-nav li:not(.themify-widget-menu) > ul, #main-nav .has-mega-sub-menu .mega-sub-menu, #main-nav .has-mega-column > .sub-menu',
			'prop' => 'background',
		),

		'main_nav_dropdown_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Dropdown Container Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav li:not(.themify-widget-menu) > ul, #main-nav .has-mega-sub-menu .mega-sub-menu, #main-nav .has-mega-column > .sub-menu',
			'prop' => 'border',
		),

		'main_nav_dropdown_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Dropdown Container Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav li:not(.themify-widget-menu) > ul, #main-nav .has-mega-sub-menu .mega-sub-menu, #main-nav .has-mega-column > .sub-menu',
			'prop' => 'padding',
		),

		'main_nav_dropdown_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Dropdown Container Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav li:not(.themify-widget-menu) > ul, #main-nav .has-mega-sub-menu .mega-sub-menu, #main-nav .has-mega-column > .sub-menu',
			'prop' => 'margin',
		),

		'main_nav_dropdown_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Dropdown Link', 'themify' ),
			),
			'selector' => '#main-nav ul a, #main-nav .current_page_item ul a, #main-nav ul .current_page_item a, #main-nav .current-menu-item ul a, #main-nav ul .current-menu-item a, #main-nav .has-mega-column > .sub-menu a',
			'prop' => 'font',
		),

		'main_nav_dropdown_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Dropdown Link Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav ul a, #main-nav .current_page_item ul a, #main-nav ul .current_page_item a, #main-nav .current-menu-item ul a, #main-nav ul .current-menu-item a, .fixed-header #main-nav ul a, #main-nav .has-mega-column > .sub-menu a',
			'prop' => 'color',
		),

		'main_nav_dropdown_link_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Dropdown Link Background', 'themify' ),
				'show_label' => false,
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#main-nav ul a, #main-nav .current_page_item ul a, #main-nav ul .current_page_item a, #main-nav .current-menu-item ul a, #main-nav ul .current-menu-item a, #main-nav .has-mega-column > .sub-menu a',
			'prop' => 'background',
		),

		'main_nav_dropdown_link_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Dropdown Link Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav ul a, #main-nav .current_page_item ul a, #main-nav ul .current_page_item a, #main-nav .current-menu-item ul a, #main-nav ul .current-menu-item a, #main-nav .has-mega-column > .sub-menu a',
			'prop' => 'border',
		),

		'main_nav_dropdown_link_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Dropdown Link Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav ul a, #main-nav .current_page_item ul a, #main-nav ul .current_page_item a, #main-nav .current-menu-item ul a, #main-nav ul .current-menu-item a, #main-nav .has-mega-column > .sub-menu a',
			'prop' => 'padding',
		),

		'main_nav_dropdown_link_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Dropdown Link Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#main-nav ul a, #main-nav .current_page_item ul a, #main-nav ul .current_page_item a, #main-nav .current-menu-item ul a, #main-nav ul .current-menu-item a, #main-nav .has-mega-column > .sub-menu a',
			'prop' => 'margin',
		),

		'main_nav_dropdown_link_hover_color' => array(
		  'setting' => array( 'transport' => 'refresh' ),
		  'control' => array(
		    'type'    => 'Themify_Color_Transparent_Control',
		    'label'   => __( 'Dropdown Link Hover', 'themify' ),
		  ),
		  'selector' => '#main-nav ul a:hover, #main-nav .current_page_item ul a:hover, #main-nav ul .current_page_item a:hover, #main-nav .current-menu-item ul a:hover, #main-nav ul .current-menu-item a:hover, .fixed-header #main-nav ul a:hover',
		  'prop' => 'color',
		),

		'main_nav_dropdown_link_hover_background' => array(
		  'setting' => array( 'transport' => 'refresh' ),
		  'control' => array(
		    'type'    => 'Themify_Color_Transparent_Control',
		    'label'   => __( 'Dropdown Link Hover Background', 'themify' ),
		    'show_label' => false,
		    'color_label' => __( 'Background Color', 'themify' ),
		  ),
		  'selector' => '#main-nav ul a:hover, #main-nav .current_page_item ul a:hover, #main-nav ul .current_page_item a:hover, #main-nav .current-menu-item ul a:hover, #main-nav ul .current-menu-item a:hover',
		  'prop' => 'background',
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

		'post_background' => array(
			'control' => array(
				'type'  => 'Themify_Background_Control',
				'label' => __( 'Post Container', 'themify' ),
			),
			'selector' => '.post',
			'prop' => 'background',
		),

		'post_border' => array(
			'control' => array(
				'type'  => 'Themify_Border_Control',
			),
			'selector' => '.post',
			'prop' => 'border',
		),

		'post_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.post',
			'prop' => 'padding',
		),

		'post_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.post',
			'prop' => 'margin',
		),

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

		'post_title_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.post-title',
			'prop' => 'background',
		),

		'post_title_border' => array(
			'control' => array(
				'type'  => 'Themify_Border_Control',
			),
			'selector' => '.post-title',
			'prop' => 'border',
		),

		'post_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.post-title',
			'prop' => 'margin',
		),

		'post_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.post-title',
			'prop' => 'padding',
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

		'post_title_hover_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.post-title a:hover',
			'prop' => 'background',
		),

		'post_title_hover_border' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'  => 'Themify_Border_Control',
			),
			'selector' => '.post-title a:hover',
			'prop' => 'border',
		),

		'post_title_hover_margin' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.post-title a:hover',
			'prop' => 'margin',
		),

		'post_title_hover_padding' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.post-title a:hover',
			'prop' => 'padding',
		),

		'single_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Single Post Title', 'themify' ),
			),
			'selector' => '.single-post .entry-title',
			'prop' => 'font',
		),

		'single_post_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.single-post .post-title',
			'prop' => 'padding',
		),

		'single_post_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.single-post .post-title',
			'prop' => 'margin',
		),

		'grid4_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Grid4 Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.grid4 .post-title, .loops-wrapper.grid4 .post-title a',
			'prop' => 'font',
		),

		'grid4_post_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.loops-wrapper.grid4 .post-title',
			'prop' => 'padding',
		),

		'grid4_post_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.loops-wrapper.grid4 .post-title',
			'prop' => 'margin',
		),

		'grid3_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Grid3 Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.grid3 .post-title, .loops-wrapper.grid3 .post-title a',
			'prop' => 'font',
		),

		'grid3_post_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.loops-wrapper.grid3 .post-title',
			'prop' => 'padding',
		),

		'grid3_post_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.loops-wrapper.grid3 .post-title',
			'prop' => 'margin',
		),

		'grid2_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Grid2 Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.grid2 .post-title, .loops-wrapper.grid2 .post-title a',
			'prop' => 'font',
		),

		'grid2_post_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.loops-wrapper.grid2 .post-title',
			'prop' => 'padding',
		),

		'grid2_post_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.loops-wrapper.grid2 .post-title',
			'prop' => 'margin',
		),

		'grid2_thumb_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Grid2 Thumb Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.grid2-thumb .post-title, .loops-wrapper.grid2-thumb .post-title a',
			'prop' => 'font',
		),

		'grid2_thumb_post_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.loops-wrapper.grid2-thumb .post-title',
			'prop' => 'padding',
		),

		'grid2_thumb_post_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.loops-wrapper.grid2-thumb .post-title',
			'prop' => 'margin',
		),

		'list_thumb_post_title_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'List Thumb Post Title', 'themify' ),
			),
			'selector' => '.loops-wrapper.list-thumb-image .post-title, .loops-wrapper.list-thumb-image .post-title a',
			'prop' => 'font',
		),

		'list_thumb_post_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.loops-wrapper.list-thumb-image .post-title',
			'prop' => 'padding',
		),

		'list_thumb_post_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.loops-wrapper.list-thumb-image .post-title',
			'prop' => 'margin',
		),

		'post_meta_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Post Meta', 'themify' ),
			),
			'selector' => '.post-meta',
			'prop' => 'color',
		),

		'post_meta_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '.post-meta, .loops-wrapper.list-post .post-meta, .single-post .post-meta, .loops-wrapper.grid4 .post-meta, .loops-wrapper.grid3 .post-meta, .loops-wrapper.grid2 .post-meta',
			'prop' => 'font',
		),

		'post_meta_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.post-meta',
			'prop' => 'background',
		),

		'post_meta_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.post-meta',
			'prop' => 'border',
		),

		'post_meta_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.post-meta',
			'prop' => 'padding',
		),

		'post_meta_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.post-meta, .loops-wrapper.list-post .post-meta, .single-post .post-meta, .loops-wrapper.grid4 .post-meta, .loops-wrapper.grid3 .post-meta, .loops-wrapper.grid2 .post-meta',
			'prop' => 'margin',
		),

		'post_meta_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Post Meta Link', 'themify' ),
			),
			'selector' => '.post-meta a',
			'prop' => 'color',
		),

		'post_meta_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
			),
			'selector' => '.post-meta a',
			'prop' => 'font',
		),

		'post_meta_link_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.post-meta a',
			'prop' => 'background',
		),

		'post_meta_link_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.post-meta a',
			'prop' => 'border',
		),

		'post_meta_link_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.post-meta a',
			'prop' => 'padding',
		),

		'post_meta_link_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.post-meta a',
			'prop' => 'margin',
		),

		'post_meta_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Post Meta Link Hover', 'themify' ),
			),
			'selector' => '.post-meta a:hover',
			'prop' => 'color',
		),

		'post_meta_link_hover_font' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '.post-meta a:hover',
			'prop' => 'font',
		),

		'post_meta_link_hover_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.post-meta a:hover',
			'prop' => 'background',
		),

		'post_meta_link_hover_border' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.post-meta a:hover',
			'prop' => 'border',
		),

		// Post Date .post-date

		'post_date_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Post Date', 'themify' ),
			),
			'selector' => '.post-date, .loops-wrapper.list-post .post .post-date, .single #pagewrap .post-content .post-date',
			'prop' => 'color',
		),

		'post_date_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '.post-date, .post .post-date .day,.post-date .month, .post .post-date .year, .loops-wrapper.list-post .post .post-date, .single #pagewrap .post-content .post-date, .single-post #pagewrap .post-date-inline .post-date .year',
			'prop' => 'font',
		),

		'post_date_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
			),
			'selector' => '.post-date, .loops-wrapper.list-post .post .post-date, .single #pagewrap .post-content .post-date',
			'prop' => 'background',
		),

		'post_date_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
			),
			'selector' => '.post-date, .loops-wrapper.list-post .post .post-date, .single #pagewrap .post-content .post-date',
			'prop' => 'width',
		),

		'post_date_height' => array(
			'control' => array(
				'type'  => 'Themify_Height_Control',
			),
			'selector' => '.post-date, .loops-wrapper.list-post .post .post-date, .single #pagewrap .post-content .post-date',
			'prop' => 'height',
		),

		'post_date_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.post-date, .loops-wrapper.list-post .post .post-date, .single #pagewrap .post-content .post-date',
			'prop' => 'border',
		),

		'post_date_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.post-date, .loops-wrapper.list-post .post .post-date, .single #pagewrap .post-content .post-date',
			'prop' => 'padding',
		),

		'post_date_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.post-date, .loops-wrapper.list-post .post .post-date, .single #pagewrap .post-content .post-date',
			'prop' => 'margin',
		),

		// More Link .more-link

		'more_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'More Link', 'themify' ),
			),
			'selector' => '.more-link',
			'prop' => 'color',
		),

		'more_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '.more-link',
			'prop' => 'font',
		),

		'more_link_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
			),
			'selector' => '.more-link',
			'prop' => 'background',
		),

		'more_link_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.more-link',
			'prop' => 'border',
		),

		'more_link_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.more-link',
			'prop' => 'padding',
		),

		'more_link_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.more-link',
			'prop' => 'margin',
		),

		// More Link Hover .more-link:hover

		'more_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'More Link Hover', 'themify' ),
			),
			'selector' => '.more-link:hover',
			'prop' => 'color',
		),

		'more_link_hover_font' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Text_Decoration_Control',
			),
			'selector' => '.more-link:hover',
			'prop' => 'font',
		),

		'more_link_hover_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Background_Control',
			),
			'selector' => '.more-link:hover',
			'prop' => 'background',
		),

		'more_link_hover_border' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.more-link:hover',
			'prop' => 'border',
		),

		// Post Nav .post-nav

		'post_nav_background' => array(
			'control' => array(
				'type'  => 'Themify_Background_Control',
				'label' => __( 'Post Nav (Next/Prev Post Link)', 'themify' ),
			),
			'selector' => '.post-nav',
			'prop' => 'background',
		),

		'post_nav_border' => array(
			'control' => array(
				'type'  => 'Themify_Border_Control',
			),
			'selector' => '.post-nav',
			'prop' => 'border',
		),

		'post_nav_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.post-nav',
			'prop' => 'padding',
		),

		'post_nav_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.post-nav',
			'prop' => 'margin',
		),

		// Post Nav Link .post-nav a

		'post_nav_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Post Nav Link', 'themify' ),
			),
			'selector' => '.post-nav a',
			'prop' => 'color',
		),

		'post_nav_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '.post-nav a',
			'prop' => 'font',
		),

		// Next/Prev Link Icon .post-nav .arrow

		'post_nav_link_icon_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Post Nav Link Icon', 'themify' ),
			),
			'selector' => '.post-nav .arrow',
			'prop' => 'color',
		),

		'post_nav_link_icon_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.post-nav .arrow',
			'prop' => 'background',
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

		'page_title_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
			),
			'selector' => '.page-title',
			'prop' => 'background',
		),

		'page_title_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.page-title',
			'prop' => 'border',
		),

		'page_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.page-title',
			'prop' => 'padding',
		),

		'page_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.page-title',
			'prop' => 'margin',
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

		'module_title_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
			),
			'selector' => '.module-title',
			'prop' => 'background',
		),

		'module_title_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.module-title',
			'prop' => 'border',
		),

		'module_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.module-title',
			'prop' => 'padding',
		),

		'module_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.module-title',
			'prop' => 'margin',
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
				'type'    => 'Themify_Font_Control',
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
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '#sidebar a:hover, #sidebar-alt a:hover',
			'prop' => 'font',
		),

		// Sidebar Widget #sidebar .widget

		'sidebar_widget_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Sidebar Widget Container', 'themify' ),
			),
			'selector' => '#sidebar .widget, #sidebar-alt .widget',
			'prop' => 'color',
		),

		'sidebar_widget_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
			),
			'selector' => '#sidebar .widget, #sidebar-alt .widget',
			'prop' => 'background',
		),

		'sidebar_widget_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '#sidebar .widget, #sidebar-alt .widget',
			'prop' => 'border',
		),

		'sidebar_widget_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '#sidebar .widget, #sidebar-alt .widget',
			'prop' => 'padding',
		),

		'sidebar_widget_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '#sidebar .widget, #sidebar-alt .widget',
			'prop' => 'margin',
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

		'sidebar_widget_title_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#sidebar .widgettitle, #sidebar-alt .widgettitle',
			'prop' => 'background',
		),

		'sidebar_widget_title_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '#sidebar .widgettitle, #sidebar-alt .widgettitle',
			'prop' => 'border',
		),

		'sidebar_widget_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '#sidebar .widgettitle, #sidebar-alt .widgettitle',
			'prop' => 'padding',
		),

		'sidebar_widget_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '#sidebar .widgettitle, #sidebar-alt .widgettitle',
			'prop' => 'margin',
		),

		// Sidebar Widget List Styling #sidebar .widget li

		'sidebar_widget_list_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Sidebar Widget List Styling', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#sidebar .widget li, #sidebar-alt .widget li',
			'prop' => 'background',
		),

		'sidebar_widget_list_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '#sidebar .widget li, #sidebar-alt .widget li',
			'prop' => 'border',
		),

		'sidebar_widget_list_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '#sidebar .widget li, #sidebar-alt .widget li',
			'prop' => 'padding',
		),

		'sidebar_widget_list_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '#sidebar .widget li, #sidebar-alt .widget li',
			'prop' => 'margin',
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

		// Footer Inner #footer

		'footerinner_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Footer Inner', 'themify' ),
			),
			'selector' => '#footer',
			'prop' => 'background',
		),

		'footerinner_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '#footer',
			'prop' => 'border',
		),

		'footerinner_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '#footer',
			'prop' => 'padding',
		),

		'footerinner_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '#footer',
			'prop' => 'margin',
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
				'type'    => 'Themify_Font_Control',
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
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '#footer a:hover',
			'prop' => 'font',
		),

		'footer_nav_position' => array(
			'control' => array(
				'type'    => 'Themify_Position_Control',
				'label'   => __( 'Footer Menu Container', 'themify' ),
			),
			'selector' => '#footer-nav',
			'prop' => 'position',
		),

		'footer_nav_width' => array(
			'control' => array(
				'type'  => 'Themify_Width_Control',
				'label' => __( 'Width', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav',
			'prop' => 'width',
		),

		'footer_nav_height' => array(
			'control' => array(
				'type'  => 'Themify_Height_Control',
				'label' => __( 'Height', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav',
			'prop' => 'height',
		),

		'footer_nav_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
				'label'   => __( 'Menu Container Background', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav',
			'prop' => 'background',
		),

		'footer_nav_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Menu Container Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav',
			'prop' => 'border',
		),

		'footer_nav_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Menu Container Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav',
			'prop' => 'padding',
		),

		'footer_nav_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Menu Container Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav',
			'prop' => 'margin',
		),

		'footer_nav_link_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
				'label'   => __( 'Footer Menu Link', 'themify' ),
			),
			'selector' => '#footer-nav a',
			'prop' => 'font',
		),

		'footer_nav_link_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Link Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav a',
			'prop' => 'color',
		),

		'footer_nav_link_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Menu Link Background', 'themify' ),
				'show_label' => false,
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#footer-nav a',
			'prop' => 'background',
		),

		'footer_nav_link_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
				'label'   => __( 'Menu Link Border', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav a',
			'prop' => 'border',
		),

		'footer_nav_link_padding' => array(
			'control' => array(
				'type'    => 'Themify_Padding_Control',
				'label'   => __( 'Menu Link Padding', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav a',
			'prop' => 'padding',
		),

		'footer_nav_link_margin' => array(
			'control' => array(
				'type'    => 'Themify_Margin_Control',
				'label'   => __( 'Menu Link Margin', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav a',
			'prop' => 'margin',
		),

		'footer_nav_link_hover_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Footer Menu Link Hover', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#footer-nav a:hover',
			'prop' => 'background',
		),

		'footer_nav_link_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Link Hover Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav a:hover, #footer-nav li:hover > a',
			'prop' => 'color',
		),

		'footer_nav_link_active_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Footer Menu Active Link', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#footer-nav .current_page_item a, #footer-nav .current-menu-item a',
			'prop' => 'background',
		),

		'footer_nav_link_active_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Active Link Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav .current_page_item a, #footer-nav .current-menu-item a',
			'prop' => 'color',
		),

		'footer_nav_link_active_hover_background' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Footer Menu Active Link Hover', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '#footer-nav .current_page_item a:hover, #footer-nav .current-menu-item a:hover',
		),

		'footer_nav_link_active_hover_color' => array(
			'setting' => array( 'transport' => 'refresh' ),
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Menu Active Link Hover Color', 'themify' ),
				'show_label' => false,
			),
			'selector' => '#footer-nav .current_page_item a:hover, #footer-nav .current-menu-item a:hover',
			'prop' => 'color',
		),

		// Footer Widget .footer-widgets .widget

		'footer_widget_color' => array(
			'control' => array(
				'type'    => 'Themify_Color_Control',
				'label'   => __( 'Footer Widget Container', 'themify' ),
			),
			'selector' => '.footer-widgets .widget',
			'prop' => 'color',
		),

		'footer_widget_font' => array(
			'control' => array(
				'type'    => 'Themify_Font_Control',
			),
			'selector' => '.footer-widgets',
			'prop' => 'font',
		),

		'footer_widget_background' => array(
			'control' => array(
				'type'    => 'Themify_Background_Control',
			),
			'selector' => '.footer-widgets .widget',
			'prop' => 'background',
		),

		'footer_widget_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.footer-widgets .widget',
			'prop' => 'border',
		),

		'footer_widget_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.footer-widgets .widget',
			'prop' => 'padding',
		),

		'footer_widget_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.footer-widgets .widget',
			'prop' => 'margin',
		),

		// Footer Widget Title .footer-widgets .widgettitle

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

		'footer_widget_title_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.footer-widgets .widgettitle',
			'prop' => 'background',
		),

		'footer_widget_title_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.footer-widgets .widgettitle',
			'prop' => 'border',
		),

		'footer_widget_title_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.footer-widgets .widgettitle',
			'prop' => 'padding',
		),

		'footer_widget_title_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.footer-widgets .widgettitle',
			'prop' => 'margin',
		),

		// Footer Widget List Styling .footer-widgets .widget li

		'footer_widget_list_background' => array(
			'control' => array(
				'type'    => 'Themify_Color_Transparent_Control',
				'label'   => __( 'Footer Widget List Styling', 'themify' ),
				'color_label' => __( 'Background Color', 'themify' ),
			),
			'selector' => '.footer-widgets .widget li',
			'prop' => 'background',
		),

		'footer_widget_list_border' => array(
			'control' => array(
				'type'    => 'Themify_Border_Control',
			),
			'selector' => '.footer-widgets .widget li',
			'prop' => 'border',
		),

		'footer_widget_list_padding' => array(
			'control' => array(
				'type'  => 'Themify_Padding_Control',
			),
			'selector' => '.footer-widgets .widget li',
			'prop' => 'padding',
		),

		'footer_widget_list_margin' => array(
			'control' => array(
				'type'  => 'Themify_Margin_Control',
			),
			'selector' => '.footer-widgets .widget li',
			'prop' => 'margin',
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