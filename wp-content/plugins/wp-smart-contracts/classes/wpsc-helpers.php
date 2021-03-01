<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Include semantic ui JS + CSS + Functions
 */

class WPSC_helpers {

	// use to avoid notices when accesing an array element
	static public function valArrElement($arr=null, $elem=null) {
		if (!is_array($arr)) return false;
		if (!$elem) return false;
		if (!array_key_exists($elem, $arr)) return false;
		return true;
	}

	// get network information from json file
	static function getNetworks() {
	    if (file_exists($json_file = dirname(dirname(__FILE__)).'/assets/json/networks.json') and 
	    	is_array( $arr = json_decode( file_get_contents($json_file), true ) ) ) {
	    	return $arr;
		} else {
			return false;
		}
	}

	// format a number with 18 decimals and the proper number separators
    static public function formatNumber($num) {

    	// convert to float
    	$num = floatval($num);

    	// validate number of decimals to show, use 4 as default
    	if (!is_numeric($ndts = WPSCSettingsPage::numberOfDecimalsToShow())) {
    		$ndts = 4;
    	}

    	// add thousands and decimals separators
        $nf = number_format($num, $ndts, WPSCSettingsPage::numberFormatDecimals(), WPSCSettingsPage::numberFormatThousands());

        return $nf;

    }

    // create a page with QR Scanner if not already created
    static public function createQRScannerPage() {

    	if (WPSC_assets::getQrScanner()===false) {

			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'WPSC QR Scanner',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_qr_scanner]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );

			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }

    	}
    }

    // return the short version of an Ethereum address
	public static function shortify($address, $ultra=false) {
		if ($ultra) {
			return substr($address, 0, 4) . '...' . substr($address, -2);
		} else {
			return substr($address, 0, 6) . '...' . substr($address, -4);
		}
	}

	public static function languages() {
		load_plugin_textdomain( 'wp-smart-contracts', false, basename( dirname( __DIR__ ) ) . '/languages/' );
	}

	public static function renderWPICInfo() {
	    $m = new Mustache_Engine;
	    return $m->render(
	      WPSC_Mustache::getTemplate('metabox-wpic-info'), 
	      [
	        "title" => __('xDai Chain', 'wp-smart-contracts'),
			"logo" => dirname( plugin_dir_url( __FILE__ )) . '/assets/img/xdai.png',
	        "description_1" => __('xDai Chain is here', 'wp-smart-contracts'),
	        "description_2" => __('Now you can deploy all smart contracts flavors in xDai Chain.', 'wp-smart-contracts'),
	        "feature_1" => __('Deploy and interact with your contracts with low fees in xDai stable coin.', 'wp-smart-contracts'),
	        "feature_2" => __('Faucets to get free xDai.', 'wp-smart-contracts'),
	        "feature_3" => __('Test networks available.', 'wp-smart-contracts'),
	        "feature_4" => __('No need to install new software.', 'wp-smart-contracts'),
	        "feature_5" => __('Use xDai from the comfort of Metamask in your browser.', 'wp-smart-contracts'),
	        "claim" => __('Claim 5,000 WPIC', 'wp-smart-contracts'),
	        "claim-note" => __('0.1 Ξ', 'wp-smart-contracts'),
	        "learn" => __('Learn more', 'wp-smart-contracts'),
	      ]
	    );
	}

}

/**
 * Warnings in wṕ-admin
 */

add_action('admin_notices', function () {

	// check Infura settings

	$options = get_option('etherscan_api_key_option');
	$infura_api_key = (WPSC_helpers::valArrElement($options, "infura_api_key") and !empty($options["infura_api_key"]))?$options["infura_api_key"]:false;

	if (empty(trim($infura_api_key))) {
		echo '<div class="notice notice-error is-dismissible">
			<h3>WP Smart Contracts alert - Action needed!</h3><p>To use Crowdfundings and ICOs properly you need to setup a free <a href="https://infura.io/" target="_blank">Infura Project ID</a> in <a href="'. get_admin_url() . 'options-general.php?page=etherscan-api-key-setting-admin">WP Smart Contracts Settings</a> in your WordPress install</p><p>Otherwise your site may not allow users to properly interact with your Smart Contract if they do not have Metamask installed</p>
		</div>';
	}

	// check that rest api is alive

	$endpoint = get_rest_url(null, 'wpsc/v1/ping');

	$response = wp_remote_get( $endpoint, null );

	if (!WPSC_helpers::valArrElement($response, "response") or !WPSC_helpers::valArrElement($response["response"], "code") or $response["response"]["code"] != "200") {
		
		$get_rest_url = get_rest_url(null, '');

		echo <<<HELP
		<div class="notice notice-error is-dismissible">
			<h3>WP Smart Contracts alert - Action needed!</h3>
			<p>Looks like the <a href="https://developer.wordpress.org/rest-api/" target="_blank">WP Rest API</a> is not working properly in your WordPress installation</p>
			<p>Please check that the URL: <a href="$get_rest_url" target="_blank">$get_rest_url</a> is not failing</p>
			<p>For more information please visit: <a href="https://wordpress.org/support/topic/404-error-on-coin-page-already-created-the-coin-on-mainnet/" target="_blank">our support page</a></p>
		</div>
HELP;
	}

	// NEWS

	if (!WPSC_helpers::valArrElement($_COOKIE, "xdai_notice_hide")) {

		$wpsc_logo = dirname( plugin_dir_url( __FILE__ )) . '/assets/img/wpsc-logo.png';
		$xdai_logo = dirname( plugin_dir_url( __FILE__ )) . '/assets/img/xdai.png';

		echo <<<XDAI
		<div id="wpic-notification" class="notice notice-info is-dismissible">
			<p><img src="$wpsc_logo" style="max-width: 250px"></p>
			<h3>WPSmartContracts now supports <img src="$xdai_logo" style="max-width: 15px"> xDai Chain</h3>
			<p>Deploy your contracts with lower fees to xDai Layer 2 solution.</p>
			<ul>
			<li>&raquo; Lower fees paid in xDai stable coin.</li>
			<li>&raquo; Easy to use.</li>
			<li>&raquo; Faster.</li>
			</ul>
			<p><a href="https://wpsmartcontracts.com/xdai-chain/" target="_blank">Learn more</a></p>
			<p style="color: #bbb"><input type="checkbox" id="wpic-no-show"> Got it, Thanks!. Please don't show this again.</p>
		</div>
XDAI;
	}

});

/**
 * Load i18n files
 */
add_action( 'plugins_loaded', ['WPSC_helpers', 'languages'] );


/**
 * One time notification
 */
class OneTimeNotifications {

    public static $_notices  = array();

    public function __construct() {
		add_action( 'shutdown', array( $this, 'save_errors' ) );
    }

    public static function add_notice( $title, $text ) {
        self::$_notices[] = [$title, $text];
    }

    public function save_errors() {
        update_option( 'wpsc_custom_notices', self::$_notices );
    }

    public function output_errors() {
        $errors = maybe_unserialize( get_option( 'wpsc_hide_custom_notices' ) );

        if ( ! empty( $errors ) ) {

            echo '<div id="mc_notices" class="error notice is-dismissible">';

			echo '<h3>WP Smart Contracts change log</h3>';

            foreach ( $errors as $error ) {
				if ($error[0]) echo '<p><b>&raquo; ' . wp_kses_post( $error[0] ) . '</b>';
                if ($error[1]) echo '<br>' . wp_kses_post( $error[1] ) . '</p>';
            }

            echo '</div>';

			update_option( 'wpsc_hide_custom_notices', 1 );

		}
    }

}

if (!get_option( 'wpsc_onetime_notice_1' )) {
	add_action( 'shutdown', function () {
		echo '<div class="notice notice-info is-dismissible">';
		echo '<h3>WP Smart Contracts change log</h3>';
		foreach ([
			["We ♥ WP5.5", "Now WPSmartContracts is compatible with WordPress 5.5"],
			["Uniswap is in da house!", "Now you can add your token to Uniswap and include it in the Exchanges section"],
			["Because good ideas needs your support!", "Starting January 1, 2021 deploy fees may apply, visit <a href=\"https://wpsmartcontracts.com\" target=\"_blank\">wpsmartcontracts.com</a> for more information"]
		] as $notice ) {
			echo '<p><b>&raquo; ' . $notice[0] . '</b>';
			echo '<br>' . $notice[1] . '</p>';
		}
		echo '</div>';
		update_option( 'wpsc_onetime_notice_1', 1 );
	});
}
