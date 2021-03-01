<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class wlb_settings {
	var $pre_user_query=false;
	var $restore_cap_msg='';
	function __construct(){
		global $wlb_plugin;
		$this->id = $wlb_plugin->id.'-opt';
		add_filter("pop-options_{$this->id}",array(&$this,'wlb_options'),10,1);	
		//add_filter('wlb_options_before',array(&$this,'wlb_options'),50,1);
		
		add_filter( 'editable_roles', array(&$this,'editable_roles'), 10, 1 );
		add_filter('admin_footer_text', array(&$this,'admin_footer_text'));
		
		add_action('admin_head', array(&$this,'hide_administrator'));
		if(1==$wlb_plugin->get_option('enable_hide_administrator') ){
			//add_action('pre_user_query',array(&$this,'pre_user_query'),10,1);
		}
	}
	
	function pre_user_query($wp_user_query){
		global $wlb_plugin,$wpdb,$wp_roles;
		if( !$wlb_plugin->is_wlb_administrator() ){			
			if( false===strpos($wp_user_query->query_from,'usermeta') ){
				$wp_user_query->query_from.=" INNER JOIN {$wpdb->usermeta} ON ({$wpdb->users}.ID = {$wpdb->usermeta}.user_id)";
			}
			$wp_capabilities = $wpdb->get_blog_prefix( $blog_id ) . 'capabilities';
			$wp_user_query->query_where .= " AND ({$wpdb->usermeta}.meta_key = '$wp_capabilities' AND CAST({$wpdb->usermeta}.meta_value AS CHAR) NOT LIKE '%\\\"".WLB_ADMIN_ROLE."\\\"%') ";			
		}	
	}
	
	function editable_roles($roles){
		if(isset($roles[WLB_ADMIN_ROLE])){
			$userdata = wp_get_current_user();
			if ( ($userdata instanceof WP_User) ){
				$WP_User = new WP_User($userdata->ID);
				if(!$WP_User->has_cap(WLB_ADMIN_ROLE)){
					unset($roles[WLB_ADMIN_ROLE]);
				}			
			}
		}
		return $roles;
	}
	function hide_administrator(){
		global $wlb_plugin;
		if(1==$wlb_plugin->get_option('enable_hide_administrator')){
			global $userdata,$wp_roles;
			if(isset($wp_roles->roles[WLB_ADMIN_ROLE])){
				$WP_User = new WP_User($userdata->ID);
				if(!$WP_User->has_cap(WLB_ADMIN_ROLE)){
					
					$wp_user_search = new WP_User_Query( array( 'role' => WLB_ADMIN_ROLE ) );
	         		$user_ids = $wp_user_search->get_results();  

					unset( $wp_roles->role_objects[WLB_ADMIN_ROLE] );
					unset( $wp_roles->role_names[WLB_ADMIN_ROLE] );
					unset( $wp_roles->roles[WLB_ADMIN_ROLE] );
	
					if(is_array($user_ids)&&count($user_ids)>0){
						$sel=array();
						foreach($user_ids as $o){
							$user_id = $o->ID;
							$sel[]="#the-list #user-$user_id";
						}

						echo "<style>".implode(',',$sel)."{display:none !important;}</style>";
						echo "<script type='text/javascript' >jQuery(document).ready(function(){jQuery('".implode(',',$sel)."').remove();});</script>";
?>
<script>
jQuery(document).ready(function($){
	var total_users = 0;
	$('.users-php .subsubsub li').each(function(i,el){
		
		val = $(el).find('.count').html();
		val = val.replace(/[(),]/g,'');
		
		if(i==0)return;
		total_users += parseInt(val);
		
	});
	
	$('.users-php .subsubsub li').first().find('.count').html("("+total_users+")");
});
</script>
<?php					
					}				
				}			
			}			
		}
	}	
	function admin_footer_text(){

	}
	function wlb_options($t,$for_admin=true){
		$i = count($t);
		//--------------------------
		global $wlb_plugin;
		if($wlb_plugin->is_wlb_administrator()){
			$i = count($t);
			$t[$i]=(object)array();
			$t[$i]->id 			= 'troubleshooting'; 
			$t[$i]->label 		= __('Troubleshooting','wlb');
			$t[$i]->right_label	= __('Troubleshooting','wlb');
			$t[$i]->page_title	= __('Troubleshooting','wlb');
			$t[$i]->theme_option = true;
			$t[$i]->plugin_option = true;
			$t[$i]->options = array();
			
			$t[$i]->options[] =	(object)array(
					'id'		=> 'enable_debug',
					'label'		=> __('Enable debug','wlb'),
					'type'		=> 'onoff',
					'description'=> __('Placeholder for future troubleshooting options.  Keep it turned off.','wlb'),
					'default'	=> '0',
					'el_properties'	=> array(),
					'save_option'=>true,
					'load_option'=>true
				);	
			
			if( current_user_can('wlb_role_manager') ){
				$this->handle_btn_restore_caps();
				
				$t[$i]->options[] =	(object)array(
					'type' => 'subtitle',
					'label'=> __('Emergency Admin restore','wlb')
				);
				
				$t[$i]->options[] =	(object)array(
						'id'		=> 'btn_restore_caps',
						'label'		=> __('Restore Administrative Capabilities','wlb'),
						'type'		=> 'callback',
						'callback'	=> array( &$this, 'cb_btn_restore_caps' ),
						'description'=> sprintf("<p>%s</p><p>%s</p><p><strong>%s</strong>&nbsp;%s</p>",
							__('Emergency Admin restore','wlb'),
							__('Choose a role from the dropdown, and press the button.  This will restore all the administrative capabilities to the chosen role.','wlb'),
							__('Warning','wlb'),
							__('This action grants admin capabilities to the chosen role, included the role manager.  The action cannot be undone.  You will need to go to the role manager and individually remove the granted capabilities.','wlb')
						),
						'el_properties'	=> array(),
						'save_option'=>true,
						'load_option'=>true
					);				
			}
			
			$t[$i]->options[] =	(object)array(
				'type' => 'subtitle',
				'label'=> __('Notifications','wlb')
			);			
		

		
		
			$t[$i]->options[]=(object)array('type'	=> 'clear');		
			$t[$i]->options[]=(object)array('label'=>__('Save changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' );							
		}
	
		//------------------
		$i = count($t);
		@$t[$i]->id 			= 'wlb_advanced_settings'; 
		$t[$i]->label 		= __('Advanced Settings','wlb');//title on tab
		$t[$i]->right_label	= '';//title on tab
		$t[$i]->page_title	= __('Advanced Settings','wlb');//title on content
		$t[$i]->options = array();
		
		if( $wlb_plugin->is_wlb_administrator() ):	
		
		$t[$i]->options[]=(object)array(
				'type'=>'subtitle',
				'label'=>__('WLB Administrator','wlb')	
			);
			
		$t[$i]->options[] =	(object)array(
				'id'		=> 'wlb_administrator',
				'label'		=> __('WLB administrator','wlb'),
				'type'		=> 'text',
				'description'=> __('Specify comma separated users to be a WLB administrator.  Hiding options will apply to other administrators.','wlb'),
				'el_properties'	=> array('class'=>'widefat'),
				'save_option'=>true,
				'load_option'=>true
				);		
					
		endif;			
					
					
		$t[$i]->options[]=(object)array(
				'type'=>'subtitle',
				'label'=>__('Role and Capability Manager','wlb')	
			);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'enable_role_manager',
				'label'		=> __('Enable Role and Capability Manager','wlb'),
				'type'		=> 'onoff',
				'description'=> __('Select yes and save.  A new panel with Role Management options will display in this options screen.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);		
		$t[$i]->options[]=(object)array(
				'type'=>'subtitle',
				'label'=>__('Email branding','wlb')	
			);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'enable_email_branding',
				'label'		=> __('Enable email branding','wlb'),
				'type'		=> 'onoff',
				'default'	=> 1,
				'description'=> __('Choose no if you are having problems with the emails sent by the system.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);						
		$t[$i]->options[] =	(object)array(
				'id'		=> 'enable_hide_administrator',
				'label'		=> __('Hide the Administrator role from users list.','wlb'),
				'type'		=> 'onoff',
				'description'=> __('This option allows you to hide the Administrator role from the user list.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);		
		
		$t[$i]->options[]=(object)array(
				'type'=>'subtitle',
				'label'=>__('Custom Dashboard','wlb')	
			);	
		$t[$i]->options[] =	(object)array(
				'id'		=> 'enable_wlb_dashboard',
				'label'		=> __('Enable WLB Custom Dashboard Tool','wlb'),
				'type'		=> 'onoff',
				'description'=> __('Select yes and save.  On the admin left menu a new menu option will appear.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);				
		/*	
		$t[$i]->options[]=(object)array(
				'type'=>'subtitle',
				'label'=>__('Other modules','wlb')	
			);
					
		$t[$i]->options[] =	(object)array(
				'id'		=> 'enable_color_scheme',
				'label'		=> __('Original Color Scheme','wlb'),
				'type'		=> 'onoff',
				'default'	=> '1',
				'description'=> __('With the introduction of the Visual CSS Editor Addon, this module is not needed.  Choose Off skip this module.','wlb'),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);	
				
		$t[$i]->options[] =	(object)array(
				'id'		=> 'enable_wlb_login',
				'label'		=> __('Login branding','wlb'),
				'type'		=> 'onoff',
				'default'	=> '0',
				'description'=> sprintf(__('This module has been depreciated at the release of version 4.1.2. It will be removed from the plugin at the end of the year. For easy styling of the default WordPress login, please download the Visual CSS Editor from the %s','wlb'),
					'<a href="' .admin_url('admin.php?page=white-label-branding-dc') . '">' . __('downloads menu','wlb') . '</a>'
				),
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
				);	
		*/	
		$t[$i]->options[]=(object)array(
				'type'=>'subtitle',
				'label'=>__('Notifications','wlb')	
			);
					
		$t[$i]->options[] = (object)array(
				'id'		=> 'enable_notifications',
				'label'		=> __('Enable notifications on non options pages.','rhc'),
				'type'		=> 'yesno',
				'default'	=> '1',
				'el_properties'	=> array(),
				'save_option'=>true,
				'load_option'=>true
			);					
					
		$t[$i]->options[]=(object)array('type'	=> 'clear');		
		$t[$i]->options[]=(object)array('label'=>__('Save changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' );	
		//------------------	
		return $t;
	}
	
	function handle_btn_restore_caps(){
		if( isset($_POST['btn_restore_roles']) && isset($_POST['restore_role']) && !empty($_POST['restore_role']) ){
			$restore_role = $_POST['restore_role'];
			
			global $wpdb;
			$roles = get_option(  $wpdb->prefix.'user_roles' );
			if(isset($roles[$restore_role])&&isset($roles[$restore_role]['capabilities'])){
				$administrator_caps = array(
					"switch_themes", "edit_themes", "activate_plugins", "edit_plugins", "edit_users", "edit_files", "manage_options", "moderate_comments", "manage_categories", "manage_links", "upload_files", "import", "unfiltered_html", "edit_posts", "edit_others_posts", "edit_published_posts", "publish_posts", "edit_pages", "read", "level_10", "level_9", "level_8", "level_7", "level_6", "level_5", "level_4", "level_3", "level_2", "level_1", "level_0", "edit_others_pages", "edit_published_pages", "publish_pages", "delete_pages", "delete_others_pages", "delete_published_pages", "delete_posts", "delete_others_posts", "delete_published_posts", "delete_private_posts", "edit_private_posts", "read_private_posts", "delete_private_pages", "edit_private_pages", "read_private_pages", "delete_users", "create_users", "unfiltered_upload", "edit_dashboard", "update_plugins", "delete_plugins", "install_plugins", "update_themes", "install_themes", "update_core", "list_users", "remove_users", "add_users", 
					"promote_users", "edit_theme_options", "delete_themes", "export", "manage_staging", "view_restricted_content",
					'wlb_branding','wlb_navigation','wlb_login','wlb_color_scheme',	'wlb_options','wlb_role_manager',	'wlb_license',	'wlb_downloads','wlb_dashboard_tool'
				);
				foreach($administrator_caps as $capability){
					$roles[$restore_role]['capabilities'][$capability]=1;
				}
				update_option( $wpdb->prefix.'user_roles' ,$roles);
				//$this->restore_cap_msg = sprintf( __('Done, %s capabilities updated.','wlb'), $restore_role );
			}else{
				//$this->restore_cap_msg = __('Roles not found','wlb');
			}			
		}
	}
	
	function cb_btn_restore_caps($tab,$i,$o,&$save_fields){
		$WP_Roles = new WP_Roles();
		$output = '<select name="restore_role" >';
		if(count($WP_Roles->roles)>0){
			foreach($WP_Roles->roles as $role_id => $row){
				if( in_array($role_id,array('subscriber','contributor','author','moderator','editor'))) continue;
				$selected = isset( $_POST['restore_role'] ) && $_POST['restore_role']==$role_id ? 'selected="selected"': ''; 
				$output.= '<option '.$selected.' value="'.$role_id.'">'.$row['name'].'</option>';
			}
		}
		$output .= '</select>';		
		$output .= sprintf('&nbsp;<input onClick="javascript:return confirm(\'%s\');" type="submit" class="button-primary" name="btn_restore_roles" value="%s" >',
			__('Please confirm that you want to grant admin capabilities to the selected role','wlb'),
			$o->label
		);
		if(!empty($this->restore_cap_msg)){
			$output.="<br><p>".$this->restore_cap_msg."</p>";
		}
		return $output;
	}
	
}

?>