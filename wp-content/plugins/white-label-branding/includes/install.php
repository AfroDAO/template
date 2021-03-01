<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

function handle_wlb_install($option_varname='MWLB', $path, $pop_path){
	//---
	$options = get_option($option_varname,array());
	$options = is_array($options)?$options:array();
	
	if(empty($options)){
		//----import old wlb settings-----
		$old_wlb_options = array('admin_color','developer_logo','developer_name','developer_url','editor_panel_content','editor_panel_title','enable_hide_administrator','enable_role_manager','enable_wlb_dashboard','force_color_scheme','header_bar_height','header_logo','hide_admin_bar','hide_admin_bar_profile','hide_contextual_help','hide_favorite_actions','hide_screen_options','hide_update_download','hide_update_nag','login_background','login_background_attachments','login_background_color','login_background_color_code','login_background_position','login_background_repeat','login_background_x','login_background_y','login_logo_url','login_styles_scripts','login_template','m_edit-comments_php','m_edit_php','m_index_php','m_link-manager_php','m_options-general_php','m_plugins_php','m_profile_php','m_separator-last','m_separator1','m_separator2','m_themes_php','m_tools_php','m_upload_php','m_users_php','panel_content','panel_title','remove_wordpress_from_title','sm_custom-background','sm_custom-header','sm_edit-comments_php','sm_edit_php','sm_export_php','sm_import_php','sm_index_php','sm_link-add_php','sm_link-manager_php','sm_media-new_php','sm_nav-menus_php','sm_options-discussion_php','sm_options-general_php','sm_options-media_php','sm_options-permalink_php','sm_options-privacy_php','sm_options-reading_php','sm_options-writing_php','sm_plugin-editor_php','sm_plugin-install_php','sm_plugins_php','sm_post-new_php','sm_profile_php','sm_themes_php','sm_tools_php','sm_update-core_php','sm_upload_php','sm_user-new_php','sm_users_php','sm_widgets_php','use_login_template');
		foreach($old_wlb_options as $option_name){
			$value = get_option($option_name);
			if(trim($value)=='')continue;
			$options[$option_name]=$value;
		}	
	}
	
	//--- update bundles
	$install_bundles = array(
		$path . 'includes/bundles/blue_sky_and_grass.php',
		$path . 'includes/bundles/old_mathematics.php',
		$path . 'includes/bundles/tiles_and_grass.php'
	);
	if(!class_exists('pop_importer'))require_once $pop_path.'class.pop_importer.php';
	$e = new pop_importer(array('plugin_id'=>'white-label-branding','options_varname'=>$option_varname,'resources_path'=>'white-label-branding','tdom'=>'wlb'));
	foreach($install_bundles as $path){
		$res = $e->import_from_file($path);
	}	
	if(empty($options)){
		//install saved options if no options are saved.
		$saved_options = $e->get_saved_options();
		if(is_array($saved_options)&&count($saved_options)>0){
			foreach($saved_options as $new_options){
				if(property_exists($new_options,'bundle') && in_array($new_options->bundle,array('tiles_and_grass'))){
					if(is_array($new_options->options)&&count($new_options->options)>0){
						foreach($new_options->options as $new_field => $new_value){
							$options[$new_field]=$new_value;
						}
					}
				}
			}
		}	
	}
	//--- end update bundles
	
	if(!empty($options)){
		update_option($option_varname,$options);
	}
}

?>