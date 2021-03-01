<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Module Name: Image Pro
 * Description: 
 */
class TB_Image_Pro_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __( 'Image Pro', 'builder-image-pro' ),
			'slug' => 'pro-image'
		));
	}

	function get_assets() {
		$instance = Builder_Image_Pro::get_instance();
		return array(
			'selector'=>'.module.module-pro-image',
			'css'=>themify_enque($instance->url.'assets/style.css'),
			'js'=>themify_enque($instance->url.'assets/scripts.js'),
			'ver'=>$instance->version
		);
	}

	public function get_options() {
		$is_img_enabled = Themify_Builder_Model::is_img_php_disabled();
		$image_sizes = !$is_img_enabled?themify_get_image_sizes_list( false ):array();
                $colors = Themify_Builder_Model::get_colors();
                unset($colors[0]);
                $colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));
                $colors[] =array('img' => 'white', 'value' => 'white', 'label' => __( 'White', 'builder-image-pro' ));
                $colors[] =array('img' => 'outline','value' => 'outline', 'label' => __( 'Outline', 'builder-image-pro' ));
		
		$options = array(
			array(
				'id' => 'mod_title_image',
				'type' => 'text',
				'label' => __( 'Module Title', 'builder-image-pro' ),
				'class' => 'large',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-title'
				)
			),
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr class="meta_fields_separator" /><h3>'. __( 'Image', 'builder-image-pro' ) .'</h3>' )
			),
			array(
				'id' => 'url_image',
				'type' => 'image',
				'label' => __( 'Image URL', 'builder-image-pro' ),
				'class' => 'fullwidth',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'link_image_type',
				'type' => 'radio',
				'label' => __( 'Image Link Type', 'builder-image-pro' ),
				'options' => array(
					'image_external' => __( 'Link', 'builder-image-pro' ),
					'image_lightbox_link' => __( 'Lightbox Link', 'builder-image-pro' ),
					'image_modal' => __( 'Text modal', 'builder-image-pro' ),
				),
				'default' => 'image_external',
				'help' => sprintf( '<span class="tb-group-element tb-group-element-image_modal">%s</span>', __( '(it will open text content in a lightbox)', 'builder-image-pro' ) ),
				'option_js' => true,
				'render_callback' => array(
                                    'binding' =>false
				)
			),
			array(
				'id' => 'image_content_modal',
				'type' => 'wp_editor',
				'class' => 'fullwidth',
				'wrap_with_class' => 'tb-group-element tb-group-element-image_modal',
                                'render_callback' => array(
                                    'binding' =>false
				)
			),
			array(
				'id' => 'link_image',
				'type' => 'text',
				'label' => false,
				'pushed' => 'pushed',
				'before' => __( 'Image Link', 'builder-image-pro' ),
				'class' => 'fullwidth',
				'wrap_with_class' => 'tb-group-element tb-group-element-image_external tb-group-element-image_lightbox_link',
                                'render_callback' => array(
                                    'binding' =>false
				)
			),
			array(
				'id' => 'link_image_new_window',
				'type' => 'radio',
				'before' => __( 'Open in new window', 'builder-image-pro' ),
				'label' => false,
				'pushed' => 'pushed',
				'options' => array(
					'yes' => __( 'Yes', 'builder-image-pro' ),
					'no' => __( 'No', 'builder-image-pro' ),
				),
				'default' => 'no',
				'wrap_with_class' => 'tb-group-element tb-group-element-image_external',
                                'render_callback' => array(
                                    'binding' =>false
				)
			),
			array(
				'id' => 'lightbox_size',
				'type' => 'multi',
				'label' => false,
				'pushed' => 'pushed',
				'before' => __( 'Lightbox Dimension', 'themify' ),
				'fields' => array(
					array(
						'id' => 'lightbox_width',
						'type' => 'text',
						'label' => __( 'Width', 'themify' ),
						'value' => '',
						'render_callback' => array(
							'binding' => false
						)
					),
					array(
						'id' => 'lightbox_size_unit_width',
						'type' => 'select',
						'label' => __( 'Units', 'themify' ),
						'options' => array(
							'pixels' => __( 'px ', 'themify' ),
							'percents' => __( '%', 'themify' )
						),
						'default' => 'pixels',
						'render_callback' => array(
							'binding' => false
						)
					),
					array(
						'id' => 'lightbox_height',
						'type' => 'text',
						'label' => __( 'Height', 'themify' ),
						'value' => '',
						'render_callback' => array(
							'binding' => false
						)
					),
					array(
						'id' => 'lightbox_size_unit_height',
						'type' => 'select',
						'label' => __( 'Units', 'themify' ),
						'options' => array(
							'pixels' => __( 'px ', 'themify' ),
							'percents' => __( '%', 'themify' )
						),
						'default' => 'pixels',
						'render_callback' => array(
							'binding' =>false
						)
					)
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-image_lightbox_link tb-group-element-image_modal',
                                'render_callback' => array(
                                    'binding' =>false
				)
			),
			array(
				'id' => 'image_size_image',
				'type' => 'select',
				'label' =>  __( 'Image Size', 'builder-image-pro' ),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'hide' => !$is_img_enabled,
				'options' => $image_sizes,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'width_image',
				'type' => 'text',
				'label' => __( 'Width', 'builder-image-pro' ),
				'class' => 'xsmall',
				'help' => 'px',
				'value' => 300,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'appearance_image',
				'type' => 'checkbox',
				'label' => '&nbsp;',
				'default' => 'rounded',
				'options' => array(
					array( 'name' => 'fullwidth_image', 'value' => __( 'Auto full width', 'builder-image-pro' ), 'help' => __( 'Span the image across available width', 'builder-image-pro' ))
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'height_image',
				'type' => 'text',
				'label' => __( 'Height', 'builder-image-pro' ),
				'class' => 'xsmall',
				'help' => 'px',
				'value' => 200,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'appearance_image2',
				'type' => 'checkbox',
				'label' => __( 'Image Appearance', 'builder-image-pro' ),
				'default' => '',
				'options' => array(
					array( 'name' => 'rounded', 'value' => __( 'Rounded', 'builder-image-pro' )),
					array( 'name' => 'circle', 'value' => __( 'Circle', 'builder-image-pro' ), 'help' => __( '(square format image only)', 'builder-image-pro' )),
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'image_filter',
				'type' => 'select',
				'label' => __( 'Image Filter', 'builder-image-pro' ),
				'options' => array(
					'none' => __( '', 'builder-image-pro' ),
					'grayscale' => __( 'Grayscale', 'builder-image-pro' ),
					'grayscale-reverse' => __( 'Grayscale Reverse', 'builder-image-pro' ),
					'sepia' => __( 'Sepia', 'builder-image-pro' ),
					'blur' => __( 'Blur', 'builder-image-pro' ),
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'image_effect',
				'type' => 'select',
				'label' => __( 'Image Hover Effect', 'builder-image-pro' ),
				'options' => array(
					'none' => __( '', 'builder-image-pro' ),
					'zoomin' => __( 'Zoom In', 'builder-image-pro' ),
					'zoomout' => __( 'Zoom Out', 'builder-image-pro' ),
					'rotate' => __( 'Rotate', 'builder-image-pro' ),
					'shine' => __( 'Shine', 'builder-image-pro' ),
					'glow' => __( 'Glow', 'builder-image-pro' ),
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'image_alignment',
				'type' => 'radio',
				'label' => __( 'Image Alignment', 'builder-image-pro' ),
				'options' => array(
					'image_alignment_left' => __( 'Left', 'builder-image-pro' ),
					'image_alignment_center' => __( 'Center', 'builder-image-pro' ),
					'image_alignment_right' => __( 'Right', 'builder-image-pro' ),
				),
				'default' => 'image_alignment_left',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr class="meta_fields_separator" /><h3>'. __( 'Overlay', 'builder-image-pro' ) .'</h3>' )
			),
			array(
				'id' => 'title_image',
				'type' => 'text',
				'label' => __( 'Image Title', 'builder-image-pro' ),
				'class' => 'fullwidth',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'caption_image',
				'type' => 'textarea',
				'label' => __( 'Image Caption', 'builder-image-pro' ),
				'class' => 'fullwidth',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'action_button',
				'type' => 'text',
				'label' => __( 'Action Button', 'builder-image-pro' ),
				'class' => 'fullwidth',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'link_address',
				'type' => 'text',
				'label' => __( 'Button Link', 'builder-image-pro' ),
				'class' => 'fullwidth',
				'binding' => array(
					'empty' => array(
						'hide' => array('link_type', 'link_new_window')
					),
					'not_empty' => array(
						'show' => array('link_type', 'link_new_window')
					)
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-external tb-group-element-lightbox_link',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'link_type',
				'type' => 'radio',
				'label' => __( 'Button Link Type', 'builder-image-pro' ),
				'options' => array(
					'external' => __( 'Link', 'builder-image-pro' ),
					'lightbox_link' => __( 'Lightbox Link', 'builder-image-pro' ),
					'modal' => __( 'Text modal', 'builder-image-pro' ),
				),
				'default' => 'external',
				'help' => sprintf( '<span class="tb-group-element tb-group-element-modal">%s</span>', __( '(it will open text content in a lightbox)', 'builder-image-pro' ) ),
				'option_js' => true,
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'link_new_window',
				'type' => 'radio',
				'label' => __( 'Open in new window', 'builder-image-pro' ),
				'options' => array(
					'yes' => __( 'Yes', 'builder-image-pro' ),
					'no' => __( 'No', 'builder-image-pro' ),
				),
				'default' => 'no',
				'wrap_with_class' => 'tb-group-element tb-group-element-external',
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'color_button',
				'type' => 'layout',
                                'mode'=>'sprite',
                                'class'=>'tb-colors',
				'label' => __( 'Button Color', 'builder-image-pro' ),
				'options' =>$colors,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'content_modal',
				'type' => 'wp_editor',
				'class' => 'fullwidth',
				'wrap_with_class' => 'tb-group-element tb-group-element-modal',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'overlay_styles',
				'type' => 'multi',
				'label' => __( 'Overlay', 'builder-image-pro' ),
				'fields' => array(
					array(
						'id' => 'overlay_color',
						'type' => 'text',
						'colorpicker' => true,
						'label' => __( 'Overlay Color', 'builder-image-pro' ),
						'class' => 'small',
						'wrap_with_class' => '',
						'render_callback' => array(
							'binding' => 'live'
						)
					),
					array(
						'id' => 'overlay_image',
						'type' => 'image',
						'label' => __( 'Overlay Image', 'builder-image-pro' ),
						'class' => 'xlarge',
						'render_callback' => array(
							'binding' => 'live'
						)
					),
				)
			),
			array(
				'id' => 'overlay_effect',
				'type' => 'select',
				'label' => __( 'Overlay Effect', 'builder-image-pro' ),
				'options' => array(
					'none' => __( 'No Effect', 'builder-image-pro' ),
					'fadeIn' => __( 'Fade In', 'builder-image-pro' ),
					'partial-overlay' => __( 'Partial Overlay', 'builder-image-pro' ),
					'flip-horizontal' => __( 'Horizontal Flip', 'builder-image-pro' ),
					'flip-vertical' => __( 'Vertical Flip', 'builder-image-pro' ),
					'fadeInUp' => __( 'fadeInUp', 'builder-image-pro' ),
					'fadeInLeft' => __( 'fadeInLeft', 'builder-image-pro' ),
					'fadeInRight' => __( 'fadeInRight', 'builder-image-pro' ),
					'fadeInDown' => __( 'fadeInDown', 'builder-image-pro' ),
					'zoomInUp' => __( 'zoomInUp', 'builder-image-pro' ),
					'zoomInLeft' => __( 'zoomInLeft', 'builder-image-pro' ),
					'zoomInRight' => __( 'zoomInRight', 'builder-image-pro' ),
					'zoomInDown' => __( 'zoomInDown', 'builder-image-pro' ),
				),
				'wrap_with_class' => '',
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
				'id' => 'css_image',
				'type' => 'text',
				'label' => __( 'Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling', 'themify' ) ),
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
		return $options;
	}

	public function get_default_settings() {
		return array(
			'url_image' => 'https://themify.me/demo/themes/themes/wp-content/uploads/addon-samples/image-pro-sample-image.jpg',
			'width_image' => 350,
			'height_image' => 275,
			'title_image' => esc_html__( 'Image Title', 'builder-image-pro' ),
			'overlay_effect' => 'fadeIn',
			'image_alignment' => 'image_alignment_left'
		);
	}

	public function get_styling() {
		return array(
                        //bacground
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-pro-image', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family( array( '.module-pro-image .image-pro-caption', '.module-pro-image .image-pro-title', '.module-pro-image .image-pro-action-button' )),
                        self::get_color(array( '.module-pro-image .image-pro-title', '.module-pro-image .image-pro-caption' ),'font_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-pro-image'),
			self::get_line_height('.module-pro-image'),
                        self::get_text_align('.module-pro-image'),
                        // Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-pro-image'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-pro-image'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-pro-image')
		);
	}

	protected function _visual_template() {
		$module_args = self::get_module_args();?>

		<# var moduleSettings = '';
			moduleSettings += data.image_filter ? ' filter-' + data.image_filter : '';
			moduleSettings += data.image_effect ? ' effect-' + data.image_effect : '';
			moduleSettings += data.appearance_image ? ' ' + data.appearance_image : '';
			moduleSettings += data.appearance_image2 ? ' ' + data.appearance_image2 : '';
			moduleSettings += data.image_alignment ? ' ' + data.image_alignment : '';
			moduleSettings += data.style_image ? ' ' + data.style_image : '';
			moduleSettings += data.css_image ? ' ' + data.css_image : '';
			moduleSettings += data.animation_effect ? ' ' + data.animation_effect : '';
			moduleSettings += data.overlay_effect ? ' entrance-effect-' + data.overlay_effect : '';
		#>
		
		<div class="module module-<?php echo $this->slug; ?> {{ moduleSettings }}" data-entrance-effect="{{ data.overlay_effect }}" data-exit-effect="">
                        <!--insert-->
			<# if( data.mod_title_image ) { #>
				<?php echo $module_args['before_title']; ?>
				{{{ data.mod_title_image }}}
				<?php echo $module_args['after_title']; ?>
			<# } #>

			<?php do_action( 'themify_builder_before_template_content_render' ); ?>
			
			<div class="image-pro-wrap">
				<# if( data.link_image || data.link_image_type == 'image_modal' ) { #>
					<a class="image-pro-external" href="#"></a>
				<# } #>
				<div class="image-pro-flip-box-wrap">
				<div class="image-pro-flip-box">
				
				<# if( data.url_image ) { #>
					<img src="{{ data.url_image }}" width="{{ data.width_image }}" height="{{ data.height_image }}">
				<# } #>

				<div class="image-pro-overlay <# 'none' == data.overlay_effect && print( 'none' ) #>" <# data.overlay_image && print( 'style="background: url(' + data.overlay_image + ')"' ) #>>

					<# if( data.overlay_color ) { #>
						<div class="image-pro-color-overlay" style="background-color: <# print( themifybuilderapp.Utils.toRGBA( data.overlay_color ) ) #>"></div>
					<# } #>

					<div class="image-pro-overlay-inner">

						<# if( data.title_image ) { #>
							<h4 class="image-pro-title">{{{ data.title_image }}}</h4>
						<# } #>

						<# if( data.caption_image ) { #>
							<div class="image-pro-caption">{{{ data.caption_image }}}</div>
						<# } #>

						<# if( data.action_button ) { #>
							<a class="ui builder_button image-pro-action-button {{ data.color_button }}" href="#">
								{{{ data.action_button }}}
							</a>
						<# } #>
					</div>
				</div><!-- .image-pro-overlay -->

				</div>
				</div>

			</div><!-- .image-pro-wrap -->

			<?php do_action( 'themify_builder_after_template_content_render' ); ?>
		</div>
	<?php
	}
}

Themify_Builder_Model::register_module( 'TB_Image_Pro_Module' );