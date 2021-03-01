<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class wlb_dashboard {
	var $content = array();
	var $show_ui = false;
	var $widgets_form = '';
	function __construct($args=array()){
		global $wlb_plugin;
		//------
		$defaults = array(
			'id'				=> $wlb_plugin->id.'-bra',
			'show_ui'			=> true,
			'show_in_menu'		=> 'white-label-branding',
			'menu_name'			=> __('Dashboard Tool','wlb')
		);
		foreach($defaults as $property => $default){
			$this->$property = isset($args[$property])?$args[$property]:$default;
		}	
		//-----	
		add_action('init',array(&$this,'dashboard_init'));
		add_action('init',array(&$this,'init'));
		add_action('wp_dashboard_setup', array( &$this,'add_dashboard_widgets'), 999999 );
		add_action('admin_head',array(&$this,'admin_head'));
		add_action('admin_footer',array(&$this,'admin_footer'));
		//-------------
		add_filter("pop-options_{$this->id}",array(&$this,'wlb_options'),10,1);		
		add_action("pop_admin_head_{$this->id}",array(&$this,'pop_admin_head'));	
		add_action('pop_handle_save',array(&$this,'pop_handle_save'),50,1);
		//metabox on dash edit
		//add_action('admin_menu', array(&$this, 'post_meta_box') );
		add_filter('screen_settings', array(&$this,'screen_settings'), 10, 2);
		add_action('admin_init',array(&$this,'disable_welcome_panel'));
	}
	
	function wlb_options($t,$for_admin=true){
		$i=intval(count($t));
		//-------
		$i = count($t);
		$t[$i]=(object)array();
		$t[$i]->id 			= 'dashboard'; 
		$t[$i]->label 		= __('Dashboard','wlb');//title on tab
		$t[$i]->right_label	= __('Customize Dashboard Panels','wlb');//title on tab
		$t[$i]->page_title	= __('Dashboard','wlb');//title on content
		$t[$i]->options = array(
			(object)array(
				'id'		=> 'use_public_dashboard',
				'label'		=> __('Enable Public Dashboard Panel','wlb'),
				'type'		=> 'yesno',
				'description'=> __('This will appear on the dashboard to any user roll.','wlb'),
				'el_properties'	=> array('OnClick'=>'javascript:yesno_panel(this,\'.use_public_dashboard\');'),
				'hidegroup'	=> '#public_dash_group',
				'save_option'=>true,
				'load_option'=>true
				),
			(object)array(
				'type'=>'clear'
			),				
			(object)array(
				'id'	=> 'public_dash_group',
				'type'=>'div_start'
			),
			(object)array(
				'id'	=> 'panel_title',
				'type'=>'text',
				'label'=>__('Title','wlb'),
				'el_properties' => array('class'=>'text-width-full'),
				'description'=> __('Add a title for your Custom Panel.','wlb'),
				'save_option'=>true,
				'load_option'=>true,
				'row_class'=>'use_dashboard'
			),
			(object)array(
				'id'	=> 'panel_content',
				'type'=>'textarea',
				'label'=>__('Panel Content','wlb'),
				'el_properties' => array('rows'=>'15','cols'=>'50'),
				'description'=> __('This is shown to any logged user.','wlb'),
				'save_option'=>true,
				'load_option'=>true,
				'row_class'=>'use_dashboard'
			),
			(object)array(
				'id'=>'public_dash_group',
				'type'=>'div_end'
			),
			(object)array(
				'id'		=> 'use_editor_dashboard',
				'label'		=> __('Enable Editor Dashboard Panel','wlb'),
				'type'		=> 'yesno',
				'description'=> __('This will appear on the dashboard to the Editor user roll.','wlb'),
				'el_properties'	=> array('OnClick'=>'javascript:yesno_panel(this,\'.use_editor_dashboard\');'),
				'hidegroup'	=> '#editor_dash_group',
				'save_option'=>true,
				'load_option'=>true
				),		
			(object)array(
				'type'=>'clear'
			),	
			(object)array(
				'id'	=> 'editor_dash_group',
				'type'=>'div_start'
			),								
			(object)array(
				'id'	=> 'editor_panel_title',
				'type'=>'text',
				'label'=>__('Title','wlb'),
				'el_properties' => array('class'=>'text-width-full'),
				'description'=> __('Add a title for your Custom Panel.','wlb'),
				'save_option'=>true,
				'load_option'=>true
			)	,
			(object)array(
				'id'	=> 'editor_panel_content',
				'type'=>'textarea',
				'label'=>__('Panel content','wlb'),
				'el_properties' => array('rows'=>'15','cols'=>'50'),
				'description'=> __('This is shown to the Editor. Add your own unique message to your client. We recommend that you add contact details or a link to how your client can get help. You can use HTML tags in this field.','wlb'),
				'save_option'=>true,
				'load_option'=>true
			),		
			(object)array(
				'type'=>'div_end'
			),
	 		(object)array(
	 				'id'		=> 'use_admin_dashboard',
	 				'label'		=> __('Enable Admin Dashboard Panel','wlb'),
	 				'type'		=> 'yesno',
	 				'description'=> __('Enable the Administrators Dashboard Panel','wlb'),
	 				'el_properties'	=> array('OnClick'=>'javascript:yesno_panel(this,\'.use_admin_dashboard\');'),
					'hidegroup'	=> '#admin_dash_group',
	 				'save_option'=>true,
	 				'load_option'=>true
	 		),	
			(object)array(
				'type'=>'clear'
			),		
			(object)array(
				'id'	=> 'admin_dash_group',
				'type'=>'div_start'
			),							
	 		(object)array(
	 				'id'	=> 'administrator_panel_title',
	 				'type'=>'text',
	 				'label'=>__('Title','wlb'),
	 				'el_properties' => array('class'=>'text-width-full'),
	 				'description'=> __('Add a title for your Custom Panel.','wlb'),
	 				'save_option'=>true,
	 				'load_option'=>true,
	 				'row_class'=>'use_admin_dashboard'
	 		),
	 		(object)array(
	 			'id'	=> 'administrator_panel_content',
	 			'type'=>'textarea',
	 			'label'=>__('Panel Content','wlb'),
	 			'el_properties' => array('rows'=>'15','cols'=>'50'),
	 			'description'=> __('This is shown to blog Administrators. Add your own unique message to your client. We recommend that you add contact details or a link to how your client can get help. You can use HTML tags in this field.','wlb'),
	 			'save_option'=>true,
	 			'load_option'=>true,
	 			'row_class'=>'use_admin_dashboard'
	 		),						
			(object)array(
				'type'=>'div_end'
			),	
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Dashboard branding','wlb')	
			),	
			(object)array(
				'type'=>'clear'
			),					
			(object)array(
				'id'	=> 'dashboard_icon',
				'type'	=>'fileuploader',
				'label'	=> __('Dashboard Icon URL','wlb'),
				'description'=> __('URL to an image that will replace the dashboard icon','wlb'),
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array(
				'type'=>'clear'
			),					
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Remove Dashboard Widgets','wlb')	
			),		
			(object)array(
				'type'=>'description',
				'description'=>__('Check the dashboard widgets that you will like to be removed. This applies to all users included the administrator.','wlb')	
			)													
		);		
		
		$t[$i]->options[]=(object)array(
				'id'		=> 'remove_dashboard_meta_box',
				'label'		=> __('Remove recent drafts','wlb'),
				'type'		=> 'callback',
				'callback'	=> array(&$this,'remove_dashboard_meta_box')
				);	
		$t[$i]->options[]=(object)array('type'=>'clear');
	 	$t[$i]->options[]=(object)array(
	 				'id'		=> 'disable_welcome_panel',
	 				'label'		=> __('Disable dashboard welcome panel','wlb'),
	 				'type'		=> 'yesno',
	 				'description'=> __('Disable dashboard welcome panel','wlb'),
	 				'save_option'=>true,
	 				'load_option'=>true
	 		);		
		$t[$i]->options[]=(object)array('type'=>'clear');		
		$t[$i]->options[]=(object)array('label'=>__('Save Changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' );	
		//-------
		return $t;
	}

	function pop_handle_save($pop){
		global $wlb_plugin;
		if($wlb_plugin->options_varname!=$pop->options_varname)return;
		if(!current_user_can('wlb_branding'))
			return;
		
		if(isset($_REQUEST['wlb_skip_dashboard_widgets']))
			return;
		
		//bug fix: cannot uncheck all and save.
		//if(!isset($_REQUEST['wlb_dashboard_widgets']))
		if(!isset($_REQUEST['dashboard_icon']))
			return;
			
		$existing_options = get_option($pop->options_varname);
		$existing_options = is_array($existing_options)?$existing_options:array();
		
		$existing_options['wlb_dashboard_widgets'] = @$_REQUEST['wlb_dashboard_widgets'];
		update_option($pop->options_varname,$existing_options);
		
	}
	
	function pop_admin_head(){
		$url = admin_url();
?>
<script type='text/javascript'>
jQuery(document).ready(function($){
load_list_of_widgets();

});
var load_list_tries = 0;
function load_list_of_widgets(){
	if(load_list_tries++>2)return;
	jQuery(document).ready(function($){
		$.post('<?php echo $url?>',{'wlb-get-dashboard-widgets':1},function(data){
			if( $(data).find('#wlb-dashboard-widgets-holder').length > 0 ){
				$('#list-of-dashboard-widgets').html( $(data).find('#wlb-dashboard-widgets-holder').show() );
			}else{
				$('#list-of-dashboard-widgets').html( '<input type="hidden" name="wlb_skip_dashboard_widgets" value="1" />' );
				load_list_of_widgets();
			}
		},'html');
	});
}
</script>
<?php	
	}
	
	function remove_dashboard_meta_box(){
?><div id="list-of-dashboard-widgets"></div><?php	
	}
	
	function admin_footer(){
		echo $this->widgets_form;
	}
	
	function admin_output_dashboard_info(){
		global $wlb_plugin,$wp_meta_boxes;
		if(isset($_REQUEST['wlb-get-dashboard-widgets']) && (current_user_can('wlb_branding') || $wlb_plugin->is_wlb_administrator()) ){
			if(is_array($wp_meta_boxes['dashboard'])&&count($wp_meta_boxes['dashboard'])>0){
				$dashboard_widgets = array();
				foreach($wp_meta_boxes['dashboard'] as $context => $pages){
					if(is_array($pages)&&count($pages)>0){
						foreach($pages as $page => $widgets ){
							if(is_array($widgets)&&count($widgets)>0){
								foreach($widgets as $id => $w){
									if(false!==strpos($w['id'],'wlbdash'))
										continue;
									if(in_array($w['id'],array('custom_panel')))
										continue;
									$dashboard_widgets[] = $w;
								}
							}
						}					
					}
				}
				
				if(count($dashboard_widgets)>0){
					ob_start();
					$hidden_widgets = $wlb_plugin->get_option('wlb_dashboard_widgets',array());
?>
<div id="wlb-dashboard-widgets-holder" class="wlb-dashboard-widgets-holder" style="display:none;">
<?Php foreach($dashboard_widgets as $w):
	$title = strip_tags( $w['title'] );
?>
	<div class="wlb-dashboard-widgets-holder">
		<input type="checkbox" <?php echo in_array($w['id'],$hidden_widgets)?'checked="checked"':''?> name="wlb_dashboard_widgets[]" value="<?php echo $w['id'] ?>" />&nbsp;<?php echo $title/*echo strip_tags($w['title'])*/ ?>	
	</div>
<?php endforeach; ?>
</div>
<?php				
					$this->widgets_form = ob_get_contents();
					ob_end_clean();
				}	
			}
		}
	}

	function screen_settings($content, $screen){
		global $wlb_plugin,$wp_meta_boxes;
		if( !isset($wp_meta_boxes[$screen->id]) || 0==count($wp_meta_boxes[$screen->id]) )return $content;
		ob_start();
?>
	<div id="extra-dash-screen-options">
		<input type="hidden" name="screen_id" value="<?php echo $screen->id ?>" />
		<input id="btn-reset-dashboard-layout" name="reset_my_dashboard" type="submit" class="button-secondary" value="<?PHP _e('Reset Layout','wlb')?>" />
<?php if($wlb_plugin->is_wlb_administrator()): ?>
		<h5><?PHP _e('Layout settings (Admin only)','wlb')?></h5>
		<input type="submit" class="button-secondary" name="dashboard_layout_setup" value="<?PHP _e('Save Layout as default layout','wlb')?>" />
		<div class="wlb-form-user-layout"><?php _e('Username:','wlb')?>&nbsp;<input type="text" name="dashboard_layout_user" value="" />&nbsp;
		<input type="submit" class="button-secondary" name="user_dashboard_layout_setup" value="<?PHP _e('Save Layout to user','wlb')?>" />
		</div>
<?php endif; ?>		
	</div>
<?php
		$content .= ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	function admin_head(){
		global $wpdb,$userdata,$wlb_plugin;
		
		$screen = get_current_screen();
		if($screen->id=='dashboard'){
			$dashboard_icon = $wlb_plugin->get_option('dashboard_icon');
		}else{
			$dashboard_icon = '';
		}
?>
<script type='text/javascript'>
jQuery(document).ready(function($){ 
	if($('.dashboard-tabs').length>0){
		$('.dashboard-tabs').tabs();
	}
<?php if(trim($dashboard_icon)!='') : ?>
console.log('<?php echo $dashboard_icon?>');
	$('#icon-index.icon32')
		.css('background-image','url(<?php echo $dashboard_icon?>)')
		.css('background-position','0 0')
	;
<?php endif ?>	
});
</script>
<?php			
		$screen_id = isset( $_REQUEST['screen_id'] ) ? $_REQUEST['screen_id'] : '' ;
		if(''==trim($screen_id))return;
		
		if(''==get_user_meta($userdata->ID, sprintf('meta-box-order_%s',$screen_id) ,true) || ''==get_user_meta($userdata->ID, sprintf('screen_layout_%s',$screen_id),true)){
			$_POST['reset_my_dashboard']=1;
		}
		$dashboard_fields = array(
			sprintf('meta-box-order_%s',$screen_id)	=> array(),
			sprintf('screen_layout_%s',$screen_id)	=> 2,
			sprintf('metaboxhidden_%s',$screen_id)	=> array(),
			sprintf('closedpostboxes_%s',$screen_id)=> array()
		);

		if(isset($_POST['reset_my_dashboard'])){
			foreach($dashboard_fields as $field => $value){
				update_user_meta($userdata->ID,$field,get_option('default_'.$field,$value));
			}	
		}
		
		if(!$wlb_plugin->is_wlb_administrator())return;//saving from here is only allowed to wlb admins.
		
		if(isset($_POST['dashboard_layout_setup']) ){
			foreach($dashboard_fields as $field => $value){
				update_option('default_'.$field,get_user_meta($userdata->ID,$field,true));
			}
		}
		if(isset($_POST['user_dashboard_layout_setup'])){
			$sql = $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_login=%s",$_POST['dashboard_layout_user']);
			$user_id = $wpdb->get_var($sql,0,0);
			if($user_id>0){
				foreach($dashboard_fields as $field => $value){
					update_user_meta($user_id,$field,get_user_meta($userdata->ID,$field,true));
				}
			}
		}
	}
	function dashboard_init(){
		//NEW POST TYPE
		$labels = array(
			'name' 				=> $this->menu_name,
			'singular_name' 	=> __('Dashboard','wlb'),
			'add_new' 			=> __('Add Metabox','wlb'),
			'edit_item' 		=> __('Edit Dashboard Metabox','wlb'),
			'new_item' 			=> __('New Dashboard Metabox','wlb'),
			'view_item'			=> __('View Dashboard Metabox','wlb'),
			'search_items'		=> __('Search Dashboard Metaboxes','wlb'),
			'not_found'			=> __('No Metaboxes found','wlb'),
			'not_found_in_trash'=> __('No Metaboxes found in trash','wlb')
		);
		register_post_type('wlbdash', array(
			'label' => __('WLB Dash','wlb'),
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'exclude_from_search'=> true,
			'show_ui' => $this->show_ui,
			'show_in_menu'=>$this->show_in_menu,
			'capability_type' => 'wlbdash',
			'capabilities'=>array(
				'edit_post'					=> 'wlb_dashboard_tool',
				'read_post'					=> 'wlb_dashboard_tool',
				'delete_post'				=> 'wlb_dashboard_tool',
				'edit_posts'				=> 'wlb_dashboard_tool',
				'edit_others_posts'			=> 'wlb_dashboard_tool',
				'publish_posts'				=> 'wlb_dashboard_tool',
				'read_private_posts'		=> 'wlb_dashboard_tool',
				'read'						=> 'wlb_dashboard_tool',
				'delete_posts'				=> 'wlb_dashboard_tool',
				'delete_private_posts'		=> 'wlb_dashboard_tool',
				'delete_published_posts'	=> 'wlb_dashboard_tool',
				'delete_otheres_posts'		=> 'wlb_dashboard_tool',
				'edit_private_posts'		=> 'wlb_dashboard_tool',
				'edit_published_posts'		=> 'wlb_dashboard_tool'
			),
			'map_meta_cap' => false,
			'hierarchical' => false,
			'rewrite' => false,
			'query_var' => false,
			'supports' => array('title','editor','revisions','page-attributes','author'),
			'exclude_from_search' => false,
			//'menu_position' => 999999,
			'show_in_nav_menus' => false,
			'taxonomies' => array(),
			'menu_icon'=> WLB_URL.'images/dashboard_tool.png'
		));
	}

	function add_dashboard_widgets(){
		$this->hide_dashboard_widgets();
		//---
		global $post,$wpdb;
		$restricted_ids = $this->current_user_excluded_post_ids();
		$sql = "SELECT ID, post_title , post_content FROM `{$wpdb->posts}` WHERE post_type='wlbdash' AND post_status='publish' ORDER BY menu_order ASC";
		if($wpdb->query($sql)&&$wpdb->num_rows>0){
			foreach($wpdb->last_result as $row){
				if(in_array($row->ID,$restricted_ids))
					continue;
				$widget_id = 'wlbdash_'.$row->ID;
				$this->content[$row->ID]=$row->post_content;
				
				wp_add_dashboard_widget($widget_id, $row->post_title, create_function('',"wlb_get_dashboard_content($row->ID);") );
			}
		}		
	}

	function hide_dashboard_widgets(){
		//generate form before removing widgets.
		$this->admin_output_dashboard_info();
		//--
		global $wlb_plugin,$wp_meta_boxes;
		$hidden_widgets = $wlb_plugin->get_option('wlb_dashboard_widgets',array());
		if(count($hidden_widgets)==0)return;
		foreach($wp_meta_boxes['dashboard'] as $context => $pages){
			if(is_array($pages)&&count($pages)>0){
				foreach($pages as $page => $widgets ){
					if(is_array($widgets)&&count($widgets)>0){
						foreach($widgets as $id => $w){
							if(false!==strpos($w['id'],'wlbdash'))
								continue;
							if(in_array($w['id'],array('custom_panel')))
								continue;
							if(in_array($w['id'],$hidden_widgets)){
								unset($wp_meta_boxes['dashboard'][$context][$page][$id]);
							}
						}
					}
				}					
			}
		}
	}

	function current_user_excluded_post_ids(){
		//Taken from pages-by-user-role to provide support for restriction by user role inside the admin.
		global $wpdb,$userdata;
		$also_exclude = array();
		$extrafilter = '';
		//if(is_user_logged_in()){
		if(true){
			$uroles = $this->get_uroles(true);		
			$uroles = empty($uroles)||!is_array($uroles)?  array("'undefined'") : $uroles ;
			if(count($uroles)>0){
				$extrafilter = "AND(M.post_id NOT IN (SELECT DISTINCT(post_id) FROM `{$wpdb->postmeta}` WHERE meta_key='pur-available-roles' AND meta_value IN (".implode(',',$uroles).")))";			
				//--
				$sql = "SELECT DISTINCT(M.post_id) FROM {$wpdb->posts} P INNER JOIN `{$wpdb->postmeta}` M ON P.post_type='wlbdash' AND P.ID=M.post_id AND P.post_status='publish' WHERE M.`meta_key` LIKE 'pur-blocked-roles'";
				$sql.= "AND(M.post_id IN (SELECT DISTINCT(post_id) FROM `{$wpdb->postmeta}` WHERE meta_key='pur-blocked-roles' AND meta_value IN (".implode(',',$uroles).")))";
				$also_exclude = $wpdb->get_col($sql,0);	
			}	
		}
		
		$sql = "SELECT DISTINCT(M.post_id) FROM {$wpdb->posts} P INNER JOIN `{$wpdb->postmeta}` M ON P.post_type='wlbdash' AND P.ID=M.post_id AND P.post_status='publish' WHERE M.`meta_key` LIKE 'pur-available-roles' $extrafilter";
		$exclude = $wpdb->get_col($sql,0);
		//---
		$exclude = array_merge($exclude,$also_exclude);				
		return empty($exclude)?array(0):$exclude;
	}

	function get_uroles($for_sql=true){
		//taken from PUR to support the current_user_excluded_post_ids method.
		global $wpdb,$userdata;

		$userinfo = new WP_User($userdata->ID);

		$uroles = array();
		if(!is_null($userinfo)&&is_array($userinfo->roles)&&count($userinfo->roles)>0){
			foreach($userinfo->roles as $urole){
				if($for_sql){
					$uroles[]=sprintf("'%s'",$urole);
				}else{
					$uroles[]=$urole;
				}
			}		
		}
		return $uroles;				
	}	
	/*
	function post_meta_box(){
		add_meta_box( 'wlb-postmeta', __('Predefined WP widget','wlb'),	array( &$this, 'form_template' ), 'wlbdash', 'side', 'high');
	}		
	*/
	function form_template($post){
		global $wp_roles;
		
		echo '<input type="hidden" name="wlb-nonce" id="wlb-nonce" value="' . wp_create_nonce( 'wlb-nonce' ) . '" />';
		//----------------------
?>
<p><?php _e('You can choose to display and existing Wordpress widget.','wlb')?></p>
<?php
	}	
	function init(){
		$options = $this->wlb_options(array(),false);
		foreach($options as $tab){
			if(count($tab->options)>0){
				foreach($tab->options as $i => $o){
					$id = property_exists($o,'id') ? $o->id : $i ;
					$method = "_".$id;				
					if(!method_exists($this,$method))
						continue;
					$this->$method($tab,$i,$o);	
				}
			}
		}	
	}
	
	function disable_welcome_panel(){
		if(!is_admin())return;
		global $userdata,$wlb_plugin;
		if(is_object($userdata)&&property_exists($userdata,'ID') && $userdata->ID>0 && '1'==$wlb_plugin->get_option('disable_welcome_panel') ){
			update_user_meta($userdata->ID,'show_welcome_panel',0);
		}
	}
	//------
	function _use_public_dashboard($tab,$i,$o){
		global $wlb_plugin;
		if(intval($wlb_plugin->get_option($o->id))){
			add_action('wp_dashboard_setup', array(&$this,'use_public_dashboard'));
		}	
	}
	function use_public_dashboard(){
		global $wlb_plugin;
		$public_title = $wlb_plugin->get_option('panel_title');
		if(trim($public_title)!=''){
			wp_add_dashboard_widget('custom_panel', $public_title, array(&$this,'public_dashboard_content') );
		}
	}
	function public_dashboard_content(){
		global $wlb_plugin;
		echo do_shortcode(stripslashes($wlb_plugin->get_option('panel_content')));
	}
	//------
	function _use_editor_dashboard($tab,$i,$o){
		global $wlb_plugin;
		if(intval($wlb_plugin->get_option($o->id))){
			add_action('wp_dashboard_setup', array(&$this,'use_editor_dashboard'));
		}	
	}
	function use_editor_dashboard(){
		global $wlb_plugin;
		if('editor'==$wlb_plugin->get_user_role()){
			$editor_title = $wlb_plugin->get_option('editor_panel_title');
			if(trim($editor_title)!=''){
				wp_add_dashboard_widget('editor_custom_panel', $editor_title, array(&$this,'editor_dashboard_content') );
			}		
		}
	}
	function editor_dashboard_content(){
		global $wlb_plugin;
		echo do_shortcode(stripslashes($wlb_plugin->get_option('editor_panel_content')));
	}	
	//------
	function _use_admin_dashboard($tab,$i,$o){
		global $wlb_plugin;
		if(intval($wlb_plugin->get_option($o->id))){
			add_action('wp_dashboard_setup', array(&$this,'use_admin_dashboard'));
		}	
	}
	function use_admin_dashboard(){
		global $wlb_plugin;
		if('administrator'==$wlb_plugin->get_user_role()){
			$title = $wlb_plugin->get_option('administrator_panel_title');
			if(trim($title)!=''){
				wp_add_dashboard_widget('admin_custom_panel', $title, array(&$this,'admin_dashboard_content') );
			}		
		}
	}
	function admin_dashboard_content(){
		global $wlb_plugin;
		echo do_shortcode(stripslashes($wlb_plugin->get_option('administrator_panel_content')));
	}		
}

function wlb_get_dashboard_content($id){
	global $wpdb;
	$sql = "SELECT post_content FROM `{$wpdb->posts}` WHERE ID=$id";
	echo apply_filters('the_content',$wpdb->get_var($sql,0,0));
}
?>