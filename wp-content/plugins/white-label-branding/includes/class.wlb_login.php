<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class wlb_login {
	function __construct( $url ){
		$this->url = $url;
		
		global $wlb_plugin;
		$this->id = $wlb_plugin->id.'-log';
		
		add_filter("pop-options_{$this->id}",array(&$this,'wlb_options'),10,1);	
		add_action('login_head', array(&$this,'login_head'));
	}
	
	function wlb_options($t){
		$i = count($t);
		//-----
		$i = count($t);
		@$t[$i]->id 			= 'login_screen'; 
		$t[$i]->label 		= __('Login Screen Customization','wlb');//title on tab
		$t[$i]->right_label = __('Customize Login Logo, Background and html template','wlb');
		$t[$i]->page_title	= __('Branding','wlb');//title on content
		$t[$i]->open = false;
		$t[$i]->options = array(	
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Default Login Screen Customization','wlb')	
			),
			(object)array(
				'id'		=> 'use_login_screen_customization',
				'label'		=> __('Enable login screen customization','wlb'),
				'type'		=> 'yesno',
				'description'=>  sprintf(__('Choose %s to customize the login screen','wlb'),__('Yes','wlb')),
				'el_properties'	=> array(),
				'hidegroup'	=> '#login_screen_group',
				'save_option'=>true,
				'load_option'=>true
				),		
			(object)array('type'	=> 'clear'),
			(object)array(
				'id'	=> 'login_screen_group',
				'type'=>'div_start'
			),							
			(object)array(
				'id'	=> 'login_logo_url',
				'type'	=>'fileuploader',
				'dcurl'	=> $this->get_login_dc_url_path(),
				'label'=>__('Login Logo URL','wlb'),
				'description'=> __('URL to the login logo. The standard size logo is 300 px wide and 80 px tall, but you can use any size you want. ','wlb'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'=>'clear'),
			(object)array(
				'id'	=> 'login_logo_opacity',
				'type'	=>'range',
				'label'	=> __('Logo Opacity','wlb'),
				'min'	=> 0,
				'max'	=> 1,
				'step'	=> 0.01,
				'default'=>1,
				'save_option'=>true,
				'load_option'=>true
			),				
			(object)array(
				'id'	=> 'login_background',
				'type'=>'fileuploader',
				'dcurl'	=> $this->get_login_dc_url_path(),
				'label'=>__('Login Background URL','wlb'),
				'description'=> __('URL to an image you want to use as login background','wlb'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'=>'clear'),
			(object)array(
				'id'	=> 'login_background_attachments',
				'type'	=> 'select',
				'label'	=> __('background-attachments','wlb'),
				'el_properties' => array(),
				'options'=>array('scroll'=>'scroll','fixed'=>'fixed','inherit'=>'inherit'),
				'value'=>'fixed',
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'login_background_color',
				'type'	=> 'select',
				'label'	=> __('background-color','wlb'),
				'el_properties' => array(),
				'options'=>array(''=>'color code','transparent'=>'transparent','inherit'=>'inherit'),
				'value'=>'transparent',
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'login_background_color_code',
				'type'	=> 'farbtastic',
				'label'	=> __('background-color code','wlb'),
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'type'	=> 'clear'
			),
			
			(object)array(
				'id'	=> 'login_background_position',
				'type'	=> 'select',
				'label'	=> __('background-position','wlb'),
				'el_properties' => array(),
				'hidegroup'	=>'#login_background_group',
				'hidevalues'=>array(''),
				'options'	=>array(
					'left top' 		=> 'left top',
					'left center'	=> 'left center',
					'left bottom'	=> 'left bottom',
					'right top'		=> 'right top',
					'right center'	=> 'right center',
					'right bottom'=>'right bottom',
					'center top'=>'center top',
					'center center'=>'center center',
					'center bottom'=>'center bottom',
					''=>'xpos ypos',
					'inherit'=>'inherit'
				),
				'value'=>'center top',
				'save_option'=>true,
				'load_option'=>true
			),
			
			(object)array('type'=>'clear'),
			(object)array(
				'id'	=> 'login_background_group',
				'type'=>'div_start'
			),				
			(object)array(
				'id'	=> 'login_background_x',
				'type'	=> 'range',
				'label'	=> __('background-x(xpos)','wlb'),
				'min'	=> -9999,
				'max'	=> 9999,
				'step'	=> 1,
				'default'=> 0,
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'login_background_y',
				'type'	=> 'range',
				'label'	=> __('background-y(ypos)','wlb'),
				'min'	=> -9999,
				'max'	=> 9999,
				'step'	=> 1,
				'default'=> 0,
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array(
				'type'=>'div_end'
			),	
			(object)array(
				'id'	=> 'login_background_repeat',
				'type'	=> 'select',
				'label'	=> __('background-repeat','wlb'),
				'el_properties' => array(),
				'options'=>array('repeat'=>'repeat','repeat-x'=>'repeat-x','repeat-y'=>'repeat-y','no-repeat'=>'no-repeat','inherit'=>'inherit'),
				'value'=>'repeat',
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'type'=>'div_end'
			)										
		);
		$t[$i]->options[]=(object)array('label'=>__('Save changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=>'' );			
		//-----
		//-----
		$i = count($t);
		@$t[$i]->id 			= 'login_form'; 
		$t[$i]->label 		= __('Login Form Customization','wlb');//title on tab
		$t[$i]->right_label = __('Customize the login form css','wlb');
		$t[$i]->page_title	= __('Login Form Customization','wlb');//title on content
		//$t[$i]->open = false;
		$t[$i]->options = array(

			(object)array(
				'id'		=> 'lf_use_login_form',
				'label'		=> __('Customize Login Form','wlb'),
				'type'		=> 'yesno',
				'description'=>  sprintf(__('Choose %s to customize the login form','wlb'),__('Yes','wlb')),
				'el_properties'	=> array(),
				'hidegroup'	=> '#login_form_group',
				'save_option'=>true,
				'load_option'=>true
				),		
			(object)array('type'=>'clear'),
			(object)array(
				'id'	=> 'login_form_group',
				'type'=>'div_start'
			),	
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Login Form Positioning','wlb')
			),	
			(object)array(
				'id'	=> 'lf_x',
				'type'	=> 'range',
				'label'	=> __('Horizontal','wlb'),
				'min'	=> -999,
				'max'	=> 999,
				'step'	=> 1,
				'default'=> 0,
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'lf_y',
				'type'	=> 'range',
				'label'	=> __('Vertical','wlb'),
				'min'	=> -999,
				'max'	=> 999,
				'step'	=> 1,
				'default'=> 0,
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Login Form Background','wlb')
			),								
			(object)array(
				'id'	=> 'lf_bg',
				'type'=>'fileuploader',
				'dcurl'	=> $this->get_login_dc_url_path(),
				'label'=>__('Image URL','wlb'),
				'description'=> __('URL to an image you want to use as login form background','wlb'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'=>'clear'),
			(object)array(
				'id'	=> 'lf_bg_attachments',
				'type'	=> 'select',
				'label'	=> __('background-attachments','wlb'),
				'el_properties' => array(),
				'options'=>array('scroll'=>'scroll','fixed'=>'fixed','inherit'=>'inherit'),
				'value'=>'fixed',
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'lf_bg_color',
				'type'	=> 'select',
				'label'	=> __('background-color','wlb'),
				'el_properties' => array(),
				'options'=>array(''=>'color code','transparent'=>'transparent','inherit'=>'inherit'),
				'value'=>'transparent',
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'lf_bg_color_code',
				'type'	=> 'farbtastic',
				'label'	=> __('background-color code','wlb'),
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'lf_bg_position',
				'type'	=> 'select',
				'label'	=> __('background-position','wlb'),
				'el_properties' => array(),
				'hidegroup'=>'#lf_bg_group',
				'hidevalues'=>array(''),
				'options'=>array(
					'left top' 		=> 'left top',
					'left center'	=> 'left center',
					'left bottom'	=> 'left bottom',
					'right top'		=> 'right top',
					'right center'	=> 'right center',
					'right bottom'=>'right bottom',
					'center top'=>'center top',
					'center center'=>'center center',
					'center bottom'=>'center bottom',
					''=>'xpos ypos',
					'inherit'=>'inherit'
				),
				'value'=>'center top',
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'=>'clear'),
			(object)array(
				'id'	=> 'lf_bg_group',
				'type'=>'div_start'
			),				
			(object)array(
				'id'	=> 'lf_bg_x',
				'type'	=> 'range',
				'label'	=> __('background-x(xpos)','wlb'),
				'min'	=> -999,
				'max'	=> 999,
				'step'	=> 1,
				'default'=> 0,
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'lf_bg_y',
				'type'	=> 'range',
				'label'	=> __('background-y(ypos)','wlb'),
				'min'	=> -999,
				'max'	=> 999,
				'step'	=> 1,
				'default'=> 0,
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'=>'div_end'),					
			(object)array(
				'id'	=> 'lf_bg_repeat',
				'type'	=> 'select',
				'label'	=> __('background-repeat','wlb'),
				'el_properties' => array(),
				'options'=>array('repeat'=>'repeat','repeat-x'=>'repeat-x','repeat-y'=>'repeat-y','no-repeat'=>'no-repeat','inherit'=>'inherit'),
				'value'=>'repeat',
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'lf_opacity',
				'type'	=>'range',
				'label'	=> __('Opacity','wlb'),
				'min'	=> 0,
				'max'	=> 1,
				'step'	=> 0.01,
				'default'=>1,
				'save_option'=>true,
				'load_option'=>true
			),				
//--- LABLES			
			(object)array('type'=>'clear'),
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Labels','wlb')
			),	
			(object)array(
				'id'	=> 'lf_label_color',
				'type'	=> 'farbtastic',
				'label'	=> __('Color','wlb'),
				'default'=>'#777777',
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),					
			(object)array(
				'id'	=> 'lf_label_size',
				'type'	=>'range',
				'label'	=> __('Font size','wlb'),
				'min'	=> 6,
				'max'	=> 30,
				'step'	=> 1,
				'default'=>14,
				'save_option'=>true,
				'load_option'=>true
			),	
//--- BORDER			
			(object)array('type'=>'clear'),
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Border','wlb')
			),	
			(object)array(
				'id'	=> 'lf_border_width',
				'type'	=>'range',
				'label'	=> __('Width','wlb'),
				'min'	=> 0,
				'max'	=> 25,
				'step'	=> 1,
				'default'=>1,
				'save_option'=>true,
				'load_option'=>true
			),		
			(object)array(
				'id'	=> 'lf_border_style',
				'type'	=> 'select',
				'label'	=> __('Style','wlb'),
				'el_properties' => array(),
				'options'=>array(''=>'none','solid'=>'solid','hidden'=>'hidden','dotted'=>'dotted','dashed'=>'dashed','double'=>'double','groove'=>'groove','ridge'=>'ridge','inset'=>'inset','outset'=>'outset','inherit'=>'inherit'),
				'value'=>'solid',
				'default'=>'solid',
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array(
				'id'	=> 'lf_border_color',
				'type'	=> 'farbtastic',
				'label'	=> __('Color','wlb'),
				'default'=>'#E5E5E5',
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),		
			(object)array('type'=>'clear'),											
//Corner radius		
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Corner Radius','wlb')
			),	
				
			(object)array(
				'type'=>'preview',
				'path'=>$this->url.'images/preview/login_form/',
				'items'=>array(
					(object)array(
						'src'=> 'lf_tl_radius.png',
						'focus_target'=>'#lf_tl_radius',
						'label'=>'',
						'description'=>''
					),
					(object)array(
						'src'=>'lf_tr_radius.png',
						'focus_target'=>'#lf_tr_radius',
						'label'=>'',
						'description'=>''
					),
					(object)array(
						'src'=>'lf_br_radius.png',
						'focus_target'=>'#lf_br_radius',
						'label'=>'',
						'description'=>''
					),
					(object)array(
						'src'=>'lf_bl_radius.png',
						'focus_target'=>'#lf_bl_radius',
						'label'=>'',
						'description'=>''
					)
					
					//lf_br_radius
				)
			),

//			(object)array(
//				'description'=>'this is a description'
//			),		
			(object)array(
				'id'	=> 'lf_tl_radius',
				'type'	=>'range',
				'label'	=> __('Top Left','wlb'),
				'default'=>3,
				'min'	=> 0,
				'max'	=> 250,
				'step'	=> 1,
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array(
				'id'	=> 'lf_tr_radius',
				'type'	=>'range',
				'label'	=> __('Top Right','wlb'),
				'default'=>3,
				'min'	=> 0,
				'max'	=> 250,
				'step'	=> 1,
				'save_option'=>true,
				'load_option'=>true
			),		
			(object)array(
				'id'	=> 'lf_br_radius',
				'type'	=>'range',
				'label'	=> __('Bottom Right','wlb'),
				'default'=>3,
				'min'	=> 0,
				'max'	=> 250,
				'step'	=> 1,
				'save_option'=>true,
				'load_option'=>true
			),	
			(object)array(
				'id'	=> 'lf_bl_radius',
				'type'	=>'range',
				'label'	=> __('Bottom Left','wlb'),
				'default'=>3,
				'min'	=> 0,
				'max'	=> 250,
				'step'	=> 1,
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'=>'clear'),
//form shadow			
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Form Shadow','wlb')
			),
			
			(object)array(
				'type'=>'preview',
				'path'=>$this->url.'images/preview/login_form/',
				'items'=>array(
					(object)array(
						'src'=> 'lf_shadow_color.png',
						'focus_target'=>'#lf_shadow_color'
					),
					(object)array(
						'src'=> 'lf_shadow_x.png',
						'focus_target'=>'#lf_shadow_x'
					),
					(object)array(
						'src'=> 'lf_shadow_y.png',
						'focus_target'=>'#lf_shadow_y'
					),
					(object)array(
						'src'=> 'lf_shadow_blur.png',
						'focus_target'=>'#lf_shadow_blur'
					),
					(object)array(
						'src'=> 'lf_shadow_size.png',
						'focus_target'=>'#lf_shadow_size'
					)					
				)
			),			
			
			(object)array(
				'id'	=> 'lf_shadow_color',
				'type'	=> 'farbtastic',
				'label'	=> __('Color','wlb'),
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'lf_shadow_x',
				'type'	=>'range',
				'label'	=> __('Horizontal Position','wlb'),
				'default'=>0,
				'min'	=> -100,
				'max'	=> 100,
				'step'	=> 1,
				'save_option'=>true,
				'load_option'=>true
			),					
			(object)array(
				'id'	=> 'lf_shadow_y',
				'type'	=>'range',
				'label'	=> __('Vertical Position','wlb'),
				'default'=>4,
				'min'	=> -100,
				'max'	=> 100,
				'step'	=> 1,
				'save_option'=>true,
				'load_option'=>true
			),					
			(object)array(
				'id'	=> 'lf_shadow_blur',
				'type'	=>'range',
				'label'	=> __('Blur','wlb'),
				'default'=>10,
				'min'	=> 0,
				'max'	=> 100,
				'step'	=> 1,
				'save_option'=>true,
				'load_option'=>true
			),					
			(object)array(
				'id'	=> 'lf_shadow_size',
				'type'	=>'range',
				'label'	=> __('Size','wlb'),
				'min'	=> -100,
				'max'	=> 100,
				'step'	=> 1,
				'default'=> -1,
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'=>'clear'),
//INPUT			
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Input text fields','wlb')
			),		
			(object)array(
				'type'=>'preview',
				'path'=>$this->url.'images/preview/login_form/',
				'items'=>array(
					(object)array(
						'src'=> 'lf_input_bg_color.png',
						'focus_target'=>'#lf_input_bg_color'
					),
					(object)array(
						'src'=> 'lf_input_border_color.png',
						'focus_target'=>'#lf_input_border_color'
					)	,
					(object)array(
						'src'=> 'lf_input_shadow_color.png',
						'focus_target'=>'#lf_input_shadow_color'
					),
					(object)array(
						'src'=> 'lf_input_shadow_opacity.png',
						'focus_target'=>'#lf_input_shadow_opacity'
					)				
				)
			),				
			(object)array(
				'id'	=> 'lf_input_bg_color',
				'type'	=> 'farbtastic',
				'label'	=> __('Background Color','wlb'),
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),		
			(object)array(
				'id'	=> 'lf_input_border_color',
				'type'	=> 'farbtastic',
				'label'	=> __('Border Color','wlb'),
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),		
			(object)array(
				'id'	=> 'lf_input_shadow_color',
				'type'	=> 'farbtastic',
				'label'	=> __('Shadow Color','wlb'),
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'lf_input_shadow_opacity',
				'type'	=> 'range',
				'label'	=> __('Shadow Opacity','wlb'),
				'min' => 0,
				'max' => 1,
				'step'=>0.01,
				'default'=>0.2,
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'=>'clear'),
//submit button			
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Submit Button','wlb')
			),	
			(object)array(
				'type'=>'preview',
				'path'=>$this->url.'images/preview/login_form/',
				'items'=>array(
					(object)array(
						'src'=> 'lf_submit_grad1.png',
						'focus_target'=>'#lf_submit_grad1'
					),
					(object)array(
						'src'=> 'lf_submit_color.png',
						'focus_target'=>'#lf_submit_color'
					)			
				)
			),				
			(object)array(
				'id'	=> 'lf_submit_grad1',
				'type'	=> 'farbtastic',
				'label'	=> __('Button Color','wlb'),
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),						
			(object)array(
				'id'	=> 'lf_submit_color',
				'type'	=> 'farbtastic',
				'label'	=> __('Font Color','wlb'),
				//'el_properties' => array('size'=>20),
				'save_option'=>true,
				'load_option'=>true
			),		
			
			(object)array(
				'type'=>'div_end'
			)													
		);			
		$t[$i]->options[] = (object)array(
				'type'=>'clear'
			);
		$t[$i]->options[]=(object)array('label'=>__('Save changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=>'' );
		//-----

		$i = count($t);
		@$t[$i]->id 			= 'login_advanced'; 
		$t[$i]->label 		= __('Advanced','wlb');//title on tab
		$t[$i]->right_label = __('Set an Alternative Login Template','wlb');
		$t[$i]->page_title	= __('Advanced Login Template Settings','wlb');//title on content
		//$t[$i]->open = false;		
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Alternative Login Template','wlb')	
			),
			(object)array(
				'id'		=> 'use_login_template',
				'label'		=> __('Use Login Template','wlb'),
				'type'		=> 'yesno',
				'description'=>  sprintf(__('Choose %s to activate the Custom Login HTML Template and CSS','wlb'),__('Yes','wlb')),
				'el_properties'	=> array(),
				'hidegroup'	=> '#login_template_group',
				'save_option'=>true,
				'load_option'=>true
				),
			(object)array(
				'type'=>'clear'
			),	
			(object)array(
				'id'	=> 'login_template_group',
				'type'=>'div_start'
			),				
			(object)array(
				'id'	=> 'login_template',
				'type'=>'textarea',
				'label'=>__('Default Login HTML Template','wlb'),
				'description'=> __('Optionally provide an html template to display instead of the default login html.  It is required that you include the following tags in the template: {loginform}: where you want the login form to occur, and {backlink}: where you want the back link to be displayed. {customlogo}:Shows the custom logo.','wlb'),
				'el_properties' => array('cols'=>'50','rows'=>'10'),
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Smaller Windows, Alternative Login Template','wlb')	
			),			
			(object)array(
				'id'	=> 'trigger_login_template_small',
				'type'=>'range',
				'label'=>__('Small template trigger width','wlb'),
				'description'=> __('If you define a Small Login HTML Template, this width determines when the template is applied.','wlb'),
				'min'=>0,
				'max'=>1024,
				'step'=>1,
				'default'=>480,	
				'save_option'=>true,
				'load_option'=>true
			),	
			(object)array(
				'id'	=> 'login_template_small',
				'type'=>'textarea',
				'label'=>__('Small Login HTML Template','wlb'),
				'description'=> __('Optionally provide an html template for smaller size windows, to display instead of the default login html.  It is required that you include the following tags in the template: {loginform}: where you want the login form to occur, and {backlink}: where you want the back link to be displayed. {customlogo}:Shows the custom logo.','wlb'),
				'el_properties' => array('cols'=>'50','rows'=>'10'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Stylesheet','wlb')	
			),				
			(object)array(
				'id'	=> 'login_styles_scripts',
				'type'=>'textarea',
				'label'=>__('Login CSS template','wlb'),
				'description'=> sprintf("<p>%s</p><p><b>%s</b>%s</p>",
					__('This is an optional free space so you can write css or javascript to be output on the login header, either for your html template or to modify the styles of the default login screen.','wlb'),
					__('Note on Small Template:','wlb'),
					__('When the window reaches the "Small template trigger width" value, the body is added the CSS class wlb-small-login, so you can define a separate set of styles for the small template.','wlb')
				),
				'el_properties' => array('cols'=>'50','rows'=>'10'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'=>'login_template_group',
				'type'=>'div_end'
			)	,
			(object)array(
				'type'=>'clear'
			),	
			(object)array('label'=>__('Save Changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=>'' )			
		);		
		
		//----
		$i = count($t);
		@$t[$i]->id 			= 'login_dc'; 
		$t[$i]->label 		= __('Saved and Downloaded Login Templates','wlb');//title on tab
		$t[$i]->right_label = __('Restore Saved or Downloaded Settings','wlb');
		$t[$i]->page_title	= __('Saved and Downloaded Login Templates','wlb');//title on content
		//$t[$i]->open = false;		
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Backup Login Template Settings','wlb')	
			),
			(object)array(
				'id'		=> 'login-save-btn',
				'type'		=>'save_settings',
				'label'		=>__('Brief description','wlb'),
				'description'=> __('This will save a copy of login settings.<br />Please observe that it only backups saved settings; any changes you made after saving will be lost.','wlb'),
				'export_fields'=>array('use_login_screen_customization','login_logo_url','login_logo_opacity','login_background','login_background_attachments','login_background_color','login_background_color_code','login_background_position','login_background_x','login_background_y','login_background_repeat','lf_use_login_form','lf_x','lf_y','lf_bg','lf_bg_attachments','lf_bg_color','lf_bg_color_code','lf_bg_position','lf_bg_x','lf_bg_y','lf_bg_repeat','lf_opacity','lf_label_color','lf_label_size','lf_border_width','lf_border_style','lf_border_color','lf_tl_radius','lf_tr_radius','lf_br_radius','lf_bl_radius','lf_shadow_color','lf_shadow_x','lf_shadow_y','lf_shadow_blur','lf_shadow_size','lf_input_bg_color','lf_input_border_color','lf_input_shadow_color','lf_input_shadow_opacity','lf_submit_grad1','lf_submit_color','use_login_template','login_template','trigger_login_template_small','login_template_small','login_styles_scripts'),
				'button_label'=> __('Backup current settings','wlb')	
			),
			(object)array(
				'type'=>'clear'
			),
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Stored Login Customization Settings','wlb')	
			),			
			(object)array(
				'id'		=> 'popex-list-login',
				'type'		=>'saved_settings_list',
				'debug'		=>false//showing a refresh button.
			),
			(object)array(
				'type'=>'clear'
			)						
		);
		
		return $t;
	}
	
	function css_login_form(){
		global $wlb_plugin;
		if(  '1'!=$wlb_plugin->get_option('lf_use_login_form') )return;
		foreach(array(
			'lf_x'=>'0',
			'lf_y'=>'0',
			'lf_tl_radius'=>'3',
			'lf_tr_radius'=>'3',
			'lf_br_radius'=>'3',
			'lf_bl_radius'=>'3',
			'lf_bg'=>'',
			'lf_bg_repeat'=>'repeat',
			'lf_bg_attachments'=>'scroll',
			'lf_bg_color'=>'',
			'lf_bg_color_code'=>'',
			'lf_bg_position'=>'',
			'lf_bg_x'=>'0',
			'lf_bg_y'=>'0',
			'lf_opacity'=>'1',
			'lf_label_color'=>'#777777',
			'lf_label_size'=>'14',
			'lf_border_width'=>'1',
			'lf_border_style'=>'solid',
			'lf_border_color'=>'#E5E5E5',
			'lf_shadow_color'=>'#c8c8c8',
			'lf_shadow_opacity'=>'0.7',
			'lf_shadow_x'=>'0',
			'lf_shadow_y'=>'4',
			'lf_shadow_blur'=>'10',
			'lf_shadow_size'=>'-1',
			
			'lf_input_bg_color'=>'',
			'lf_input_border_color'=>'',
			'lf_input_shadow_color'=>'',
			'lf_input_shadow_opacity'=>'0.2',
			'lf_submit_grad1'=>'',
			'lf_submit_color'=>''
			) as $option => $default){
			$$option = $wlb_plugin->get_option($option,$default);
			
			if(in_array($option, array('lf_bg'))){
				$$option = str_replace("{pluginurl}",$this->url,$$option);
				$$option = str_replace("{dcurl}", $this->get_login_dc_url_path() ,$$option);
			}
		}
?>
#login,
#custom-logo {
position:relative;
top:<?php echo $lf_y?>px;
left:<?php echo $lf_x?>px;
}
.login label, label {
	color:<?php echo $lf_label_color?>;
	font-size:<?php echo sprintf("%spx",$lf_label_size)?>;
}
.login form,
form {
	-moz-border-radius: <?php echo sprintf("%spx %spx %spx %spx",$lf_tl_radius,$lf_tr_radius,$lf_br_radius,$lf_bl_radius)?>;
	-khtml-border-radius: <?php echo sprintf("%spx %spx %spx %spx",$lf_tl_radius,$lf_tr_radius,$lf_br_radius,$lf_bl_radius)?>;
	-webkit-border-radius: <?php echo sprintf("%spx %spx %spx %spx",$lf_tl_radius,$lf_tr_radius,$lf_br_radius,$lf_bl_radius)?>;
	border-radius: <?php echo sprintf("%spx %spx %spx %spx",$lf_tl_radius,$lf_tr_radius,$lf_br_radius,$lf_bl_radius)?>;
	background: <?php echo sprintf("%s %s %s %s %s",
		(trim($lf_bg)==''?'none':sprintf("url(%s)",$lf_bg)),
		$lf_bg_repeat,
		$lf_bg_attachments,
		(trim($lf_bg_position)==''?(sprintf("%spx %spx",$lf_bg_x,$lf_bg_y)):$lf_bg_position),
		(trim($lf_bg_color)==''?$lf_bg_color_code:$lf_bg_color)
	)?>;
	border: <?php echo sprintf("%spx %s %s",$lf_border_width,$lf_border_style,$lf_border_color)?>;
	
	-moz-box-shadow: <?php echo sprintf("%s %spx %spx %spx %spx",$this->get_rgba($lf_shadow_color,$lf_shadow_opacity),$lf_shadow_x,$lf_shadow_y,$lf_shadow_blur,$lf_shadow_size)?>;
	-webkit-box-shadow: <?php echo sprintf("%s %spx %spx %spx %spx",$this->get_rgba($lf_shadow_color,$lf_shadow_opacity),$lf_shadow_x,$lf_shadow_y,$lf_shadow_blur,$lf_shadow_size)?>;
	-khtml-box-shadow: <?php echo sprintf("%s %spx %spx %spx %spx",$this->get_rgba($lf_shadow_color,$lf_shadow_opacity),$lf_shadow_x,$lf_shadow_y,$lf_shadow_blur,$lf_shadow_size)?>;
	box-shadow: <?php echo sprintf("%s %spx %spx %spx %spx",$this->get_rgba($lf_shadow_color,$lf_shadow_opacity),$lf_shadow_x,$lf_shadow_y,$lf_shadow_blur,$lf_shadow_size)?>;
	opacity: <?php echo $lf_opacity?>;
}

body form .input  {
	background-color:<?php echo $lf_input_bg_color?>;
	border-color:<?php echo $lf_input_border_color?>;
<?php if(''!=trim($lf_input_shadow_color)):?>	
	-moz-box-shadow: <?php echo sprintf("%s 1px 1px 2px inset",$this->get_rgba($lf_input_shadow_color,$lf_input_shadow_opacity))?>;
	-webkit-box-shadow: <?php echo sprintf("%s 1px 1px 2px inset",$this->get_rgba($lf_input_shadow_color,$lf_input_shadow_opacity))?>;
	-khtml-box-shadow: <?php echo sprintf("%s 1px 1px 2px inset",$this->get_rgba($lf_input_shadow_color,$lf_input_shadow_opacity))?>;
	box-shadow: <?php echo sprintf("%s 1px 1px 2px inset",$this->get_rgba($lf_input_shadow_color,$lf_input_shadow_opacity))?>;
<?php endif;?>
}	

<?php
if(!in_array($lf_submit_grad1,array('','#'))):
	$grad2 = $lf_submit_grad1;
	$grad1 = $this->get_color($grad2,16);
?>
.wp-core-ui .button-primary,
input.button-primary:active, button.button-primary:active, a.button-primary:active,
input.button-primary, button.button-primary, a.button-primary {
	color: <?php echo $lf_submit_color;?>;
	background-color: <?php echo $grad1 ?>;
	background:-moz-linear-gradient(top,<?php echo $grad1 ?>, <?php echo $grad2 ?>);
	background:-webkit-gradient(linear, left top, left bottom, from(<?php echo $grad1 ?>), to(<?php echo $grad2 ?>));
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $grad1 ?>', EndColorStr='<?php echo $grad2 ?>');
	border-color:<?php echo $this->get_color($grad1,-7)?>;
}
<?php
	$grad2 = $this->get_color($lf_submit_grad1,7);;
	$grad1 = $this->get_color($grad2,16);
?>
.wp-core-ui .button-primary:active,
.wp-core-ui .button-primary.hover, .wp-core-ui .button-primary:hover, .wp-core-ui .button-primary.focus, .wp-core-ui .button-primary:focus,
input.button-primary:hover, button.button-primary:hover, a.button-primary:hover {
	color: <?php echo $this->get_color($lf_submit_color,7);?>;
	background-color: <?php echo $grad1 ?>;
	background:-moz-linear-gradient(top,<?php echo $grad1 ?>, <?php echo $grad2 ?>);
	background:-webkit-gradient(linear, left top, left bottom, from(<?php echo $grad1 ?>), to(<?php echo $grad2 ?>));
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $grad1 ?>', EndColorStr='<?php echo $grad2 ?>');
	border-color:<?php echo $this->get_color($grad1,-7)?>;
}
<?php endif; ?>
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
	function login_head(){
		global $wlb_plugin;
		if( isset($_REQUEST['wlb_skip_login']) || apply_filters('wlb_skip_login',false) ){
			return true;
		}
	
		if(  '1'!=$wlb_plugin->get_option('use_login_screen_customization') )return true;
		//if($_SERVER['SCRIPT_NAME']!='/wp-login.php')return;
		if(function_exists('minimeta_init')&&$_SERVER['SCRIPT_NAME']!='/wp-login.php')return;
		$vars = array('login_logo_url','login_logo_opacity','login_background','login_background_color','login_background_color_code','login_template','login_template_small','trigger_login_template_small','login_styles_scripts','use_login_template');
		foreach($vars as $var){
			$$var = $wlb_plugin->get_option($var);
			$$var = str_replace("{pluginurl}",$this->url,$$var);
			$$var = str_replace("{dcurl}", $this->get_login_dc_url_path() ,$$var);
		}
		
		$bgcss='';
		if(trim($login_background)!=''){
			$tmp = array();
			foreach( array('login_background_repeat','login_background_attachments','login_background_position','login_background_x','login_background_y') as $var){
				if( in_array($var,array('login_background_x','login_background_y')) && ''!=$wlb_plugin->get_option('login_background_position') )continue;
				$tmp[]=$wlb_plugin->get_option($var);
			}
			
			if($login_background_color==''&&trim($login_background_color_code)!=''){
				$tmp[] = false===strpos($login_background_color_code,'#')?'#'.$login_background_color_code:$login_background_color_code;
			}else{
				$tmp[] = $login_background_color;
			}			
			
			$login_background = ''==trim($login_background)? 'none' : "url(\"$login_background\")" ;
			$login_background = str_replace("{pluginurl}",$this->url,$login_background);
			array_unshift($tmp, $login_background );
			$bgcss=sprintf("background:%s;",implode(' ',$tmp));
		}else if(trim($login_background_color_code)!=''){
			$login_background_color_code = false===strpos($login_background_color_code,'#')?'#'.$login_background_color_code:$login_background_color_code;
			$bgcss=sprintf("background:%s;",$login_background_color_code);
		}
		
		$login_template = trim($login_template);
		if($login_template!=''){
			$login_template = str_replace("{loginform}","<div id=\"login-form-holder\"></div>",$login_template);
			$login_template = str_replace("{backlink}","<div id=\"login-back-link\"></div>",$login_template);
			$login_template = str_replace("{customlogo}","<div id=\"login-custom-logo\"></div>",$login_template);
			$login_template = json_encode($login_template);
		}
		//---
		$trigger_login_template_small = intval($trigger_login_template_small)==0?480:intval($trigger_login_template_small);
		//---
		$login_template_small = trim($login_template_small);
		if($login_template_small!=''){
			$login_template_small = str_replace("{loginform}","<div id=\"login-form-holder\"></div>",$login_template_small);
			$login_template_small = str_replace("{backlink}","<div id=\"login-back-link\"></div>",$login_template_small);
			$login_template_small = str_replace("{customlogo}","<div id=\"login-custom-logo\"></div>",$login_template_small);
			$login_template_small = json_encode($login_template_small);
		}
		//---
		
		$login_styles_scripts = trim($login_styles_scripts);
		if($login_styles_scripts!=''){
			$login_styles_scripts = str_replace("{pluginurl}",$this->url,$login_styles_scripts);
		}
		
		wp_print_scripts('jquery');
?>
<style>
#custom-logo {
	width:100%;
	text-align: center;
	padding:0 0 10px 0;
	margin-top: 5em;
	opacity: <?php echo $login_logo_opacity?>;
}

#login {
	margin-top: 10px !important;
<?php if($use_login_template==1 && $login_template!=''):?>
	display:none;
<?php endif;?>	
<?php if($login_logo_url!=''):?>
	padding-top:0;
<?php endif;?>
}

#login-wrapper {
	<?php echo $bgcss ?>position:absolute;display:block; width:100%; height:100%; top:0; left:0; overflow:auto;
}
.login-wrapped {
	padding:0 0 0 0 !important;
	margin:0 0 0 0 !important;
}
<?php $this->css_login_form()?>
</style>

<script>
jQuery(document).ready(function($){
	if($('body.login > #login').length>0){
<?php if($login_logo_url!=''):?>
	$('#login h1').remove();
	$('body').prepend('<div id="custom-logo"><a href="<?php echo site_url();?>"><img border="0" src="<?php echo $login_logo_url?>" /></a></div>');	
<?php endif;?>

	 apply_login_background();

<?php if($use_login_template==1):?>
	$(window).resize(function(e){
		if( $(this).width()< <?php echo $trigger_login_template_small?> ){
			if( ! $('body').hasClass('wlb-small-login') ){
				$('body').addClass('wlb-small-login');
				apply_custom_login_template();
			}
		}else{
			if( $('body').hasClass('wlb-small-login') ){
				$('body').removeClass('wlb-small-login');
				apply_custom_login_template();
			}
		}
	}).resize();
	apply_custom_login_template();
<?php endif;?>
	}
});

function apply_login_background(){
	jQuery(document).ready(function($){
<?php if($bgcss!=''):?>
	var con = $('body').children();
	if( $('#login-wrapper').length == 0 ){
		$('body').append('<div id="login-wrapper"></div>');
		$('#login-wrapper').append( con );
	}
	
	//not working well on latest wp: $('<div id="login-wrapper"></div>').append( $('body').children() ).appendTo('body');
	$('body').addClass('login-wrapped');
<?php endif;?>
	});
}

function apply_custom_login_template(){
	jQuery(document).ready(function($){
		var _template = '';
		if($('body').hasClass('wlb-small-login')){
			<?php if(!empty($login_template_small)): ?>
			_template = <?php echo $login_template_small?>;
			<?php endif; ?>
		}else{
			<?php if(!empty($login_template)): ?>
			_template = <?php echo $login_template?>;
			<?php endif; ?>
		}
		
		if(''!=_template){
			var con = $('body').children();
			$('body').prepend('<div id="custom-logo"><a href="<?php echo site_url();?>"><img border="0" src="<?php echo $login_logo_url?>" /></a></div>');//custom logo.
			if( $('#hide-wrapper').length==0 ) $('body').append('<div id="hide-wrapper" style="display:none;"></div>');
			$('#hide-wrapper').hide();
			
			$('#hide-wrapper').append( con );
			//$('<div id="hide-wrapper" style="display:none;"></div>').append( $('body').children() ).appendTo('body');	
				
			$('body').append( _template );
			$('body').append( $('#hide-wrapper') );//move to bottom
			
			$('#login').appendTo( $('#login-form-holder') );
			$('#backtoblog a').addClass('wlb-back-link').appendTo( $('#login-back-link') );
			$('.wlb-back-link').appendTo( $('#login-back-link') );
			$('#custom-logo').appendTo( $('#login-custom-logo') );
		
			$('#hide-wrapper').remove();
			apply_login_background();
			$('#login').show();
		}		
	});
}
</script>
<?php 	
		echo $use_login_template==1?$login_styles_scripts:'';
	}	
	
	function get_login_dc_url_path(){
		global $wlb_plugin;
		$upload_dir = wp_upload_dir();
		return $upload_dir['baseurl'].'/'.$wlb_plugin->resources_path.'/login_templates/';	
	}
}
?>