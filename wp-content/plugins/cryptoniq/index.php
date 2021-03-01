<?php

/**
 * Plugin Name: Cryptoniq
 * Plugin URI: https://cryptoniq.io/wp/
 * Description: Pay with cryptocurrencies.
 * Version: 1.5
 * Author: DivEngine
 * Author URI: https://codecanyon.net/user/divengine
 * Text Domain: cryptoniq
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

// Define Version!
if ( !defined( 'CRYPTONIQ_VER' ) ) {
    define( 'CRYPTONIQ_VER', '1.5' );
} 

// Define Directory URL
if ( !defined( 'CRYPTONIQ_DIR_URL' ) ) {
    define( 'CRYPTONIQ_DIR_URL', plugin_dir_url( __FILE__ ) );
}

// Define Directory PATH
if ( !defined( 'CRYPTONIQ_DIR_PATH' ) ) {
    define( 'CRYPTONIQ_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

// Define Payment ID
if ( !defined( 'CRYPTONIQ_PAY_ID' ) ) {
    define( 'CRYPTONIQ_PAY_ID', 'cryptoniq' );
}

// Define Option
if ( !defined( 'CRYPTONIQ_OPTION' ) ) {
    define( 'CRYPTONIQ_OPTION', 'cryptoniq_option' );
}


// Configure Cryptoniq
// ======================================================

class Cryptoniq_Engine
{
	private $files = array(
		'gateway.php',
		'tgma/activator.php',
		'admin/index.php',
		'functions.php',
		'ajax.php',
		'prices.php',
		'coins/index.php',
		'prefixes.php'
	);
	
    // Plugin initialization
    function __construct() {
        // Load Localization
        load_plugin_textdomain( 'cryptoniq', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
		
        // Include Functions
        $this->include_files();

		// Register Assets
        add_action( 'init', array( $this, 'register_assets' ) );
		
		// Add Assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 9999, 1 );
    }
	
    private function include_files() {
        foreach ( $this->files as $file ) {
			 require_once( CRYPTONIQ_DIR_PATH . $file );
		}
    }
	
    public function load_textdomain() {
        // load_plugin_textdomain( 'cryptoniq', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
    }
	
	public function register_assets() {
		wp_register_style(
			'cryptoniq-libs-ionicons',
			CRYPTONIQ_DIR_URL . 'assets/libs/ionicons/css/ionicons.css',
			array(),
			CRYPTONIQ_VER,
			'all'
		);
        
		wp_register_style(
			'cryptoniq-libs-cryptofont',
			CRYPTONIQ_DIR_URL . 'assets/libs/cryptofont/css/cryptofont.css',
			array(),
			CRYPTONIQ_VER,
			'all'
		);
        
		wp_register_style(
			'cryptoniq-engine',
			CRYPTONIQ_DIR_URL . 'assets/css/cryptoniq.engine.css',
			array(),
			CRYPTONIQ_VER,
			'all'
		);
		
		wp_register_script(
			'cryptoniq-libs',
			CRYPTONIQ_DIR_URL . 'assets/js/cryptoniq.libs.js',
			array( 'jquery' ),
			CRYPTONIQ_VER,
			false
		);    
                
		wp_register_script(
			'cryptoniq-engine',
			CRYPTONIQ_DIR_URL . 'assets/js/cryptoniq.engine.js',
			array( 'jquery' ),
			CRYPTONIQ_VER,
			false
		);    
        
		$localize = array(
			'url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'cryptoniq_front_ajax_nonce' )
		);
        
		wp_localize_script(
			'cryptoniq-engine',
			'cryptoniq_paybox_ajax_data',
			$localize
		);
	}

	public function enqueue_assets() {
		// css assets
		wp_enqueue_style( 'cryptoniq-libs-ionicons' );
		wp_enqueue_style( 'cryptoniq-libs-cryptofont' );
		wp_enqueue_style( 'cryptoniq-engine' );
        
		// js assets
		wp_enqueue_script( 'cryptoniq-libs' );
		wp_enqueue_script( 'cryptoniq-engine' );
	}
}
new Cryptoniq_Engine;
  
// Add Functions to Cron
// ======================================================

function cryptoniq_cron_update_prices( $schedules ) {
    $schedules['cryptoniq_updater_30m'] = array(
    	'interval'  => 60 * 60,
    	'display'   => esc_html__( 'Every 30 Minutes', 'cryptoniq' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cryptoniq_cron_update_prices' );

function cryptoniq_cron_check_orders( $schedules ) {
    $schedules['cryptoniq_updater_3h'] = array(
    	'interval'  => 3 * 60 * 60,
    	'display'   => esc_html__( 'Every 3 Hours', 'cryptoniq' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cryptoniq_cron_check_orders' );

function cryptoniq_price_options_updater() {
	$all_options = wp_load_alloptions();
	
	if ( !array_key_exists( 'cryptoniq_currency_prices', $all_options ) ) {
    	add_option( 'cryptoniq_currency_prices', cryptoniq_get_currency_prices(), '', 'yes' );
	} else {
    	update_option( 'cryptoniq_currency_prices', cryptoniq_get_currency_prices(), 'yes' );
	}
	
	if ( !array_key_exists( 'cryptoniq_coin_prices', $all_options ) ) {
		add_option( 'cryptoniq_coin_prices', cryptoniq_get_coin_prices(), '', 'yes' );
	} else {
		update_option( 'cryptoniq_coin_prices', cryptoniq_get_coin_prices(), 'yes' );
	}
}
add_action( 'cryptoniq_cron_update_prices', 'cryptoniq_price_options_updater' );

function cryptoniq_orders_global_checker() {
	$query = new WC_Order_Query( array(
   		'limit' => -1,
    	'orderby' => 'date',
    	'order' => 'DESC',
    	'return' => 'ids',
		'status' => 'pending',
		'payment_method' => CRYPTONIQ_PAY_ID,
	) );
	$query_ids = $query->get_orders();
	
	foreach ( $query_ids as $query_id ) {
		$order = wc_get_order( $query_id );
		$id = $order->get_id();
	
		$coin = get_post_meta( $id, 'cryptoniq_coin_name', true );
		$wallet = get_post_meta( $id, 'cryptoniq_wallet', true );
		$amount = get_post_meta( $id, 'cryptoniq_coin_amount', true );
		$step = get_post_meta( $id, 'cryptoniq_step', true );
	
    	if ( $step == 2 ) {
			if ( $coin == 'ETH' ) {				
                cryptoniq_coin_txs_eth( $wallet, $amount, $id, 'no' );	
			} elseif ( $coin == 'DOGE' ) { 				
                cryptoniq_coin_txs_doge( $wallet, $amount, $id, 'no' );	
			} elseif ( $coin == 'LTC' ) { 
                cryptoniq_coin_txs_ltc( $wallet, $amount, $id, 'no' );	
			} elseif ( $coin == 'ZEC' ) { 
                cryptoniq_coin_txs_zec( $wallet, $amount, $id, 'no' );	
			} elseif ( $coin == 'DASH' ) { 
                cryptoniq_coin_txs_dash( $wallet, $amount, $id, 'no' );	
			} else {				
            	cryptoniq_coin_txs_btc( $wallet, $amount, $id, 'no' );					
        	}
        	
        	sleep(5);
		}
	}	
}
add_action( 'cryptoniq_cron_check_orders', 'cryptoniq_orders_global_checker' );

function cryptoniq_cron_activation() {
	if ( !wp_next_scheduled( 'cryptoniq_cron_update_prices' ) ) {
		wp_schedule_event( time(), 'cryptoniq_updater_30m', 'cryptoniq_cron_update_prices' );
	}
	
	if ( !wp_next_scheduled( 'cryptoniq_cron_check_orders' ) ) {
		wp_schedule_event( time(), 'cryptoniq_updater_3h', 'cryptoniq_cron_check_orders' );
	}
}
register_activation_hook( __FILE__, 'cryptoniq_cron_activation' );

function cryptoniq_cron_deactivation() {
	wp_clear_scheduled_hook( 'cryptoniq_cron_activation' );
}
register_deactivation_hook( __FILE__, 'cryptoniq_cron_deactivation' );