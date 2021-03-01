<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class admin_menu_sort {
	var $id;
	var $menu_order = array();
	var $menu_label = array();
	var $menu_icon = array();
	function __construct( $url ){
		$this->url = $url;
		
		global $wlb_plugin;
		$this->id = $wlb_plugin->id.'-nav';	
		add_filter("pop-options_{$this->id}",array(&$this,'wlb_options'),10,1);	
		add_action('pop_admin_head_'.$this->id,array(&$this,'admin_head'),10);		
		add_action('pop_handle_save',array(&$this,'pop_handle_save'),50,1);
		//--- 
		if( '1'==$wlb_plugin->get_option('use_admin_sort_menu',false) ){
			add_filter('menu_order',array(&$this,'menu_order'),10,1);
			add_filter('custom_menu_order',create_function('','return true;'));		
			add_filter('add_menu_classes',array(&$this,'add_menu_classes'),99,1);
		}
		add_action( 'admin_enqueue_scripts', array(&$this,'admin_enqueue_scripts') );
		add_filter( 'wlb_iconset' , array(&$this,'wlb_iconset'), 10, 1);
	}
	
	function pop_handle_save($pop){
		global $wlb_plugin;
		if($wlb_plugin->options_varname!=$pop->options_varname)return;
		foreach(array('admin_menu_order','admin_menu_label','admin_menu_icon') as $field){
			if(isset($_REQUEST[$field])&&is_array($_REQUEST[$field])){
				$existing_options = get_option($pop->options_varname);
				$existing_options = is_array($existing_options)?$existing_options:array();
				$existing_options[$field] = $_REQUEST[$field];
				update_option($pop->options_varname,$existing_options);		
			}
		}
	}
	
	function wlb_options($t){
		$i = count($t);
		//-----
		$i = count($t);
		@$t[$i]->id 			= 'admin_sort_menu'; 
		$t[$i]->label 		= __('Admin Menu Settings','wlb');//title on tab
		$t[$i]->right_label	= __('Customize Admin Menu','wlb');//title on tab
		$t[$i]->page_title	= __('Admin Menu Settings','wlb');//title on content
		$t[$i]->options = array(
			(object)array(
				'type'=>'subtitle',
				'label'=>__('Sort wp-admin main menu','wlb')	
			),
			(object)array(
				'id'		=> 'use_admin_sort_menu',
				'label'		=> __('Enable custom admin menu order.','wlb'),
				'type'		=> 'yesno',
				'description'=> __('Choose yes if you want to customize the order of the WordPress admin main menu.','wlb'),
				'el_properties'	=> array(),
				'hidegroup'	=> '#admin_sort_menu_group',
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array(
				'type'=>'description',
				'label'=>__('Customize the WordPress admin menu.  Drag and drop the main menu item to their new custom position.','wlb')
			),
			(object)array(
				'type'=>'clear'
			),
			(object)array(
				'id'	=> 'admin_sort_menu_group',
				'type'=>'div_start'
			),
			(object)array('label'=>__('Save Changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' ),
			(object)array(
				'type'=>'clear'
			),
			(object)array('description'=>__('Drag and drop a menu item to its new custom positions.  After reorganizing the menu items click "Save Changes"','wlb'),'type'=>'description'),
			(object)array(
				'id'=>'admin_menu_sortable',
				'type'=>'callback',
				'callback'=>array(&$this,'pop_menu_sort')
			),	
			(object)array('type'=>'div_end'),
			(object)array('label'=>__('Save Changes','wlb'),'type'=>'submit','class'=>'button-primary', 'value'=> '' )								
		);		
		return $t;
	}
	
	function admin_head(){
		wp_print_scripts( 'jquery-ui-sortable' );	
?>
<script type='text/javascript'>
jQuery(document).ready(function($){ 
	$('#admin_menu_sortable').sortable({
		placeholder:'sortable-placeholder',
		revert:true
	});
	
	$('.pop-menu-order-item .handlediv').unbind('click').click(function(e){
		$(this).parents('.pop-menu-order-item').toggleClass('closed');
	}).trigger('click');
	
	$('.wlb-iconset-helper .wlb-icon').unbind('click').click(function(e){
		$(this).parents('.wlb-iconset-helper')
			.find('input.inp-wlb-icon')
				.val(
					$(this).data('icon')
				).trigger('change')
			.end()
		;
	});
	
	$('input.inp-wlb-icon').change(function(e){
		$(this)
			.parents('.wlb-iconset-helper')
			.find('.wlb-icon-holder').removeClass('wlb-icon-selected')
		;
		var icon = $(this).val();
		if(''!=icon){
			var sel = '.wlb-icon-holder.' + icon ;
			$(this)
				.parents('.wlb-iconset-helper')
				.find(sel).addClass('wlb-icon-selected')
			;		
		}

	}).trigger('change');
	
	$('.wlb-icon-helper-clear').click(function(e){
		$(this).parents('.wlb-iconset-helper')
			.find('input.inp-wlb-icon')
				.val('').trigger('change')
			.end()
		;	
		e.stopPropagation();
		return false;	
	});
});
</script>
<?php	
	}
	
	function pop_menu_sort($tab,$i,$o,$save_fields){
		global $menu,$wlb_plugin;
		$admin_menu = $menu;

		$this->default_menu_order = array();
		foreach ( $menu as $menu_item ) {
			$this->default_menu_order[] = $menu_item[2];
		}	
		$this->menu_order = $wlb_plugin->get_option('admin_menu_order',false);
		$this->menu_label = $wlb_plugin->get_option('admin_menu_label',false);
		$this->menu_icon = $wlb_plugin->get_option('admin_menu_icon',false);

		$this->menu_set = array();
		if(is_array($this->menu_order) && count($this->menu_order)>0){
			foreach($this->menu_order as $i => $menu){
				$this->menu_set[$menu]=(object)array(
					'menu'	=> $menu,
					'label'	=> isset($this->menu_label[$i])?$this->menu_label[$i]:'',
					'icon'	=> isset($this->menu_icon[$i])?$this->menu_icon[$i]:''
				);
			}		
		}
		
		if(is_array($this->menu_order)&&count($this->menu_order)>0){
			$this->menu_order = array_flip($this->menu_order);
			usort($admin_menu, array(&$this,'sort_menu') );
		}
	
		$this->icon_set = apply_filters('wlb_iconset',array());
		//---
		echo sprintf('<div class="js"><div id="%s" class="pt-option meta-box-sortables pop-menu-order-cont">',$o->id);
		foreach($admin_menu as $i => $item){
			echo $this->pop_menu_sort_item($i, $item);
		}
		echo '</div></div>';
	}
	
	function pop_menu_sort_item($i,$item){
		$skip = array('separator1','separator2','separator-last');
		$custom_label = isset($this->menu_set[$item[2]])?$this->menu_set[$item[2]]->label:'';
		$custom_icon = isset($this->menu_set[$item[2]])?$this->menu_set[$item[2]]->icon:'';
		$label = ($item[0]==''?$item[2]:$item[0]);
		ob_start();
?>
	<div id="pop-menu-order-item-<?php echo $i?>" class="postbox pop-menu-order-item menu-item-handle <?php echo $item[4]?> <?php echo in_array($item[2],$skip)?' no-content':''?>">
		<div class="handlediv" title="Click to toggle"><br></div>
		<h3 class="hndle pop-menu-order-head"><span><?php echo $label?></span></h3>
		<input type="hidden" name="admin_menu_order[]" value="<?php echo $item[2]?>" />		
		<div class="inside">
			<input type="text" class="mo-label" name="admin_menu_label[]" value="<?php echo $custom_label?>" placeholder="<?php _e('Customize label','wlb')?>" />
			<div class="wlb-iconset-helper">
				<input type="hidden" class="inp-wlb-icon" name="admin_menu_icon[]" value="<?php echo $custom_icon?>" placeholder="<?php _e('Customize icon','wlb')?>" />
				<?php foreach($this->icon_set as $icon):?>
				<div class="wlb-icon-holder <?php echo $icon?>"><div class="wlb-icon wp-menu-image" data-icon="<?php echo $icon?>"></div></div>
				<?php endforeach;?>
				<button class="wlb-icon-helper-clear button-secondary"><?php _e('Clear','wlb')?></button>
			</div>
		</div>
	</div>
<?php	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
		return sprintf('', $i, $item[4], ($item[0]==''?$item[2]:$item[0]) ,$item[2]);
	}
	
	function sort_menu($a, $b) {
		//from the wordpress core
		$menu_order 			= $this->menu_order; 
		$default_menu_order		= $this->default_menu_order;
		$a = $a[2];
		$b = $b[2];
		if ( isset($menu_order[$a]) && !isset($menu_order[$b]) ) {
			return -1;
		} elseif ( !isset($menu_order[$a]) && isset($menu_order[$b]) ) {
			return 1;
		} elseif ( isset($menu_order[$a]) && isset($menu_order[$b]) ) {
			if ( $menu_order[$a] == $menu_order[$b] )
				return 0;
			return ($menu_order[$a] < $menu_order[$b]) ? -1 : 1;
		} else {
			if( isset($default_menu_order[$a]) && isset($default_menu_order[$b]) ){
				return ($default_menu_order[$a] <= $default_menu_order[$b]) ? -1 : 1;
			}else{
				return 1;
			}
			
		}
	}
	
	function admin_footer(){

	}
	
	function menu_order($menu_order){
		global $wlb_plugin;
		$menu_order = $wlb_plugin->get_option('admin_menu_order',array());
		return $menu_order;
	}
	
	function add_menu_classes($menu){

		global $wlb_plugin;
		$this->menu_order = $wlb_plugin->get_option('admin_menu_order',false);
		$this->menu_label = $wlb_plugin->get_option('admin_menu_label',false);
		$this->menu_icon = $wlb_plugin->get_option('admin_menu_icon',false);

		$this->menu_set = array();
		foreach($this->menu_order as $i => $m){
			$this->menu_set[$m]=(object)array(
				'menu'	=> $m,
				'label'	=> isset($this->menu_label[$i])?$this->menu_label[$i]:'',
				'icon'	=> isset($this->menu_icon[$i]) && !empty($this->menu_icon[$i]) ?$this->menu_icon[$i].' wlb-custom-icon':''
			);
		}
		
		foreach($menu as $i => $set){
			if(isset($this->menu_set[$set[2]])){
				if( !empty($this->menu_set[$set[2]]->label) ){
					$menu[$i][0]=$this->menu_set[$set[2]]->label;
				}
				if( !empty($this->menu_set[$set[2]]->icon) ){
					$menu[$i][4].=' '.$this->menu_set[$set[2]]->icon;
					$menu[$i][4] = trim($menu[$i][4]);			
					if( isset( $menu[$i][6] ) ){
						$menu[$i][6] = '';
					}	
				}
			}
		}
	
		return $menu;
	}
	
	function admin_enqueue_scripts(){
		wp_enqueue_style('wlb-iconset', $this->url.'css/wlb-iconset.css', array(),'1.0.1');
	}
	
	function wlb_iconset($arr){
		$arr = is_array($arr)?$arr:array();
		$default=array(
			//'icon-tag',
			'icon-heart',
			'icon-cloud',
			'icon-star',
			'icon-tv',
			'icon-sound',
			'icon-video',
			'icon-trash',
			'icon-user',
			'icon-key',
			'icon-search',
			'icon-settings',
			'icon-camera',
			'icon-tag',
			'icon-lock',
			'icon-bulb',
			'icon-pen',
			'icon-diamond',
			'icon-display',
			'icon-location',
			'icon-eye',
			'icon-bubble',
			'icon-stack',
			'icon-cup',
			'icon-phone',
			'icon-news',
			'icon-mail',
			'icon-like',
			'icon-photo',
			'icon-note',
			'icon-clock',
			'icon-paperplane',
			'icon-params',
			'icon-banknote',
			'icon-data',
			'icon-music',
			'icon-megaphone',
			'icon-study',
			'icon-lab',
			'icon-food',
			'icon-t-shirt',
			'icon-fire',
			'icon-clip',
			'icon-shop',
			'icon-calendar',
			'icon-wallet',
			'icon-star',
			'icon-vynil',
			'icon-truck',
			'icon-world',
			'icon-link',
			'icon-link2',
			'icon-support',
			'icon-cloudy',
			'icon-sun',
			'icon-cloudy2',
			'icon-weather',
			'icon-wrench',
			'icon-pie',
			'icon-lamp',
			'icon-users',
			'icon-eyedropper',
			'icon-clock2',
			'icon-compass',
			'icon-location2',
			'icon-user2',
			'icon-equalizer',
			'icon-meter',
			'icon-atom',
			'icon-atom2',
			'icon-puzzle',
			'icon-link3',
			'icon-home',
			'icon-pen2',
			'icon-camera2',
			'icon-mic',
			'icon-bag',
			'icon-database',
			'icon-bars',
			'icon-dashboard',
			'icon-checkmark',
			'icon-filter'
		);
		
		return array_merge($arr,$default);
	}
}

?>