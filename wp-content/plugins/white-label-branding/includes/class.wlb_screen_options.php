<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class wlb_screen_options {
	var $admin_footer;
	function __construct($args=array()){
		global $wlb_plugin;
		$this->id = $wlb_plugin->id;
		
		add_filter('screen_settings', array(&$this,'screen_settings'), 999999, 2);
		add_action('admin_footer',array(&$this,'admin_footer'));
		add_action('admin_head',array(&$this,'admin_head'));
		
		add_action('admin_init',array(&$this,'handle_screen_settings_save'));
		//$this->handle_screen_settings_save();//on some sites calling save here doesnt work, because user roles are not set.
	}
	
	function admin_head(){
		global $wlb_plugin,$current_screen;
		echo "<style rel=\"wlb-screen-options\">";
?>
.sos-label {padding-right:15px;white-space: nowrap;}
.sos-label input {margin: 0 5px 0 2px;white-space: nowrap;}
<?php		
		if(!$wlb_plugin->is_wlb_administrator()){
			$disabled_screen_options = $wlb_plugin->get_option('disabled_screen_options',array());
			$disabled_screen_options = is_array($disabled_screen_options)?$disabled_screen_options:array();
			if(isset($disabled_screen_options[$current_screen->id])){
				foreach($disabled_screen_options[$current_screen->id] as $id){
					echo sprintf("label[for=%s-hide] {display:none !important;}",$id);
					echo sprintf("#%s {display:none !important;}",$id);
				}
			}		
		}
		echo "</style>";
	}
	
	function admin_footer(){
		echo $this->admin_footer;
	}
	
	function handle_screen_settings_save(){
		global $wpdb,$userdata,$wlb_plugin;	
		if(!isset($_POST['save_screen_options']))return;
		if(!$wlb_plugin->is_wlb_administrator())return;
		$screen_id = @$_REQUEST['screen_id'];
		if(''==trim($screen_id))return;
		
		$existing_options = get_option($wlb_plugin->options_varname);
		$existing_options['disabled_screen_options'][$screen_id]=$_POST['disabled_screen_options'];
		update_option($wlb_plugin->options_varname,$existing_options);	
		$wlb_plugin->load_options();//refresh loaded options.
	}
	
	function screen_settings($content, $screen){	
		global $wlb_plugin,$wp_meta_boxes,$menu,$submenu;
		if(!is_array($wp_meta_boxes)||count($wp_meta_boxes)==0)return $content;
		$arr = array_keys($wp_meta_boxes);
		

		$disabled_screen_options = $wlb_plugin->get_option('disabled_screen_options');
		$hidden = isset($disabled_screen_options[$screen->id])?$disabled_screen_options[$screen->id]:array();
		if(!$wlb_plugin->is_wlb_administrator())return $content;

		$str = 	
		ob_start();	
		
?>
<h5><?php _e('Disable screen options (Admin only)','wlb')?></h5>
<?php $ret = $this->meta_box_prefs($screen,$wp_meta_boxes,$hidden); ?>
<input type="submit" class="button-secondary" name="save_screen_options" value="<?php _e('Save','wlb')?>" />
<?php	
		if($ret)$content .= ob_get_contents();
		ob_end_clean();
		return $content;				
	}

	function meta_box_prefs($screen,$wp_meta_boxes,$hidden) {
		$ret = false;
		//taken from wordpress core, the only diference is the checked values are passed as an argument.
		if ( is_string($screen) )
			$screen = convert_to_screen($screen);
	
		if ( empty($wp_meta_boxes[$screen->id]) )
			return false;
	
		foreach ( array_keys($wp_meta_boxes[$screen->id]) as $context ) {
			foreach ( array_keys($wp_meta_boxes[$screen->id][$context]) as $priority ) {
				foreach ( $wp_meta_boxes[$screen->id][$context][$priority] as $box ) {
					if ( false == $box || ! $box['title'] )
						continue;
					$box_id = $box['id'];
					echo '<label class="sos-label" for="' . $box_id . '-disable">';
					echo '<input class="" name="disabled_screen_options[]" type="checkbox" id="sos-' . $box_id . '" value="' . $box_id . '"' . ( in_array($box_id, $hidden) ? ' checked="checked"' : '') . ' />';
					echo "{$box['title']}</label>\n";
					$ret = true;
				}
			}
		}
		return $ret;
	}	
}
?>