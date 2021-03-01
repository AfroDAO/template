<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Flush permalinks after first crowdfunding save
 */ 
add_action( 'save_post', function ( $post_id, $post, $update ) {

    $post_type = get_post_type($post_id);

    // If this isn't a 'coin' post, don't update it.
    if ( "crowdfunding" != $post_type ) return;

    $option_name = "crowdfunding_permalinks_flushed";

    if (!get_option($option_name)) {

        // clean up permalink settings
        flush_rewrite_rules();

        // flush only once
        update_option($option_name, true);

    }

}, 10, 3 );

/**
 * Load help metaboxes for crowdfunding cpt
 */
require_once("wpsc-metabox-crowdfunding.php");

/**
 * Create CrowdfundingCustom Post Type
 */

new WPSC_CrowdfundingCPT();

class WPSC_CrowdfundingCPT {

    // Define the CrowdfundingCPT
    function __construct() {

        // create CPT
        add_action( 'init', [$this, 'initialize'] );
    
        // add extra columns to crowdfunding view
        add_filter( 'manage_crowdfunding_posts_columns', [$this, 'setCustomEditCrowdfundingColumns'] );
        add_action( 'manage_crowdfunding_posts_custom_column' , [$this, 'customCrowdfundingColumn'], 10, 2 );

        // add column styles
        add_action('admin_head', [$this, 'myThemeAdminHead']);
    
    }

    // Create CrowdfundingsCPT
    public function initialize() {

        $labels = array(
            'name'               => _x( 'Crowdfundings', 'post type general name', 'wp-smart-contracts' ),
            'singular_name'      => _x( 'Crowdfunding', 'post type singular name', 'wp-smart-contracts' ),
            'menu_name'          => _x( 'Crowdfundings', 'admin menu', 'wp-smart-contracts' ),
            'name_admin_bar'     => _x( 'Crowdfunding', 'add new on admin bar', 'wp-smart-contracts' ),
            'add_new'            => _x( 'Add New', 'crowdfunding', 'wp-smart-contracts' ),
            'add_new_item'       => __( 'Add New Crowdfunding', 'wp-smart-contracts' ),
            'new_item'           => __( 'New Crowdfunding', 'wp-smart-contracts' ),
            'edit_item'          => __( 'Edit Crowdfunding', 'wp-smart-contracts' ),
            'view_item'          => __( 'View Crowdfunding', 'wp-smart-contracts' ),
            'all_items'          => __( 'All Crowdfundings', 'wp-smart-contracts' ),
            'search_items'       => __( 'Search Crowdfundings', 'wp-smart-contracts' ),
            'parent_item_colon'  => __( 'Parent Crowdfundings:', 'wp-smart-contracts' ),
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
            'menu_icon'          => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/icon-crowdfunding.png',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'crowdfunding' ),
            'capability_type'    => 'page',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'thumbnail' )
        );

        register_post_type( 'crowdfunding', $args );

    }

    // Define column headers in the CPT list
    public function setCustomEditCrowdfundingColumns($columns) {
    
        unset($columns['date']);   // remove date from the columns list

        $columns['minimum'] = __( 'Minimum', 'wp-smart-contracts' );
        $columns['percentage'] = __( 'Percentage', 'wp-smart-contracts' );
        $columns['smart-contract'] = __( 'Smart Contract', 'wp-smart-contracts' );
        $columns['network'] = __( 'Network', 'wp-smart-contracts' );
        $columns['flavor'] = __( 'Flavor', 'wp-smart-contracts' );

        $columns['date'] = __( 'Date', 'wp-smart-contracts' ); // now add date to the end

        return $columns;

    }

    // Define the values of each column in the CPT list
    public function customCrowdfundingColumn( $column, $post_id ) {

        $m = new Mustache_Engine;

        switch ( $column ) {
            case 'minimum' :
                echo strtoupper(get_post_meta($post_id, 'wpsc_minimum', true)) . " ether";
                break;

            case 'percentage' :
                echo strtoupper(get_post_meta($post_id, 'wpsc_approvers', true)); 
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
                    $atts["etherscan"] = $etherscan;
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
                    if ($wpsc_flavor=="mango") $color = "orange";

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
        if ( 'crowdfunding' == $post_type ) {
            ?>
            <style type="text/css">
                .column-smart-contract { width: 30%; } 
            </style>
            <?php
        }
    }

}


// Create a template view for the new CPT
add_filter('single_template', function ($single) {

    global $post;

    if ( $post->post_type == 'crowdfunding' ) {
        // custom path to prevent error with symlinks
        $the_file = dirname(__FILE__);
        $wpsc_plugin_path = plugin_dir_path($the_file, basename(dirname($the_file)) . '/' . basename($the_file)) . 'crowdfunding.php';
        if ( file_exists( $wpsc_plugin_path ) ) {
            return $wpsc_plugin_path;
        }
    }

    return $single;

});