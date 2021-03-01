<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Module Name: WooCommerce Product Categories
 */
class TB_Product_Categories_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Product Categories', 'builder-wc'),
			'slug' => 'product-categories'
		));
	}

	function get_assets() {
		$instance = Builder_Woocommerce::get_instance();
		return array(
			'selector' => '.module-product-categories',
			'css' => themify_enque($instance->url . 'assets/style.css'),
			'ver' => $instance->version
		);
	}

	public function get_options() {
		return array(
			array(
				'id' => 'mod_title',
				'type' => 'text',
				'label' => __('Module Title', 'builder-wc'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
                                )
			),
			array(
				'id' => 'columns',
				'type' => 'layout',
                                'mode'=>'sprite',
				'label' => __('Layout', 'builder-wc'),
				'options' => array(
					array('img' => 'list-post', 'value' => 1, 'label' => __('1 Column', 'builder-wc')),
					array('img' => 'grid2', 'value' => 2, 'label' => __('2 Columns', 'builder-wc')),
					array('img' => 'grid3', 'value' => 3, 'label' => __('3 Columns', 'builder-wc')),
					array('img' => 'grid4', 'value' => 4, 'label' => __('4 Columns', 'builder-wc'),'selected'=>true),
				)
			),
			array(
				'id' => 'child_of',
				'type' => 'product_categories',
				'label' => __('Categories', 'builder-wc'),
				'description' => __('Show all categories or sub-categories of a category.', 'builder-wc'),
			),
			array(
				'id' => 'orderby',
				'type' => 'select',
				'label' => __('Order By', 'builder-wc'),
				'options' => array(
					'name' => __('Name', 'builder-wc'),
					'id' => __('ID', 'builder-wc'),
					'count' => __('Product Count', 'builder-wc'),
				)
			),
			array(
				'id' => 'order',
				'type' => 'select',
				'label' => __('Order', 'builder-wc'),
				'help' => __('Descending = show newer posts first', 'builder-wc'),
				'options' => array(
					'desc' => __('Descending', 'builder-wc'),
					'asc' => __('Ascending', 'builder-wc')
				)
			),
			array(
				'id' => 'hide_empty',
				'type' => 'select',
				'label' => __('Hide Empty Categories', 'builder-wc'),
				'options' => array(
					'yes' => __('Yes', 'builder-wc'),
					'no' => __('No', 'builder-wc'),
				)
			),
			array(
				'id' => 'pad_counts',
				'type' => 'select',
				'label' => __('Show Product Counts', 'builder-wc'),
				'options' => array(
					'yes' => __('Yes', 'builder-wc'),
					'no' => __('No', 'builder-wc'),
				)
			),
			array(
				'id' => 'latest_products',
				'type' => 'select',
				'label' => __('Latest Products', 'builder-wc'),
				'options' => array(
					'0' => 0,
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
					'7' => 7,
					'8' => 8,
					'9' => 9,
					'10' => 10
				),
				'help' => __('Number of latest products to show.', 'builder-wc'),
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_products',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'builder-wc'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'builder-wc') )
			)
		);
	}

	public function get_default_settings() {
		return array(
			'latest_products' => 3,
			'columns' => 4
		);
	}

	public function get_animation() {
		return array();
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }

	public function get_styling() {
		return array(
                        // Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-product-categories', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-product-categories'),
                        self::get_color('.module-product-categories','font_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-product-categories'),
                        self::get_line_height('.module-product-categories'),
                        self::get_text_align('.module-product-categories'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color('.module-product-categories a','link_color'),
                        self::get_text_decoration('.module-product-categories a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-product-categories'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-product-categories'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-product-categories')
		);
	}
}

function themify_builder_field_product_categories( $field, $module_name ) {
        $dropdown = wp_dropdown_categories( array(
				'taxonomy' => 'product_cat',
				'class' => 'tb_lb_option',
				'show_option_all' => __( 'All Categories', 'builder-wc' ),
				'show_option_none'   => __( 'Only Top Level Categories', 'builder-wc' ),
				'option_none_value'  => 'top-level',
				'hide_empty' => 1,
				'echo' => false,
				'name' => $field['id'],
				'selected' => '',
				'value_field' => 'slug',
			) );
        $dropdown = str_replace('<select','<select data-control-type="change" data-control-binding="refresh" ',$dropdown);
	echo '<div class="themify_builder_field ' . $field['id'] . '">
		<div class="themify_builder_label">'. $field['label'] .'</div>
		<div class="themify_builder_input"><div class="selectwrapper">',$dropdown,'</div>';
    
        if( isset( $field['description'] ) ){
            echo '<p class="description">' . $field['description'] . '</p>';
        }

	echo '</div></div>';
}

Themify_Builder_Model::register_module( 'TB_Product_Categories_Module' );