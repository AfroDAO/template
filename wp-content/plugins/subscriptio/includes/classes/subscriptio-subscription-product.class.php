<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to subscription products
 *
 * @class Subscriptio_Subscription_Product
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Subscription_Product')) {

class Subscriptio_Subscription_Product
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        add_filter('product_type_options', array($this, 'wc_hook_product_type_options'));
        add_action('woocommerce_product_options_general_product_data', array($this, 'wc_hook_woocommerce_product_options_general_product_data'));
        add_action('woocommerce_variation_options', array($this, 'wc_hook_woocommerce_variation_options'), 10, 3);
        add_action('woocommerce_product_after_variable_attributes', array($this, 'wc_hook_woocommerce_product_after_variable_attributes'), 10, 3);
        add_action('woocommerce_process_product_meta_simple', array($this, 'wc_hook_woocommerce_process_product_meta'));
        add_action('woocommerce_process_product_meta_variable', array($this, 'wc_hook_woocommerce_process_product_meta_variable'));
        add_action('woocommerce_ajax_save_product_variations', array($this, 'wc_hook_woocommerce_process_product_meta_variable'));
        add_filter('manage_edit-product_columns', array($this, 'wc_hook_manage_edit_product_columns'), 99);
        add_action('manage_product_posts_custom_column', array($this, 'wc_hook_manage_product_posts_custom_column'), 99);
        add_filter('woocommerce_is_purchasable', array($this, 'is_product_purchasable'), 99, 2);
        add_filter('woocommerce_add_to_cart_validation', array($this, 'add_trial_notice'), 99, 3);
        add_filter('woocommerce_cart_needs_payment', array($this, 'maybe_activate_gateways'), 10, 2);

        // Change product prices
        add_filter('woocommerce_get_price_html', array($this, 'change_product_price'), 10, 2);
        add_filter('woocommerce_get_variation_price_html', array($this, 'change_product_price'), 10, 2);
        add_filter('woocommerce_grouped_price_html', array($this, 'change_product_price'), 10, 2);

        // Hook to WordPress 'init' action
        add_action('init', array($this, 'on_init'));
    }

    /**
     * Executred on init action
     *
     * @access public
     * @return void
     */
    public function on_init()
    {
        // Maybe change Add To Cart button text
        if (Subscriptio::option('add_to_cart')) {
            add_filter('woocommerce_product_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_variable_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_grouped_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_external_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_out_of_stock_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
        }
    }

    /**
     * Add simple product property checkbox (checkbox that converts simple product to simple subscription product)
     *
     * @access public
     * @param array $checkboxes
     * @return array
     */
    public function wc_hook_product_type_options($checkboxes)
    {
        $checkboxes['subscriptio'] = array(
            'id'            => '_subscriptio',
            'wrapper_class' => 'show_if_simple',
            'label'         => __('Subscription', 'subscriptio'),
            'description'   => __('Sell this product as a subscription product with recurring billing.', 'subscriptio'),
            'default'       => 'no'
        );

        return $checkboxes;
    }

    /**
     * Display subscription settings fields on product page (simple product)
     *
     * @access public
     * @return void
     */
    public function wc_hook_woocommerce_product_options_general_product_data()
    {
        // Get post
        global $post;
        $post_id = $post->ID;

        // Load product object
        $product = wc_get_product($post_id);

        // Retrieve required post meta fields
        $_subscriptio_price_time_value      = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_price_time_value', true);
        $_subscriptio_price_time_unit       = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_price_time_unit', true);
        $_subscriptio_free_trial_time_value = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_free_trial_time_value', true);
        $_subscriptio_free_trial_time_unit  = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_free_trial_time_unit', true);
        $_subscriptio_signup_fee            = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_signup_fee', true);
        $_subscriptio_max_length_time_value = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_max_length_time_value', true);
        $_subscriptio_max_length_time_unit  = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_max_length_time_unit', true);

        require SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/product/simple-product-meta.php';
    }

    /**
     * Display subscription settings fields on product page (variation product)
     *
     * @access public
     * @param int $loop
     * @param array $variation_data
     * @param object $variation
     * @return void
     */
    public function wc_hook_woocommerce_product_after_variable_attributes($loop, $variation_data, $variation)
    {
        // Load product object
        $product = wc_get_product($variation->ID);

        // Retrieve required post meta fields
        $_subscriptio_price_time_value      = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_price_time_value', true);
        $_subscriptio_price_time_unit       = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_price_time_unit', true);
        $_subscriptio_free_trial_time_value = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_free_trial_time_value', true);
        $_subscriptio_free_trial_time_unit  = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_free_trial_time_unit', true);
        $_subscriptio_signup_fee            = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_signup_fee', true);
        $_subscriptio_max_length_time_value = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_max_length_time_value', true);
        $_subscriptio_max_length_time_unit  = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_max_length_time_unit', true);

        // Include view file
        require SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/product/variable-product-meta.php';
    }

    /**
     * Add variable product property checkbox (checkbox that converts variable product to variable subscription product)
     *
     * @access public
     * @param int $loop
     * @param array $variation_data
     * @param object $variation
     * @return void
     */
    public function wc_hook_woocommerce_variation_options($loop, $variation_data, $variation)
    {
        // Get tip text
        $tip = __('Sell this variable product as a subscription product with recurring billing.', 'subscriptio');

        // Format tip
        if (RightPress_Helper::wc_version_gte('2.5')) {
            $tip = '<span class="woocommerce-help-tip" data-tip="' . $tip . '"></span>';
        }
        else {
            $tip = '<a class="tips" data-tip="' . $tip . '" href="#">[?]</a>';
        }

        echo '<label><input type="checkbox" class="checkbox _subscriptio_variable" name="_subscriptio[' . $loop . ']" ' . checked(self::is_subscription($variation->ID /* WC31 */), true, false) . ' /> ' . __('Subscription', 'subscriptio') . ' ' . $tip . '</label>';
    }

    /**
     * Save simple product subscription-related meta data
     *
     * @access public
     * @param int $post_id
     * @param bool $variable
     * @return void
     */
    public function wc_hook_woocommerce_process_product_meta($post_id, $variable = false, $loop = null)
    {
        if ($variable) {
            if ((empty($_POST['_subscriptio']) || !isset($_POST['_subscriptio'][$loop])) && !isset($_POST['_subscriptio_price_time_unit'][$loop])) {
                return array('result' => false);
            }
            $_subscriptio = $_POST['_subscriptio'][$loop];
        }
        else if (!empty($_POST['_subscriptio'])) {
            $_subscriptio = $_POST['_subscriptio'];
        }

        // Load product object
        $product = wc_get_product($post_id);

        // Not a subscription?
        if (!isset($_subscriptio) || $_subscriptio != 'on') {
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_price_time_value');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_price_time_unit');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_free_trial_time_value');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_free_trial_time_unit');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_max_length_time_value');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_max_length_time_unit');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_signup_fee');
            return;
        }

        // Mark this product as subscription
        RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio', 'yes');

        // Get time units
        $time_units = Subscriptio::get_time_units();

        // Price period value (how many periods of time (by time unit) the price covers, e.g. price can be for 3 months)
        $_subscriptio_price_time_value = $variable ? $_POST['_subscriptio_price_time_value'][$loop] : $_POST['_subscriptio_price_time_value'];
        $price_period_value = Subscriptio::get_natural_number($_subscriptio_price_time_value);
        RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio_price_time_value', $price_period_value);

        // Price time unit (e.g. a month)
        $_subscriptio_price_time_unit = $variable ? $_POST['_subscriptio_price_time_unit'][$loop] : $_POST['_subscriptio_price_time_unit'];
        $price_time_unit = isset($time_units[$_subscriptio_price_time_unit]) ? $_subscriptio_price_time_unit : array_shift(array_keys($time_units));
        RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio_price_time_unit', $price_time_unit);

        // Get correct trial period field value
        $_subscriptio_free_trial_time_value = $variable ? $_POST['_subscriptio_free_trial_time_value'][$loop] : $_POST['_subscriptio_free_trial_time_value'];

        // Trial period
        if (isset($_subscriptio_free_trial_time_value) && !empty($_subscriptio_free_trial_time_value)) {

            // Free trial period value (how many periods of time (by time unit) the trial period covers, e.g. trial period can be 2 weeks)
            $trial_period_value = Subscriptio::get_natural_number($_subscriptio_free_trial_time_value);
            RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio_free_trial_time_value', $trial_period_value);

            // Free trial time unit
            $_subscriptio_free_trial_time_unit = $variable ? $_POST['_subscriptio_free_trial_time_unit'][$loop] : $_POST['_subscriptio_free_trial_time_unit'];
            $trial_time_unit = isset($time_units[$_subscriptio_free_trial_time_unit]) ? $_subscriptio_free_trial_time_unit : array_shift(array_keys($time_units));
            RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio_free_trial_time_unit', $trial_time_unit);
        }
        else {
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_free_trial_time_value');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_free_trial_time_unit');
        }

        // Get correct maximum length period value
        $_subscriptio_max_length_time_value = $variable ? $_POST['_subscriptio_max_length_time_value'][$loop] : $_POST['_subscriptio_max_length_time_value'];

        // Max length period
        if (isset($_subscriptio_max_length_time_value) && !empty($_subscriptio_max_length_time_value)) {

            // Max subscription length period value (e.g. up to 12 months)
            $length_period_value = Subscriptio::get_natural_number($_subscriptio_max_length_time_value);
            RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio_max_length_time_value', $length_period_value);

            // Max subscription length time unit
            $_subscriptio_max_length_time_unit = $variable ? $_POST['_subscriptio_max_length_time_unit'][$loop] : $_POST['_subscriptio_max_length_time_unit'];
            $length_time_unit = isset($time_units[$_subscriptio_max_length_time_unit]) ? $_subscriptio_max_length_time_unit : array_shift(array_keys($time_units));
            RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio_max_length_time_unit', $length_time_unit);
        }
        else {
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_max_length_time_value');
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_max_length_time_unit');
        }

        // Get correct signup fee field value
        $_subscriptio_signup_fee = $variable ? $_POST['_subscriptio_signup_fee'][$loop] : $_POST['_subscriptio_signup_fee'];

        // Signup fee
        if (isset($_subscriptio_signup_fee) && !empty($_subscriptio_signup_fee)) {
            $signup_fee = Subscriptio::get_float_number_as_string($_subscriptio_signup_fee);

            if ($signup_fee != 0) {
                RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio_signup_fee', $signup_fee);
            }
            else {
                RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_signup_fee');
            }
        }
        else {
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio_signup_fee');
        }

        return !$variable ? null : array('result' => true);
    }

    /**
     * Save variable product subscription-related meta data
     *
     * @access public
     * @param int $post_id
     * @return void
     */
    public function wc_hook_woocommerce_process_product_meta_variable($post_id)
    {
        // Check if post id is set
        if (!isset($_POST['variable_post_id'])) {
            return;
        }

        // Load parent product object
        $product = wc_get_product($post_id);

        // Find max post id
        $all_ids = $_POST['variable_post_id'];
        $max_id = max(array_keys($all_ids));

        $variable_product_has_subscriptions = false;

        // Iterate over all variations and save them
        for ($i = 0; $i <= $max_id; $i++) {

            // Skip non-existing keys
            if (!isset($all_ids[$i])) {
                continue;
            }

            // Get post ID for current variable product
            $variable_post_id = (int) $all_ids[$i];

            // Handle as simple product
            $result = $this->wc_hook_woocommerce_process_product_meta($variable_post_id, true, $i);

            if ($result['result']) {
                $variable_product_has_subscriptions = true;
            }
        }

        if ($variable_product_has_subscriptions) {
            RightPress_WC_Legacy::product_update_meta_data($product, '_subscriptio', 'yes');
        }
        else {
            RightPress_WC_Legacy::product_delete_meta_data($product, '_subscriptio');
        }
    }

    /**
     * Insert custom column into product list view header
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function wc_hook_manage_edit_product_columns($columns)
    {
        // Check if array format is as expected
        if (!is_array($columns) || !isset($columns['product_type'])) {
            return $columns;
        }

        // Insert new column after column product_type
        $offset = array_search('product_type', array_keys($columns)) + 1;

        return array_merge (
                array_slice($columns, 0, $offset),
                array('subscriptio' => '<span class="subscriptio_product_list_header_icon tips" data-tip="' . __('Subscription', 'subscriptio') . '">' . __('Subscription', 'subscriptio') . '</span>'),
                array_slice($columns, $offset, null)
            );
    }

    /**
     * Insert custom column into product list view
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function wc_hook_manage_product_posts_custom_column($column)
    {
        global $post, $woocommerce, $the_product;

        if (empty($the_product) || RightPress_WC_Legacy::product_get_id($the_product) != $post->ID /* WC31 */) {
            $the_product = get_product($post);
        }

        if ($column == 'subscriptio') {
            if (self::is_subscription($the_product)) {
                $tip = RightPress_WC_Legacy::product_get_type($the_product) == 'simple' ? __('This product is a subscription', 'subscriptio') : __('Contains at least one subscription', 'subscriptio');
                echo '<i class="fa fa-repeat subscriptio_product_list_icon tips" data-tip="' . $tip . '"></i>';
            }
        }
    }

    /**
     * Check if Subscriptio settings for specific product seems to be ok
     *
     * @access public
     * @param mixed $product
     * @return bool
     */
    public static function is_ok($product)
    {
        // Load product object if needed
        if (!is_object($product)) {
            $product = wc_get_product($product);
        }

        // Check if this product is a subscription
        if (!Subscriptio_Subscription_Product::is_subscription($product)) {
            return false;
        }

        // Get time units
        $time_units = Subscriptio::get_time_units();

        // Get price time unit and value
        $price_time_unit = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_price_time_unit', true);
        $price_time_value = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_price_time_value', true);

        // Make sure the payment interval looks ok
        if (!empty($price_time_value) && !empty($price_time_unit)) {
            if (!isset($time_units[$price_time_unit])) {
                return false;
            }
            if (!is_numeric($price_time_value)) {
                return false;
            }
        }
        else {
            return false;
        }

        return true;
    }

    /**
     * Check if regular product is a subscription or if variable product contains subscription variations
     *
     * @access public
     * @param mixed $product
     * @return bool
     */
    public static function is_subscription($product)
    {
        // Load product object if needed
        if (!is_object($product)) {
            $product = wc_get_product($product);
        }

        // Check if this is WooCommerce product
        if (!is_a($product, 'WC_Product')) {
            return false;
        }

        // Check for flag in meta
        if (RightPress_WC_Legacy::product_get_meta($product, '_subscriptio')) {
            return true;
        }

        // Check children
        foreach ($product->get_children() as $child_id) {

            // Load child product object
            $child_product = wc_get_product($child_id);

            // Check for flag in meta
            if (RightPress_WC_Legacy::product_get_meta($child_product, '_subscriptio')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format recurring amount
     *
     * @access public
     * @param float $amount
     * @param string $unit
     * @param int $value
     * @param string $currency
     * @param bool $sale_price
     * @param bool $display_price_suffix
     * @return string|bool
     */
    public static function format_recurring_amount($amount, $unit, $value, $currency = null, $sale_price = false, $display_price_suffix = true)
    {
        // Get time units
        $time_units = Subscriptio::get_time_units();

        if (!isset($time_units[$unit]) || !is_numeric($value)) {
            return false;
        }

        // Get store currency if none passed in
        if (!$currency) {
            $currency = get_woocommerce_currency();
        }

        // Hide striked-through regular price in case product is on sale when regular price is used as recurring price
        if (Subscriptio::option('sale_price_handling') === 'initial_order') {
            $sale_price = false;
        }

        // Format price with currency
        $formatted_amount = Subscriptio::get_formatted_price($amount, $currency, $sale_price, $display_price_suffix);

        // Possibly pluralize unit name first
        $formatted_unit_name = call_user_func($time_units[$unit]['translation_callback'], $unit, $value);

        // Format and return
        if ($value == 1) {
            return apply_filters('subscriptio_formatted_recurring_amount', sprintf(__('%1$s / %2$s', 'subscriptio'), $formatted_amount, $formatted_unit_name));
        }
        else {
            return apply_filters('subscriptio_formatted_recurring_amount', sprintf(__('%1$s / %2$s %3$s', 'subscriptio'), $formatted_amount, $value, $formatted_unit_name));
        }
    }

    /**
     * Change frontend product price with Subscription price
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return string
     */
    public function change_product_price($price, $product)
    {
        // Product is subscription?
        if (!is_admin() && self::is_subscription($product)) {

            // Variable or grouped product? Then we need to check if all prices are equal or choose cheapest variation and display "From: ..."
            if (in_array(RightPress_WC_Legacy::product_get_type($product), array('variable', 'grouped'))) {
                list($id, $equal) = self::get_cheapest_child_price($product);
                return Subscriptio_Subscription_Product::get_formatted_subscription_price($id, false, false, 1, null, true, $equal);
            }

            // Double check that particular variation is a subscription
            if (RightPress_WC_Legacy::product_get_type($product) != 'variation' || self::is_subscription($product)) {
                return Subscriptio_Subscription_Product::get_formatted_subscription_price(RightPress_WC_Legacy::product_get_id($product), false, false);
            }
        }

        return $price;
    }

    /**
     * Check if variation or grouped product prices are all equal and return cheapest price
     *
     * @access public
     * @param object $product
     * @return array
     */
    public static function get_cheapest_child_price($product)
    {
        $ids = array();

        // Grouped product?
        if (RightPress_WC_Legacy::product_get_type($product) == 'grouped') {

            // Iterate over children
            foreach ($product->get_children() as $child_id) {
                $ids[] = $child_id;
            }
        }

        // Variable product
        else {
            $variations = $product->get_available_variations();

            foreach ($variations as $variation) {
                $ids[] = $variation['variation_id'];    // WC31: check if this is still correct
            }
        }

        if (empty($ids)) {
            return array(0, true);
        }

        $selected_id = null;
        $cheapest = null;
        $equal = true;

        // If selected method is cheapest price in absolute
        if (Subscriptio::option('cheapest_price_method') == 1) {
            $absolute_prices = array();
        }

        foreach ($ids as $id) {

            // We skip non-subscription variations as there is no easy way to compare recurring payments with one-off payments
            if (!self::is_subscription($id)) {
                $equal = false;
                continue;
            }

            $child_product = RightPress_Helper::wc_get_product($id);

            // Collect absolute prices if needed
            if (isset($absolute_prices)) {
                $absolute_prices[$id] = $child_product->get_price();
                continue;
            }

            $child_days = Subscriptio_Subscription::get_period_length_in('day', RightPress_WC_Legacy::product_get_meta($child_product, '_subscriptio_price_time_unit', true), RightPress_WC_Legacy::product_get_meta($child_product, '_subscriptio_price_time_value', true));
            $price_per_day = $child_days == 0 ? 0 : $child_product->get_price() / $child_days;

            if ($cheapest === null) {
                $selected_id = $id;
                $cheapest = $price_per_day;
            }
            else if ($price_per_day < $cheapest) {
                $selected_id = $id;
                $cheapest = $price_per_day;
                $equal = false;
            }
            else if ($price_per_day > $cheapest) {
                $equal = false;
            }
        }

        // Return minimal absolute price if needed
        if (!empty($absolute_prices)) {
            return array(array_shift(array_keys($absolute_prices, min($absolute_prices))), false);
        }

        return array($selected_id, $equal);
    }

    /**
     * Check if cart item price needs to be changed and return new price if it does
     *
     * @access public
     * @param int $id
     * @param float $price
     * @return float|bool
     */
    public static function get_new_price($id, $price)
    {
        $new_price = false;

        // Load product object
        $product = wc_get_product($id);

        // Free trial is set and allowed for this customer?
        $free_trial_time_value = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_free_trial_time_value', true);

        if (!empty($free_trial_time_value) && self::allow_trial($id)) {
            $new_price = 0;
        }

        // Signup fee?
        $signup_fee = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_signup_fee', true);

        if (!empty($signup_fee)) {
            $new_price = (float) $signup_fee + ($new_price === false ? $price : $new_price);
        }

        return $new_price;
    }

    /**
     * Return recurring subscription price
     * For now this is basically product price as set in settings
     * adjusted to account for tax display mode
     *
     * @access public
     * @param int $id
     * @param bool $is_cart
     * @param int $quantity
     * @param float $price
     * @param bool $is_signup_fee
     * @return float
     */
    public static function get_recurring_price($id, $is_cart = false, $quantity = 1, $price = '', $is_signup_fee = false)
    {
        // Load product object
        $product = RightPress_Helper::wc_get_product($id);

        // Get regular price if recurring price can't be set to sale price
        if ($product->is_on_sale() && Subscriptio::option('sale_price_handling') === 'initial_order' && !$is_signup_fee) {
            $price = $product->get_regular_price();
        }

        // Get correct price
        if (self::tax_display_mode_is_inclusive($is_cart)) {
            $price = RightPress_WC_Legacy::product_get_price_including_tax($product, $quantity, $price);
        }
        else {
            $price = RightPress_WC_Legacy::product_get_price_excluding_tax($product, $quantity, $price);
        }

        return (float) apply_filters('wcml_raw_price_amount', $price);
    }

    /**
     * Return sale price for subscription
     *
     * @access public
     * @param int $id
     * @param bool $is_cart
     * @param int $quantity
     * @return float
     */
    public static function get_sale_price($id, $is_cart = false, $quantity = 1)
    {
        // Load product object
        $product = RightPress_Helper::wc_get_product($id);

        // Is it on sale?
        if ($product->is_on_sale()) {

            // Get correct sale price
            if (self::tax_display_mode_is_inclusive($is_cart)) {
                $price = RightPress_WC_Legacy::product_get_price_including_tax($product, $quantity, $product->get_sale_price());
            }
            else {
                $price = RightPress_WC_Legacy::product_get_price_excluding_tax($product, $quantity, $product->get_sale_price());
            }

            return (float) apply_filters('wcml_raw_price_amount', $price);
        }

        else {
            return false;
        }
    }

    /**
     * Check if tax display mode is inclusive
     *
     * @access public
     * @param bool $is_cart
     * @return string
     */
    public static function tax_display_mode_is_inclusive($is_cart = false)
    {
        return ($is_cart ? get_option('woocommerce_tax_display_cart') : get_option('woocommerce_tax_display_shop')) === 'incl';
    }

    /**
     * Return formatted subscription price to be displayed on product pages and cart
     *
     * @access public
     * @param int $id
     * @param bool $is_cart
     * @param bool $is_subtotal
     * @param int $quantity
     * @param float $price_now
     * @param bool $is_variable
     * @param bool $variations_equal
     * @return string
     */
    public static function get_formatted_subscription_price($id, $is_cart, $is_subtotal = false, $quantity = 1, $price_now = null, $is_variable = false, $variations_equal = false)
    {
        $product = RightPress_Helper::wc_get_product($id);

        $recurring_price = Subscriptio_Subscription_Product::get_recurring_price($id, $is_cart, $quantity);
        $sale_price = Subscriptio_Subscription_Product::get_sale_price($id, $is_cart, $quantity);

        // Get product subscription settings
        $price_time_unit_setting = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_price_time_unit', true);
        $price_time_value_setting = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_price_time_value', true);
        $signup_fee_setting = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_signup_fee', true);
        $free_trial_time_unit_setting = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_free_trial_time_unit', true);
        $free_trial_time_value = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_free_trial_time_value', true);

        // Check if product is configured properly, if not - revert to standard price display
        if (empty($price_time_unit_setting) || empty($price_time_value_setting)) {
            return Subscriptio::get_formatted_price($recurring_price, get_woocommerce_currency());
        }

        // Cart/checkout page?
        if ($is_subtotal) {

            // Item is not on sale and does not have a signup fee
            if ($price_now === $recurring_price) {
                return self::format_recurring_amount($recurring_price, $price_time_unit_setting, $price_time_value_setting);
            }
            // Item is on sale or has a signup fee
            else {
                $price_now_html = Subscriptio::get_formatted_price($price_now, get_woocommerce_currency());
                $recurring_price_html = self::format_recurring_amount($recurring_price, $price_time_unit_setting, $price_time_value_setting, null, $sale_price);
                return sprintf(__('%1$s now then %2$s', 'subscriptio'), $price_now_html, $recurring_price_html);
            }
        }

        // Other pages
        else {

            $recurring_price_html = self::format_recurring_amount($recurring_price, $price_time_unit_setting, $price_time_value_setting, null, $sale_price);
            $sale_price_html = $sale_price !== false ? Subscriptio::get_formatted_price($sale_price, get_woocommerce_currency()) : false;

            // Get tax mode adjusted signup fee
            if (!empty($signup_fee_setting)) {
                $signup_fee = self::get_recurring_price($id, $is_cart, $quantity, $signup_fee_setting, true);
                $signup_fee = Subscriptio::get_formatted_price($signup_fee, get_woocommerce_currency());
            }

            // Any free trial?
            if (!empty($free_trial_time_value)) {
                $time_units = Subscriptio::get_time_units();

                if (isset($time_units[$free_trial_time_unit_setting]) && is_numeric($free_trial_time_value)) {
                    $free_trial = $free_trial_time_value . ' ' . call_user_func($time_units[$free_trial_time_unit_setting]['translation_callback'], $free_trial_time_unit_setting, $free_trial_time_value);
                }
                else {
                    $free_trial = '';
                }
            }

            // Maybe use regular price as recurring price if this option is selected in settings and product is on sale
            $use_regular_price_for_recurring = ($sale_price !== false && $sale_price !== $recurring_price && Subscriptio::option('sale_price_handling') === 'initial_order');

            // Free trial & Signup fee
            if (!empty($signup_fee_setting) && !empty($free_trial_time_value)) {
                if ($is_variable && !$variations_equal) {
                    return sprintf(__('From %1$s', 'subscriptio'), $recurring_price_html);
                }
                else if ($use_regular_price_for_recurring) {
                    return sprintf(__('%1$s now then %2$s with a free trial of %3$s and a sign-up fee of %4$s', 'subscriptio'), $sale_price_html, $recurring_price_html, $free_trial, $signup_fee);
                }
                else {
                    return sprintf(__('%1$s with a free trial of %2$s and a sign-up fee of %3$s', 'subscriptio'), $recurring_price_html, $free_trial, $signup_fee);
                }
            }

            // Free trial
            else if (!empty($free_trial_time_value)) {
                if ($is_variable && !$variations_equal) {
                    return sprintf(__('From %1$s', 'subscriptio'), $recurring_price_html);
                }
                else if ($use_regular_price_for_recurring) {
                    return sprintf(__('%1$s now then %2$s with a free trial of %3$s', 'subscriptio'), $sale_price_html, $recurring_price_html, $free_trial);
                }
                else {
                    return sprintf(__('%1$s with a free trial of %2$s', 'subscriptio'), $recurring_price_html, $free_trial);
                }
            }

            // Signup fee
            else if (!empty($signup_fee_setting)) {
                if ($is_variable && !$variations_equal) {
                    return sprintf(__('From %1$s', 'subscriptio'), $recurring_price_html);
                }
                else if ($use_regular_price_for_recurring) {
                    return sprintf(__('%1$s now then %2$s with a sign-up fee of %3$s', 'subscriptio'), $sale_price_html, $recurring_price_html, $signup_fee);
                }
                else {
                    return sprintf(__('%1$s with a sign-up fee of %2$s', 'subscriptio'), $recurring_price_html, $signup_fee);
                }
            }

            // Plain recurring price
            else {
                if ($is_variable && !$variations_equal) {
                    return sprintf(__('From %s', 'subscriptio'), $recurring_price_html);
                }
                else if ($use_regular_price_for_recurring) {
                    return sprintf(__('%1$s now then %2$s', 'subscriptio'), $sale_price_html, $recurring_price_html);
                }
                else {
                    return $recurring_price_html;
                }
            }
        }
    }

    /**
     * Change Add To Cart button label
     *
     * @access public
     * @return string
     */
    public function change_add_to_cart_label($label)
    {
        global $product;

        // Change label only if product is subscription
        if (self::is_subscription($product)) {
            return Subscriptio::option('add_to_cart');
        }

        // Or return the default label
        return $label;
    }

    /**
     * Make product not purchasable if set in settings and user already has subscription
     *
     * @access public
     * @param bool $is_purchasable
     * @param object $product
     * @return bool
     */
    public function is_product_purchasable($is_purchasable, $product)
    {
        global $wp;

        // At first check that it's a subscription product and we are not on the order "thank you" page
        if (self::is_subscription($product) && empty($wp->query_vars['order-received'])) {

            // One active subscription of specific product per customer
            if (Subscriptio::option('limit_subscriptions') == 1) {

                // Limit qty
                $this->add_limit_qty_filters();

                // Disable the purchasing if customer already has such product (and show the link)
                if (Subscriptio_User::has_subscription_product(RightPress_WC_Legacy::product_get_id($product))) {
                    add_filter('woocommerce_single_product_summary', array($this, 'create_subscription_per_product_link'), 99, 2);
                    return false;
                }
            }

            // One active subscription per customer
            else if (Subscriptio::option('limit_subscriptions') == 2) {

                // Limit qty
                $this->add_limit_qty_filters();

                // Disable the purchasing if customer already has any active subscription (and show the link)
                if (Subscriptio_User::has_subscription()) {
                    add_filter('woocommerce_single_product_summary', array($this, 'create_subscription_per_site_link'), 99, 2);
                    return false;
                }
            }
        }

        // Return the default
        return $is_purchasable;
    }

    /**
     * Create subscription link for one subscription per product option
     *
     * @access public
     * @return string
     */
    public function create_subscription_per_product_link()
    {
        global $post;

        // WC31: this will stop working for WooCommerce when they change how they store products (post_type)
        if (is_singular() && isset($post->ID) && $post->post_type == 'product') {

            // Add notice
            wc_print_notice(__('Sorry, but you are not allowed to purchase this product, because you already have it in one of your subscriptions.', 'subscriptio'), 'error');

            return $this->render_subscription_link($post->ID);
        }
    }

    /**
     * Create subscription link for one subscription per site option
     *
     * @access public
     * @return string
     */
    public function create_subscription_per_site_link()
    {
        if (is_singular()) {

            // Add notice
            wc_print_notice(__('Sorry, but you are not allowed to purchase this product, because you already have an active subscription on this site.', 'subscriptio'), 'error');

            return $this->render_subscription_link();
        }
    }

    /**
     * Render subscription link
     *
     * @access public
     * @return void
     */
    public function render_subscription_link($product_id = null)
    {
        // Get specific subscriptions
        if ($product_id) {
            $subscriptions = Subscriptio_User::find_subscriptions_with_product($product_id, true);
        }
        else {
            $subscriptions = Subscriptio_User::find_subscriptions(true);
        }

        // Print the links
        foreach ($subscriptions as $subscription) {
            printf('<a href="%s" class="subscriptio_subscription_link">' . __('Subscription #%s is active', 'subscriptio') . '</a><br>', $subscription->get_frontend_link('view-subscription'), $subscription->id);
        }
    }

    /**
     * Add limiting filters
     *
     * @access public
     * @return void
     */
    public function add_limit_qty_filters()
    {
        add_filter('woocommerce_quantity_input_args', array($this, 'limit_product_qty'), 99, 2);
        add_filter('woocommerce_available_variation', array($this, 'limit_product_qty'), 99, 2);
        add_filter('woocommerce_add_to_cart_validation', array($this, 'limit_product_qty_cart'), 99, 3);
    }

    /**
     * Limit product qty
     *
     * @access public
     * @param array $args
     * @param object $product
     * @return void
     */
    public function limit_product_qty($args, $product)
    {
        // Double-check for subscription
        if (self::is_subscription($product)) {

            // For variation
            if (isset($args['variation_id'])) {
                $args['max_qty'] = 1;
            }
            // For simple product
            else {
                $args['max_value'] = 1;
            }
        }

        return $args;
    }

    /**
     * Limit product qty in cart
     *
     * @access public
     * @param bool $bool
     * @param int $product_id
     * @param int $quantity
     * @return void
     */
    public function limit_product_qty_cart($bool, $product_id, $quantity)
    {
        global $woocommerce;

        // Check the cart items
        foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $product_id && $cart_item['quantity'] >= 1) {
                RightPress_Helper::wc_add_notice(__('Sorry, but you can only purchase one instance of this product and you already added it to cart.', 'subscriptio'), 'notice');
                return false;
            }
        }

        return $bool;
    }

    /**
     * Check if trial can be allowed for this product
     *
     * @access public
     * @param int $product_id
     * @return bool
     */
    public static function allow_trial($product_id)
    {
        // At first check that it's subscription product
        if (self::is_subscription($product_id)) {

            // Get user id
            $user_id = get_current_user_id();

            if (!$user_id || $user_id == 0) {
                return true;
            }

            // Get ids
            $user_trial_product_ids = RightPress_WC_Legacy::customer_get_meta($user_id, '_subscriptio_trial_product_ids', false);

            // Do not limit trials
            if (Subscriptio::option('limit_trials') == 0) {
                return true;
            }

            // One trial per product per customer
            else if (Subscriptio::option('limit_trials') == 1) {
                if (is_array($user_trial_product_ids) && in_array($product_id, $user_trial_product_ids)) {
                    return false;
                }
            }

            // One trial per site per customer
            else if (Subscriptio::option('limit_trials') == 2) {
                if (!empty($user_trial_product_ids)) {
                    return false;
                }
            }

            return true;
        }

        return true;
    }

    /**
     * Add notice for subscription product which is not allowed to be in trial
     *
     * @access public
     * @param bool $bool
     * @param int $product_id
     * @param int $quantity
     * @return bool
     */
    public function add_trial_notice($bool, $product_id, $quantity)
    {
        // Add notice if trial isn't allowed
        if (!self::allow_trial($product_id)) {

            // Create notice depending on settings
            $notice = '';

            if (Subscriptio::option('limit_trials') == 1) {
                $notice .= __('Sorry, but you are not allowed to have a trial period of this subscription product anymore.', 'subscriptio');
            }

            else if (Subscriptio::option('limit_trials') == 2) {
                $notice .= __('Sorry, but you are not allowed to have a trial period on this site anymore.', 'subscriptio');
            }

            $notice .= ' ' . __('You will be charged with full price of subscription and it will be started without a trial period.', 'subscriptio');

            RightPress_Helper::wc_add_notice($notice, 'notice');
        }

        // Return validation unchanged
        return $bool;
    }

    /**
     * Maybe activate gateways for free trials
     *
     * @access public
     * @param bool $is_active
     * @param obj $cart
     * @return bool
     */
    public function maybe_activate_gateways($is_active, $cart)
    {
        // Check cart and user
        if ($cart->total == 0) {
            foreach ($cart->cart_contents as $cart_item) {

                // Load product object
                $product = wc_get_product($cart_item['product_id']);

                // Check if product has trial set
                $subscriptio_free_trial_time_value = RightPress_WC_Legacy::product_get_meta($product, '_subscriptio_free_trial_time_value', true);

                if (self::is_subscription($product) && !empty($subscriptio_free_trial_time_value) && is_numeric($subscriptio_free_trial_time_value)) {
                    return true;
                }
            }
        }

        // Return unchanged
        return $is_active;
    }


}

new Subscriptio_Subscription_Product();

}
