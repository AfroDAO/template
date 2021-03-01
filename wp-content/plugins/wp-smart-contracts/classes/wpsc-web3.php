<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Include web3 and metamask checker functions
 */

new WPSC_Web3();

class WPSC_Web3 {

    function __construct() {

        // Load JS Web3 Library
        add_action( 'admin_enqueue_scripts' , [$this, 'loadWeb3Script'], 10, 2 );

        // Load wp admin bar
        add_action('admin_bar_menu', [$this, 'addToolbar'], 999);

    }

    // Enqueue web3 JS files
    public function loadWeb3Script($hook) {

        wp_enqueue_script( 'web3', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/web3.js' );
        wp_enqueue_script( 'wp-smart-contracts', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc.js' );

        // add translations for JS
        wp_localize_script('wp-smart-contracts', WPSC_Mustache::createJSObjectNameFromTag('global'), [
            'MANDATORY_FIELD' => __("Mandatory Field", 'wp-smart-contracts'),
            'SELECT_SOCIAL_NET' => __("Please select a Social Network", 'wp-smart-contracts'),
            'PROFILE_LINK' => __("Please write your profile link", 'wp-smart-contracts'),
            'CONFIRM_REMOVE_SOCIAL' => __("Are you sure you want to delete this social network?", 'wp-smart-contracts'),
            'CODE_COPIED' => __("Code copied to clipboard!", 'wp-smart-contracts'),
            'WRITE_ADDRESS' => __("Please write the address of the Smart Contract you want to load", 'wp-smart-contracts'),
        ]);

    }

}

