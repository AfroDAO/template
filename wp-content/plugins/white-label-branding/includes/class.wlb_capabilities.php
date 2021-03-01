<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class wlb_capabilities {
	function __construct(){
		global $wlb_plugin;
		$this->id = $wlb_plugin->id.'-cap';		
		add_filter("pop-options_{$this->id}",array(&$this,'wlb_options'),10,1);	
		add_action('pop_handle_save',array(&$this,'pop_handle_save'),50,1);
		//add_action('wlb_handle_save',array(&$this,'wlb_handle_save'),50);
		//--edit users form		
		add_action('edit_user_profile',array(&$this,'edit_user_profile'),50);
		add_action('edit_user_profile_update', array(&$this,'edit_user_profile_update'));		
		//--
		add_action('admin_init',array(&$this,'admin_init'));
		
		
	}
	
	function admin_init(){
		if( defined('DOING_AJAX') && DOING_AJAX ) return;
		global $wlb_plugin;
		$current_user = wp_get_current_user();
		if(is_array($current_user->roles)&&$current_user->roles>0){
			foreach($current_user->roles as $role_id){
				if( '1'== $wlb_plugin->get_option('disable_wpadmin_'.$role_id,'') ){
					if( $wlb_plugin->is_wlb_administrator() ) return;
					if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')return;//do not redirect for ajax, as many plugins call ajax for non-logged users.
					$url = $wlb_plugin->get_option('disabled_wpadmin_url_'.$role_id, site_url('/') );
					wp_redirect($url);
					die();
				}
			}
		}
	}
	
	function edit_user_profile_update(){
		global $user_id;
		$WP_Roles = new WP_Roles();
		$WP_User = new WP_User($user_id);
		if(isset($_POST['user_enable_custom_cap'])&&$_POST['user_enable_custom_cap']==1){
			update_user_meta($user_id,'user_enable_custom_cap',1);
			$WP_User->remove_all_caps();
			$all_caps = $this->get_all_caps_from_wp_roles($WP_Roles);
			if(is_array($_POST['ROLES'])&&count($_POST['ROLES'])>0){
				foreach($all_caps as $capability){
					if(array_key_exists($capability,$_POST['ROLES'])){
						$WP_User->add_cap( $capability, true );
					}else{
						$WP_User->add_cap( $capability, false );
					}
				}
			}
		}else{
			update_user_meta($user_id,'user_enable_custom_cap',0);
			$WP_User->remove_all_caps();
		}
	}
	
	function edit_user_profile(){
		global $user_id;
		
		global $wlb_plugin;
		if(WLB_ADMIN_ROLE!=$wlb_plugin->get_user_role()){
			//only display for the site admiinstrator.
			return;
		} 
		
		$WP_Roles = new WP_Roles();
		$WP_User = new WP_User($user_id);

		$all_caps = $this->get_all_caps_from_wp_roles($WP_Roles);
		sort($all_caps);

		$output = '<style>.capability-option{float:left;width:210px;}</style>';
		
		$output .= sprintf("<tr><th colspan='3' style=\"text-align:left;\"><h4>%s</h4></th></tr>",__('Customized User capabilities','wlb'));
		$output .= "<tr><td colspan=\"3\">";
		foreach($all_caps as $capability){
			$checked = isset($WP_User->allcaps[$capability])&&$WP_User->allcaps[$capability]==1?'checked="checked"':'';
			$output .= "<span class=\"capability-option\"><input type=\"checkbox\" $checked name=\"ROLES[$capability]\" value=1 />&nbsp;".str_replace('Wlb','WLB',ucfirst(str_replace("_"," ",$capability)))."</span>&nbsp; ";
		}
		$output .= "</td></tr>";	
		
		$user_enable_custom_cap = 0==intval(get_user_meta($user_id,'user_enable_custom_cap',true))?0:1;			
?>
<h3><?php _e('WLB Role Manager') ?></h3>
<p>
<input id="user_enable_custom_cap" type="checkbox" <?php echo $user_enable_custom_cap==1?'checked="checked"':'' ?> name="user_enable_custom_cap" value="1" />&nbsp;<?php _e('Enable Customized User Capabilities.','wlb')?><br />
<span class="description"><?php _e('Check this option to modify the Customized User Capabilities.  If you uncheck this option and save, all Customized Capabilities will be removed from the user, and only those on the selected user role will apply.','wlb')?></span>
</P>
<table class="customized-user-caps" <?php echo $user_enable_custom_cap==0?'style="display:none;"':'' ?> >
<?php echo $output ?>
</table>
<script>
jQuery(document).ready(function($){   
	$('#user_enable_custom_cap').click(function(){
		if($(this).attr('checked')){
			$('.customized-user-caps').slideDown();
		}else{
			$('.customized-user-caps').slideUp();
		}
	});
});
</script>
<?php	
	}
	
	function wlb_options($t,$for_admin=true){
		$i = count($t);
		//-----
		$WP_Roles = new WP_Roles();
		$role_names = $WP_Roles->get_names();
		$all_caps = $this->get_all_caps_from_wp_roles($WP_Roles);
		sort($all_caps);
			
		foreach($role_names as $role_id => $role_name){
			$i = count($t);
			@$t[$i]->id 			= 'role_'.$role_id; 
			$t[$i]->label 		= sprintf(__('%s','wlb'),$role_name);//title on tab
			$t[$i]->right_label	= sprintf(__('Manage %s Capabilities','wlb'),$role_name);//title on tab
			$t[$i]->page_title	= sprintf(__('%s Capabilities','wlb'),$role_name);//title on content
			$t[$i]->role_id 	= $role_id;
			$t[$i]->role_name	= $role_name;
			$t[$i]->name 		= $role_name;
			$t[$i]->all_caps 	= $all_caps;
		
			$t[$i]->options = array();	
			
			$t[$i]->options[] = (object)array(
					'type'=>'subtitle',
					'label'=>sprintf(__('Manage %s Capabilities','wlb'),$role_name)
				);
			
			$t[$i]->options[] = (object)array(
					'type'=>'callback',
					'callback'=>array(&$this,'_role_capabilities')
				);
			
			$t[$i]->options[] = (object)array('type'	=> 'clear') ;
			
			$t[$i]->options[] = (object)array('type'	=> 'clear') ;
			
			$t[$i]->options[] = (object)array(
					'id'=>'disable_wpadmin_'.$role_id,
					'label'=> sprintf(__('Disable wp-admin for role %s','wlb'),$role_name),
					'type'=>'checkbox',
					'save_option'=>true,
					'load_option'=>true
				);
			
			$t[$i]->options[] = (object)array(
					'id'=>'disabled_wpadmin_url_'.$role_id,
					'label'=> sprintf(__('Provide a URL to redirect user if wp-admin is disabled.','wlb'),$role_name),
					'description'=>__('Do not provide a link on the wp-admin or it will loop endlessly.','wlb'),
					'type'=>'text',
					'el_properties'=>array('style'=>'width:380px'),
					'save_option'=>true,
					'load_option'=>true
				);
			
			$t[$i]->options[] = (object)array('type'	=> 'clear');
			
			$t[$i]->options[] = (object)array('type'	=> 'clear','el_properties'=>array('style'=>'margin-bottom:12px;'));
			
			$t[$i]->options[] = (object)array('type'=>'submit','class'=>'button-primary', 'label'=> __('Save changes','wlb'));
		}		
		//-----
		/* all caps in a sinngle tab.
		$i = count($t);
		$t[$i]->id 			= 'capabilities'; 
		$t[$i]->label 		= __('Capabilities','wlb');//title on tab
		$t[$i]->right_label	= __('Manage role capabilities','wlb');//title on tab
		$t[$i]->page_title	= __('Capabilities','wlb');//title on content
		$t[$i]->open 		= true;
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Roles and capabilities','wlb')	
			),
			(object)array(
				'type'=>'description',
				'label'=>__('Manage wordpress roles and capabilities.','wlb')
			),
			(object)array(
				'type'=>'callback',
				'callback'=>array(&$this,'capabilities')
			),
			(object)array('type'	=> 'clear'),		
			(object)array('type'=>'submit','class'=>'button-primary', 'label'=> __('Save changes','wlb'))
		);	
		*/
		//--------
		$i = count($t);
		@$t[$i]->id 			= 'self_rescue'; 
		$t[$i]->label 		= __('Self rescue','wlb');//title on tab
		$t[$i]->right_label	= __('Self rescue','wlb');//title on tab
		$t[$i]->page_title	= __('Self rescue','wlb');//title on content
		$t[$i]->priority	= 10;
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Administrator role capabilities restore','wlb')	
			)	
		);	
		
		$t[$i]->options[] = (object)array(
				'id'=>'wlb_panic_key',
				'label'=> __('Panic key','wlb'),
				'description'=>__('Set a key for the self rescue link.','wlb'),
				'type'=>'text',
				'default'=>'default',
				'el_properties'=>array('style'=>'width:380px'),
				'save_option'=>true,
				'load_option'=>true
			);	
		
		global $wlb_plugin;
		$panic = $wlb_plugin->get_option( 'wlb_panic_key', 'default', true );
		
		$t[$i]->options[] = (object)array(
				'id'=>'wlb_self_rescue_link',
				'label'=> __('Self rescue link','wlb'),
				'description'=>__('Copy paste this url to a secure location.  If you lock yourselve out while modifying the administrator role, copy paste the link to the browser to restore core capabilities to the administrator role.','wlb'),
				'type'=>'textarea',
				'default' => site_url('/?wlb_panic=' . $panic ),
				'el_properties'=>array('class'=>'widefat','readonly'=>'readonly'),
				'save_option'=>false,
				'load_option'=>true
			);	
			
		$t[$i]->options[] = (object)array('type'	=> 'clear');	
		
		$t[$i]->options[] = (object)array('type'=>'submit','class'=>'button-primary', 'label'=> __('Save changes','wlb'));		
		//--------
		$i = count($t);
		@$t[$i]->id 			= 'new_roles'; 
		$t[$i]->label 		= __('Roles and Capabilities','wlb');//title on tab
		$t[$i]->right_label	= __('Add new Roles and Capabilities','wlb');//title on tab
		$t[$i]->page_title	= __('Add new Roles and Capabilities','wlb');//title on content
		$t[$i]->priority	= 10;
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Add new Roles and Capabilities','wlb')	
			),
			(object)array(
				'type'=>'callback',
				'callback'=>array(&$this,'new_capabilities')
			)		
		);	
		
		return $t;
	}
	
	
	function pop_handle_save($pop){
		global $wlb_plugin;
		if($wlb_plugin->options_varname!=$pop->options_varname)return;
		if(!$wlb_plugin->is_wlb_administrator())
			return;

		if( isset($_POST['ROLES']) && is_array($_POST['ROLES'])&&count($_POST['ROLES'])>0){		
			global $wp_roles;
			$WP_Roles = new WP_Roles();

			$all_caps = $this->get_all_caps_from_wp_roles($WP_Roles);
			foreach($_POST['ROLES'] as $role_id => $capabilities){
				foreach($all_caps as $capability){
					if(array_key_exists($capability,$capabilities)){
						//echo "Adding $capability to role $role_id<br />";
						$WP_Roles->add_cap( $role_id, $capability );
					}else{
						//echo "Removing $capability from role $role_id<br />";
						$WP_Roles->remove_cap( $role_id, $capability );
					}
				}
			}
			if(trim($_POST['new_role_name'])!=''){
				$WP_Roles->add_role( strtolower(str_replace(" ","_",$_POST['new_role_name'])), $_POST['new_role_name'] );
				$WP_Roles = new WP_Roles();
			}
		
			if(trim($_POST['new_capability'])!='' && isset($_POST['role_to_add_caps']) && $_POST['role_to_add_caps']!=''){
				$WP_Roles->add_cap( $_POST['role_to_add_caps'], strtolower(str_replace(" ","_",$_POST['new_capability'])) );
				$WP_Roles = new WP_Roles();
			}
		
			if(isset($_POST['f_delete_role']) /*&& isset($_POST['role_to_delete']) && trim($_POST['role_to_delete'])!=''*/){
				$WP_Roles->remove_role( $_POST['role_to_delete'] );//!!
				$WP_Roles = new WP_Roles();
			}
			
			$wp_roles = $WP_Roles;						
		}
	}
	
	function get_all_caps_from_wp_roles($WP_Roles){
		$all_caps = array();
		if(count($WP_Roles->roles)>0){
			foreach($WP_Roles->roles as $role_id => $row){
				foreach($row['capabilities'] as $capability => $allowed){
					$all_caps[$capability]=$capability;
				}
			}
		}
		return $all_caps;	
	}
	
	function role_capabilities($WP_Roles,$role_id,$role_name,$all_caps){
		$output = sprintf('<div class="pt-option pt-option-subtitle "><h3 class="option-panel-subtitle">%s</h3></div>',$role_name);
		$output .= "<div class=\"pt-option pt-role-manager\">";
		foreach($all_caps as $capability){
			$checked = isset($WP_Roles->roles[$role_id]['capabilities'][$capability])&&$WP_Roles->roles[$role_id]['capabilities'][$capability]==1?'checked="checked"':'';
			$output .= "<span class=\"pt-label capability-option\"><input type=\"checkbox\" $checked name=\"ROLES[$role_id][$capability]\" value=1 />&nbsp;".str_replace('Wlb','WLB',ucfirst(str_replace("_"," ",$capability)))."</span>&nbsp; ";
		}
		$output .= "<div class=\"pt-clear\"></div>";	
		$output .= "</div>";	
		return $output;
	}
	
	function _role_capabilities($tab,$i,$o,&$save_fields){
		$WP_Roles = new WP_Roles();
		return $this->role_capabilities($WP_Roles,$tab->role_id,$tab->role_name,$tab->all_caps);
	}
	
	function capabilities(){
		$WP_Roles = new WP_Roles();
		$role_names = $WP_Roles->get_names();
		$all_caps = $this->get_all_caps_from_wp_roles($WP_Roles);
		sort($all_caps);
		//--
		$output = '';
		foreach($role_names as $role_id => $role_name){
			$output .= $this->role_capabilities($WP_Roles,$role_id,$role_name,$all_caps);
		}
		return $output;
	}
	
	function new_capabilities(){
		if(!class_exists('pop_input'))require_once WLB_PATH.'options-panel/class.pop_input.php';
		$WP_Roles = new WP_Roles();
		ob_start();
?>
<div class="pt-clear"></div>
<div class="pt-option pt-option-hr ">&nbsp;</div>

<?php sprintf('<div class="pt-option pt-option-subtitle "><h3 class="option-panel-subtitle">%s</h3></div>',__('New roles and capabilities','wlb'));?>

<?php echo pop_input::translucent_description(__('Type a name for the new role and press add, you can select Capabilities for it after saving.','wlb'));?>
<div class="pt-option">
	<span class="pt-label"><?php _e('New role name:','wlb')?></span>
	<input type="text" name="new_role_name" size="40" value="" />&nbsp;<input type="submit" value="<?php _e('Add','wlb') ?>" name="theme_options_submit" class="button-primary">
</div>

<?php echo pop_input::translucent_description(__('Select the role that you want to add this Capability to (will activate it immediately for that role). Capability names can contain letters and numbers.  The capability will be saved lowercase and spaces replaced with underscore.','wlb'));?>
<div class="pt-option">
	<span class="pt-label"><?php echo sprintf(__("New capability %s<br /> for role %s",'wlb'),'<input type="text" name="new_capability" value="" />',$this->roles_dropdown($WP_Roles,'role_to_add_caps','role_to_add_caps',''))?></span>
	<input type="submit" value="<?php _e('Add','wlb') ?>" name="theme_options_submit" class="button-primary">
</div>	
<div class="pt-clear"></div>

<?php echo pop_input::translucent_description(__('This action cannot be undone and can have severe consequences, proceed only if you know what you are doing.','wlb'));?>
<div class="pt-option">
	<span class="pt-label"><?php _e('Delete role','wlb')?>:&nbsp;<?php echo $this->roles_dropdown($WP_Roles,'role_to_delete','role_to_delete','',array(WLB_ADMIN_ROLE)) ?></span>
	<input OnClick="javascript:return confirm('<?php _e('WARNING! Deleting a role can have severe consequences, proceed only if you know what you are doing.','wlb')?>');" type="submit" value="<?php _e('Delete','wlb') ?>" name="f_delete_role" class="button-primary">
</div>
<div class="pt-clear"></div>

<?php		
		$output.=ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	function roles_dropdown($WP_Roles,$id,$name,$misc,$exclude=array()){
		$str = '';
		$role_names = $WP_Roles->get_names();
		if(count($role_names)>0){
			$str .= sprintf("<select id=\"%s\" name=\"%s\" $misc>",$id,$name,$misc);
			$str.=sprintf("<option value=\"%s\">%s</option>",'',__('--choose--','wlb') );
			foreach($role_names as $role_id => $role_name){
				if(in_array($role_id,$exclude))
					continue;
				$str.=sprintf("<option value=\"%s\">%s</option>",$role_id,$role_name);
			}
			$str .= "</select>";
		}
		return $str;
	}
}
?>