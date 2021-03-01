<?php

/**

* Theme integration instructions:
* 1. copy the plugin to the root of your theme
* 2. copy paste the content of this file to the themes functions.php
* 3. enjoy.
* 
* Note: Please observe that you need to activate the theme in order to create the wlb capabilities.  if you already had the theme activated and added this to functions, wlb will not show.
* You can change the location of the file or rename the directory, but you will need to adjust the WLB_PATH and WLB_URL constants.
*  **/

//---- White Label Branding --------------------------------------------------
if(!defined('WLB_PATH')):
//----------
define('WLB_VERSION','3.0.6');
define('WLB_PATH', dirname( __FILE__ ). "/white-label-branding/" ); 
define("WLB_URL", get_bloginfo('stylesheet_directory') . "/white-label-branding/"  ); 
define("WLB_ADMIN_ROLE",'administrator');
define("WLB_ADMIN_CAP",'wlb_options');
define("WLB_PLUGIN_CODE",'WLB');
//---
define('WLB_SUBSITE_ADMINISTRATOR',WLB_ADMIN_ROLE); 

load_plugin_textdomain('wlb', null, WLB_PATH.'/languages' );
if(is_admin()&&!defined('TDOM_POP')){define('TDOM_POP',true);load_plugin_textdomain('pop', null, WLB_PATH.'/options-panel/languages' );}

require_once WLB_PATH.'includes/class.plugin_white_label_branding.php';

$settings = array(
	'theme'					=> true,
	'options_capability' 	=> WLB_ADMIN_CAP,
	'options_panel_version'	=> '2.0.5'
);

global $wlb_plugin;
$wlb_plugin = new plugin_white_label_branding($settings);

//-- Installation script:---------------------------------
function wlb_install(){
	$WP_Roles = get_role(WLB_ADMIN_ROLE);
	//$WP_Roles = new WP_Roles();	
	foreach(array(
		'wlb_branding',
		'wlb_navigation',
		'wlb_login',
		'wlb_color_scheme',
		'wlb_options',
		'wlb_role_manager',
		'wlb_license',
		'wlb_downloads',
		'wlb_dashboard_tool'
		) as $cap){
		$WP_Roles->add_cap( $cap );
	}
	include WLB_PATH.'includes/install.php';
	/*if(function_exists('handle_wlb_install'))*/handle_wlb_install();	
}

add_action("after_switch_theme", "wlb_install", 10 ,  2);


endif;
//-----------------------------------------------------------------------------------------
?>