<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class wlb_branding {
	function __construct( $url ){
		$this->url = $url;
		
		global $wlb_plugin;
		$this->id = $wlb_plugin->id.'-bra';
		
		add_action("admin_head",array(&$this,'admin_head'));
		add_action("wp_head",array(&$this,'wp_head'));
		add_filter("pop-options_{$this->id}",array(&$this,'wlb_options'),10,1);	
		add_filter('admin_title', array(&$this,'admin_title'),10,2);//filter was introduced on wp3.1
		//--implemet.----
		add_action("wp_head",array(&$this,'favicon'));
		
		if('1'==$wlb_plugin->get_option('enable_email_branding','1')){
			add_filter("wp_mail",array(&$this,'filter_wp_mail'),11,1);	
		}

		if('1'==$wlb_plugin->get_option('disable_pointers','')){
			add_action('admin_init',array(&$this,'disable_pointers'));	
		}

		if( !in_array( trim($wlb_plugin->get_option('replace_howdy','')), array('','Howdy, %1$s'))){
			add_filter('gettext',array(&$this,'replace_howdy'),1111111,3);
		}
	}
	
	function replace_howdy($translation,$text,$domain){
		if($text=='Howdy, %1$s'){
			global $wlb_plugin;
			return $wlb_plugin->get_option('replace_howdy','');
		}
		return $translation;
	}
	
	function disable_pointers(){
		remove_action( 'admin_enqueue_scripts', array( 'WP_Internal_Pointers', 'enqueue_scripts' ) );
	}

	function filter_wp_mail($args){		
		if( is_string($args['headers']) && preg_match("/^from:/mi",$args['headers']) ){
		
		}else{
			//only do branding if header from: is not set
			add_filter("wp_mail_from",array(&$this,'filter_from_email'),11,1);
			add_filter("wp_mail_from_name",array(&$this,'filter_from_name'),11,1);		
		}
		return $args;
	}
	
	function filter_from_email($from_email){
		global $wlb_plugin;
		$wlb_from_email = $wlb_plugin->get_option('wp_mail_from','');
		if(''==trim($wlb_from_email)){
			return $from_email;
		}else{
			return $wlb_from_email;
		}
	}
	
	function filter_from_name($from_name){
		global $wlb_plugin;
		$wlb_from_name = $wlb_plugin->get_option('wp_mail_from_name','');
		if(''==trim($wlb_from_name)){
			return $from_name;
		}else{
			if(false!==strpos($wlb_from_name,"{site_url}")){
				$url = site_url();
				$url = str_replace("https://",'',$url);
				$url = str_replace("http://",'',$url);			
				
				$wlb_from_name = str_replace("{site_url}",$url,$wlb_from_name);
			}
			if(false!==strpos($wlb_from_name,"{site_title}")){
				$site_title = get_bloginfo('name');		
				$wlb_from_name = str_replace("{site_title}",$site_title,$wlb_from_name);
			}
			return $wlb_from_name;
		}
	}
	
	function wlb_options($t,$for_admin=true){
		$i = count($t);
	
		$i++;
		@$t[$i]->id 			= 'Branding'; 
		$t[$i]->label 		= __('Branding','wlb');//title on tab
		$t[$i]->right_label = __('Customize Favicon, Header, Footer Logo and WordPress Nag Messages','wlb');
		$t[$i]->page_title	= __('Branding','wlb');//title on content
		//$t[$i]->open	= true;
		$t[$i]->options = array(
			(object)array(
				'id'		=> 'use_branding_customization',
				'label'		=> __('Enable admin branding options','wlb'),
				'type'		=> 'yesno',
				'description'=>  sprintf(__('Choose %s to customize the admin','wlb'),__('Yes','wlb')),
				'el_properties'	=> array(),
				'hidegroup'	=> '#branding_group',
				'save_option'=>true,
				'load_option'=>true
				),	
			(object)array('type'	=> 'clear'),	
			(object)array(
				'id'	=> 'branding_group',
				'type'=>'div_start'
			),								
			(object)array(
				'type'=>'subtitle',
				'label'=>'Favicon'	
			),
			(object)array(
				'id'	=> 'favicon',
				'type'	=>'fileuploader',
				//'preview_selector'=>'#desc_favicon_prev',
				'label'	=> __('Favicon URL','wlb'),
				'description'=> sprintf('%s<div id="desc_favicon_prev"></div>', __('URL to an image to use as Favicon ','wlb') ),
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array(
				'type'=>'clear'
			),		
			(object)array(
				'type'=>'subtitle',
				'label'=>'Header'	
			),
			(object)array(
				'id'	=> 'header_logo',
				'type'	=>'fileuploader',
				'label'	=> __('Header Logo URL','wlb'),
				'description'=> __('URL to an image that will replace the header logo','wlb'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'header_logo_link',
				'type'	=>'text',
				'label'	=> __('Header Logo Link Url','wlb'),
				'description'=> __('If left blank clicking on the top left logo will redirect to the about page.','wlb'),
				'el_properties' => array('class'=>'text-width-full'),
				'save_option'=>true,
				'load_option'=>true
			),	
			(object)array(
				'id'	=> 'header_logo_title',
				'type'	=>'text',
				'label'	=> __('Header Logo title','wlb'),
				'description'=> __('Only used when logo link is also set.  Its the text displayed on a mouse over the header logo.','wlb'),
				'el_properties' => array('class'=>'text-width-full'),
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array('type'	=> 'clear'),
			(object)array(
				'id'	=> 'header_logo_width',
				'type'	=> 'range',
				'min'	=> 0,
				'max'	=> 100,
				'step'	=> 1,
				'default'=> 20,				
				'label'=> __('Header Logo Width','wlb'),
				'description'=> __('Optionally specify a width for the logo if you do not wish to use the default 20px width.','wlb'),
				'el_properties' => array('size'=>'5'),
				'save_option'=>true,
				'load_option'=>true
			),

/* 			
			(object)array(
				'id'	=> 'header_bar_height',
				'type'	=> 'range',
				'min'	=> 32,
				'max'	=> 500,
				'step'	=> 1,
				'default'=> 32,				
				'label'=> __('Header Bar Height','wlb'),
				'description'=> __('Specify a height in px if the header logo is taller than 32px, this will adjust the header height.','wlb'),
				'el_properties' => array('size'=>'5'),
				'save_option'=>true,
				'load_option'=>true
			),
*/			
			
//			(object)array(
//				'type'=>'hr'
//			),
			(object)array(
				'type'=>'subtitle',
				'label'=> __('Footer','wlb')	
			),
			(object)array(
				'id'	=> 'developer_logo',
				'type'=>'fileuploader',
				'label'=>__('Developer Logo URL','wlb'),
				'description'=> __('URL to an image that will be displayed in the footer.','wlb'),
				'save_option'=>true,
				'load_option'=>true
			),

			(object)array(
				'id'	=> 'developer_name',
				'type'	=> 'text',
				'label'	=> __('Developer Name','wlb'),
				'description'=> __('Developer name, displayed on links to the developer website in the footer.','wlb'),
				'el_properties' => array('class'=>'text-width-full'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'developer_url',
				'type'=>'text',
				'label'=>__('Developer URL','wlb'),
				'description'=> __('URL to the developer website.','wlb'),
				'el_properties' => array('class'=>'text-width-full'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'remove_footer_version',
				'type'=>'yesno',
				'label'=>__('Hide WP version on footer','wlb'),
				'description'=> __('Hide the WordPress version from the footer','wlb'),
				'el_properties' => array('class'=>'text-width-full'),
				'save_option'=>true,
				'load_option'=>true
			)	
		);	
		
		
		$t[$i]->options[] = (object)array(
				'type'=>'subtitle',
				'label'=>__('WordPress Messages','wlb')	
			);		
		$t[$i]->options[] =	(object)array(
				'id'		=> 'replace_howdy',
				'label'		=> __('Replace Howdy, username','wlb'),
				'type'		=> 'text',
				'description'=> __('Provide an alternate text for the Howdy, username message in the top left.  Write %1$s where you want the username. Example:  Welcome %1$s'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
			);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'remove_wordpress_from_title',
				'label'		=> __('Remove "&#8212; WordPress" from title','wlb'),
				'type'		=> 'yesno',
				'description'=>  sprintf(__('Choose %s to remove the word "&#8212; WordPress" from the header title on the admin area.<br />Note:Prior to WP3.1 there was no filter for the title, so it is removed with javascript on wordpress versions prior to 3.1.','wlb'),__('Yes','wlb')),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
			);				
		
		$t[$i]->options[] = (object)array(
				'type'=>'description',
				'label'=>__('Hide WordPress Update Messages.','wlb')
			);
			
		$t[$i]->options[] =	(object)array(
				'id'		=> 'hide_update_nag',
				'label'		=> __('Hide Update Nag','wlb'),
				'type'		=> 'yesno',
				'description'=> __('Hide WordPress Update Message at the top.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'hide_update_download',
				'label'		=> __('Hide Update Download Link','wlb'),
				'type'		=> 'yesno',
				'description'=> __('Hide WordPress download link message at the bottom.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'hide_contextual_help',
				'label'		=> __('Hide Contextual Help','wlb'),
				'type'		=> 'yesno',
				'description'=> __('Hide WordPress Contextual Help.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'hide_screen_options',
				'label'		=> __('Hide Screen Options','wlb'),
				'type'		=> 'yesno',
				'description'=> __('Hide Screen Options.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'hide_favorite_actions',
				'label'		=> __('Hide Favorite Actions','wlb'),
				'type'		=> 'yesno',
				'description'=> __('Hide WordPress Favorite Actions (Dropdown located on the top right corner of wp-admin).','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'disable_pointers',
				'label'		=> __('Disable WordPress Pointers','wlb'),
				'type'		=> 'yesno',
				'description'=> __('Choose Yes to disable the WordPress pointers','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);			
		$t[$i]->options[]=(object)array('type'	=> 'div_end');
		$t[$i]->options[]=(object)array('type'	=> 'clear');
		$t[$i]->options[]=(object)array('type'=>'submit','class'=>'button-primary', 'label'=> __('Save changes','wlb') );
		//------------------	
		if(is_multisite()){
			$i++;
			$t[$i]=(object)array();
			$t[$i]->id 			= 'branding-ms'; 
			$t[$i]->label 		= __('Multisite','wlb');//title on tab
			$t[$i]->right_label = __('Multisite specific branding','wlb');
			$t[$i]->page_title	= __('Branding','wlb');//title on content
			//$t[$i]->open	= true;
			$t[$i]->options = array();
			
			$t[$i]->options[]=(object)array(
				'id'		=> 'hide_blavatar',
				'label'		=> __('Hide wp logo in site list','wlb'),
				'type'		=> 'yesno',
				'description'=>  __('Choose yes to hide the WordPress logo in front of site links in the admin bar.  Branding customization options need to be enabled.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);
						
			$t[$i]->options[]=(object)array('type'	=> 'clear');
			$t[$i]->options[]=(object)array('type'=>'submit','class'=>'button-primary', 'label'=> __('Save changes','wlb') );			
		}		
		//------------------	
		
		global $wlb_plugin;
		if('1'==$wlb_plugin->get_option('enable_email_branding','1')):		
		$i++;
		@$t[$i]->id 			= 'email_branding'; 
		$t[$i]->label 		= __('Email Branding','wlb');//title on tab
		$t[$i]->right_label = __('Customize the Standard WordPress from email and from name','wlb');
		$t[$i]->page_title	= __('Email Branding','wlb');//title on content
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>'Email Branding'	
			),
			(object)array(
				'id'		=> 'wp_mail_from',
				'label'		=> __('Email from','wlb'),
				'type'		=> 'text',
				'description'=> sprintf("<p>%s</p><p>%s</p><p>%s</p>",__('By default WordPress uses the from name "WordPress" and address "wordpress@yourdomain.com" when it sends notifications to users.','wlb'),__('We let you change the from email and the from name.','wlb'),__('You can use the following tags tags in the Email from name: {site_url} and {site_title} to dynamically display the sites url and name defined in the WordPress general settings.','wlb')),				
				'el_properties'	=> array('class'=>'text-width-full'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'		=> 'wp_mail_from_name',
				'label'		=> __('Email from name','wlb'),
				'type'		=> 'text',
				'el_properties'	=> array('class'=>'text-width-full'),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array('type'	=> 'clear'),
			(object)array('type'=>'submit','class'=>'button-primary', 'label'=> __('Save changes','wlb') )		
		);	
		endif;
		
		return $t;
	}
	
	function favicon(){
		global $wlb_plugin;
		$favicon = $wlb_plugin->get_option('favicon');
		if(trim($favicon)!=''){
			$favicon = str_replace("{pluginurl}",$this->url,$favicon);
			echo sprintf('<link rel="icon" type="image/png" href="%s">',$favicon);
		}
	}
	
	function admin_head(){
		global $wp_version, $wlb_plugin;
		
		if(  '1'!=$wlb_plugin->get_option('use_branding_customization') )return true;
		
		$vars = array('dashboard_icon','header_logo','header_logo_link','header_logo_title','hide_blavatar','header_logo_width','header_bar_height','developer_logo','developer_name','developer_url','hide_update_nag','hide_update_download','hide_contextual_help','hide_screen_options','hide_favorite_actions','remove_wordpress_from_title','remove_footer_version');
		foreach($vars as $var){
			$$var = $wlb_plugin->get_option($var);
			if(in_array($var,array('header_logo'))){
				$$var = str_replace("{pluginurl}", $this->url, $$var);
			}
		}
		//---footer
		$developer_url = $developer_url==''?'javascript:void(0);':$developer_url;
	
		$footer = '';
		if($developer_logo!=''){
			$footer.=sprintf("<a href=\"%s\"><img id=\"footer-developer-logo\" src=\"%s\" height=\"32\" align=\"MIDDLE\" /></a>",$developer_url,$developer_logo);
		}	
		if($developer_name!=''){
			$footer.=sprintf("<a id=\"footer-developer-name\" href=\"%s\">%s</a>",$developer_url,$developer_name);
		}
		
		$footer = str_replace("'","\'",$footer);	
		//--for dashboard icon
		if(function_exists('get_current_screen')){//compatibility fix.
			$screen = get_current_screen();
			
			if($screen->id!='dashboard'){
				$dashboard_icon = '';
			}		
		}else{
		
		}
?>
<?php $this->favicon();?>
<style>
<?php if(trim($dashboard_icon)!='') : ?>
#icon-index.icon32 {
	background-image: url(<?php echo $dashboard_icon?>);
	background-position:0 0;
}
<?php endif; ?>
#icon-white-label-branding,
#icon-edit.icon32-posts-wlbdash {
background:url(<?php echo $this->url.'images/dashboard_tool.png'?>) no-repeat scroll 0 0 transparent;
}
#header-logo {
	display:none !important;
}
#admin-custom-head-logo{
	float:left;
	margin:7px 0 0 15px;
	vertical-align:middle;
}
#wphead{
<?php if($header_bar_height!=''):?>
	height: <?php echo intval($header_bar_height) ?>px;
<?php endif;?>
}
#footer p#footer-left{
	vertical-align:middle;
	padding:6px 15px 0 15px !important;
}
#footer-developer-logo{
	padding:0 10px 0 0;
}
#footer-developer-name{
	position:relative;
	top:5px;
}
<?php if(!$wlb_plugin->is_wlb_administrator()):?>
#dashboard_right_now .versions p, #dashboard_right_now .versions #wp-version-message  { display: none; }
<?php endif; ?>
<?php if(!$wlb_plugin->is_wlb_administrator() && $hide_update_nag=='1'):?>
.update-nag {display: none !important;}
<?php endif; ?>
<?php if(!$wlb_plugin->is_wlb_administrator() && $hide_update_download=='1'):?>
#footer-upgrade {display:none !important;}
<?php endif; ?>
<?php if(!$wlb_plugin->is_wlb_administrator() && $hide_contextual_help=='1'):?>
#contextual-help-link-wrap {display:none !important;}
<?php endif; ?>
<?php if(!$wlb_plugin->is_wlb_administrator() && $hide_screen_options=='1'):?>
#screen-options-link-wrap {display:none !important;}
<?php endif; ?>
<?php if(!$wlb_plugin->is_wlb_administrator() && $hide_favorite_actions=='1'):?>
#favorite-actions {display:none !important;}
<?php endif; ?>
<?php if(!$wlb_plugin->is_wlb_administrator() && $remove_footer_version=='1'):?>
#footer-upgrade { display:none; }
<?php endif; ?>
</style>
<script>
jQuery(document).ready(function($){
	$('#footer #footer-left').html('');
	var sel = '#footer #footer-left';
	if( $('#wpfooter #footer-left').length>0 ){
		sel = '#wpfooter #footer-left';
	}
	$(sel).append('<?php echo $footer?>');

	if( $('input[name="<?php echo $wlb_plugin->id?>_use_dashboard"]:checked').val()!=1 ){
		jQuery('.use_dashboard').hide();
	}
	
	if( $('input[name="<?php echo $wlb_plugin->id?>_editor_appeareance"]:checked').val()!=1 ){
		//jQuery('.theme-options').hide();
	}	
});

<?php if($wp_version<'3.1' && 1==$remove_wordpress_from_title):?>	
	document.title = document.title.replace(/(\s)([^\s]*)(\s)WordPress/gi,'');
<?php endif; ?>
</script>
<?php	
		$this->handle_wp_logo_in_admin_bar($header_logo,$header_logo_width,$header_logo_link,$header_logo_title,$hide_blavatar);
	}	
	
	function wp_head(){
		global $wlb_plugin;
		if(  '1'!=$wlb_plugin->get_option('use_branding_customization') )return true;
		$vars = array('header_logo','header_logo_width','header_logo_link','header_logo_title','hide_blavatar');
		foreach($vars as $var){
			$$var = $wlb_plugin->get_option($var);
			if(in_array($var,array('header_logo'))){
				$$var = str_replace("{pluginurl}", $this->url, $$var);
			}
		}	
		$this->handle_wp_logo_in_admin_bar($header_logo,$header_logo_width,$header_logo_link,$header_logo_title,$hide_blavatar);	
	}
	
	function handle_wp_logo_in_admin_bar($header_logo,$header_logo_width,$header_logo_link='',$header_logo_title='',$hide_blavatar=''){
?>
<script type="text/javascript">
jQuery(document).ready(function($){
<?php if(trim($header_logo)!=''):?>
	$('#wp-admin-bar-wp-logo > .ab-item .ab-icon').prepend('<img class="wlb-custom-header-logo" src="<?php echo $header_logo ?>" />');
	//to avoid consuming limited IE styles, we do this with jquery css()
	$('#wp-admin-bar-wp-logo > .ab-item .ab-icon').css('background-image','none');
	$('#wpadminbar .wlb-custom-header-logo')
		.css('float','none')
		.css('clear','both')
		.css('position','relative')
		.css('width','100%')
		.css('height','100%')
	;
	<?php if($header_logo_width>0): ?>
	$('#wp-admin-bar-wp-logo > .ab-item .ab-icon').css('width','<?php echo $header_logo_width?>px');
	<?php endif;?>
	<?php if(''!=$header_logo_link): ?>
	$('#wp-admin-bar-wp-logo > a.ab-item').attr('href','<?php echo $header_logo_link?>').attr('title','<?php echo $header_logo_title?>');
	<?php endif;?>
<?php endif; ?>

	<?php if('1'==$hide_blavatar): ?>
	$('#wpadminbar .blavatar').remove();
	<?php endif;?>
});
</script>
<style>
<?php if('1'==$hide_blavatar): ?>
.blavatar {
display:none !important;
}
<?php endif;?>
</style>
<?php	
	}
	
	function admin_title($admin_title,$title){
		global $wlb_plugin;
		//$remove_wordpress_from_title = get_option(sprintf("%s_%s",$this->id,'remove_wordpress_from_title'));
		$remove_wordpress_from_title = $wlb_plugin->get_option('remove_wordpress_from_title');
		if(1==$remove_wordpress_from_title){
			$admin_title = str_replace("&#8212; WordPress","",$admin_title);
		}
		return $admin_title;
	}
}

?>