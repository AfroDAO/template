<?php

$tile_size_options = array();
foreach( Themify_Tiles::get_instance()->get_tile_sizes() as $key => $size ) {
	$tile_size_options[] = array( 'img' => $size['image'], 'value' => $key, 'label' => $size['label'] );
}

return apply_filters( 'themify_builder_module_tile', array(
	'options' => array(
		array(
			'id' => 'size',
			'type' => 'layout',
			'label' => __('Size', 'builder-tiles'),
			'options' => $tile_size_options
		),
		array(
			'type' => 'tabs',
			'id' => 'tile',
			'tabs' => array(
				'front' => array(
					'label' => __( 'Front', 'builder-tiles' ),
					'fields' => array(
						array(
							'id' => 'type_front',
							'type' => 'layout',
							'label' => __('Type', 'builder-tiles'),
							'options' => array(
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-text.png', 'value' => 'text', 'label' => __('Text', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-button.png', 'value' => 'button', 'label' => __('Button', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-gallery.png', 'value' => 'gallery', 'label' => __('Gallery', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-map.png', 'value' => 'map', 'label' => __('Map', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-blank.png', 'value' => 'blank', 'label' => __('Blank', 'builder-tiles')),
							),
							'option_js' => true
						),
						array(
							'id' => 'color_front',
							'type' => 'layout',
							'label' => __('Tile Color', 'builder-tiles'),
							'options' => apply_filters( 'builder_tile_colors', array(
								array('img' => THEMIFY_TILES_URI . 'assets/color-default.png', 'value' => 'default', 'label' => __('default', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-black.png', 'value' => 'black', 'label' => __('black', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-grey.png', 'value' => 'gray', 'label' => __('gray', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-blue.png', 'value' => 'blue', 'label' => __('blue', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-light-blue.png', 'value' => 'light-blue', 'label' => __('light-blue', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-green.png', 'value' => 'green', 'label' => __('green', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-light-green.png', 'value' => 'light-green', 'label' => __('light-green', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-purple.png', 'value' => 'purple', 'label' => __('purple', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-light-purple.png', 'value' => 'light-purple', 'label' => __('light-purple', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-brown.png', 'value' => 'brown', 'label' => __('brown', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-orange.png', 'value' => 'orange', 'label' => __('orange', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-yellow.png', 'value' => 'yellow', 'label' => __('yellow', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-red.png', 'value' => 'red', 'label' => __('red', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-pink.png', 'value' => 'pink', 'label' => __('pink', 'builder-tiles'))
							) ),
							'bottom' => true,
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text tf-tile-options-gallery',
						),
						array(
							'id' => 'title_front',
							'type' => 'text',
							'label' => __('Title', 'builder-tiles'),
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text'
						),
						/* Text Tile options */
						array(
							'id' => 'text_front',
							'type' => 'wp_editor',
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-text'
						),

						/* Button Tile options */
						array(
							'id' => 'button_link_front',
							'type' => 'text',
							'label' => __('Title Link', 'builder-tiles'),
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button'
						),
						array(
							'id' => 'button_link_params_front',
							'type' => 'checkbox',
							'label' => false,
							'pushed' => 'pushed',
							'options' => array(
								array( 'name' => 'lightbox', 'value' => __('Open link in lightbox', 'builder-tiles')),
								array( 'name' => 'newtab', 'value' => __('Open link in new tab', 'builder-tiles'))
							),
							'new_line' => false,
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button'
						),
						array(
							'id' => 'button_icon_front',
							'type' => 'group',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button',
							'fields' => array(
								array(
									'id' => 'icon_type_front',
									'type' => 'radio',
									'label' => __('Icon Type', 'builder-tiles'),
									'options' => array(
										'icon' => __('Icon', 'builder-tiles'),
										'image' => __('Image', 'builder-tiles'),
									),
									'default' => 'icon',
									'option_js' => true,
									'wrap_with_class' => 'tf-tile-options tf-tile-options-button'
								),
								array(
									'id' => 'icon_front',
									'type' => 'icon',
									'label' => __('Icon', 'builder-tiles'),
									'class' => 'small',
									'wrap_with_class' => 'tf-group-element tf-group-element-icon tf-tile-options tf-tile-options-button'
								),
								array(
									'id' => 'image_front',
									'type' => 'image',
									'label' => __('Image URL', 'builder-tiles'),
									'class' => 'xlarge',
									'wrap_with_class' => 'tf-group-element tf-group-element-image tf-tile-options tf-tile-options-button'
								),
								array(
									'id' => 'icon_color_front',
									'type' => 'text',
									'colorpicker' => true,
									'label' => __('Icon Color', 'builder-tiles'),
									'class' => 'small',
									'wrap_with_class' => 'tf-group-element tf-group-element-icon tf-tile-options tf-tile-options-button'
								),
							)
						),

						/* Gallery Tile options */
						array(
							'id' => 'gallery_front',
							'type' => 'textarea',
							'class' => 'fullwidth tf-shortcode-input',
							'label' => __('Gallery Slider', 'builder-tiles'),
							'help' => sprintf('<a href="#" class="builder_button tf-gallery-btn">%s</a>', __('Insert Gallery', 'builder-tiles')),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-gallery'
						),
						array(
							'id' => 'gallery_autoplay_front',
							'type' => 'select',
							'label' => __('Auto play', 'builder-tiles'),
							'options' => array(
								'off' => __( 'Off', 'builder-tiles' ),
								'1000' => __( '1 Second', 'builder-tiles' ),
								'2000' => __( '2 Seconds', 'builder-tiles' ),
								'3000' => __( '3 Seconds', 'builder-tiles' ),
								'4000' => __( '4 Seconds', 'builder-tiles' ),
								'5000' => __( '5 Seconds', 'builder-tiles' ),
								'6000' => __( '6 Seconds', 'builder-tiles' ),
								'7000' => __( '7 Seconds', 'builder-tiles' ),
								'8000' => __( '8 Seconds', 'builder-tiles' ),
								'9000' => __( '9 Seconds', 'builder-tiles' ),
								'10000' => __( '10 Seconds', 'builder-tiles' ),
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-gallery'
						),
						array(
							'id' => 'gallery_hide_timer_front',
							'type' => 'select',
							'label' => __('Hide Timer', 'builder-tiles'),
							'options' => array(
								'no' => __( 'No', 'builder-tiles' ),
								'yes' => __( 'Yes', 'builder-tiles' ),
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-gallery'
						),

						/* Map Tile options */
						array(
							'id' => 'address_map_front',
							'type' => 'textarea',
							'value' => '',
							'class' => 'fullwidth',
							'label' => __('Address', 'builder-tiles'),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),
						array(
							'id' => 'type_map_front',
							'type' => 'select',
							'label' => __('Type', 'builder-tiles'),
							'options' => array(
								'ROADMAP' => 'Road Map',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid',
								'TERRAIN' => 'Terrain'
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),
						array(
							'id' => 'zoom_map_front',
							'type' => 'select',
							'label' => __('Zoom', 'builder-tiles'),
							'options' => range( 1, 18 ),
							'default' => 8,
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),
						array(
							'id' => 'scrollwheel_map_front',
							'type' => 'select',
							'label' => __( 'Scrollwheel', 'builder-tiles' ),
							'options' => array(
								'enable' => __( 'Enable', 'builder-tiles' ),
								'disable' => __( 'Disable', 'builder-tiles' )
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),
						array(
							'id' => 'draggable_map_front',
							'type' => 'select',
							'label' => __( 'Draggable', 'builder-tiles' ),
							'options' => array(
								'enable' => __( 'Enable', 'builder-tiles' ),
								'disable' => __( 'Disable', 'builder-tiles' )
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),

						array(
							'id' => 'action_text_front',
							'type' => 'text',
							'label' => __('Action Button Text', 'builder-tiles'),
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-text tf-tile-options-gallery'
						),
						array(
							'id' => 'action_link_front',
							'type' => 'text',
							'label' => __('Action Button Link', 'builder-tiles'),
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-text tf-tile-options-gallery'
						),
						array(
							'id' => 'action_param_front',
							'type' => 'checkbox',
							'label' => false,
							'pushed' => 'pushed',
							'options' => array(
								array( 'name' => 'lightbox', 'value' => __('Open link in lightbox', 'builder-tiles')),
								array( 'name' => 'newtab', 'value' => __('Open link in new tab', 'builder-tiles'))
							),
							'new_line' => false,
							'wrap_with_class' => 'tf-tile-options tf-tile-options-text tf-tile-options-gallery'
						),
						array(
							'id' => 'tile_front_style',
							'label' => __( 'Custom Style', 'builder-tiles' ),
							'type' => 'multi',
							'fields' => array(
								array(
									'id' => 'background_color_front',
									'type' => 'text',
									'colorpicker' => true,
									'class' => 'large',
									'label' => __('Background Color', 'builder-tiles'),
								),
								array(
									'id' => 'text_color_front',
									'type' => 'text',
									'colorpicker' => true,
									'class' => 'large',
									'label' => __('Text Color', 'builder-tiles'),
								),
								array(
									'id' => 'link_color_front',
									'type' => 'text',
									'colorpicker' => true,
									'class' => 'large',
									'label' => __('Link Color', 'builder-tiles'),
								),
							),
							'separated' => 'top',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text',
						),
						array(
							'id' => 'background_image_front',
							'type' => 'image',
							'label' => __('Background Image URL', 'builder-tiles'),
							'class' => 'xlarge',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text',
						),
					)
				),
				'back' => array(
					'label' => __( 'Back', 'builder-tiles' ),
					'fields' => array(
						array(
							'id' => 'type_back',
							'type' => 'layout',
							'label' => __('Type', 'builder-tiles'),
							'options' => array(
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-text.png', 'value' => 'text', 'label' => __('Text', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-button.png', 'value' => 'button', 'label' => __('Button', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-gallery.png', 'value' => 'gallery', 'label' => __('Gallery', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-map.png', 'value' => 'map', 'label' => __('Map', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/tile-type-blank.png', 'value' => 'blank', 'label' => __('Blank', 'builder-tiles')),
							),
							'option_js' => true
						),
						array(
							'id' => 'color_back',
							'type' => 'layout',
							'label' => __('Tile Color', 'builder-tiles'),
							'options' => apply_filters( 'builder_tile_colors', array(
								array('img' => THEMIFY_TILES_URI . 'assets/color-default.png', 'value' => 'default', 'label' => __('default', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-black.png', 'value' => 'black', 'label' => __('black', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-grey.png', 'value' => 'gray', 'label' => __('gray', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-blue.png', 'value' => 'blue', 'label' => __('blue', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-light-blue.png', 'value' => 'light-blue', 'label' => __('light-blue', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-green.png', 'value' => 'green', 'label' => __('green', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-light-green.png', 'value' => 'light-green', 'label' => __('light-green', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-purple.png', 'value' => 'purple', 'label' => __('purple', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-light-purple.png', 'value' => 'light-purple', 'label' => __('light-purple', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-brown.png', 'value' => 'brown', 'label' => __('brown', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-orange.png', 'value' => 'orange', 'label' => __('orange', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-yellow.png', 'value' => 'yellow', 'label' => __('yellow', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-red.png', 'value' => 'red', 'label' => __('red', 'builder-tiles')),
								array('img' => THEMIFY_TILES_URI . 'assets/color-pink.png', 'value' => 'pink', 'label' => __('pink', 'builder-tiles'))
							) ),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text tf-tile-options-gallery',
							'bottom' => true
						),
						array(
							'id' => 'title_back',
							'type' => 'text',
							'label' => __('Title', 'builder-tiles'),
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text'
						),
						/* Text Tile options */
						array(
							'id' => 'text_back',
							'type' => 'wp_editor',
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-text'
						),

						/* Button Tile options */
						array(
							'id' => 'button_link_back',
							'type' => 'text',
							'label' => __('Title Link', 'builder-tiles'),
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button'
						),
						array(
							'id' => 'button_link_params_back',
							'type' => 'checkbox',
							'label' => false,
							'pushed' => 'pushed',
							'options' => array(
								array( 'name' => 'lightbox', 'value' => __('Open link in lightbox', 'builder-tiles')),
								array( 'name' => 'newtab', 'value' => __('Open link in new tab', 'builder-tiles'))
							),
							'new_line' => false,
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button'
						),
						array(
							'id' => 'button_icon_back',
							'type' => 'group',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button',
							'fields' => array(
								array(
									'id' => 'icon_type_back',
									'type' => 'radio',
									'label' => __('Icon Type', 'builder-tiles'),
									'options' => array(
										'icon' => __('Icon', 'builder-tiles'),
										'image' => __('Image', 'builder-tiles'),
									),
									'default' => 'icon',
									'option_js' => true,
									'wrap_with_class' => 'tf-tile-options tf-tile-options-button'
								),
								array(
									'id' => 'icon_back',
									'type' => 'icon',
									'label' => __('Icon', 'builder-tiles'),
									'class' => 'small',
									'wrap_with_class' => 'tf-group-element tf-group-element-icon tf-tile-options tf-tile-options-button'
								),
								array(
									'id' => 'image_back',
									'type' => 'image',
									'label' => __('Image URL', 'builder-tiles'),
									'class' => 'xlarge',
									'wrap_with_class' => 'tf-group-element tf-group-element-image tf-tile-options tf-tile-options-button'
								),
								array(
									'id' => 'icon_color_back',
									'type' => 'text',
									'colorpicker' => true,
									'label' => __('Icon Color', 'builder-tiles'),
									'class' => 'small',
									'wrap_with_class' => 'tf-group-element tf-group-element-icon tf-tile-options tf-tile-options-button'
								),
							)
						),

						/* Gallery Tile options */
						array(
							'id' => 'gallery_back',
							'type' => 'textarea',
							'class' => 'fullwidth tf-shortcode-input',
							'label' => __('Gallery Slider', 'builder-tiles'),
							'help' => sprintf('<a href="#" class="builder_button tf-gallery-btn">%s</a>', __('Insert Gallery', 'builder-tiles')),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-gallery'
						),
						array(
							'id' => 'gallery_autoplay_back',
							'type' => 'select',
							'label' => __('Auto play', 'builder-tiles'),
							'options' => array(
								'off' => __( 'Off', 'builder-tiles' ),
								'1000' => __( '1 Second', 'builder-tiles' ),
								'2000' => __( '2 Seconds', 'builder-tiles' ),
								'3000' => __( '3 Seconds', 'builder-tiles' ),
								'4000' => __( '4 Seconds', 'builder-tiles' ),
								'5000' => __( '5 Seconds', 'builder-tiles' ),
								'6000' => __( '6 Seconds', 'builder-tiles' ),
								'7000' => __( '7 Seconds', 'builder-tiles' ),
								'8000' => __( '8 Seconds', 'builder-tiles' ),
								'9000' => __( '9 Seconds', 'builder-tiles' ),
								'10000' => __( '10 Seconds', 'builder-tiles' ),
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-gallery'
						),
						array(
							'id' => 'gallery_hide_timer_back',
							'type' => 'select',
							'label' => __('Hide Timer', 'builder-tiles'),
							'options' => array(
								'no' => __( 'No', 'builder-tiles' ),
								'yes' => __( 'Yes', 'builder-tiles' ),
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-gallery'
						),

						/* Map Tile options */
						array(
							'id' => 'address_map_back',
							'type' => 'textarea',
							'value' => '',
							'class' => 'fullwidth',
							'label' => __('Address', 'builder-tiles'),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),
						array(
							'id' => 'type_map_back',
							'type' => 'select',
							'label' => __('Type', 'builder-tiles'),
							'options' => array(
								'ROADMAP' => 'Road Map',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid',
								'TERRAIN' => 'Terrain'
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),
						array(
							'id' => 'zoom_map_back',
							'type' => 'select',
							'label' => __('Zoom', 'builder-tiles'),
							'options' => range( 1, 18 ),
							'default' => 8,
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),
						array(
							'id' => 'scrollwheel_map_back',
							'type' => 'select',
							'label' => __( 'Scrollwheel', 'builder-tiles' ),
							'options' => array(
								'enable' => __( 'Enable', 'builder-tiles' ),
								'disable' => __( 'Disable', 'builder-tiles' )
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),
						array(
							'id' => 'draggable_map_back',
							'type' => 'select',
							'label' => __( 'Draggable', 'builder-tiles' ),
							'options' => array(
								'enable' => __( 'Enable', 'builder-tiles' ),
								'disable' => __( 'Disable', 'builder-tiles' )
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-map'
						),

						array(
							'id' => 'action_text_back',
							'type' => 'text',
							'label' => __('Action Button Text', 'builder-tiles'),
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-text tf-tile-options-gallery'
						),
						array(
							'id' => 'action_link_back',
							'type' => 'text',
							'label' => __('Action Button Link', 'builder-tiles'),
							'class' => 'fullwidth',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-text tf-tile-options-gallery'
						),
						array(
							'id' => 'action_param_back',
							'type' => 'checkbox',
							'label' => false,
							'pushed' => 'pushed',
							'options' => array(
								array( 'name' => 'lightbox', 'value' => __('Open link in lightbox', 'builder-tiles')),
								array( 'name' => 'newtab', 'value' => __('Open link in new tab', 'builder-tiles'))
							),
							'new_line' => false,
							'wrap_with_class' => 'tf-tile-options tf-tile-options-text tf-tile-options-gallery'
						),
						array(
							'id' => 'tile_autoflip',
							'type' => 'select',
							'label' => __( 'Auto Flip', 'builder-tiles' ),
							'options' => array(
								'0' => __( 'Disabled', 'builder-tiles' ),
								'1' => __( '1 Second', 'builder-tiles' ),
								'2' => __( '2 Second', 'builder-tiles' ),
								'3' => __( '3 Second', 'builder-tiles' ),
								'4' => __( '4 Second', 'builder-tiles' ),
								'5' => __( '5 Second', 'builder-tiles' ),
								'6' => __( '6 Second', 'builder-tiles' ),
								'7' => __( '7 Second', 'builder-tiles' ),
								'8' => __( '8 Second', 'builder-tiles' ),
								'9' => __( '9 Second', 'builder-tiles' ),
								'10' => __( '10 Second', 'builder-tiles' ),
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text tf-tile-options-gallery tf-tile-options-map',
						),
						array(
							'id' => 'flip_effect',
							'type' => 'select',
							'label' => __( 'Flip Effect', 'builder-tiles' ),
							'options' => array(
								'flip-horizontal' => __( 'Horizontal Flip', 'builder-tiles' ),
								'flip-vertical' => __( 'Vertical Flip', 'builder-tiles' ),
								'fadeIn' => __( 'Fade In', 'builder-tiles' ),
								'fadeInUp' => __( 'fadeInUp', 'builder-tiles' ),
								'fadeInLeft' => __( 'fadeInLeft', 'builder-tiles' ),
								'fadeInRight' => __( 'fadeInRight', 'builder-tiles' ),
								'fadeInDown' => __( 'fadeInDown', 'builder-tiles' ),
								'zoomInUp' => __( 'zoomInUp', 'builder-tiles' ),
								'zoomInLeft' => __( 'zoomInLeft', 'builder-tiles' ),
								'zoomInRight' => __( 'zoomInRight', 'builder-tiles' ),
								'zoomInDown' => __( 'zoomInDown', 'builder-tiles' ),
							),
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text tf-tile-options-gallery tf-tile-options-map',
						),
						array(
							'id' => 'tile_back_style',
							'label' => __( 'Custom Style', 'builder-tiles' ),
							'type' => 'multi',
							'fields' => array(
								array(
									'id' => 'background_color_back',
									'type' => 'text',
									'colorpicker' => true,
									'class' => 'large',
									'label' => __('Background Color', 'builder-tiles'),
								),
								array(
									'id' => 'text_color_back',
									'type' => 'text',
									'colorpicker' => true,
									'class' => 'large',
									'label' => __('Text Color', 'builder-tiles'),
								),
								array(
									'id' => 'link_color_back',
									'type' => 'text',
									'colorpicker' => true,
									'class' => 'large',
									'label' => __('Link Color', 'builder-tiles'),
								),
							),
							'separated' => 'top',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text',
						),
						array(
							'id' => 'background_image_back',
							'type' => 'image',
							'label' => __('Background Image URL', 'builder-tiles'),
							'class' => 'xlarge',
							'wrap_with_class' => 'tf-tile-options tf-tile-options-button tf-tile-options-text',
						),
					)
				)
			)
		)
	),
) );