<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class wlb_color_scheme {
	var $color_scheme = array();
	function __construct( $path, $url ){
		$this->path = $path;
		$this->url = $url;
		
		global $wlb_plugin;
		$this->id = $wlb_plugin->id.'-css';
		add_filter("pop-options_{$this->id}",array(&$this,'wlb_options'),10,1);	
		//add_filter('wlb_options_before',array(&$this,'wlb_options'),40,2);
		add_action('pop_handle_save',array(&$this,'pop_handle_save'),50,1);
		
		add_action('admin_head-profile.php',array(&$this,'personal_options'),10,1);	
		add_action('admin_head-user-edit.php',array(&$this,'personal_options'),10,1);	
		
		add_filter("get_user_option_admin_color", array(&$this,'get_user_option_admin_color'), 40, 3);
	
		add_action('admin_head', array(&$this,'admin_head'));
	}
	
	function color_scheme(){
		return array(
//			'menu_current_color' 	=> (object)array(
//				'label' 		=> __('Current menu color','wlb'),
//				'description' 	=> __('','wlb'),
//				'style'			=> ""
//			),

			'cs_menu_holder_color' 		=> (object)array(
				'label' 		=> __('Container Color(#adminmenuback)','wlb'),
				'description' 	=> '',
				'style'			=> "div#adminmenuback {background-color: {color};}",
				'subtitle'		=> __('Menu color customization','wlb')
			),
			
			'cs_menu_main_color' 		=> (object)array(
				'label' 		=> __('Menu Main Color','wlb'),
				'description' 	=> '',
				'style'			=> implode("\n",array(
					"#adminmenuback, #adminmenuwrap, #adminmenu li.menu-top > a.menu-top {background-color: {color};}",//the main menu bg color
					"ul#adminmenu li.wp-menu-separator {background-color: {color};}",//the separator
					"ul#adminmenu {background-color: {color};}"//the collapse button and bg color of holder
				))
			),
			
			'cs_menu_separator_color' 		=> (object)array(
				'label' 		=> __('Separator color','wlb'),
				'description' 	=> '',
				'style'			=> "body ul#adminmenu li.wp-menu-separator {background-color: {color};}"
			),
			
			'cs_menu_main_color_hover'		=> (object)array(
				'label'			=> __('Menu Main Color Hover','wlb'),
				'description' 	=> '',
				'style'			=> "#adminmenu a:hover, #adminmenu li.menu-top:hover, #adminmenu li.opensub > a.menu-top, #adminmenu li > a.menu-top:focus, #adminmenu li.menu-top:hover > a, #adminmenu li.menu-top.focused > a, #adminmenu li.menu-top > a:focus {background-color: {color};}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -4
			),		
			'cs_menu_link_color'		=> (object)array(
				'label'			=> __('Menu Link Color','wlb'),
				'description' 	=> '',
				'style'			=> "#adminmenu a.menu-top, #adminmenu li.opensub > a.menu-top {color: {color};}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -30
			),
	
			'cs_menu_link_color_hover' => (object)array(
				'label'	=> __('Menu Link Hover Color','wlb'),
				'description' => '',
				'style'			=> "#adminmenu a.menu-top:hover, #adminmenu li.menu-top > a:focus {color: {color};}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -20
			),
	
			'cs_menu_link_hover_text_shadow' => (object)array(
				'label'	=> __('Menu Link Hover Text Shadow','wlb'),
				'description' => '',
				'style'			=> "#adminmenu li.wp-not-current-submenu .wp-menu-arrow div, #adminmenu li.wp-not-current-submenu .wp-menu-arrow, #adminmenu li.menu-top:hover > a, #adminmenu li.menu-top.focused > a, #adminmenu li.menu-top > a:focus {text-shadow:0 1px 0 {color};}",
				'alternative'	=> "cs_menu_link_color",
				'variation'		=> -10
			),
			
			'cs_menu_current_font_color' 	=> (object)array(
				'label' 		=> __('Current Menu Font Color','wlb'),
				'description' 	=> '',
				'style'			=> "#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.current a.menu-top, #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head {color: {color};}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> 20
			),
//			'menu_current_bg' 	=> (object)array(
//				'label' 		=> __('Current menu background','wlb'),
//				'description' 	=> '',
//				'style'			=> ""
//			),

			'cs_menu_current_bg2' 	=> (object)array(
				'label' 		=> __('Current Gradient Top','wlb'),
				'description' 	=> '',
				'style'			=> "",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -30
			),
			'cs_menu_current_bg1' 	=> (object)array(
				'label' 		=> __('Current Gradient Bottom','wlb'),
				'description' 	=> '',
				'style'			=> "",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -20
			),
			/*
			'menu_border_color' => (object)array(
				'label'	=> __('Menu border right','wlb'),
				'description' => '',
				'style'			=> "#adminmenuback, #adminmenuwrap {border-color: {color};}"
			),
			'menu_separator_top' => (object)array(
				'label'	=> __('Menu border top','wlb'),
				'description' => '',
				'style'			=> "#adminmenu a.menu-top, .folded #adminmenu li.menu-top, #adminmenu .wp-submenu .wp-submenu-head {border-top-color: {color};}"
			),
			'menu_separator_bot' => (object)array(
				'label'	=> __('Menu border bottom','wlb'),
				'description' => '',
				'style'			=> "#adminmenu a.menu-top, .folded #adminmenu li.menu-top, #adminmenu .wp-submenu .wp-submenu-head {border-bottom-color: {color};}"
			),
			*/
			'cs_submenu_bg_color'	=> (object)array(
				'label' => __('Submenu Background','wlb'),
				'description' => '',
				'style'			=> implode("\n",array(
					"#adminmenu .wp-has-current-submenu .wp-submenu, #adminmenu .wp-has-submenu ul {background-color: {color};}",
					"#adminmenu li.wp-has-submenu.wp-not-current-submenu.opensub:hover:after {border-right-color: {color};}"
				)),
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> 25
			),
			
			
			
//			'submenu_bottom_border'	=> (object)array(
//				'label' => __('Submenu bottom border','wlb'),
//				'description' => '',
//				'style'			=> "#adminmenu li.wp-menu-open {border-color: {color};}"
//			),
			'cs_submenu_link_color'	=> (object)array(
				'label' => __('Submenu Link Color','wlb'),
				'description' => '',
				'style'			=> "#adminmenu .wp-submenu  a {color: {color};}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -20
			),
			'cs_submenu_link_color_hover'	=> (object)array(
				'label'	=> __('Submenu Link Hover Color', 'wlb'),
				'description' => '',
				'style'			=> "#adminmenu .wp-submenu a:hover {color: {color} !important;}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -30
			),
			'cs_submenu_hover_color'	=> (object)array(
				'label'	=> __('Submenu Hover Color', 'wlb'),
				'description' => '',
				'style'			=> "#adminmenu .wp-submenu a:hover {background-color:{color} !important;}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> 12
			),
			'cs_submenu_current_font_color'	=> (object)array(
				'label'	=> __('Submenu Active Color', 'wlb'),
				'description' => '',
				'style'			=> "#adminmenu .wp-submenu li.current, #adminmenu .wp-submenu li.current a, #adminmenu .wp-submenu li.current a:hover {color: {color};font-weight:bold;}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -30
			),
//			'menu_separator1'	=> (object)array(
//				'label'	=> __('Menu separator background', 'wlb'),
//				'description' => '',
//				'style'			=> "#adminmenu li.wp-menu-separator {background: none repeat scroll 0 0 {color};}"
//			),
//			'menu_separator2'	=> (object)array(
//				'label'	=> __('Menu separator border', 'wlb'),
//				'description' => '',
//				'style'			=> "#adminmenu li.wp-menu-separator {border-color: {color};}"
//			),
//			'separator'	=> (object)array(
//				'label'	=> __('Menu dividers', 'wlb'),
//				'description' => '',
//				'style'			=> "#adminmenu div.separator {border-color: {color};}"
//			)
//CONTENT AREA----
			'cs_bg_color'	=> (object)array(
				'subtitle' => __('Content Area ','wlb'),
				'label'	=> __('Background Color','wlb'),
				'description'=> '',
				'style' => implode("\n", array(
					"html, .wp-dialog, #post-body {background-color:{color};}",
					"ul#adminmenu a.wp-has-current-submenu:after, ul#adminmenu>li.current>a.current:after {border-right-color:{color};}"
				)),
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> 25			
			),

			'cs_content_color'	=> (object)array(
				'label'	=> __('Font Color','wlb'),
				'description'=> '',
				'style'	=> "body, #wpbody, .form-table .pre {color: {color};}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -50			
			),
			'cs_content_shadow'	=> (object)array(
				'label'	=> __('H2 and Subtitle Text Shadow','wlb'),
				'style'	=> ".wrap h2, .subtitle {text-shadow: 0 1px 0 {color};}",
				'alternative'	=> "cs_content_color",
				'variation'		=> 30
			),
			'cs_content_link_color'	=> (object)array(
				'label'	=> __('Link Color','wlb'),
				'description'=> '',
				'style'	=> "a, #poststuff #edButtonPreview, #poststuff #edButtonHTML, #the-comment-list p.comment-author strong a, #media-upload a.del-link, #media-items a.delete, .plugins a.delete, .ui-tabs-nav a {color: {color};}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -40			
			),
			'cs_content_link_color_hover'	=> (object)array(
				'label'	=> __('Link Hover Color','wlb'),
				'description'=> '',
				'style'	=> "a:hover, a:active, a:focus {color: {color};}",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -50			
			),
//	METABOXES WIGETS AND TABLES		
			'cs_header_grad1'	=> (object)array(
				'subtitle' 		=> __('Metaboxes, Widgets and Tables','wlb'),
				'label'			=> __('Head gradient(bottom)','wlb'),
				'description'	=> '',
				'style'			=> "",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -2	
			),	
			
			'cs_header_grad2'	=> (object)array(	
				'label'			=> __('Header Gradient (upper)','wlb'),
				'description'	=> '',
				'style'			=> "",
				'alternative'	=> "cs_header_grad1",
				'variation'		=> 14
			),	

			'cs_header_font_color'	=> (object)array(	
				'label'			=> __('Header Font Color','wlb'),
				'description'	=> '',
				'style'			=> "",
				'alternative'	=> "cs_content_color",
				'variation'		=> 1
			),				
			
			'cs_header_font_hover'	=> (object)array(	
				'label'			=> __('Header Font Hover Color','wlb'),
				'description'	=> '',
				'style'			=> "",
				'alternative'	=> "cs_header_font_color",
				'variation'		=> -10
			),		
						
			'cs_header_font_shadow_color'	=> (object)array(	
				'label'			=> __('Header Font Shadow','wlb'),
				'description'	=> '',
				'style'			=> "",
				'alternative'	=> "cs_header_font_color",
				'variation'		=> -7
			),
			
			'cs_widget_body_grad1'=> (object)array(	
				'label'			=> __('Content Gradient (upper)','wlb'),
				'description'	=> '',
				'style'			=> "",
				'alternative'	=> "cs_bg_color",
				'variation'		=> -4
			),	
			
			'cs_widget_body_grad2'=> (object)array(	
				'label'			=> __('Content Gradient (bottom)','wlb'),
				'description'	=> '',
				'style'			=> "",
				'alternative'	=> "cs_widget_body_grad1",
				'variation'		=> 7
			),
			
			'cs_widget_border_color'=> (object)array(	
				'label'			=> __('Border Color','wlb'),
				'style'			=> ".widget, #widget-list .widget-top, .postbox, #titlediv, #poststuff .postarea, .stuffbox, .sidebar-name, #available-widgets .widget-holder, div.widgets-sortables, #widgets-left .inactive, #pop-options-cont .toggle-option h3.option-title, #pop-options-cont .option-content, #pop-options-cont .toggle-option h3.option-title, #adminmenu .wp-submenu-wrap, #adminmenu .wp-submenu ul {border-color: {color};}",
				'alternative'	=> "cs_header_grad1",
				'variation'		=> 10
			),
		
			'cs_widefat_td'	=> (object)array(
				'subtitle'	=> __('Tables (widefat)','wlb'),
				'label'	=> __('Font color','wlb'),
				'description'=> '',
				'style'	=> ".widefat td {color:{color};}",
				'alternative'	=> "cs_content_color"	
			),
			
			'cs_widefat_th'	=> (object)array(
				'label'	=> __('Header Font Color','wlb'),
				'description'=> '',
				'style'	=> ".widefat thead tr th, .widefat tfoot tr th {color:{color};}",
				'alternative'	=> "cs_content_color"	
			),

			'cs_widefat_th_shadow'	=> (object)array(
				'label'	=> __('Header Font Shadow','wlb'),
				'description'=> '',
				'style'	=> "",
				'alternative'	=> "cs_widefat_th",
				'variation'		=> -2	
			),			
			
			'cs_widefat_row_color'	=> (object)array(
				'label'			=> __('Row Color','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_bg_color",
				'variation' 	=> -1
			),
			'cs_widefat_alt_row_color'	=> (object)array(
				'label'			=> __('Alternate Row Color','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_bg_color",
				'variation' 	=> -3
			),
			'cs_widefat_border_color'	=> (object)array(
				'label'			=> __('Border Color','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -8	
			),
//contextual help and screen options
			'cs_link_wrap_grad1'	=> (object)array(
				'subtitle'		=> __('Admin Header (Screen Options, Contextual Help)','wlb'),
				'label'			=> __('Tab Gradient 1','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_header_grad1",
				'variation'		=> 4	
			),
			'cs_link_wrap_grad2'	=> (object)array(
				'label'			=> __('Tab Gradient 2','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_link_wrap_grad1",
				'variation'		=> -4	
			),
			'cs_screen_meta_bg'	=> (object)array(
				'label'			=> __('Background Color','wlb'),
				'style'			=> "#screen-options-wrap, #contextual-help-wrap {background-color:{color};}",
				'alternative'	=> "cs_bg_color",
				'variation'		=> -4	
			),
			'cs_screen_meta_border'	=> (object)array(
				'label'			=> __('Border Color','wlb'),
				'style'			=> "#screen-options-wrap, #contextual-help-wrap {border-color:{color};}",
				'alternative'	=> "cs_screen_meta_bg",
				'variation'		=> -10
			),
			'cs_header_border'	=> (object)array(
				'label'			=> __('Header Border Color','wlb'),
				'style'			=> "#wphead {border-color:{color};}",
				'alternative'	=> "cs_screen_meta_bg",
				'variation'		=> -10
			),
//--Messages			
			'cs_up_nag_bg'	=> (object)array(
				'subtitle'		=> __('Messages','wlb'),
				'label'			=> __('Update Nag Background','wlb'),
				'style'			=> "#update-nag, .update-nag {background-color:{color};}",
				'alternative'	=> "cs_bg_color",
				'variation'		=> -3
			),
			'cs_up_nag_border'	=> (object)array(
				'label'			=> __('Update Nag Border','wlb'),
				'style'			=> "#update-nag, .update-nag {border-color:{color};}",
				'alternative'	=> "cs_up_nag_bg",
				'variation'		=> -10
			)		,
			'cs_up_nag_font'	=> (object)array(
				'label'			=> __('Update Nag Font Color','wlb'),
				'style'			=> "#update-nag, .update-nag {color:{color};}",
				'alternative'	=> "cs_up_nag_bg",
				'variation'		=> -40
			),
			'cs_msg_upd_bg'	=> (object)array(
				'label'			=> __('Updated Background','wlb'),
				'style'			=> "div.updated {background-color:{color};}",
				'alternative'	=> "cs_bg_color",
				'variation'		=> -3
			)		,
			'cs_msg_upd_border'	=> (object)array(
				'label'			=> __('Updated Border','wlb'),
				'style'			=> "div.updated {border-color:{color};}",
				'alternative'	=> "cs_msg_upd_bg",
				'variation'		=> -10
			)		,
			'cs_msg_upd_color'	=> (object)array(
				'label'			=> __('Updated Font Color','wlb'),
				'style'			=> "div.updated {color:{color};}",
				'alternative'	=> "cs_msg_upd_bg",
				'variation'		=> -50
			)	,
			'cs_msg_err_bg'	=> (object)array(
				'label'			=> __('Error Background','wlb'),
				'style'			=> "div.error {background-color:{color};}",
			),
			'cs_msg_err_border'	=> (object)array(
				'label'			=> __('Error Border','wlb'),
				'style'			=> "div.error {border-color:{color};}",
				'alternative'	=> "cs_msg_err_bg",
				'variation'		=> -10
			),
			'cs_msg_err_color'	=> (object)array(
				'label'			=> __('Error Font Color','wlb'),
				'style'			=> "div.error {color:{color};}",
				'alternative'	=> "cs_msg_err_bg",
				'variation'		=> -50
			),
//---forms			
			'cs_input_bg'	=> (object)array(
				'subtitle'		=> __('Forms','wlb'),
				'label'			=> __('Background Color','wlb'),
				'style'			=> '#postcustomstuff table input, #postcustomstuff table textarea, .inline-editor ul.cat-checklist, select, textarea, input[type="text"], input[type="password"], input[type="file"], input[type="button"], input[type="submit"], input[type="reset"], select {background-color: {color};}',
				'alternative'	=> "cs_bg_color",
				'variation'		=> 7
			),
			'cs_input_border'	=> (object)array(
				'label'			=> __('Border','wlb'),
				'style'			=> '#postcustomstuff table input, #postcustomstuff table textarea, .inline-editor ul.cat-checklist, select, textarea, input[type="text"], input[type="password"], input[type="file"], input[type="button"], input[type="submit"], input[type="reset"], select {border-color: {color};}',
				'alternative'	=> "cs_input_bg",
				'variation'		=> -10
			),
//primary button			
			'cs_primary_btn_grad1'	=> (object)array(
				'subtitle'		=> __('Primary Button','wlb'),
				'label'			=> __('Gradient 1','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> -30
			),				
			'cs_primary_btn_grad2'	=> (object)array(
				'label'			=> __('Gradient 2','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_primary_btn_grad1",
				'variation'		=> 10
			),						
			'cs_primary_btn_border'	=> (object)array(
				'label'			=> __('Border','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_primary_btn_grad1"
			),						
			'cs_primary_btn_font'	=> (object)array(
				'label'			=> __('Font','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_menu_main_color",
				'variation'		=> 20
			),						
			'cs_primary_btn_hborder'	=> (object)array(
				'label'			=> __('Hover Border','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_primary_btn_border",
				'variation'		=> -30
			),						
			'cs_primary_btn_hfont'	=> (object)array(
				'label'			=> __('Hover Font','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_primary_btn_font",
				'variation'		=> -7
			),			
			'cs_primary_btn_disabled_bg'	=> (object)array(
				'label'			=> __('Disabled Background','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_primary_btn_grad1",
				'variation'		=> 22
			),			
			'cs_primary_btn_disabled_border'	=> (object)array(
				'label'			=> __('Disabled Border','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_primary_btn_disabled_bg",
				'variation'		=> -5
			),				
			'cs_primary_btn_disabled_color'	=> (object)array(
				'label'			=> __('Disabled Font Color','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_primary_btn_disabled_bg",
				'variation'		=> 15
			),
//-- SECONDARY ----
			'cs_secondary_btn_grad1'	=> (object)array(
				'subtitle'		=> __('Secondary Button','wlb'),
				'label'			=> __('Gradient 1','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_bg_color",
				'variation'		=> 3
			),				
			'cs_secondary_btn_grad2'	=> (object)array(
				'label'			=> __('Gradient 2','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_secondary_btn_grad1",
				'variation'		=> -12
			),						
			'cs_secondary_btn_border'	=> (object)array(
				'label'			=> __('Border','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_secondary_btn_grad2",
				'variation'		=> -2
			),						
			'cs_secondary_btn_font'	=> (object)array(
				'label'			=> __('Font','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_content_color"
			),						
			'cs_secondary_btn_font_shadow'	=> (object)array(
				'label'			=> __('Font Shadow','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_secondary_btn_font",
				'variation'		=> -7
			),						
			'cs_secondary_btn_hborder'	=> (object)array(
				'label'			=> __('Hover Border','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_secondary_btn_border",
				'variation'		=> -30
			),						
			'cs_secondary_btn_hfont'	=> (object)array(
				'label'			=> __('Hover Font','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_secondary_btn_font"
			),			
			'cs_secondary_btn_disabled_bg'	=> (object)array(
				'label'			=> __('Disabled Background','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_secondary_btn_grad2",
				'variation'		=> 8
			),			
			'cs_secondary_btn_disabled_border'	=> (object)array(
				'label'			=> __('Disabled Border','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_secondary_btn_disabled_bg",
				'variation'		=> -5
			),				
			'cs_secondary_btn_disabled_color'	=> (object)array(
				'label'			=> __('Disabled font color','wlb'),
				'style'			=> '',
				'alternative'	=> "cs_secondary_btn_font",
				'variation'		=> 20
			),			
//-- POST PAGES and CUSTOM POST TYPES			
			'cs_edit_post'	=> (object)array(
				'subtitle'		=> __('Post, Page and Custom Post Types Edit Screen','wlb'),
				'label'			=> __('Add new background','wlb'),
				'style'			=> ".wrap .add-new-h2 {background-color:{color};}",
				'alternative'	=> "cs_bg_color",
				'variation'		=> -10
			),
			'cs_edit_title_bg'	=> (object)array(
				'label'			=> __('Title Input Background','wlb'),
				'style'			=> "#titlediv #title {background-color:{color};}",
				'alternative'	=> "cs_input_bg"
			),		
			'cs_edit_title_border'	=> (object)array(
				'label'			=> __('Title Input Border','wlb'),
				'style'			=> "#titlediv #title {border-color:{color};}",
				'alternative'	=> "cs_input_border"
			),			
	//POST EDIT CUSTOM FIELDS		
			'cs_custom_field_h'	=> (object)array(
				'label'			=> __('Custom Fields Header','wlb'),
				'style'			=> "#postcustomstuff thead th {background-color:{color};}",
				'alternative'	=> "cs_widget_body_grad2",
				'variation'		=> -4
			),
			'cs_custom_field_hb'=> (object)array(
				'label'			=> __('Custom Fields Header Border','wlb'),
				'style'			=> "#postcustomstuff thead th {border-color:{color};}",
				'alternative'	=> "cs_custom_field_h",
				'variation'		=> -4
			),
			'cs_custom_field_c'	=> (object)array(
				'label'			=> __('Custom Fields Background','wlb'),
				'style'			=> "#postcustomstuff table {background-color:{color};}",
				'alternative'	=> "cs_custom_field_h",
				'variation'		=> 4
			)	,
			'cs_custom_field_cb'	=> (object)array(
				'label'			=> __('Custom Fields Content Border','wlb'),
				'style'			=> "#postcustomstuff table {border-color:{color};}",
				'alternative'	=> "cs_custom_field_c",
				'variation'		=> -4
			),
//THE EDITOR,	
			'cs_editor_grad1'	=> (object)array(
				'subtitle'		=> __('The Editor (default tinymce)','wlb'),
				'label'			=> __('Header Gradient 1','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_header_grad1"
			),
			'cs_editor_grad2'	=> (object)array(
				'label'			=> __('Header Gradient 1','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_editor_grad1",
				'variation'		=> 4
			),
			'cs_editor_border'	=> (object)array(
				'label'			=> __('Border Color','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_editor_grad1",
				'variation'		=> -4
			),
			'cs_editor_footer'	=> (object)array(
				'label'			=> __('Footer Color','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_editor_grad1",
				'variation'		=> 3
			)	,
			'cs_editor_content'	=> (object)array(
				'label'			=> __('Background Color','wlb'),
				'style'			=> "",
				'alternative'	=> "cs_editor_footer",
				'variation'		=> 10
			)	
		);
	}

	function get_color($color, $per){
		$color = str_replace("#","",$color);
		if($per==0)return $color;
		$rgb = '';
	    $per = $per/100*255;
	    if  ($per < 0 ){
	        $per =  abs($per);
	        for ($x=0;$x<3;$x++){
	            $c = hexdec(substr($color,(2*$x),2)) - $per;
	            $c = ($c < 0) ? 0 : dechex($c);
	            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
	        }  
	    }else{
	        for ($x=0;$x<3;$x++){            
	            $c = hexdec(substr($color,(2*$x),2)) + $per;
	            $c = ($c > 255) ? 'ff' : dechex($c);
	            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
	        }   
	    }
	    return '#'.$rgb;
	} 

	function get_icons_path($stylesheet){
		
	}
	
	function get_icon_sets($r = array()){
		global $wlb_plugin;
		$upload_dir = wp_upload_dir();

		//--
		$icons_path = $this->path.'resources/icons_sets'; 
		$r = $this->get_icon_sets_from_path('core',$icons_path,$r);
		//--
		$icons_path = $upload_dir['basedir'].'/'.$wlb_plugin->resources_path.'/icons_sets';
		$r = $this->get_icon_sets_from_path('dc',$icons_path,$r);
		//---
		$r = apply_filters('wlb-icon-sets',$r);
		
		return $r;
	}

	function get_icon_sets_from_path($id,$icons_path,$r=array()){
		if (is_dir($icons_path)&&$handle = opendir($icons_path)) {
		    while (false !== ($file = readdir($handle))) {
		    	if(in_array($file,array('.','..')))continue; 
				if(strpos($file,'.')===0)continue;
				if(!is_dir($icons_path.'/'.$file))continue;		
				$file_data = @get_file_data( $icons_path.'/'.$file.'/style.css', array('name'=>'Set Name'));			
				$index = $id.','.$file;
				$r[$index]=trim($file_data['name'])==''?$file:$file_data['name'];
		    }
		    closedir($handle);	
		}	
		return $r;	
	}
	
	function css_icon_set(){
		global $wlb_plugin;
		$stylesheet = $wlb_plugin->get_option('cs_url_css');
		if(''!=trim($stylesheet)){
			$arr = explode(',',$stylesheet,2);
			if(is_array($arr)&&count($arr)==2){
				if($arr[0]=='core'){
					echo sprintf('<link rel="stylesheet" href="%s" type="text/css" media="all" />',
						$this->url.'resources/icons_sets/'.$arr[1].'/style.css'
					);
				}else if($arr[0]=='dc'){
					$upload_dir = wp_upload_dir();
					echo sprintf('<link rel="stylesheet" href="%s" type="text/css" media="all" />',
						$upload_dir['baseurl'].'/'.$wlb_plugin->resources_path.'/icons_sets/'.$arr[1].'/style.css'
					);
				}else{
					do_action('wlb-icon-set-style',$stylesheet);
				}
				return;			
			}else{
				do_action('wlb-icon-set-style',$stylesheet);
				return;				
			}
		}
		$stylesheet = $wlb_plugin->get_option('cs_url_css_external');
		if(''!=trim($stylesheet)){
			echo sprintf('<link rel="stylesheet" href="%s" type="text/css" media="all" />',$stylesheet);
		}
		return;
	}
	
	function admin_head(){
		global $wlb_plugin;
		
		$this->css_icon_set();
		
		if(1==$wlb_plugin->get_option('force_color_scheme')){
			if(strlen($wlb_plugin->get_option('cs_menu_main_color',''))>1){
				add_action('pop-options-cont-class',create_function('','echo  "wlb-color-scheme-active ";'));
			}
?>
<!--[if IE]>
<style type="text/css" media="screen">
<?php			
			$color_scheme = $this->color_scheme();
			foreach($color_scheme as $id => $r){
				$color = $this->get_color_from_options($id,$r);
				if( !in_array(trim($color),array('','#')) && property_exists($r,'style')){
					echo str_replace("{color}",$color,$r->style);	
				}
			}
			$this->get_color_scheme_css($color_scheme,true);//ieversion
?>
</style>
<![endif]-->
<!--[if !IE]><! -->
<style type="text/css" media="screen">
<?php			
			$color_scheme = $this->color_scheme();
			foreach($color_scheme as $id => $r){
				$color = $this->get_color_from_options($id,$r);
				if( !in_array(trim($color),array('','#')) && property_exists($r,'style')){
					echo str_replace("{color}",$color,$r->style);	
				}
			}
			$this->get_color_scheme_css($color_scheme);
?>
</style>
<!-- <![endif]-->
<?php			
		}
	}
	
	function get_color_from_options($id){
		global $wlb_plugin;
		$color = $wlb_plugin->get_option($id);
		if(!in_array(trim($color),array('','#')))return $color;
		if('1'==$wlb_plugin->get_option('disable_auto_color_scheme'))return '';
		if(empty($this->color_scheme))$this->color_scheme = $this->color_scheme();
		$r = isset($this->color_scheme[$id])?$this->color_scheme[$id]:(object)array();
		if(!property_exists($r,'alternative'))return '';
		$color = $this->get_color_from_options($r->alternative);
		if(in_array(trim($color),array('','#')))return '';
		if(!property_exists($r,'variation'))return $color;
		return $this->get_color($color,$r->variation);		
	}
		
	function get_color_scheme_css($color_scheme,$ie=false){
		global $wlb_plugin,$wp_version;

		$menu_main_color = $this->get_color_from_options('cs_menu_main_color',$color_scheme['cs_menu_main_color']);//get_option('');
		$cs_bg_color = $this->get_color_from_options('cs_bg_color',$color_scheme['cs_bg_color']);
		if($ie){
			$this->ie_css_current_menu($this->get_color_from_options('cs_menu_current_bg1',$color_scheme['cs_menu_current_bg1']));
		}else{
			$this->css_current_menu( 
				$this->get_color_from_options('cs_menu_current_bg1',$color_scheme['cs_menu_current_bg1']),
				$this->get_color_from_options('cs_menu_current_bg2',$color_scheme['cs_menu_current_bg2'])
			);
		}
		
		if($wp_version<3.3){
			$this->css_current_menu_arrow($cs_bg_color);
		}else{
			$this->css_current_menu_arrow_wp33(
				$this->get_color_from_options('cs_menu_current_bg1',$color_scheme['cs_menu_current_bg1']),
				$this->get_color_from_options('cs_menu_current_bg2',$color_scheme['cs_menu_current_bg2'])			
			);
		}
		
		$this->css_menu_borders($menu_main_color);
		
		$this->css_widget_header(
			$this->get_color_from_options('cs_header_grad1'),
			$this->get_color_from_options('cs_header_grad2'),
			$this->get_color($this->get_color_from_options('cs_header_grad1'),2),
			$this->get_color_from_options('cs_header_font_color'),
			$this->get_color_from_options('cs_header_font_hover'),
			$this->get_color_from_options('cs_header_font_shadow_color')	
		);
	
		$this->css_widget_content(
			$this->get_color_from_options('cs_widget_body_grad1'),
			$this->get_color_from_options('cs_widget_body_grad2'),
			$this->get_color($this->get_color_from_options('cs_widget_body_grad1'),2)	
		);

		$this->css_list_rows(
			$this->get_color_from_options('cs_widefat_border_color'),
			$this->get_color_from_options('cs_widefat_row_color'),
			$this->get_color_from_options('cs_widefat_alt_row_color'),
			$this->get_color_from_options('cs_widefat_th_shadow')
		);		
		
		$this->css_link_wrap(
			$this->get_color_from_options('cs_link_wrap_grad1'),
			$this->get_color_from_options('cs_link_wrap_grad2'),
			$this->get_color_from_options('cs_link_wrap_grad1')
		);
		
		$this->css_the_editor(
			$this->get_color_from_options('cs_editor_border'),//$border_color, 
			$this->get_color_from_options('cs_editor_grad1'),//$header_grad1, 
			$this->get_color_from_options('cs_editor_grad2'),//$header_grad2, 
			$this->get_color($this->get_color_from_options('cs_editor_grad1'),2),//$header_intermediate, 
			$this->get_color_from_options('cs_editor_content'),//$content_bg, 
			$this->get_color_from_options('cs_editor_footer')//$footer_bg
		);
		$this->css_secondary_button(
			$this->get_color_from_options('cs_secondary_btn_grad1'),//$grad1,
			$this->get_color_from_options('cs_secondary_btn_grad2'),//$grad2,
			$this->get_color_from_options('cs_secondary_btn_border'),//$border,
			$this->get_color_from_options('cs_secondary_btn_font'),//$font,
			$this->get_color_from_options('cs_secondary_btn_font_shadow'),//$shadow,
			$this->get_color_from_options('cs_secondary_btn_hborder'),//$h_border,
			$this->get_color_from_options('cs_secondary_btn_hfont'),//$h_font,
			$this->get_color_from_options('cs_secondary_btn_disabled_bg'),
			$this->get_color_from_options('cs_secondary_btn_disabled_border'),
			$this->get_color_from_options('cs_secondary_btn_disabled_color')
		);		
		$this->css_primary_button(
			$this->get_color_from_options('cs_primary_btn_grad1'),//$grad1,
			$this->get_color_from_options('cs_primary_btn_grad2'),//$grad2,
			$this->get_color_from_options('cs_primary_btn_border'),//$border,
			$this->get_color_from_options('cs_primary_btn_font'),//$font,
			$this->get_color_from_options('cs_primary_btn_hborder'),//$h_border,
			$this->get_color_from_options('cs_primary_btn_hfont'),//$h_font,
			$this->get_color_from_options('cs_primary_btn_disabled_bg'),
			$this->get_color_from_options('cs_primary_btn_disabled_border'),
			$this->get_color_from_options('cs_primary_btn_disabled_color')
		);
		
		$this->css_main_menu(
			$this->get_color_from_options('cs_menu_main_color_hover'),
			$this->get_color_from_options('cs_menu_link_color')
		);
		
		$this->cs_extra_css();
	}
	
	function cs_extra_css(){
		global $wlb_plugin;
		echo $wlb_plugin->get_option('cs_extra_css');
	}	
	
	function css_main_menu($hover_color,$text_color){
		if(in_array(trim($hover_color),array('','#')) or in_array(trim($text_color),array('','#')))return;
?>
#adminmenu li.wp-not-current-submenu .wp-menu-arrow div {
	border-color:<?php echo $this->get_color($hover_color,-7)?>;
}
#adminmenu li.wp-not-current-submenu .wp-menu-arrow div,
#adminmenu li.wp-not-current-submenu .wp-menu-arrow ,
#adminmenu li.menu-top:hover > a, #adminmenu li.menu-top.focused > a,
#adminmenu li.menu-top > a:focus {
	background-color: <?php echo $hover_color?>;
	/*text-shadow: 0 1px 0 <?php echo $this->get_rgba( $this->get_color($text_color,2), 0.4 )?>;*/
}
<?php	
	}
	
	function css_current_menu($grad1,$grad2){
		if(in_array(trim($grad1),array('','#')) or in_array(trim($grad2),array('','#')))return;
?>
#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.current a.menu-top, #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head {
    border-bottom-color: <?php echo $grad1?>;
    border-top-color: <?php echo $grad2 ?>;
    color: #FFFFFF;
    text-shadow: 0 -1px 0 #333333;
}

#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,#adminmenu li.current a.menu-top,.folded #adminmenu li.wp-has-current-submenu,.folded #adminmenu li.current.menu-top,#adminmenu .wp-menu-arrow,#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head{
	background-color:<?php echo $grad1?>;
	background-image:-ms-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:-moz-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:-o-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:-webkit-gradient(linear,left bottom,left top,from(<?php echo $grad1; ?>),to(<?php echo $grad2; ?>));
	background-image:-webkit-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
}
<?php	
	}//end of handle_current_menu
	
	function ie_css_current_menu($color){
		if(in_array(trim($color),array('','#')))return;
?>
#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.current a.menu-top, #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head {
    border-bottom-color: <?php echo $color?>;
    border-top-color: <?php echo $color ?>;
    color: #FFFFFF;
    text-shadow: 0 -1px 0 #333333;
}
#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,#adminmenu li.current a.menu-top,.folded #adminmenu li.wp-has-current-submenu,.folded #adminmenu li.current.menu-top,#adminmenu .wp-menu-arrow,#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head{
	background-color:<?php echo $color?>;
}
<?php	
	}//end of ie_handle_current_menu
	
	function css_list_rows($border_color,$row_color,$alternate_row_color,$cs_widefat_th_shadow){
		if(in_array(trim($border_color),array('','#')) or in_array(trim($row_color),array('','#')) or in_array(trim($alternate_row_color),array('','#')))return;
?>
.widefat,
.plugins .inactive,
.plugins .inactive th, .plugins .inactive td {
    background-color: <?php echo $row_color?>;
    border-color: <?php echo $border_color?>;
}

.alternate, .alt
{
    background-color: <?php echo $alternate_row_color?>;
}
.widefat td, .widefat th,
.plugins .inactive th, .plugins .inactive td {
    border-bottom-color: <?php echo $this->get_color($row_color,-10)?>;
    border-top-color: <?php echo $this->get_color($row_color,10)?>;
}
.widefat .alternate td, .widefat .alternate th {
    border-bottom-color: <?php echo $this->get_color($alternate_row_color,-10)?>;
    border-top-color: <?php echo $this->get_color($alternate_row_color,10)?>;
}
.plugins tr {
	background-color: <?php echo $this->get_color($alternate_row_color,-5)?>;
}
.widefat th {
    text-shadow: 0px 1px 1px <?php echo $this->get_rgba($cs_widefat_th_shadow,0.8)?>;
}
<?php	
	}
		
	
	function css_menu_borders($menu_main_color){
		if(in_array(trim($menu_main_color),array('','#')))return;
?>
#adminmenuback, #adminmenuwrap
{border-color: <?php echo $this->get_color($menu_main_color,-8)?>;}
#adminmenu a.menu-top, .folded #adminmenu li.menu-top, #adminmenu .wp-submenu .wp-submenu-head, #adminmenu li.wp-not-current-submenu .wp-menu-arrow {border-top-color: <?php echo $this->get_color($menu_main_color,9)?>;}
#adminmenu a.menu-top, .folded #adminmenu li.menu-top, #adminmenu .wp-submenu .wp-submenu-head, #adminmenu li.wp-not-current-submenu .wp-menu-arrow {border-bottom-color: <?php echo $this->get_color($menu_main_color,-8)?>;}
<?php /* menu_separator2: top of menu_separator1, a bit darker. */?>
#adminmenu li.wp-menu-separator {border-color: <?php echo $this->get_color($menu_main_color,-9)?>;}
<?php /* separator between center and top border of separator.*/?>
#adminmenu div.separator {border-color: <?php echo $this->get_color($menu_main_color,-4)?>;}
<?php /* menu_separator1: separator center */?>
#adminmenu li.wp-menu-separator {background: none repeat scroll 0 0 <?php echo $this->get_color($menu_main_color,-5)?>;}
<?php /* submenu bottom border when open */?>
#adminmenu li.wp-menu-open {border-color: <?php echo $this->get_color($menu_main_color,-8)?>;}
<?php 	
	}
	
	function css_widget_content($grad2,$grad1,$intermediate){
		if(in_array(trim($grad2),array('','#')) or in_array(trim($grad1),array('','#')) or in_array(trim($intermediate),array('','#')))return;
?>
#available-widgets .widget-holder ,
div.widgets-sortables, #widgets-left .inactive,
.widget, #widget-list .widget-top, .postbox, .menu-item-settings, .debug-class
{
	background-color: <?php echo $intermediate?>;
	background-image:-ms-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
    background-image:-moz-linear-gradient(center top , <?php echo $grad1; ?>, <?php echo $grad2; ?>);
	background-image:-o-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:-webkit-gradient(linear,left bottom,left top,from(<?php echo $grad1; ?>),to(<?php echo $grad2; ?>));
	background-image:-webkit-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
}

div.tabs-panel, .wp-tab-panel, ul.category-tabs li.tabs, ul.add-menu-item-tabs li.tabs, .wp-tab-active {
	background-color: <?PHP echo $this->get_color($grad2,1)?>;
}

#post-body /*nav-menus.php*/,
.popular-tags, .feature-filter {
	background-color: <?PHP echo $grad1 ?>;
	border-color: <?php echo $grad2 ?>;
}

.fc-arrow{
	border-color:transparent <?php echo $grad1 ?> transparent transparent;
}
.fc-arrow-border {
	border-color:transparent <?php echo $this->get_color($grad1,-5)?> transparent transparent;
}
<?php	
	}
	
	function css_widget_header($grad2,$grad1,$intermediate,$font_color,$font_hover,$font_shadow){
		if(in_array(trim($grad2),array('','#')) or in_array(trim($grad1),array('','#')) or in_array(trim($intermediate),array('','#')))return;
?>
.widget .widget-top, .postbox h3, .stuffbox h3, .widefat thead tr th, .widefat tfoot tr th, h3.dashboard-widget-title, h3.dashboard-widget-title span, h3.dashboard-widget-title small, 
.find-box-head, .sidebar-name, #nav-menu-header, #nav-menu-footer, 
.menu-item-handle, #fullscreen-topbar,
#pop-options-cont .toggle-option h3.option-title
{
	background-color: <?php echo $intermediate?>;
	background-image:-ms-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
    background-image:-moz-linear-gradient(center top , <?php echo $grad1; ?>, <?php echo $grad2; ?>);
	background-image:-o-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:-webkit-gradient(linear,left bottom,left top,from(<?php echo $grad1; ?>),to(<?php echo $grad2; ?>));
	background-image:-webkit-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
}
#widget-list .widget-top {
	-moz-box-shadow: inset 0 0 0 <?php echo $grad1 ?>;
	-webkit-box-shadow: inset 0 0 0 <?php echo $grad1 ?>;
	box-shadow: inset 0 0 0 <?php echo $grad1 ?>;
}
div.tabs-panel, .wp-tab-panel, ul.category-tabs li.tabs, ul.add-menu-item-tabs li.tabs, .wp-tab-active {
	background-color: <?PHP echo $this->get_color($grad2,1)?>;
}

#menu-management .nav-tab {
	background-color: <?php echo $grad1?>;
	border-color: <?php echo $grad2 ?>;
}
#menu-management .nav-tab-active { /* nav-menu.php */
	background-color: <?php echo $grad2?>;
	border-color: <?php echo $grad2 ?>;
	border-bottom-color: <?php echo $this->get_color($grad2,-3) ?>;
}

.sidebar-name,
.postbox h3, .stuffbox h3 {
	border-bottom-color: <?php echo $this->get_color($grad2,-3) ?>;
	-moz-box-shadow: 0 1px 0 <?php echo $this->get_color($grad2,3) ?>;
	-webkit-box-shadow: 0 1px 0 <?php echo $this->get_color($grad2,3) ?>;
	box-shadow: 0 1px 0 <?php echo $this->get_color($grad2,3) ?>;	
}

.widget .widget-top, .widget, .widget-top {
	-moz-box-shadow: inset 0 0 0 <?php echo $this->get_color($grad2,1) ?>;
	-webkit-box-shadow: inset 0 0 0 <?php echo $this->get_color($grad2,1) ?>;
	box-shadow: inset 0 0 0 <?php echo $this->get_color($grad2,1) ?>;	
}

.sidebar-name, .sidebar-name h3,
#screen-meta a.show-settings,
#screen-meta-links a.show-settings,
.widget .widget-top, .postbox h3, .stuffbox h3,
#pop-options-cont .toggle-option h3.option-title  {
	text-shadow: 0 1px 1px <?php echo $this->get_rgba($font_shadow,0.8)?>;
	color: <?php echo $font_color?>;
}

.sidebar-name:hover, .sidebar-name h3:hover,
#screen-meta a.show-settings:hover,
#screen-meta-links a.show-settings:hover,
.widget .widget-top:hover, .postbox h3:hover, .stuffbox h3:hover {
	color: <?php echo $font_hover?>;
}

#pop-options-cont .toggle-option h3.option-title span.pop-right {
	color: <?php echo $this->get_rgba($this->get_color($font_color,5),0.6)?>;
}
<?php	
	}	
	
	function get_rgba($hex,$opacity='0.7'){
		$color = $this->HexToRGB($hex);
		return sprintf("rgba(%s, %s, %s, %s)",$color['r'],$color['g'],$color['b'],$opacity);
	}
	function HexToRGB($hex) {
		$hex = str_replace("#", "", $hex);
		$color = array();	 
		if(strlen($hex) == 3) {
			$color['r'] = hexdec(substr($hex, 0, 1) . $r);
			$color['g'] = hexdec(substr($hex, 1, 1) . $g);
			$color['b'] = hexdec(substr($hex, 2, 1) . $b);
		}
		else if(strlen($hex) == 6) {
			$color['r'] = hexdec(substr($hex, 0, 2));
			$color['g'] = hexdec(substr($hex, 2, 2));
			$color['b'] = hexdec(substr($hex, 4, 2));
		}
		return $color;
	}
	
	function css_link_wrap($grad2,$grad1,$intermediate){
		if(in_array(trim($grad2),array('','#')) or in_array(trim($grad1),array('','#')) or in_array(trim($intermediate),array('','#')))return;
?>
#screen-options-link-wrap,
#contextual-help-link-wrap {
	background-color: <?php echo $intermediate?>; /* Fallback */
	border-right: 1px solid transparent;
	border-left: 1px solid transparent;
	border-bottom: 1px solid transparent;
	background-image: -ms-linear-gradient(bottom, <?php echo $grad1; ?>,<?php echo $grad2; ?>); /* IE10 */
	background-image: -moz-linear-gradient(bottom, <?php echo $grad1; ?>,<?php echo $grad2; ?>); /* Firefox */
	background-image: -o-linear-gradient(bottom, <?php echo $grad1; ?>,<?php echo $grad2; ?>); /* Opera */
	background-image: -webkit-gradient(linear, left bottom, left top, from(<?php echo $grad1; ?>), to(<?php echo $grad2; ?>)); /* old Webkit */
	background-image: -webkit-linear-gradient(bottom, <?php echo $grad1; ?>,<?php echo $grad2; ?>); /* new Webkit */
	background-image: linear-gradient(bottom, <?php echo $grad1; ?>,<?php echo $grad2; ?>); /* proposed W3C Markup */
}
<?php	
	}
	
	function css_current_menu_arrow_wp33($grad1,$grad2){
		if(in_array(trim($grad2),array('','#')) or in_array(trim($grad1),array('','#')))return;
?>
#adminmenu .wp-menu-arrow div {
	background-color: <?php echo $grad1 ?>; /* Fallback */
	background-image: -ms-linear-gradient(right bottom, <?php echo $grad1 ?>, <?php echo $grad2 ?>); /* IE10 */
	background-image: -moz-linear-gradient(right bottom, <?php echo $grad1 ?>, <?php echo $grad2 ?>); /* Firefox */
	background-image: -o-linear-gradient(right bottom, <?php echo $grad1 ?>, <?php echo $grad2 ?>); /* Opera */
	background-image: -webkit-gradient(linear, right bottom, left top, from(<?php echo $grad1 ?>), to(<?php echo $grad2 ?>)); /* old Webkit */
	background-image: -webkit-linear-gradient(right bottom, <?php echo $grad1 ?>, <?php echo $grad2 ?>); /* new Webkit */
	background-image: linear-gradient(right bottom, <?php echo $grad1 ?>, <?php echo $grad2 ?>); /* proposed W3C Markup */
}

<?php	
	}
	
	function css_current_menu_arrow($cs_bg_color){
		if(in_array(trim($cs_bg_color),array('','#')))return;
		if(is_rtl())return $this->css_current_menu_arrow_rtl($cs_bg_color);
		if(!in_array(trim($cs_bg_color),array('','#'))):?>

#adminmenu .wp-menu-arrow div {background-image:url(<?php echo $this->url.'css/images/wlb-menu-arrow-frame.png'?>) !important;}
#adminmenu .wp-menu-arrow div:after {
	content:"";
	top:0px;
	display:block;
	position:absolute;	
	font-size: 0px; line-height: 0%; width: 0px;
	border-top: 15px solid <?php echo $cs_bg_color ?>;
	border-bottom: 15px solid <?php echo $cs_bg_color ?>;
	border-left: 8px solid transparent;
	border-right: none;	
	left:7px;
}
<?php		
		endif;
	}
	
	function css_current_menu_arrow_rtl($cs_bg_color){
		if(!in_array(trim($cs_bg_color),array('','#'))):?>

#adminmenu .wp-menu-arrow div {background-image:url(<?php echo $this->url.'css/images/wlb-menu-arrow-frame-rtl.png'?>) !important;}
#adminmenu .wp-menu-arrow div:after {
	content:"";
	top:0px;
	display:block;
	position:absolute;	
	font-size: 0px; line-height: 0%; width: 0px;
	border-top: 15px solid <?php echo $cs_bg_color ?>;
	border-bottom: 15px solid <?php echo $cs_bg_color ?>;
	border-right: 8px solid transparent;
	border-left: none;	
	right:7px;
}
<?php		
		endif;
	}
	
	function css_the_editor($border_color, $grad1, $grad2, $intermediate, $content_bg, $footer_bg){
		if(in_array(trim($grad1),array('','#')))return;
?>
#quicktags,
.wp_themeSkin tr.mceFirst td.mceToolbar {
	background-image: none;
	border-color: <?php echo $border_color?>;
	background-color:<?php echo $intermediate?>;
	background-image:-ms-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:-moz-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:-o-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:-webkit-gradient(linear,left bottom,left top,from(<?php echo $grad1; ?>),to(<?php echo $grad2; ?>));
	background-image:-webkit-linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);
	background-image:linear-gradient(bottom,<?php echo $grad1; ?>,<?php echo $grad2; ?>);	
}
#editorcontainer,
.wp_themeSkin table.mceLayout {
    border-color: <?php echo $border_color?>;
}
#post-status-info {
	border-color: <?php echo $border_color ?>;
	background-color: <?PHP echo $footer_bg ?>;
}
.wp_themeSkin .mceStatusbar {
	background-color: <?php echo $content_bg?>;
}
#poststuff .wp_themeSkin .mceStatusbar {
	border-color: <?php echo $border_color?>;
}
#poststuff #editor-toolbar .active {
    background-color: <?php echo $grad2?>;
    border-color: <?php echo $border_color?> <?php echo $border_color?> <?php echo $grad2?>;
}
<?php 
	$inactive_bg_color = $this->get_color($grad2,2);
	$inactive_border = $this->get_color($inactive_bg_color,-3);
?>
#poststuff #edButtonPreview, #poststuff #edButtonHTML {
    background-color: <?php echo $inactive_bg_color?>;
    border-color: <?php echo $inactive_border?> <?php echo $inactive_border?> <?php echo $inactive_bg_color?>;
}
<?php	
	}
	
	function css_primary_button($grad1,$grad2,$border,$font,$h_border,$h_font,$disabled_bg,$disabled_border,$disabled_color){
		if(in_array(trim($grad1),array('','#')))return;
?>
.wp-core-ui .button-primary-disabled, .wp-core-ui .button-primary[disabled], .wp-core-ui .button-primary:disabled {
	background: none repeat scroll 0 0 <?php echo $disabled_bg?> !important;
	color:<?php echo $disabled_color?> !important;
	border-color:<?php echo $disabled_border?> !important;
}
.wp-core-ui input.button-primary:hover, .wp-core-ui button.button-primary:hover, .wp-core-ui a.button-primary:hover {
	color: <?Php echo $h_font?>;
	border-color:<?php echo $h_border?>;

	background-color: <?php echo $grad1 ?>;
	background:-moz-linear-gradient(top,<?php echo $grad1 ?>, <?php echo $grad2 ?>);
	background:-webkit-gradient(linear, left top, left bottom, from(<?php echo $grad1 ?>), to(<?php echo $grad2 ?>));
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $grad1 ?>', EndColorStr='<?php echo $grad2 ?>');
	
	box-shadow: 0 1px 0 <?php echo $grad1 ?> inset;
}
.wp-core-ui input.button-primary:active, .wp-core-ui button.button-primary:active, .wp-core-ui a.button-primary:active,
.wp-core-ui input.button-primary, .wp-core-ui button.button-primary, .wp-core-ui a.button-primary {
	white-space: nowrap;
	line-height:1em;
	position:relative;
	outline: none;
	overflow: visible;
	cursor: pointer;
	color:<?php echo $font?>;
	border: 1px solid <?php echo $border; ?>;
	border-bottom:rgba(0, 0, 0, .4) 1px solid;
	-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
	-moz-box-shadow: 0 1px 2px rgba(0,0,0,.2);
	box-shadow: 0 1px 2px rgba(0,0,0,.2);

	background-color: <?php echo $grad1 ?>;
	background:-moz-linear-gradient(top,<?php echo $grad1 ?>, <?php echo $grad2 ?>);
	background:-webkit-gradient(linear, left top, left bottom, from(<?php echo $grad1 ?>), to(<?php echo $grad2 ?>));
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $grad1 ?>', EndColorStr='<?php echo $grad2 ?>');
	
	-moz-user-select: none;
	-webkit-user-select:none;
	-khtml-user-select: none;
	user-select: none;
}
<?php	
	}
	
	function css_secondary_button($grad1,$grad2,$border,$font,$shadow,$h_border,$h_font,$disabled_bg,$disabled_border,$disabled_color){
		if(in_array(trim($grad1),array('','#')))return;
?>
.wp-core-ui a.button, .wp-core-ui a.button-secondary {	color: <?php echo $font?>;}
.wp-core-ui a.button:hover, .wp-core-ui a.button-secondary:hover {color: <?php echo $h_font?>;}
.wp-core-ui .button, .wp-core-ui .submit input, .wp-core-ui .button-secondary {text-shadow:0 1px 1px <?php echo $this->get_rgba($shadow,0.7)?>;}
.wp-core-ui .button, .wp-core-ui .submit input, .wp-core-ui .button-secondary
.wp-core-ui .button-disabled, .wp-core-ui .button-secondary[disabled], .wp-core-ui .button-secondary-disabled {
	background: none repeat scroll 0 0 <?php echo $disabled_bg?> !important;
	color:<?php echo $disabled_color?> !important;
	border-color:<?php echo $disabled_border?> !important;
}
.wp-core-ui .button:hover, .wp-core-ui .submit input:hover, .wp-core-ui .button-secondary:hover, .wp-core-ui input[type="button"]:hover, .wp-core-ui input[type="submit"]:hover {
	color: <?Php echo $h_font?>;
	border-color:<?php echo $h_border?>;

	background-color: <?php echo $grad1 ?>;
	background:-moz-linear-gradient(top,<?php echo $grad1 ?>, <?php echo $grad2 ?>);
	background:-webkit-gradient(linear, left top, left bottom, from(<?php echo $grad1 ?>), to(<?php echo $grad2 ?>));
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $grad1 ?>', EndColorStr='<?php echo $grad2 ?>');
}
.wp-core-ui .button:active, .wp-core-ui .submit input:active, .wp-core-ui .button-secondary:active,
.wp-core-ui .button, .wp-core-ui .submit input, .wp-core-ui .button-secondary, .wp-core-ui input[type="button"], .wp-core-ui input[type="submit"] {
	color:<?php echo $font?>;
	border-color:<?PHP echo $border?>;
	background-color: <?php echo $grad1 ?>;
	background:-moz-linear-gradient(top,<?php echo $grad1 ?>, <?php echo $grad2 ?>);
	background:-webkit-gradient(linear, left top, left bottom, from(<?php echo $grad1 ?>), to(<?php echo $grad2 ?>));
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $grad1 ?>', EndColorStr='<?php echo $grad2 ?>');
	
	-moz-user-select: none;
	-webkit-user-select:none;
	-khtml-user-select: none;
	user-select: none;
}
<?php	
	}
		
	function get_user_option_admin_color($result, $option, $user){
		global $wlb_plugin;
		if(1==$wlb_plugin->get_option('force_color_scheme')){
			$result = $wlb_plugin->get_option('admin_color','fresh');
		}	
		return $result;
	}
	
	function personal_options(){
		global $wlb_plugin;
		if(1==$wlb_plugin->get_option('force_color_scheme')){
			remove_all_actions('admin_color_scheme_picker');
		}		
	}
	
	function wlb_options($t,$for_admin=true){
		$i = count($t);
		//-----
		global $wlb_plugin;

		$i = count($t);
		@$t[$i]->id 			= 'color_scheme'; 
		$t[$i]->label 		= __('WordPress Admin Color Scheme','wlb');//title on tab
		$t[$i]->right_label	= __('Customize Color Schemes','wlb');//title on tab
		$t[$i]->page_title	= __('WordPress Admin Color Scheme','wlb');//title on content
		//$t[$i]->open =true;
		$t[$i]->options = array();
		
		$t[$i]->options[] = (object)array(
				'type'=>'subtitle',
				'label'=>__('Admin Color Scheme','wlb')	
			);			
		
		$t[$i]->options[] =	(object)array(
				'id'		=> 'force_color_scheme',
				'label'		=> __('Customize Color Scheme','wlb'),
				'type'		=> 'yesno',
				'description'=> __('Choose yes if you want to apply the same Admin Color Scheme to all your users.  You can also customize the colors.  This will also hide the option to choose a Color Scheme on the Profile Screen.','wlb'),
				'el_properties'	=> array(),
				'hidegroup'	=> '#force_color_scheme_group',
				'save_option'=>true,
				'load_option'=>true
				);	
			
		$t[$i]->options[] =	(object)array(
				'type'=>'clear'
			);		
		$t[$i]->options[] =	(object)array(
				'id'	=> 'force_color_scheme_group',
				'type'=>'div_start'
			);
			
		$t[$i]->options[] =	(object)array(
				'id'		=> 'disable_auto_color_scheme',
				'label'		=> __('Disable automatic color scheme','wlb'),
				'type'		=> 'yesno',
				'description'=> __('By default if you do not fill certain colors, they are automatically generated using variations of colors you have filled in.  Choose yes if you do not want automatic colors to be generated.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);	
		
		$t[$i]->options[] =	(object)array(
				'type'	=> 'description',
				'description' => __('Choose a Color Scheme to use as base for the Color Customization.  This determines the icons set to be used.','wlb')
			);
						
		$t[$i]->options[] =	(object)array(
				'type'=>'callback',
				'callback'=>array(&$this,'choose_color_scheme')
			);	
			
		foreach($this->color_scheme() as $id => $r){
			if(property_exists($r,'subtitle')){
				$t[$i]->options[]=(object)array('label'=>__('Save Changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' );
				$t[$i]->options[] = (object)array(
					'type'	=> 'clear'
				);
				$t[$i]->options[] = (object)array(
					'type'	=> 'subtitle',
					'label'	=> $r->subtitle
				);
			}
			
			if(property_exists($r,'preview')){
				$t[$i]->options[] = $r->preview;
			}
			
			$t[$i]->options[] =	(object)array(
					'id'		=> $id,
					'label'		=> $r->label,
					'type'		=> 'farbtastic',
					'description'=> @$r->description,
					'el_properties'	=> array(),
					'save_option'=>true,
					'load_option'=>true
					);			
		}
		
		$t[$i]->options[] = (object)array(
				'type'=>'clear'
			);	
				
		$t[$i]->options[] =	(object)array(
				'type'=>'div_end'
			);		
		$t[$i]->options[] = (object)array(
				'type'=>'clear'
			);	
							
		$t[$i]->options[]=(object)array('label'=>__('Save Changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' );
		//-------------------------------------------
		$i = count($t);
		@$t[$i]->id 			= 'menu_branding'; 
		$t[$i]->label 		= __('Saved and downloaded color schemes','wlb');//title on tab
		$t[$i]->right_label = __('Restore saved or downloaded settings','wlb');
		$t[$i]->page_title	= __('Saved and downloaded color schemes','wlb');//title on content
		//$t[$i]->open = true;		
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Backup color scheme','wlb')	
			),
			(object)array(
				'id'			=> 'branding-save-btn',
				'type'			=>'save_settings',
				'label'			=>__('Brief description','wlb'),
				'export_fields'	=> array('cs_url_css', 'cs_url_css_external', 'force_color_scheme', 'cs_menu_main_color', 'cs_menu_link_color', 'cs_menu_link_color_hover', 'cs_menu_current_font_color', 'cs_menu_current_bg2', 'cs_menu_current_bg1', 'cs_submenu_bg_color', 'cs_submenu_link_color', 'cs_submenu_link_color_hover', 'cs_submenu_hover_color', 'cs_submenu_current_font_color', 'cs_bg_color', 'cs_content_color', 'cs_content_shadow', 'cs_content_link_color', 'cs_content_link_color_hover', 'cs_header_grad1', 'cs_header_grad2', 'cs_header_font_color', 'cs_header_font_hover', 'cs_header_font_shadow_color', 'cs_widget_body_grad1', 'cs_widget_body_grad2', 'cs_widget_border_color', 'cs_widefat_td', 'cs_widefat_th', 'cs_widefat_th_shadow', 'cs_widefat_row_color', 'cs_widefat_alt_row_color', 'cs_widefat_border_color', 'cs_link_wrap_grad1', 'cs_link_wrap_grad2', 'cs_screen_meta_bg', 'cs_screen_meta_border', 'cs_header_border', 'cs_up_nag_bg', 'cs_up_nag_border', 'cs_up_nag_font', 'cs_msg_upd_bg', 'cs_msg_upd_border', 'cs_msg_upd_color', 'cs_msg_err_bg', 'cs_msg_err_border', 'cs_msg_err_color', 'cs_input_bg', 'cs_input_border', 'cs_primary_btn_grad1', 'cs_primary_btn_grad2', 'cs_primary_btn_border', 'cs_primary_btn_font', 'cs_primary_btn_hborder', 'cs_primary_btn_hfont', 'cs_primary_btn_disabled_bg', 'cs_primary_btn_disabled_border', 'cs_primary_btn_disabled_color', 'cs_secondary_btn_grad1', 'cs_secondary_btn_grad2', 'cs_secondary_btn_border', 'cs_secondary_btn_font', 'cs_secondary_btn_font_shadow', 'cs_secondary_btn_hborder', 'cs_secondary_btn_hfont', 'cs_secondary_btn_disabled_bg', 'cs_secondary_btn_disabled_border', 'cs_secondary_btn_disabled_color', 'cs_edit_post', 'cs_edit_title_bg', 'cs_edit_title_border', 'cs_custom_field_h', 'cs_custom_field_hb', 'cs_custom_field_c', 'cs_custom_field_cb', 'cs_editor_grad1', 'cs_editor_grad2', 'cs_editor_border', 'cs_editor_footer', 'cs_editor_content'),
				'description'	=> __('This will save a copy of the color scheme settings.<br />Please observe that it only backups saved settings; any changes you made after saving will be lost.','wlb'),
				'button_label'	=> __('Backup current settings','wlb')	
			),
			(object)array(
				'type'=>'clear'
			),
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Stored Color Scheme Settings','wlb')	
			),
			(object)array(
				'id'		=> 'popex-list-branding',
				'type'		=>'saved_settings_list'	
			),
			(object)array(
				'type'=>'clear'
			)
		);
		//----------------
		$i = count($t);
		@$t[$i]->id 			= 'admin_resources'; 
		$t[$i]->label 		= __('Resources','wlb');//title on tab
		$t[$i]->right_label	= __('Icons and Images','wlb');//title on tab
		$t[$i]->page_title	= __('Resources','wlb');//title on content
		//$t[$i]->open =true;
		$t[$i]->options = array(
			(object)array(
				'id'	=> "cs_url_css",
				'type'	=> "select",
				'label'	=> "Installed Icon Sets",
				'hidegroup'	=> '#icon_sets_group',
				'hidevalues'=> array(''),
				'options'=> $this->get_icon_sets(array(''=>'--none--')),
				'save_option'=>true,
				'load_option'=>true				
			),
			(object)array(
				'id'	=> 'icon_sets_group',
				'type'=>'div_start'
			),			
			(object)array(
				'id'	=> "cs_url_css_external",
				'type'	=> "text",
				'label'	=> "Icon Sets, External Stylesheet URL",
				'el_properties'=>array('class'=>'widefat'),
				'save_option'=>true,
				'load_option'=>true				
			),
			(object)array(
				'type'=>'div_end'
			)		
		);
		$t[$i]->options[]=(object)array('label'=>__('Save Changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' );
		//--------------------------------------------				
		//----
		return $t;
	}	
//-------xxxxxxxxxxxxxx	
	function pop_handle_save($pop){
		global $wlb_plugin;
		if($wlb_plugin->options_varname!=$pop->options_varname)return;
		$existing_options = get_option($pop->options_varname);
		$existing_options = is_array($existing_options)?$existing_options:array();
		$existing_options['admin_color'] = isset($_REQUEST['admin_color'])?$_REQUEST['admin_color']:'fresh';
		update_option($pop->options_varname,$existing_options);
	}
	
	function choose_color_scheme(){
		global $wlb_plugin;
		$current_color = $wlb_plugin->get_option('admin_color','fresh');
		ob_start();
?>
<div class="pt-option admin-menu-branding">
	<span class="pt-label"><?php _e('Color Scheme','wlb')?></span>
	<?php $this->_choose_color_scheme($current_color); ?><div class="clear"></div>	
</div>
<?php	
		$output=ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	function _choose_color_scheme($current_color){
	//based on wp core
	global $_wp_admin_css_colors, $user_id; 
	if(empty($_wp_admin_css_colors)){
		return '<input type="hidden" name="admin_color" value="classic" />';
	}
	?>
<fieldset class="holder-color-scheme"><legend class="screen-reader-text"><span><?php _e('Admin Color Scheme')?></span></legend>
<?php
if ( empty($current_color) )
	$current_color = 'fresh';
foreach ( $_wp_admin_css_colors as $color => $color_info ): ?>
<div class="color-option-wp38">
	<div class="pt-col pt-col-1">
		<input name="admin_color" id="admin_color_<?php echo $color; ?>" type="radio" value="<?php echo esc_attr($color) ?>" class="tog" <?php checked($color, $current_color); ?> />
	</div>
	
	<div class="pt-col pt-col-2">
		<table class="color-palette">
		<tr>
		<?php foreach ( $color_info->colors as $html_color ): ?>
		<td style="background-color: <?php echo $html_color ?>" title="<?php echo $color ?>">&nbsp;</td>
		<?php endforeach; ?>
		</tr>
		</table>	
	</div>

	<div class="pt-col pt-col-3">
		<label for="admin_color_<?php echo $color; ?>"><?php echo $color_info->name ?></label>
	</div>
	
</div>
	<?php endforeach; ?>
</fieldset>
<?php	
	}
}

?>