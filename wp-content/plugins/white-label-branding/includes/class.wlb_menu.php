<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class wlb_menu {
	function __construct(){
		global $wlb_plugin;
		$this->id = $wlb_plugin->id.'-nav';	
		add_filter("pop-options_{$this->id}",array(&$this,'wlb_options'),10,1);
		add_action('admin_menu', array(&$this,'admin_menu'),1000);
	}
	
	function get_id_from_menu($str,$prefix='m_',$m=array()){	
		//--handle special conditions
		if( isset($m[1]) && is_string( $m[1] ) && 'customize'==$m[1] ){
			return 'wp_theme_customize';		
		}
		//--handle regular
		$id = strtolower(str_replace(' ','_',str_replace('.','_',$str)));
		$id = str_replace('?','_',$id);
		$id = str_replace('&amp;','_',$id);
		$id = str_replace('&','_',$id);
		$id = str_replace('=','_',$id);
		$id = str_replace('[','_',$id);
		$id = str_replace(']','_',$id);
		$id = str_replace('%','_',$id);
		$id = str_replace('-','_',$id);
		
		return $prefix.$id;
	}
	
	function get_value_from_menu($m){
		//--handle special conditions
		if( isset($m[1]) && is_string( $m[1] ) && 'customize'==$m[1] ){
			return 'wp_theme_customize';		
		}
		//--handle regular	
		return html_entity_decode($m[2]);
	}
		
	function admin_menu(){
		global $menu,$submenu,$wlb_plugin;

		$wlb_plugin->menu = $menu;//make a copy of available menus
		$wlb_plugin->submenu = $submenu;
		if(!$wlb_plugin->is_wlb_administrator()){
			if(isset($menu)&&is_array($menu)&&count($menu)>0){
				foreach($menu as $k => $m){
					//$id = 'm_'.strtolower(str_replace(' ','_',str_replace('.','_',$m[2])));
					$id = $this->get_id_from_menu($m[2],'m_',$m);
					if( $this->get_value_from_menu($m) == $wlb_plugin->get_option($id) ){
						unset($menu[$k]);
					} 
				}			
			}
			if(isset($submenu)&&is_array($submenu)&&count($submenu)>0){
				foreach($submenu as $key => $submenu_group){
					foreach($submenu_group as $k => $m){
						//$id = 'sm_'.strtolower(str_replace(' ','_',str_replace('.','_',$m[2])));		
						$id = $this->get_id_from_menu($m[2],'sm_',$m);
						if( $this->get_value_from_menu($m) == $wlb_plugin->get_option($id) ){
							unset($submenu[$key][$k]);
						} 
					}					
				}
		
			}			
		}
	}	
	function wlb_options($t,$for_admin=true){
		$i = count($t);
		//-----
		global $wlb_plugin;

		
		if($for_admin){
			$menu 	= $wlb_plugin->menu;	
			$submenu = $wlb_plugin->submenu;
		}else{
			global $menu,$submenu;
		}
		
		$i = count($t);
		@$t[$i]->id 			= 'hide_menu'; 
		$t[$i]->label 		= __('Menus','wlb');//title on tab
		$t[$i]->right_label	= __('Customize Menus','wlb');//title on tab
		$t[$i]->page_title	= __('Menus','wlb');//title on content
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Main Menu Configuration','wlb')	
			),
			(object)array(
				'type'=>'description',
				'label'=>__('Changes made here will only effect people that does not have the Administrator role.  If you are Administrator you cannot see this changes until you login as a diferent user.','wlb')
			)			
		);
		
		if(is_array($menu)&&count($menu)>0){
			foreach($menu as $k => $m){
				$label = trim($m[0])==''?$m[2]:$m[0];			
				if(in_array($m[2],array('plugins.php','edit-comments.php'))){
					$label = substr($label,0,strpos($label,' '));
				}
				//$id = 'm_'.strtolower(str_replace(' ','_',str_replace('.','_',$m[2])));
				$id = $this->get_id_from_menu($m[2],'m_',$m);
				$t[$i]->options[] = (object)array(
					'id'	=> $id,
					'type'=>'checkbox',
					'label'=> __('Hide','wlb').' '.$label,
					'option_value'=> $this->get_value_from_menu( $m ),
					'el_properties' => array(),
					'save_option'=>true,
					'load_option'=>true
				);					
			}		
		}
		
		//Your profile
		//$id = 'm_'.strtolower(str_replace(' ','_',str_replace('.','_','profile.php')));
		$id = $this->get_id_from_menu('profile.php','m_');
		$t[$i]->options[] = (object)array(
			'id'	=> $id,
			'type'=>'checkbox',
			'label'=> __('Hide','wlb').' '.__('Your Profile','wlb'),
			'option_value'=> 'profile.php',
			'el_properties' => array(),
			'save_option'=>true,
			'load_option'=>true
		);				

		global $current_user;
		$user_roles = $current_user->roles;
	
		$t[$i]->options[]=(object)array('label'=>__('Save Changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' );	
		//------------------------------------------------------------------------------------
		//Hide submenus
		$i = count($t);
		@$t[$i]->id 			= 'hide_submenu'; 
		$t[$i]->label 		= __('Sub menus','wlb');//title on tab
		$t[$i]->right_label	= __('Customize Sub Menus','wlb');//title on tab
		$t[$i]->page_title	= __('Customize Sub Menus','wlb');//title on content
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Sub Menu Configuration','wlb')	
			),
			(object)array(
				'type'=>'description',
				'label'=>__('Changes made here will only effect people that does not have the Administrator role.  If you are an Administrator you cannot see this changes until you login as a diferent user.','wlb')
			)			
		);		
		if(is_array($submenu)&&count($submenu)>0){
	
			foreach($submenu as $key => $submenu_group){
				//---
				foreach($menu as $mm){
					if($mm[2]==$key){
						$t[$i]->options[]=(object)array(
							'type'=>'subtitle',
							'label'=> sprintf("%s (%s)",$mm[0],$mm[1]) 	
						);
					}
				}
				
				//---
				foreach($submenu_group as $k => $m){
					/*
					if(in_array($m[2],array('theme-editor.php')))
						continue;
					*/	
					$label = trim($m[0])==''?sprintf("%s(%s)",$m[2],$m[1]):sprintf("%s (%s)",$m[0],$m[1]);			
					//$id = 'sm_'.strtolower(str_replace(' ','_',str_replace('.','_',$m[2])));
					$id = $this->get_id_from_menu($m[2],'sm_',$m);
					$t[$i]->options[] = (object)array(
						'id'	=> $id,
						'type'=>'checkbox',
						'label'=> _('Hide').' '.strip_tags($label),
						'option_value'=> $this->get_value_from_menu( $m ),
						'el_properties' => array('rel'=>$m[2]),
						'save_option'=>true,
						'load_option'=>true,
						'row_class'=>'theme-options'
					);					
				}				
			}
			
			$t[$i]->options[]=(object)array('label'=>__('Save changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' );	
		}	
		//-----
		return $t;
	}	
}

?>