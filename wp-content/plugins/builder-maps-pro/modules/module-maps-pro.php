<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Module Name: Maps Pro
 */
class TB_Maps_Pro_Module extends Themify_Builder_Component_Module {
	public function __construct() {
		parent::__construct(array(
			'name' => __( 'Maps Pro', 'builder-maps-pro' ),
			'slug' => 'maps-pro'
		));
	}

	/**
	 * Filter the marker texts
	 */
	public static function sanitize_text( $text ) {
		return preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $text );
	}

	function get_assets() {
		$instance = Builder_Maps_Pro::get_instance();
		return array(
			'selector'=>'.module-maps-pro, .module-type-maps-pro',
			'css'=>themify_enque($instance->url.'assets/style.css'),
			'js'=>themify_enque($instance->url.'assets/scripts.js'),
			'ver'=>$instance->version,
		);
	}

	public function get_options() {
           
		$map_styles = array();
		foreach( Builder_Maps_Pro::get_instance()->get_map_styles() as $key => $style ) {
			$name = str_replace( '.json', '', $key );
			$map_styles[$name] = $name;
		}

		return array(
			array(
				'id' => 'mod_title',
				'type' => 'text',
				'label' => __('Module Title', 'builder-maps-pro'),
				'class' => 'large',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'map_display_type',
				'type' => 'radio',
				'label' => __('Type', 'builder-maps-pro'),
				'options' => array(
					'dynamic' => __( 'Dynamic', 'builder-maps-pro' ),
					'static' => __( 'Static image', 'builder-maps-pro' ),
				),
				'default' => 'dynamic',
				'option_js' => true,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'w_map',
				'type' => 'text',
				'class' => 'xsmall',
				'label' => __('Width', 'builder-maps-pro'),
				'unit' => array(
					'id' => 'unit_w',
					'type' => 'select',
					'options' => array(
						array( 'id' => 'pixel_unit_w', 'value' => 'px'),
						array( 'id' => 'percent_unit_w', 'value' => '%')
					),
					'render_callback' => array(
						'binding' => 'live'
					)
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'w_map_static',
				'type' => 'text',
				'class' => 'xsmall',
				'label' => __('Width', 'builder-maps-pro'),
				'value' => 500,
				'after' => 'px',
				'wrap_with_class' => 'tb-group-element tb-group-element-static',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'h_map',
				'type' => 'text',
				'label' => __('Height', 'builder-maps-pro'),
				'class' => 'xsmall',
				'after' => 'px',
				'value' => 300,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'type_map',
				'type' => 'select',
				'label' => __('Type', 'builder-maps-pro'),
				'options' => array(
					'ROADMAP' => __( 'Road Map', 'builder-maps-pro' ),
					'SATELLITE' => __( 'Satellite', 'builder-maps-pro' ),
					'HYBRID' => __( 'Hybrid', 'builder-maps-pro' ),
					'TERRAIN' => __( 'Terrain', 'builder-maps-pro' )
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'style_map',
				'type' => 'select',
				'label' => __('Style', 'builder-maps-pro'),
				'options' => array( '' => '' ) + $map_styles,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'scrollwheel_map',
				'type' => 'select',
				'label' => __( 'Scrollwheel', 'builder-maps-pro' ),
				'options' => array(
					'disable' => __( 'Disable', 'builder-maps-pro' ),
					'enable' => __( 'Enable', 'builder-maps-pro' ),
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'draggable_map',
				'type' => 'select',
				'label' => __( 'Draggable', 'builder-maps-pro' ),
				'options' => array(
					'enable' => __( 'Enable', 'builder-maps-pro' ),
					'desktop_only' => __( 'Enable only on desktop', 'builder-maps-pro' ),
					'disable' => __( 'Disable', 'builder-maps-pro' )
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'disable_map_ui',
				'type' => 'select',
				'label' => __( 'Disable Map Controls', 'builder-maps-pro' ),
				'options' => array(
					'no' => __( 'No', 'builder-maps-pro' ),
					'yes' => __( 'Yes', 'builder-maps-pro' ),
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'map_link',
				'type' => 'checkbox',
				'label' => __( 'Map link', 'builder-maps-pro' ),
				'options' => array(
					array( 'name' => 'gmaps', 'value' => __('Open Google Maps', 'builder-maps-pro'))
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-static',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'zoom_map',
				'type' => 'selectbasic',
				'label' => __('Zoom', 'builder-maps-pro'),
				'default' => 4,
				'options' => range( 1, 18 ),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'map_polyline',
				'type' => 'select',
				'label' => __( 'Use polyline', 'themify' ),
				'options' => array(
					'no' => __( 'No','themify' ),
					'yes' => __( 'Yes','themify' )
				),
				'render_callback' => array(
					'binding' => 'live'
				),
				'binding' => array(
					'yes' => array( 'show' => array( 'map_polyline_options' ) ),
					'no' => array( 'hide' => array( 'map_polyline_options' ) )
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic',
			),
			array(
				'id' => 'map_polyline_options',
				'type' => 'multi',
				'label' => '&nbsp;',
				'fields' => array(
					array(
						'id' => 'map_polyline_geodesic',
						'type' => 'select',
						'label' => __( 'Geodesic', 'themify' ),
						'options' => array(
							'no' => __( 'No','themify' ),
							'yes' => __( 'Yes','themify' )
						),
						'render_callback' => array(
							'binding' => 'live'
						)
					),
					array(
						'id' => 'map_polyline_stroke',
						'type' => 'text',
						'label' => __( 'Stroke Weight', 'themify' ),
						'class' => 'xsmall',
						'help' => 'px',
						'render_callback' => array(
							'binding' => 'live'
						)
					),
					array(
						'id' => 'map_polyline_color',
						'type' => 'text',
						'label' => __( 'Stroke Color', 'themify' ),
						'colorpicker' => true,
						'render_callback' => array(
							'binding' => 'live'
						)
					)
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic tb-group-element-map_polyline'
			),
			array(
				'id' => 'map_center',
				'type' => 'textarea',
				'value' => '',
				'class' => 'fullwidth',
				'label' => __('Base Map Address (Also accepts Lat/Lng values)', 'builder-maps-pro'),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'type' => 'map_pro'
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_class',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'builder-maps-pro'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling', 'builder-maps-pro' ) ),
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
	}

	public function get_default_settings() {
		return array(
			'type_map' => 'ROADMAP',
			'scrollwheel_map' => 'enable',
			'draggable_map' => 'enable',
			'disable_map_ui' => 'no',
			'unit_w' => '%',
			'unit_h' => 'px',
			'w_map' => 100,
			'h_map' => 350,
			'zoom_map' => 4,
			'style_map' => 'pale-dawn',
			'map_polyline' => 'no',
			'map_polyline_geodesic' => 'yes',
			'map_polyline_stroke' => 2,
			'map_polyline_color' => 'ff0000_1',
			'map_center' => 'Toronto, ON, Canada',
			'map_display_type' => 'dynamic',
			'w_map_static' => 500,
			'markers' => array( array(
				'address' => '3 Bedford Road, Toronto, ON, Canada',
				'title' => 'Our Shop',
				'image' => 'https://themify.me/demo/themes/themes/wp-content/uploads/addon-samples/shop-map-marker.png'
			) )
		);
	}

	public function get_styling() {
		return array(
                         //bacground
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-maps-pro', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
                        // Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-maps-pro'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-maps-pro'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-maps-pro')
		);
	}

	protected function _visual_template() {
		$module_args = self::get_module_args();?>

		<#
			var moduleSettings = {
				'zoom': data.zoom_map,
				'type': data.type_map,
				'address': data.map_center,
				'width': data.w_map,
				'height': data.h_map,
				'style_map': data.style_map,
				'scrollwheel': data.scrollwheel_map,
				'polyline': data.map_polyline,
				'geodesic': data.map_polyline_geodesic,
				'polylineStroke': data.map_polyline_stroke,
				'polylineColor': data.map_polyline_color,
				'draggable': ( 'enable' === data.draggable_map || ( 'desktop_only' === data.draggable_map ) ) ? 'enable' : 'disable',
				'disable_map_ui': data.disable_map_ui
			};
			if(!data.unit_w){
				data.unit_w = 'px';
			}
		#>

		<div class="module module-<?php echo $this->slug; ?> {{ data.css_class }}" data-config="{{window.btoa(JSON.stringify( moduleSettings )) }}">

			<# if( data.mod_title ) { #>
				<?php echo $module_args['before_title']; ?>
				{{{ data.mod_title }}}
				<?php echo $module_args['after_title']; ?>
			<# } #>

			<?php do_action( 'themify_builder_before_template_content_render' ); ?>
			
			<# if( data.map_display_type === 'dynamic' ) { #>

				<div class="maps-pro-canvas-container">
					<div class="maps-pro-canvas map-container" style="width: {{ data.w_map }}{{ data.unit_w }}; height: {{ data.h_map }}px;">
					</div>
				</div>

				<div class="maps-pro-markers" style="display: none;">

					<# _.each( data.markers, function( marker ) { #>
						<div class="maps-pro-marker" data-address="{{{ marker.address }}}" data-image="{{ marker.image }}">
							<# marker.title && print( marker.title ) #>
						</div>
					<# } ) #>
				</div>

			<# } else {
				var args = '';
				args += data.map_center ? 'center=' + data.map_center : '';
				args += data.zoom_map ? '&zoom=' + data.zoom_map : '';
				args += data.type_map ? '&maptype=' + data.type_map.toLowerCase() : '';
				args += data.w_map_static ? '&size=' + data.w_map_static.replace( /[^0-9]/, '' ) + 'x' + data.h_map.replace( /[^0-9]/, '' ) : '';
				<?php echo "args += '&key=" . Themify_Builder_Model::getMapKey() . '\';'; ?>

				if( data.markers ) {
					_.each( data.markers, function( marker ) {
						args += marker.image 
							? '&markers=icon:' + encodeURI( marker.image ) + '%7C' + encodeURI( marker.address )
							: '&markers=' + encodeURI( marker.address );
					} );
				}

				if( data.map_link === 'gmaps' && data.map_center ) { #>
					<a href="http://maps.google.com/?q={{ data.map_center }}" target="_blank" rel="nofollow" title="Google Maps">
				<# } #>
			
				<img src="//maps.googleapis.com/maps/api/staticmap?{{ args }}" />

				<# if( data.map_link === 'gmaps' && data.map_center ) { #>
					</a>
				<# } #>

			<# } #>

			<?php do_action( 'themify_builder_after_template_content_render' ); ?>
		</div>
	<?php
	}
}


function themify_builder_field_map_pro( $field, $module_name ) {
	echo '<div id="map-preview"><div id="map-canvas"></div>';
	themify_builder_module_settings_field( array(
		array(
			'id' => 'markers',
			'type' => 'builder',
			'options' => array(
				array(
					'id' => 'address',
					'type' => 'textarea',
					'label' => __('Address (or Lat/Lng)', 'builder-maps-pro'),
					'class' => '',
					'render_callback' => array(
						'binding' => 'live',
						'repeater' => 'markers'
					)
				),
				array(
					'id' => 'latlng',
					'type' => 'textarea',
					'label' => '',
					'class' => 'latlng',
				),
				array(
					'id' => 'title',
					'type' => 'textarea',
					'label' => __('Tooltip Text', 'builder-maps-pro'),
					'class' => '',
					'render_callback' => array(
						'binding' => 'live',
						'repeater' => 'markers'
					)
				),
				array(
					'id' => 'image',
					'type' => 'image',
					'label' => __('Icon', 'builder-maps-pro'),
					'class' => '',
					'render_callback' => array(
						'binding' => 'live',
						'repeater' => 'markers'
					)
				),
			),
			'render_callback' => array(
				'binding' => 'live',
				'control_type' => 'repeater'
			)
		),
	), $module_name );

	echo '<div class="themify_builder_field tb-group-element tb-group-element-static">';
		esc_html_e( 'In static mode, Google allows up to 5 custom icons, though each unique icons may be used multiple times. Icons are limited to sizes of 4096 pixels (64x64 for square images), and also the API does not support custom icon URLs that use HTTPS.', 'builder-maps-pro' );
	echo '</div></div>';

}

Themify_Builder_Model::register_module( 'TB_Maps_Pro_Module' );