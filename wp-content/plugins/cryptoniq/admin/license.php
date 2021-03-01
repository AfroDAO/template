<?php

// License Class
// ======================================================

class Cryptoniq_License {
	private static $bearer = 'gocM2Bx8fyiLTL3ABp1OJCFL3Zaqnu0P';
	private static $cryptoniq_license = array(
		'valid' => null,
		'key' => null
	);
	
    function __construct() {
        $this->option_create();
    }	
	
	static function getPurchaseData( $code ) {
		// headers
		$bearer   = 'bearer ' . self::$bearer;
		$header   = array();
		$header[] = 'Content-length: 0';
		$header[] = 'Content-type: application/json; charset=utf-8';
		$header[] = 'Authorization: ' . $bearer;
		
		$verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:' . $code . '.json';
		$ch_verify = curl_init( $verify_url . '?code=' . $code );
		
		curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
		
		$cinit_verify_data = curl_exec( $ch_verify );
		curl_close( $ch_verify );

		if ( $cinit_verify_data != '' ) {
			return json_decode( $cinit_verify_data );
		} else {
			return false;
		}
	}
	
	public function option_create() {
		$all_options = wp_load_alloptions();
	
		if ( !array_key_exists( 'cryptoniq_license', $all_options ) ) {
    		add_option( 'cryptoniq_license', self::$cryptoniq_license, '', 'yes' );
		}
	}
	
	static function option_update( $code ) {
		if ( self::check( $code ) === 1 ) {
			$new_license = array(
				'valid' => 1,
				'key' => $code		
			);
			
			update_option( 'cryptoniq_license', $new_license, 'yes' );
		} else {
			update_option( 'cryptoniq_license', self::$cryptoniq_license, 'yes' );
		}
	}
		
	static function check( $code ) {
		$verify_obj = self::getPurchaseData($code);
		
		if ( ( false === $verify_obj ) || !is_object( $verify_obj ) || !isset( $verify_obj->{'verify-purchase'} ) || !isset( $verify_obj->{'verify-purchase'}->item_name ) ) {
      		return -1;
		}
		
    	if ( $verify_obj->{'verify-purchase'}->item_id == 22419379 && ( $verify_obj->{'verify-purchase'}->supported_until == '' || $verify_obj->{'verify-purchase'}->supported_until != null ) ) {
			return 1;  
		}
    
		return 0;
	}
}

new Cryptoniq_License();
	

// Add Custom Validate Callback
// ======================================================

function cryptoniq_license_validate( $field, $value, $existing_value ) {
	$error = true;
	$field['msg'] = esc_html__( 'Invalid License Key', 'cryptoniq' );
	$license = Cryptoniq_License::check( $value );
	Cryptoniq_License::option_update( $value );
	
	if ( $license === 1 ) {
		$error = false;
		$return['value'] = $value;
	}
	
	if ( $error == true ) {
		$return['error'] = $field;
	}
		
    return $return;
}