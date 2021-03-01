<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Template Pricing Table
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):
    $fields_default = array(
        'mod_appearance_pricing_table' => '',
        'mod_pricing_blank_button'=>'',
        'mod_color_pricing_table' => 'blue',
        'mod_enlarge_pricing_table' => '',
        'mod_title_pricing_table' => '',
        'mod_title_icon_pricing_table' => '',
        'mod_price_pricing_table' => '',
        'mod_recurring_fee_pricing_table' => '',
        'mod_description_pricing_table' => '',
        'mod_feature_list_pricing_table' => '',
        'mod_unavailable_feature_list_pricing_table' => '',
        'mod_button_text_pricing_table' => '',
        'mod_button_link_pricing_table' => '',
        'mod_pop_text_pricing_table' => '',
        'animation_effect' => '',
        'css_pricing_table' => ''
    );
    if (isset($mod_settings['mod_appearance_pricing_table'])) {
        $mod_settings['mod_appearance_pricing_table'] = self::get_checkbox_data($mod_settings['mod_appearance_pricing_table']);
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    if(!$fields_args['mod_color_pricing_table']){
        $fields_args['mod_color_pricing_table'] = 'blue';
    }
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);
    $container_class =apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, 'ui', $module_ID, $animation_effect, $fields_args['mod_appearance_pricing_table'], $fields_args['mod_color_pricing_table'], $fields_args['css_pricing_table']
        ), $mod_name, $module_ID, $fields_args);
    
    if ($fields_args['mod_enlarge_pricing_table'] === 'enlarge') {
        $container_class[] = 'pricing-enlarge';
    }
    if ($fields_args['mod_pop_text_pricing_table'] !== '') {
        $container_class[] = 'pricing-pop';
    }
    $feature_list = explode("\n", $fields_args['mod_feature_list_pricing_table']);
    $unavailable_feature_list = explode("\n", $fields_args['mod_unavailable_feature_list_pricing_table']);
    $container_class = implode(' ', $container_class);
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>

    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php do_action('themify_builder_before_template_content_render'); ?>

        <?php if ($fields_args['mod_pop_text_pricing_table'] !== ''): ?>
            <span class="module-pricing-table-pop"><?php echo $fields_args['mod_pop_text_pricing_table']; ?></span>
        <?php endif; ?>

        <div class="module-pricing-table-header ui <?php echo $fields_args['mod_color_pricing_table'], ' ', $fields_args['mod_appearance_pricing_table']; ?>" >
            <?php if ($fields_args['mod_title_pricing_table'] !== ''): ?>
                <span class="module-pricing-table-title">
                    <?php if ($fields_args['mod_title_icon_pricing_table'] !== ''): ?>
                        <i class="fa <?php echo $fields_args['mod_title_icon_pricing_table']; ?>"></i>
                    <?php endif; ?>
                    <span ><?php echo $fields_args['mod_title_pricing_table']; ?></span>
                </span>
            <?php endif; ?>
            <?php if ($fields_args['mod_price_pricing_table'] !== ''): ?>
                <span class="module-pricing-table-price"><?php echo $fields_args['mod_price_pricing_table']; ?></span>
            <?php endif; ?>
            <?php if ($fields_args['mod_recurring_fee_pricing_table'] !== ''): ?>
                <p class="module-pricing-table-reccuring-fee"><?php echo $fields_args['mod_recurring_fee_pricing_table']; ?></p>
            <?php endif; ?>
            <?php if ($fields_args['mod_description_pricing_table'] !== ''): ?>
                <p class="module-pricing-table-description"><?php echo $fields_args['mod_description_pricing_table']; ?></p>
            <?php endif; ?>
        </div><!-- .module-pricing-table-header -->
        <div class="module-pricing-table-content">
            <?php if (!empty($feature_list)): ?>
                <?php foreach ($feature_list as $line): ?>
                    <p class="module-pricing-table-features"><?php echo $line; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($unavailable_feature_list)): ?>
                <?php foreach ($unavailable_feature_list as $line): ?>
                    <p class="module-pricing-table-features unavailable-features"><?php echo $line; ?></p>
                <?php endforeach ?>
            <?php endif; ?>
            <?php if ($fields_args['mod_button_text_pricing_table'] !== ''): ?>
                <?php
                $class = array();
                if ($fields_args['mod_pricing_blank_button'] === 'modal') {
                    $class[] = 'lightbox-builder themify_lightbox';
                }
                if ($fields_args['mod_color_pricing_table'] !== '') {
                    $class[] = $fields_args['mod_color_pricing_table'];
                }
                if ($fields_args['mod_appearance_pricing_table']) {
                    $class[] = $fields_args['mod_appearance_pricing_table'];
                }
                ?>
                <a class="module-pricing-table-button ui <?php echo implode(' ', $class) ?>" href="<?php echo $fields_args['mod_button_link_pricing_table'] !== '' ? $fields_args['mod_button_link_pricing_table'] : '#' ?>"<?php if ($fields_args['mod_pricing_blank_button'] === 'external'): ?> target="_blank"<?php endif; ?>>
                    <?php echo $fields_args['mod_button_text_pricing_table']; ?> 
                </a> 
            <?php endif; ?>
        </div><!-- .module-pricing-table-content -->
        <?php do_action('themify_builder_after_template_content_render'); ?>
    </div><!-- /module pricing-table -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>