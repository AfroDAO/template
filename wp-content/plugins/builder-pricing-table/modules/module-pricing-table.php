<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Module Name: Pricing table
 * Description:
 */

class TB_Pricing_Table_Module extends Themify_Builder_Component_Module {

	function __construct() {
		parent::__construct(array(
			'name' => __('Pricing Table', 'builder-pricing-table'),
			'slug' => 'pricing-table'
		));
	}

	function get_assets() {
		$instance = Builder_Pricing_Table::get_instance();
		return array(
			'selector'=>'.module-pricing-table',
			'css'=>themify_enque($instance->url.'assets/style.css'),
			'ver'=>$instance->version
		);
	}

	public function get_options() {
                $colors = Themify_Builder_Model::get_colors();
                $colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));
		return array(
			array(
				'id' => 'mod_color_pricing_table',
				'type' => 'layout',
                                'mode'=>'sprite',
                                'class'=>'tb-colors',
				'label' => __('Appearance', 'builder-pricing-table'),
				'options' => $colors,
				'render_callback' => array(
                                    'binding' => 'live'
				)
			),
			array(
				'id' => 'mod_appearance_pricing_table',
				'type' => 'checkbox',
				'label' => __(' ', 'builder-pricing-table'),
				'default' => array(),
				'options' => Themify_Builder_Model::get_appearance(),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'mod_title_pricing_table',
				'type' => 'text',
				'label' => __('Title', 'builder-pricing-table'),
				'class' => 'large',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-pricing-table-title>span'
				)
			),
			array(
				'id' => 'mod_title_icon_pricing_table',
				'type' => 'icon',
				'label' => __('Title icon', 'builder-pricing-table'),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'mod_price_pricing_table',
				'type' => 'text',
				'label' => __('Price', 'builder-pricing-table'),
				'class' => 'large',
				'after' => sprintf('<br/><small>%s</small>',
						__('(eg. $29)', 'builder-pricing-table')),
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-pricing-table-price'
				)
			),
			array(
				'id' => 'mod_recurring_fee_pricing_table',
				'type' => 'text',
				'label' => __('Recurring Fee', 'builder-pricing-table'),
				'class' => 'large',
				'after' => sprintf('<br/><small>%s</small>',
						__('(eg. per month)', 'builder-pricing-table')),
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-pricing-table-reccuring-fee'
				)
			),
			array(
				'id' => 'mod_description_pricing_table',
				'type' => 'text',
				'label' => __('Description', 'builder-pricing-table'),
				'class' => 'large',
				'after' => sprintf('<br/><small>%s</small>',
						__('(eg. For Basic Users)', 'builder-pricing-table')),
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-pricing-table-description'
				)
			),
			array(
				'id' => 'mod_feature_list_pricing_table',
				'type' => 'textarea',
				'label' => __('Feature List', 'builder-pricing-table'),
				'class' => 'large exclude-from-reset-field textarea-feature',
				'description' => sprintf('<br/><small>%s</small>',
						__('Enter one line per each feature', 'builder-pricing-table')),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'mod_unavailable_feature_list_pricing_table',
				'type' => 'textarea',
				'label' => __('Unavailable Feature List', 'builder-pricing-table'),
				'class' => 'large exclude-from-reset-field textarea-unfeature',
				'description' => sprintf('<br/><small>%s</small>',
						__('Unavailable feature list will appear greyed-out',
								'builder-pricing-table')),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'mod_button_text_pricing_table',
				'type' => 'text',
				'label' => __('Buy Button Text', 'builder-pricing-table'),
				'class' => 'large',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-pricing-table-button'
				)
			),
			array(
				'id' => 'multi_link_pricing_table',
				'type' => 'multi',
				'label' => __('Buy Button Link', 'builder-pricing-table'),
				'fields' => array(
					array(
						'id' => 'mod_button_link_pricing_table',
						'type' => 'text',
						'class' => 'large',
						'prop' => 'href',
						'selector' => '.module-pricing-table',
						'render_callback' => array(
							'binding' => 'live'
						)
					),
					array(
						'id' => 'mod_pricing_blank_button',
						'type' => 'radio',
						'class' => 'large',
						'options' => array(
							'default' => __('Default', 'builder-pricing-table'),
							'modal' => __('Open in lightbox', 'builder-pricing-table'),
							'external' => __('Open in external link', 'builder-pricing-table'),
						),
						'default' => 'default',
						'render_callback' => array(
							'binding' => FALSE
						)
					)
				)
			),
			array(
				'id' => 'mod_pop_text_pricing_table',
				'type' => 'text',
				'label' => __('Pop-out Text', 'builder-pricing-table'),
				'class' => 'large',
				'after' => sprintf('<br/><small>%s</small>',
						__('(eg. Popular)', 'builder-pricing-table')),
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-pricing-table-pop'
				)
			),
			array(
				'id' => 'mod_enlarge_pricing_table',
				'type' => 'checkbox',
				'label' => __('Enlarge this pricing box', 'builder-pricing-table'),
				'options' => array(
					array('name' => 'enlarge', 'value' => __('', 'builder-pricing-table'))
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_pricing_table',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'themify') ),
				'class' => 'large exclude-from-reset-field',
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
	}

	public function get_default_settings() {
		return array(
			'mod_color_pricing_table' => 'blue',
			'mod_title_pricing_table' => esc_html__( 'Package Title', 'builder-pricing-table' ),
			'mod_price_pricing_table' => '$200',
			'mod_description_pricing_table' => esc_html__( 'Description Here', 'builder-pricing-table' ),
			'mod_feature_list_pricing_table' => esc_html__( "Feature Item\r\nFeature Item 2", 'builder-pricing-table' ),
			'mod_button_text_pricing_table' => esc_html__( 'Buy Now', 'builder-pricing-table' ),
			'mod_button_link_pricing_table' => 'https://themify.me'
		);
	}

	public function get_styling() {
		$table_header = array(
                        // Background
                        self::get_seperator('image_background',__('Background', 'themify'),false),
                        self::get_color('.module.module-pricing-table .module-pricing-table-header','mod_title_background_color',__('Background Color', 'themify'),'background-color'),
                        // Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module.module-pricing-table .module-pricing-table-header','mod_title_font_family'),
                        self::get_color('.module.module-pricing-table .module-pricing-table-header', 'mod_title_font_color', __('Font Color', 'themify')),
                        self::get_font_size('.module.module-pricing-table .module-pricing-table-header','font_size_title'),
                        self::get_line_height('.module.module-pricing-table .module-pricing-table-header','mod_line_height_title'),
                        self::get_text_align('.module.module-pricing-table .module-pricing-table-header','mod_text_align_title')
                       
		);
		// Features list
		$feature_list = array(
                        // Background
                        self::get_seperator('image_background',__('Background', 'themify'),false),
                        self::get_color('.module-pricing-table .module-pricing-table-content','mod_feature_bg_color',__('Background Color', 'themify'),'background-color'),
                        // Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-pricing-table .module-pricing-table-content','mod_feature_font_family'),
                        self::get_color('.module-pricing-table .module-pricing-table-content', 'mod_feature_font_color', __('Font Color', 'themify')),
                        self::get_font_size('.module-pricing-table .module-pricing-table-content','font_size_content'),
                        self::get_line_height('.module-pricing-table .module-pricing-table-content','mod_line_height_content'),
                        self::get_text_align('.module-pricing-table .module-pricing-table-content','mod_text_align_content')
		);
		//Pop text
		$pop_text = array(
                        self::get_font_family('.module-pricing-table .module-pricing-table-pop','mod_pop_font_family'),
                        self::get_color('.module-pricing-table .module-pricing-table-pop', 'mod_pop_font_color', __('Font Color', 'themify'))
		);
		//Buy button
		$buy_button = array(
                        // Background
                        self::get_seperator('image_background',__('Background', 'themify'),false),
                        self::get_color('.module-pricing-table .module-pricing-table-button','mod_button_bg_color',__('Background Color', 'themify'),'background-color'),
                        // Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-pricing-table .module-pricing-table-button','mod_button_font_family'),
                        self::get_color('.module-pricing-table .module-pricing-table-button', 'mod_button_font_color', __('Font Color', 'themify')),
                        self::get_font_size('.module-pricing-table .module-pricing-table-button','font_size_button'),
                        self::get_line_height('.module-pricing-table .module-pricing-table-button','mod_line_height_button'),
                        self::get_text_align('.module-pricing-table .module-pricing-table-button','mod_text_align_button')
		);
		$general = array(
                        //bacground
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image( '.ui.module-pricing-table'),
                        self::get_color( '.ui.module-pricing-table', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
                        self::get_repeat( '.ui.module-pricing-table'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.ui.module-pricing-table'),
                        // Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-pricing-table'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-pricing-table'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-pricing-table')
		);

		return array(
			array(
				'type' => 'tabs',
				'id' => 'module-styling',
				'tabs' => array(
					'general' => array(
						'label' => __('General', 'builder-pricing-table'),
						'fields' => $general
					),
					'top-table-header' => array(
						'label' => __('Top Table Header', 'builder-pricing-table'),
						'fields' => $table_header
					),
					'feature-list' => array(
						'label' => __('Features list', 'builder-pricing-table'),
						'fields' => $feature_list
					),
					'buy-button' => array(
						'label' => __('Buy Button', 'builder-pricing-table'),
						'fields' => $buy_button
					),
					'pop-text' => array(
						'label' => __('Pop-out Text', 'builder-pricing-table'),
						'fields' => $pop_text
					)
				)
			),
		);
	}

	protected function _visual_template() {?>
		<#
                if(!data.mod_color_pricing_table){
                    data.mod_color_pricing_table = 'blue';
		}
                var appearance = data.mod_appearance_pricing_table?data.mod_appearance_pricing_table.replace(/\|/ig,' '):''; #>
		<div class="ui module module-<?php echo $this->slug; ?> <# data.mod_pop_text_pricing_table && print( 'pricing-pop' ) #> <# data.mod_enlarge_pricing_table == 'enlarge' && print( 'pricing-enlarge' ) #> {{ appearance }} {{ data.mod_color_pricing_table }}">
			<!--insert-->
                        <?php do_action('themify_builder_before_template_content_render'); ?>

			<# if( data.mod_pop_text_pricing_table ) { #>
				<span class="fa module-pricing-table-pop">{{ data.mod_pop_text_pricing_table }}</span>
			<# } #>

			<div class="module-pricing-table-header ui {{ data.mod_color_pricing_table }} {{ appearance }}" >
				<# if( data.mod_title_pricing_table ) { #>
					<span class="module-pricing-table-title">
						<# if( data.mod_title_icon_pricing_table ) { #>
							<i class="fa {{ data.mod_title_icon_pricing_table }}"></i>
						<# } #>
						<span>{{ data.mod_title_pricing_table }}</span>
					</span>
				<# } #>

				<# if( data.mod_price_pricing_table ) { #>
					<span class="module-pricing-table-price">{{ data.mod_price_pricing_table }}</span>
				<# } #>

				<# if( data.mod_recurring_fee_pricing_table ) { #>
					<p class="module-pricing-table-reccuring-fee">{{ data.mod_recurring_fee_pricing_table }}</p>
				<# } #>

				<# if( data.mod_description_pricing_table ) { #>
					<p	class="module-pricing-table-description">{{ data.mod_description_pricing_table }}</p>
				<# } #>
			</div><!-- .module-pricing-table-header -->
                        <div class="module-pricing-table-content">
				<# if( data.mod_feature_list_pricing_table ) { 
					_.each( data.mod_feature_list_pricing_table.split( "\n" ), function( line ) { #>
						<p class="module-pricing-table-features">{{ line }}</p>
					<# } );
				} 
				
				if( data.mod_unavailable_feature_list_pricing_table ) {
					_.each( data.mod_unavailable_feature_list_pricing_table.split( "\n" ), function( line ) { #>
						<p class="module-pricing-table-features unavailable-features">{{ line }}</p>
					<# } );
				}

				if( data.mod_button_text_pricing_table ) { #>
					<a class="module-pricing-table-button ui {{ data.mod_color_pricing_table }} {{ appearance }}" href="{{ data.mod_button_link_pricing_table }}">
						{{{ data.mod_button_text_pricing_table }}}
					</a>
				<# } #>
				
			</div><!-- .module-pricing-table-content -->

			<?php do_action('themify_builder_after_template_content_render'); ?>
		</div>
	<?php
	}

}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module('TB_Pricing_Table_Module');
