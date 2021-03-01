<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Image Pro
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):
    $fields_default = array(
        'mod_title_image' => '',
        'style_image' => '',
        'url_image' => '',
        'appearance_image' => '',
        'appearance_image2' => '',
        'image_size_image' => '',
        'width_image' => '',
        'height_image' => '',
        'title_image' => '',
        'param_image' => array(),
        'caption_image' => '',
        'css_image' => '',
        'image_effect' => '',
        'image_filter' => '',
        'image_alignment' => '',
        'overlay_color' => '',
        'overlay_image' => '',
        'overlay_effect' => 'fadeIn',
        'action_button' => '',
        'color_button' => '',
        'link_type' => 'external',
        'link_new_window' => 'no',
        'link_address' => '',
        'content_modal' => '',
        'lightbox_width' => '',
        'lightbox_height' => '',
        'lightbox_size_unit_width' => 'pixels',
        'lightbox_size_unit_height' => 'pixels',
        'link_image_type' => 'image_external',
        'link_image' => '',
        'link_image_new_window' => 'no',
        'image_content_modal' => '',
        'animation_effect' => ''
    );

    if (isset($mod_settings['appearance_image'])) {
        $mod_settings['appearance_image'] = self::get_checkbox_data($mod_settings['appearance_image']);
    }
    if (isset($mod_settings['appearance_image2'])) {
        $mod_settings['appearance_image2'] = self::get_checkbox_data($mod_settings['appearance_image2']);
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);
    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, 'filter-' . $fields_args['image_filter'], 'effect-' . $fields_args['image_effect'], $fields_args['appearance_image'], $fields_args['appearance_image2'], $fields_args['image_alignment'], $fields_args['style_image'], $fields_args['css_image'], $animation_effect, 'entrance-effect-' . $fields_args['overlay_effect']
                    ), $mod_name, $module_ID, $fields_args)
    );
    $title_image = $fields_args['title_image'];
    $image_alt = '' !== $title_image ? $title_image : wp_strip_all_tags($fields_args['caption_image']);

    $lightbox_size_unit_width = $fields_args['lightbox_size_unit_width'] === 'pixels' ? 'px' : '%';
    $lightbox_size_unit_height = $fields_args['lightbox_size_unit_height'] === 'pixels' ? 'px' : '%';

    $lightbox_data = $fields_args['link_image_type'] !== 'image_external' && (!empty($fields_args['lightbox_width']) || !empty($fields_args['lightbox_height']) ) ? sprintf(' data-zoom-config="%s|%s"'
                    , $fields_args['lightbox_width'] . $lightbox_size_unit_width, $fields_args['lightbox_height'] . $lightbox_size_unit_height) : false;


    if (Themify_Builder_Model::is_img_php_disabled()) {
        // get image preset
        global $_wp_additional_image_sizes;
        $preset = $fields_args['image_size_image'] !== '' ? $fields_args['image_size_image'] : themify_builder_get('setting-global_feature_size', 'image_global_size_field');
        if (isset($_wp_additional_image_sizes[$preset]) && $fields_args['image_size_image'] !== '') {
            $width_image = (int) $_wp_additional_image_sizes[$preset]['width'];
            $height_image = (int) $_wp_additional_image_sizes[$preset]['height'];
        } else {
            $width_image = $fields_args['width_image'] !== '' ? $fields_args['width_image'] : get_option($preset . '_size_w');
            $height_image = $fields_args['height_image'] !== '' ? $fields_args['height_image'] : get_option($preset . '_size_h');
        }
        $image = '<img src="' . esc_url($fields_args['url_image']) . '" alt="' . esc_attr($image_alt) . '" title="' . esc_attr( $title_image ) . '"  width="' . esc_attr($width_image) . '" height="' . esc_attr($height_image) . '"/>';
    } else {
        $image = themify_get_image('src=' . esc_url($fields_args['url_image']) . '&w=' . $fields_args['width_image'] . '&h=' . $fields_args['height_image'] . '&title=' . esc_attr( $title_image ) . '&ignore=true');
    }

    $out_effect = array(
        'none' => '',
        'partial-overlay' => '',
        'flip-horizontal' => '',
        'flip-vertical' => '',
        'fadeInUp' => 'fadeOutDown',
        'fadeIn' => 'fadeOut',
        'fadeInLeft' => 'fadeOutLeft',
        'fadeInRight' => 'fadeOutRight',
        'fadeInDown' => 'fadeOutUp',
        'zoomInUp' => 'zoomOutDown',
        'zoomInLeft' => 'zoomOutLeft',
        'zoomInRight' => 'zoomOutRight',
        'zoomInDown' => 'zoomOutUp',
    );

    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module image pro -->
    <div <?php echo self::get_element_attributes($container_props); ?> data-entrance-effect="<?php echo $fields_args['overlay_effect']; ?>" data-exit-effect="<?php echo $out_effect[$fields_args['overlay_effect']]; ?>">
        <!--insert-->
        <?php if ($fields_args['mod_title_image'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_image'], $fields_args) . $fields_args['after_title']; ?>
        <?php endif; ?>

        <?php do_action('themify_builder_before_template_content_render'); ?>

        <div class="image-pro-wrap">
            <?php if (!empty($fields_args['link_image']) || 'image_modal' === $fields_args['link_image_type']): ?>
                <a class="image-pro-external<?php if ($fields_args['link_image_type'] !== 'image_external'): ?> themify_lightbox<?php endif; ?>" href="<?php echo 'image_modal' !== $fields_args['link_image_type'] ? esc_url($fields_args['link_image']) : '#modal-image-' . $module_ID ?>" <?php if ($fields_args['link_image_new_window'] === 'yes') : ?>target="_blank"<?php
                endif;
                echo $lightbox_data;
                ?>></a>
               <?php endif; ?>
            <div class="image-pro-flip-box-wrap">
                <div class="image-pro-flip-box">

                    <?php echo $image; ?>

                    <div class="image-pro-overlay <?php echo ( 'none' === $fields_args['overlay_effect'] ) ? 'none' : ''; ?>" style="visibility: hidden">

                        <?php if ($fields_args['overlay_color'] !== '') : ?>
                            <div class="image-pro-color-overlay" style="background-color: <?php echo Themify_Builder_Stylesheet::get_rgba_color($fields_args['overlay_color']); ?>"></div>
                        <?php endif; ?>

                        <div class="image-pro-overlay-inner">

                            <?php if ($title_image !== '') : ?>
                                <h4 class="image-pro-title"><?php echo $title_image; ?></h4>
                            <?php endif; ?>

                            <?php if ($fields_args['caption_image'] !== '') : ?>
                                <div class="image-pro-caption"><?php echo $fields_args['caption_image']; ?></div>
                            <?php endif; ?>

                            <?php if ($fields_args['action_button'] !== '') : ?>
                                <a class="ui builder_button image-pro-action-button <?php echo $fields_args['color_button']; ?> <?php if ($fields_args['link_type'] === 'lightbox_link' || $fields_args['link_type'] === 'modal') echo 'themify_lightbox' ?>" href="<?php
                                if ($fields_args['link_type'] === 'modal') {
                                    echo '#modal-' . $module_ID;
                                } else {
                                    echo $fields_args['link_address'];
                                }
                                ?>" <?php if ($fields_args['link_new_window'] === 'yes') : ?>target="_blank"<?php
                                   endif;
                                   echo $lightbox_data;
                                   ?>>
                                       <?php echo $fields_args['action_button']; ?>
                                </a>
                            <?php endif; ?>

                        </div>
                    </div><!-- .image-pro-overlay -->

                </div>
            </div>

        </div><!-- .image-pro-wrap -->

        <?php if ('modal' === $fields_args['link_type']) : ?>
            <div id="modal-<?php echo $module_ID ?>" class="mfp-hide">
                <?php echo apply_filters('themify_builder_module_content', $fields_args['content_modal']); ?>
            </div>
        <?php endif; ?>
        <?php if ('image_modal' === $fields_args['link_image_type']) : ?>
            <div id="modal-image-<?php echo $module_ID ?>" class="mfp-hide">
                <?php echo apply_filters('themify_builder_module_content', $fields_args['image_content_modal']); ?>
            </div>
        <?php endif; ?>

        <?php do_action('themify_builder_after_template_content_render'); ?>
        <?php if ($fields_args['overlay_image'] !== ''):?>
            <style type="text/css">
                    #<?php  echo  $module_ID ?> .image-pro-overlay { background-image: url(<?php echo $fields_args['overlay_image']?>); } ?>
            </style>
        <?php endif;?>
    </div>
    <!-- /module image pro -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>