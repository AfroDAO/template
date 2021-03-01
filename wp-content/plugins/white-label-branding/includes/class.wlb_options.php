<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class wlb_options {
	var $plugin_id;
	function __construct($args=array()){
		$defaults = array(
			'plugin_id'	=> 'white-label-branding',
			'option_show_in_metabox'=> false
		);
		foreach($defaults as $property => $default){
			$this->$property = isset($args[$property])?$args[$property]:$default;
		}	
		
		add_action("pop_admin_head_{$this->plugin_id}",array(&$this,'pop_admin_head'));
		add_filter("pop-options_{$this->plugin_id}",array(&$this,'options'),10,1);			
	}
	function options($t){
		$i = count($t);
		//-------------------------		
		$i = count($t);
		$t[$i]->id 			= 'branding'; 
		$t[$i]->label 		= __('Branding','wlb');
		$t[$i]->right_label	= __('Customize Header and Footer Logo','wlb');
		$t[$i]->page_title	= __('Branding','wlb');
		$t[$i]->theme_option = true;
		$t[$i]->plugin_option = true;
		$t[$i]->options = array(
			(object)array(
				'type'	=> 'label',
				'label'	=> __('Header','wlb')
			),
			(object)array(
				'type'	=> 'clear',
			),
			(object)array(
				'type'	=> 'submit',
				'label'	=> __('Save','wlb'),
				'class' => 'button-primary',
				'save_option'=>false,
				'load_option'=>false
			)
		);		
		
		//-----------------
		return $t;
	}
	
	function pop_admin_head(){
?>
<script>
jQuery(document).ready(function($){ 
	
});	
</script>
<?php	
	}
}
?>