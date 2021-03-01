<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Flush permalinks after first coin save
 */ 
add_action( 'save_post', function ( $post_id, $post, $update ) {

    $post_type = get_post_type($post_id);

    // If this isn't a 'coin' post, don't update it.
    if ( "coin" != $post_type ) return;

    $option_name = "coin_permalinks_flushed";

    if (!get_option($option_name)) {

        // clean up permalink settings
        flush_rewrite_rules();

        // flush only once
        update_option($option_name, true);

    }


}, 10, 3 );

/**
 * Load form fields for crypto cpt
 */
require_once("wpsc-metabox-coin.php");

/**
 * Create Coin Custom Post Type
 */

new WPSC_CryptocurrencyCPT();

class WPSC_CryptocurrencyCPT {

    // Define the Coin CPT
    function __construct() {

        // create CPT
        add_action( 'init', [$this, 'initialize'] );
    
        // add extra columns to coin view
        add_filter( 'manage_coin_posts_columns', [$this, 'setCustomEditCryptocurrencyColumns'] );
        add_action( 'manage_coin_posts_custom_column' , [$this, 'customCryptocurrencyColumn'], 10, 2 );

        // add column styles
        add_action('admin_head', [$this, 'myThemeAdminHead']);
    
    }

    // Create Coins CPT
    public function initialize() {

        $labels = array(
            'name'               => _x( 'Coins', 'post type general name', 'wp-smart-contracts' ),
            'singular_name'      => _x( 'Coin', 'post type singular name', 'wp-smart-contracts' ),
            'menu_name'          => _x( 'Coins', 'admin menu', 'wp-smart-contracts' ),
            'name_admin_bar'     => _x( 'Coin', 'add new on admin bar', 'wp-smart-contracts' ),
            'add_new'            => _x( 'Add New', 'coin', 'wp-smart-contracts' ),
            'add_new_item'       => __( 'Add New Coin', 'wp-smart-contracts' ),
            'new_item'           => __( 'New Coin', 'wp-smart-contracts' ),
            'edit_item'          => __( 'Edit Coin', 'wp-smart-contracts' ),
            'view_item'          => __( 'View Coin', 'wp-smart-contracts' ),
            'all_items'          => __( 'All Coins', 'wp-smart-contracts' ),
            'search_items'       => __( 'Search Coins', 'wp-smart-contracts' ),
            'parent_item_colon'  => __( 'Parent Coins:', 'wp-smart-contracts' ),
            'not_found'          => __( 'No cryptocurrencies found.', 'wp-smart-contracts' ),
            'not_found_in_trash' => __( 'No cryptocurrencies found in Trash.', 'wp-smart-contracts' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'wp-smart-contracts' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
//            'show_in_rest'       => true, // <<< this turns off Gutenberg in coin edition
            'menu_icon'          => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/icon-token.png',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'coin' ),
            'capability_type'    => 'page',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'thumbnail' )
        );

        register_post_type( 'coin', $args );

    }

    // Define column headers in the CPT list
    public function setCustomEditCryptocurrencyColumns($columns) {
      
        unset($columns['date']);   // remove date from the columns list

        $columns['symbol'] = __(   'Symbol', 'wp-smart-contracts' );
        $columns['decimals'] = __( 'Decimals', 'wp-smart-contracts' );
        $columns['total-supply'] = __( 'Initial Supply', 'wp-smart-contracts' );
        $columns['smart-contract'] = __( 'Smart Contract', 'wp-smart-contracts' );
        $columns['network'] = __( 'Network', 'wp-smart-contracts' );
        $columns['flavor'] = __( 'Flavor', 'wp-smart-contracts' );

        $columns['date'] = __( 'Date', 'wp-smart-contracts' ); // now add date to the end

        return $columns;

    }

    // Define the values of each column in the CPT list
    public function customCryptocurrencyColumn( $column, $post_id ) {

        $m = new Mustache_Engine;

        switch ( $column ) {
            case 'symbol' :
                echo strtoupper(get_post_meta($post_id, 'wpsc_coin_symbol', true)); 
                break;

            case 'decimals' :
                echo strtoupper(get_post_meta($post_id, 'wpsc_coin_decimals', true)); 
                break;

            case 'total-supply' :
                echo WPSC_helpers::formatNumber(get_post_meta($post_id, 'wpsc_total_supply', true));
                break;

            case 'smart-contract' :
                if ( $wpsc_network = get_post_meta(get_the_ID(), 'wpsc_network', true) ) {
                    list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);
                }

                if ($contract = get_post_meta($post_id, 'wpsc_contract_address', true)) {

                    $atts['contract'] = $contract;
                    $atts['short-contract'] = substr($contract, 0, 8) . '...' . substr($contract, -6);
                    $atts['id'] = $post_id;

                    if ($blockie = get_post_meta($post_id, 'wpsc_blockie', true)) {
                        $atts['blockie'] = $blockie;
                    }
                    if ($qr = get_post_meta($post_id, 'wpsc_qr_code', true)) {
                        $atts['qr-code'] = $qr;
                    }
                    $atts["etherscan"] = isset($etherscan)?$etherscan:null;
                    echo $m->render(WPSC_Mustache::getTemplate('contract-identicons-be'), $atts);
                } else {
                    echo '';
                }
                break;

            case 'network':
                if ( $wpsc_network = get_post_meta(get_the_ID(), 'wpsc_network', true) ) {
                    list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);
                    echo $m->render(WPSC_Mustache::getTemplate('network'), 
                        [
                            "color" => $color,
                            "network_val" => $network_val,
                        ]
                    );
                } else {
                    echo "";
                }

                break;

            case 'flavor' :

                if ( $wpsc_flavor = get_post_meta(get_the_ID(), 'wpsc_flavor', true) ) {

                    $color = false;
                    if ($wpsc_flavor=="chocolate") $color = "brown";
                    if ($wpsc_flavor=="vanilla") $color = "yellow";
                    if ($wpsc_flavor=="pistachio") $color = "olive";

                    echo $m->render('<a class="ui {{color}} label"><span class="wpsc-capitalize">{{flavor}}</span></a>', 
                    [
                        'color' => $color,
                        'flavor' => $wpsc_flavor,
                    ]);

                } else {
                    echo "";
                }
                break;

        }
    }

    // define the size of specific columns in the CPT list
    public function myThemeAdminHead() {
        global $post_type;
        if ( 'coin' == $post_type ) {
            ?>
            <style type="text/css">
                .column-smart-contract { width: 30%; } 
                .column-decimals { width: 7%; }
                .column-symbol { width: 7%; }
            </style>
            <?php
        }
    }

}


// Create a template view for the new CPT
add_filter('single_template', function ($single) {

    global $post;

    if ( $post->post_type == 'coin' ) {
        // custom path to prevent error with symlinks
        $the_file = dirname(__FILE__);
        $wpsc_plugin_path = plugin_dir_path($the_file, basename(dirname($the_file)) . '/' . basename($the_file)) . 'coin.php';
        if ( file_exists( $wpsc_plugin_path ) ) {
            return $wpsc_plugin_path;
        }
    }

    return $single;

});