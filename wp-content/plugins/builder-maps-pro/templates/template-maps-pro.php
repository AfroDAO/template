<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Maps Pro
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):
    $fields_default = array(
        'mod_title' => '',
        'map_link' => '',
        'map_center' => '',
        'zoom_map' => 4,
        'w_map' => '100', 
        'unit_w' => 'px',
        'h_map' => '', 
        'type_map' => 'ROADMAP',
        'scrollwheel_map' => 'enable',
        'draggable_map' => 'enable',
		'disable_map_ui' => 'no',
		'map_polyline' => 'no',
		'map_polyline_geodesic' => 'yes',
		'map_polyline_stroke' => 2,
		'map_polyline_color' => 'ff0000_1',
        'markers' => array(),
        'map_display_type' => 'dynamic',
        'w_map_static' => 500,
        'animation_effect' => '',
        'style_map' => '',
        'css_class' => '',
    );
    $marker_defaults = array(
        'title' => '', 
        'address' => '',
        'image' => ''
	);
	
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);
    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['css_class'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    if ('' !== $fields_args['style_map'] && $fields_args['map_display_type'] === 'dynamic') {
        echo '
	<script type="text/javascript" defer>
		map_pro_styles = window.map_pro_styles || [];
		map_pro_styles["' . $fields_args['style_map']  . '"] = ' . json_encode(Builder_Maps_Pro::get_instance()->get_map_style($fields_args['style_map']  )) . ';
	</script>';
	}

    $map_options = array(
        'zoom' =>$fields_args['zoom_map'],
        'type' => $fields_args['type_map'],
        'address' => $fields_args['map_center'],
        'width' => $fields_args['w_map'],
        'height' => $fields_args['h_map'],
        'style_map' => $fields_args['style_map'] ,
        'scrollwheel' => $fields_args['scrollwheel_map'],
        'draggable' => ( 'enable' ===$fields_args['draggable_map']  || ( 'desktop_only' === $fields_args['draggable_map'] && !themify_is_touch() ) ) ? 'enable' : 'disable',
		'disable_map_ui' => $fields_args['disable_map_ui'],
		'polyline' => $fields_args['map_polyline'],
		'geodesic' => $fields_args['map_polyline_geodesic'],
		'polylineStroke' => $fields_args['map_polyline_stroke'],
		'polylineColor' => $fields_args['map_polyline_color']
    );

    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module maps pro -->
    <div <?php echo self::get_element_attributes($container_props); ?> data-config='<?php  esc_attr_e(base64_encode(json_encode($map_options))); ?>'>
        <?php unset($map_options);?>
        <?php if ($fields_args['mod_title'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>

        <?php do_action('themify_builder_before_template_content_render'); ?>

        <?php if ($fields_args['map_display_type'] === 'dynamic') : ?>

            <div class="maps-pro-canvas-container">
                <div class="maps-pro-canvas map-container" style="width: <?php echo $fields_args['w_map'] .$fields_args['unit_w'] ; ?>; height: <?php echo $fields_args['h_map']?>px;">
                </div>
            </div>

            <div class="maps-pro-markers" style="display: none;">

                <?php
                foreach ($fields_args['markers'] as $marker) :
                    $marker = wp_parse_args($marker, $marker_defaults);
                    ?>
                    <div class="maps-pro-marker" data-address="<?php echo !empty($marker['latlng']) ? $marker['latlng'] : $marker['address'] ?>" data-image="<?php echo $marker['image']; ?>">
                        <?php echo TB_Maps_Pro_Module::sanitize_text($marker['title']); ?>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php
        else :

            $args = '';
            if (!empty($fields_args['map_center'] )) {
                $args .= 'center=' . $fields_args['map_center'];
            }
            $args .= '&zoom=' .$fields_args['zoom_map'] ;
            $args .= '&maptype=' . strtolower($fields_args['type_map']);
            $args .= '&size=' . preg_replace('/[^0-9]/', '',$fields_args['w_map_static'] ) . 'x' . preg_replace('/[^0-9]/', '', $fields_args['h_map']);
            $args .= '&key=' . Themify_Builder_Model::getMapKey();

            /* markers */
            if (!empty($markers)) {
               foreach ($fields_args['markers'] as $marker){
                    $marker = wp_parse_args($marker, $marker_defaults);
                    if (empty($marker['image'])) {
                        $args .= '&markers=' . urlencode($marker['address']);
                    } else {
                        $args .= '&markers=icon:' . urlencode($marker['image']) . '%7C' . urlencode($marker['address']);
                    }
                }
            }

            /* Map style */
            if ('' !== $fields_args['style_map']) {
                $style = Builder_Maps_Pro::get_instance()->get_map_style($style_map);
                foreach ($style as $rule) {
                    $args .= '&style=';
                    if (isset($rule->featureType)) {
                        $args .= 'feature:' . $rule->featureType . '%7C';
                    }
                    if (isset($rule->elementType)) {
                        $args .= 'element:' . $rule->featureType . '%7C';
                    }
                    if (isset($rule->stylers)) {
                        foreach ($rule->stylers as $styler) {
                            foreach ($styler as $prop => $value) {
                                $value = str_replace('#', '0x', $value);
                                $args .= $prop . ':' . $value . '%7C';
                            }
                        }
                    }
                }
            }

            if ('gmaps' ===$fields_args['map_link']  && !empty($fields_args['map_center']))
                echo '<a href="http://maps.google.com/?q=' . esc_attr($fields_args['map_center']) . '" target="_blank" rel="nofollow" title="Google Maps"/>';
            ?>
                <img src="//maps.googleapis.com/maps/api/staticmap?<?php echo $args; ?>" />
            <?php
            if ('gmaps' === $fields_args['map_link']  && !empty($fields_args['map_center'] ))
                echo '</a>';
            ?>

    <?php endif; ?>

    <?php do_action('themify_builder_after_template_content_render'); ?>
    </div>
    <!-- /module maps pro -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>