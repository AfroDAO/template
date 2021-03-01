<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Include semantic ui JS + CSS + Functions
 */

new WPSC_assets();

class WPSC_assets {

    function __construct() {

        // Load JS Web3 Library in admin
        add_action( 'admin_enqueue_scripts' , [$this, 'loadAssets'], 10, 2 );

        // Load JS Web3 Library in FE
        add_action( 'wp_enqueue_scripts' , [$this, 'loadAssetsFrontEnd'], 10, 2 );

    }

    public static function localizeWPSC($is_a_smart_contract, $is_deployer=false) {

        $option = get_option('etherscan_api_key_option');
        if (WPSC_helpers::valArrElement($option, 'api_key')) {
            wp_localize_script( 'wp-smart-contracts', 'etherscan_api_key', $option['api_key'] );
        }

        wp_localize_script( 'wp-smart-contracts', 'is_a_smart_contract', $is_a_smart_contract );
        wp_localize_script( 'wp-smart-contracts', 'endpoint_url', get_rest_url() );
        wp_localize_script( 'wp-smart-contracts', 'nonce', self::get_rest_nonce());

        if ($is_deployer) {
            wp_localize_script( 'wp-smart-contracts', 'is_deployer', "true");
        }

        // add translations for JS
        wp_localize_script('wp-smart-contracts', WPSC_Mustache::createJSObjectNameFromTag('global'), [
            'MANDATORY_FIELD' => __("Mandatory Field", 'wp-smart-contracts'),
            'SELECT_SOCIAL_NET' => __("Please select a Social Network", 'wp-smart-contracts'),
            'SELECT_APPROVERS_PERCENT' => __("Please select approvers percentage ", 'wp-smart-contracts'),
            'PROFILE_LINK' => __("Please write your profile link", 'wp-smart-contracts'),
            'ERC20_RECEIVE_TOKEN'  => __("Please write the address of all tokens, or remove the row", 'wp-smart-contracts'),
            'ERC20_RECEIVE_RATE'  => __("Please write the rate for all tokens, or remove the row", 'wp-smart-contracts'),
            'ERC20_RECEIVE_RATE_INT'  => __("The rate must be a positive integer for all tokens", 'wp-smart-contracts'),
            'CONFIRM_REMOVE_SOCIAL' => __("Are you sure you want to delete this social network?", 'wp-smart-contracts'),
            'CODE_COPIED' => __("Code copied to clipboard!", 'wp-smart-contracts'),
            'WRITE_ADDRESS' => __("Please write the address of the Smart Contract you want to load", 'wp-smart-contracts'),
        ]);

    }

    public function loadAssets($hook) {

        // creatting or editing a coin flag
        $is_a_smart_contract = "false";
        $is_edit = false;

        if ( ('edit.php' == $hook) or
             ('post.php' == $hook and $post_id = WPSC_Metabox::cleanUpText($_GET["post"])) or
             ('post-new.php' == $hook)
        ) {
            $is_edit = true;
        }

        wp_enqueue_script( 'wpsc-notices', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpic-notice.js' );

        // check if we are editing or adding a smart contract
        if (
                (
                    'post.php' == $hook and $post_id = WPSC_Metabox::cleanUpText($_GET["post"]) and (
                        get_post_type($post_id) == "coin" or
                        get_post_type($post_id) == "crowdfunding"
                    )
                ) or
                (
                    'post-new.php' == $hook and (
                        WPSC_Metabox::cleanUpText($_GET["post_type"]) == "coin" OR 
                        WPSC_Metabox::cleanUpText($_GET["post_type"]) == "crowdfunding" OR 
                        WPSC_Metabox::cleanUpText($_GET["post_type"]) == "ico"
                    )
                )
        ) {
            $is_a_smart_contract = "true";
        }

        // queue for all admin area        
        wp_enqueue_script(  'web3', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/web3.js' );
        wp_enqueue_script(  'wp-smart-contracts', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc.js' );

        self::localizeWPSC($is_a_smart_contract);

        // enqueue it only if we are creating or editing a coin
        if ($is_edit) {

            wp_enqueue_script( 'blockies', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/blockies.min.js' );
            wp_enqueue_script( 'copytoclipboard', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/copytoclipboard.js' );
            wp_localize_script( 'copytoclipboard', 'copied', __('Copied!', 'wp-smart-contracts') );
            wp_enqueue_script( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.js', ['jquery'] );
            wp_enqueue_script( 'zoom-qr', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/zoom-qr.js' );
            wp_enqueue_script( 'wpsc-google-prettify', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/prettify/run_prettify.js?autoload=true&skin=desert' );
            wp_localize_script( 'wpsc-google-prettify', 'wpsc_plugin_path', dirname( plugin_dir_url( __FILE__ ) ) );

            wp_enqueue_style( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.css');
            wp_enqueue_style( 'wp-smart-contracts-styles', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/styles.css', ['wp-smart-contracts-semantic']);

        }

        if ('settings_page_etherscan-api-key-setting-admin' == $hook) {
            wp_enqueue_script( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.js', ['jquery'] );
            wp_enqueue_style( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.css');
            wp_enqueue_style( 'wp-smart-contracts-styles', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/styles.css', ['wp-smart-contracts-semantic']);
        }

        // enqueue in all admin pages
        wp_enqueue_style( 'wp-smart-contracts-admin-bar', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/wp-admin-bar.css');

        // Load wp admin bar
        add_action('admin_bar_menu', [$this, 'addToolbar'], 999);

    }

    // load wp admin toolbar with metamask info
    public function addToolbar($wp_admin_bar) {


        $m = new Mustache_Engine;

        $wp_admin_bar->add_node( [
            'id'    => 'wp-smart-contracts',
            'title' => 'WPSmartContracts'
        ]);
        /*
        $wp_admin_bar->add_node( [
            'id'    => 'wpsc-connect-metamask',
            'title' => $m->render(
                WPSC_Mustache::getTemplate('wp-admin-bar-connect-ethereum'), 
                [
                    'text' => __('Connect to Ethereum Network', 'wp-smart-contracts'),
                    'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                ]
            )
        ]);
        */
    }

    public function loadAssetsFrontEnd() {
        
        // flag for is a contract
        $is_a_token = false;
        $is_a_crowd = false;
        $is_a_ico = false;
        $is_a_scanner = true;

        $id = get_the_ID();

        // is a coin?
        if (get_post_type($id)=="coin") {
            $is_a_token = true;
        } elseif (has_shortcode(get_post_field('post_content', $id), 'wpsc_coin')) {
            $is_a_token = true;
        }

        // is a crowd?
        if (get_post_type($id)=="crowdfunding") {
            $is_a_crowd = true;
        } elseif (has_shortcode(get_post_field('post_content', $id), 'wpsc_crowdfunding')) {
            $is_a_crowd = true;
        }

        // is an ico?
        if (get_post_type($id)=="ico") {
            $is_a_ico = true;
        } elseif (has_shortcode(get_post_field('post_content', $id), 'wpsc_ico')) {
            $is_a_ico = true;
        }

        // is a QR Scanner?
        if (has_shortcode(get_post_field('post_content', $id), 'wpsc_qr_scanner')) {
            $is_a_scanner = true;
        }


        // load global assets for all contracts
        if ($is_a_token or $is_a_crowd or $is_a_ico or $is_a_scanner) {

            wp_enqueue_script( 'copytoclipboard', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/copytoclipboard.js' );
            wp_localize_script( 'copytoclipboard', 'copied', __('Copied!', 'wp-smart-contracts') );
            wp_enqueue_script( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.js', ['jquery'] );
            wp_enqueue_script( 'zoom-qr', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/zoom-qr.js' );
            wp_enqueue_script( 'blockies', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/blockies.min.js' );
            wp_enqueue_script( 'wpsc-google-prettify', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/prettify/run_prettify.js?autoload=true&skin=desert' );
            wp_localize_script( 'wpsc-google-prettify', 'wpsc_plugin_path', dirname( plugin_dir_url( __FILE__ ) ) );

            // token specific assets
            if ($is_a_token) {
                
                wp_enqueue_script( 'wpsc-fe', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc-fe.js' );
                wp_localize_script( 'wpsc-fe', 'endpoint_url', get_rest_url() );
                wp_localize_script( 'wpsc-fe', 'nonce', self::get_rest_nonce() );
                wp_localize_script( 'wpsc-fe', 'is_block_explorer', $is_a_token?"true":"false" );

                $wpsc_adv_burn = get_post_meta($id, 'wpsc_adv_burn', true);
                $wpsc_adv_pause = get_post_meta($id, 'wpsc_adv_pause', true);
                $wpsc_adv_mint = get_post_meta($id, 'wpsc_adv_mint', true);

                if ($wpsc_adv_burn) wp_localize_script( 'wpsc-fe', 'wpsc_adv_burn', $wpsc_adv_burn);
                if ($wpsc_adv_pause) wp_localize_script( 'wpsc-fe', 'wpsc_adv_pause', $wpsc_adv_pause);
                if ($wpsc_adv_mint) wp_localize_script( 'wpsc-fe', 'wpsc_adv_mint', $wpsc_adv_mint);

                // get the first page defined as qr-scanner
                if ($page = self::getQrScanner()) {
                    wp_localize_script( 'wpsc-fe', 'qr_scanner_page', $page );
                }
            }

            if ($network_id = get_post_meta($id, 'wpsc_network', true) and $network_array = WPSC_helpers::getNetworks()) {
                $network_name = $network_array[$network_id]["name"];
            }

            // crowd specific assets
            if ($is_a_crowd) {
                wp_enqueue_script( 'wpsc-fe-crowd', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc-fe-crowd.js' );
                wp_localize_script( 'wpsc-fe-crowd', 'endpoint_url', get_rest_url() );
                wp_localize_script( 'wpsc-fe-crowd', 'nonce', self::get_rest_nonce() );
                wp_localize_script( 'wpsc-fe-crowd', 'is_crowd', $is_a_crowd?"true":"false" );
                if (isset($network_name) and $network_name) {
                    wp_localize_script( 'wpsc-fe-crowd', 'wpsc_network_name', $network_name );
                }

                // get the first page defined as qr-scanner
                if ($page = self::getQrScanner()) {
                    wp_localize_script( 'wpsc-fe-crowd', 'qr_scanner_page', $page );
                }
            }

            // ICO specific assets
            if ($is_a_ico) {

                wp_enqueue_script( 'wpsc-fe-ico', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc-fe-ico.js' );
                wp_localize_script( 'wpsc-fe-ico', 'endpoint_url', get_rest_url() );
                wp_localize_script( 'wpsc-fe-ico', 'nonce', self::get_rest_nonce() );
                wp_localize_script( 'wpsc-fe-ico', 'is_ico', $is_a_ico?"true":"false" );
                // add rate and cap to calculate in JS the #of tokens sold and the reached cap feature
                wp_localize_script( 'wpsc-fe-ico', 'rate', ($id and get_post_meta($id, 'wpsc_rate', true))?get_post_meta($id, 'wpsc_rate', true):'0' );
                wp_localize_script( 'wpsc-fe-ico', 'wpsc_adv_hard', ($id and get_post_meta($id, 'wpsc_adv_hard', true))?get_post_meta($id, 'wpsc_adv_hard', true):'0' );
                wp_localize_script( 'wpsc-fe-ico', 'wpsc_adv_cap', ($id and get_post_meta($id, 'wpsc_adv_cap', true))?get_post_meta($id, 'wpsc_adv_cap', true):'0' );
                wp_localize_script( 'wpsc-fe-ico', 'endpoint_url', get_rest_url() );
                wp_localize_script( 'wpsc-fe-ico', 'nonce', self::get_rest_nonce());
        
                if (isset($network_name) and $network_name) {
                    wp_localize_script( 'wpsc-fe-ico', 'wpsc_network_name', $network_name );
                }

                // get the first page defined as qr-scanner
                if ($page = self::getQrScanner()) {
                    wp_localize_script( 'wpsc-fe-ico', 'qr_scanner_page', $page );
                }

            }

            wp_enqueue_style( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.css');
            wp_enqueue_style( 'wp-smart-contracts-styles', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/styles.css', ['wp-smart-contracts-semantic']);

        }

    }

    // get the wp rest nonce with the proper separator & or ?
    private static function get_rest_nonce() {

        $nonce = wp_create_nonce('wp_rest');
        
        if (strpos(get_rest_url(), '?')===false) {
            return urlencode("?_wpnonce=" . $nonce);
        } else {
            return urlencode("&_wpnonce=" . $nonce);
        }

    }
    
    static public function getQrScanner() {

        $pages = get_pages([
            'meta_key' => '_wp_page_template',
            'meta_value' => 'wpsc-clean-template.php'
        ]);

        if (is_array($pages)) {
            $page = current($pages);
            if (is_object($page)) {
                return get_permalink($page->ID);
            }
        }

        return false;

    }

}

