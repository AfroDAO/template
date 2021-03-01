<?php

/**
Plugin Name: White Label Branding for WordPress
Plugin URI: http://plugins.righthere.com/white-label-branding/
Description: Take complete control over wp-admin, customize menus for each User Role. Hide WordPress branding and nag messages. Add your own custom branding. Customize the WordPress Toolbar and many other features.
Version: 4.2.0.83030
Author: Alberto Lau (RightHere LLC)
Author URI: http://plugins.righthere.com
 **/

define('WLB_VERSION','4.2.0');
define('WLB_STANDALONE', true);
define('WLB_PATH', plugin_dir_path(__FILE__) ); 
define("WLB_URL", plugin_dir_url(__FILE__) ); 
define("WLB_SLUG", plugin_basename( __FILE__ ) );
define("WLB_ADMIN_ROLE",'administrator');
define("WLB_ADMIN_CAP",'wlb_options');
define("WLB_PLUGIN_CODE",'WLB');
//---
define('WLB_SUBSITE_ADMINISTRATOR',WLB_ADMIN_ROLE); 

function wlb_custom_theme_setup(){
	load_plugin_textdomain('wlb', null, dirname( plugin_basename( __FILE__ ) ).'/languages' );
	if(is_admin()&&!defined('TDOM_POP')){define('TDOM_POP',true);load_plugin_textdomain('pop', null, dirname( plugin_basename( __FILE__ ) ).'/options-panel/languages' );}
}
add_action('plugins_loaded', 'wlb_custom_theme_setup');

require_once WLB_PATH.'includes/class.plugin_white_label_branding.php';

$settings = array(
	'options_capability' 	=> WLB_ADMIN_CAP,
	'options_panel_version'	=> '2.7.6',
	'path'					=> WLB_PATH.'includes/',
	'url'					=> WLB_URL.'includes/',
	'pop_path'				=> WLB_PATH.'options-panel/',
	'pop_url'				=> WLB_URL.'options-panel/',
	'layout'				=> 'horizontal'
);

if( defined('MLPM_PATH') ){
	$settings['admin_menu_sort']=false;
}

global $wlb_plugin;
$wlb_plugin = new plugin_white_label_branding($settings);

//---register the starter bundle
//if(is_admin()){
//	add_filter( sprintf("%s_%s",$wlb_plugin->id.'-opt','bundles'), create_function('$t','$t[]=array("login_starter","'.__('Login starter','wlb').'","'.WLB_PATH.'bundles/login_starter.php'.'");return $t;'), 10, 1 );
//}

//-- Installation script:---------------------------------
function wlb_install(){
	$WP_Roles = new WP_Roles();	
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
		$WP_Roles->add_cap( WLB_ADMIN_ROLE, $cap );
	}
	include WLB_PATH.'includes/install.php';
	if(function_exists('handle_wlb_install'))handle_wlb_install( 'MWLB', WLB_PATH, WLB_PATH.'options-panel/' );	
}
register_activation_hook(__FILE__, 'wlb_install');
//-------------------------------------------------------- 
function wlb_uninstall(){
	$WP_Roles = new WP_Roles();
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
		$WP_Roles->remove_cap( WLB_ADMIN_ROLE, $cap );
	}
	//-----
}
register_deactivation_hook( __FILE__, 'wlb_uninstall' );
//--------------------------------------------------------
?>