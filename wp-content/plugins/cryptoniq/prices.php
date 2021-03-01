<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

function cryptoniq_get_currency_prices() {
	$license = get_option( 'cryptoniq_license' );
	$key = '';
	
	if ( $license && is_array( $license ) ) {
		if ( $license['key'] ) {
			$key = '?key=' . $license['key'];
		}
	}
	
	$url = 'https://api.cryptoniq.io/currencies' . $key;
    $request = wp_remote_get( $url );
		
	if( is_wp_error( $request ) ) {
		return false; 
	}

	$body = wp_remote_retrieve_body( $request );
	$coinslist = json_decode( $body, true );
	
	return $coinslist;
}

function cryptoniq_get_coin_prices() {
	$license = get_option( 'cryptoniq_license' );
	$key = '';
	
	if ( $license && is_array( $license ) ) {
		if ( $license['key'] ) {
			$key = '?key=' . $license['key'];
		}
	}
	
	$url = 'https://api.cryptoniq.io/prices' . $key;
	$request = wp_remote_get( $url );
	
	if( is_wp_error( $request ) ) {
		return false; 
	}

	$body = wp_remote_retrieve_body( $request );
	$pricelist = json_decode( $body, true );
		
	return $pricelist;
}
	
function cryptoniq_get_price( $coin = 'BTC', $amount = '', $currency = '', $random = 'no' ) {
	if ( $amount == 0 ) {
		return '0.00000000';
	}
		
	$base_currency = get_woocommerce_currency();
	$currency_prices = get_option( 'cryptoniq_currency_prices' );
	$coin_prices = get_option( 'cryptoniq_coin_prices' );
		
	if ( $currency && array_key_exists( $currency, $currency_prices ) ) {
		$amount = $amount / $currency_prices[$currency];
	} elseif ( $base_currency && $base_currency != 'USD' && array_key_exists( $base_currency, $currency_prices ) ) {
		$amount = $amount / $currency_prices[$base_currency];
	}
	
	if ( !empty( $coin ) && ( $coin == 'BTC' || $coin == 'ETH' || $coin == 'DOGE' || $coin == 'LTC' || $coin == 'ZEC' || $coin == 'DASH' ) ) {
		$price = $amount / $coin_prices[$coin];
	} else {
		return;
	}
	
	// Calc Formula		
	$extra_random_number = ( $random == 'yes' ) ? rand( 100, 999 ) / 100000000 : 0;
	$coins_num = $price + $extra_random_number;
	$coins_num = number_format( (float) $coins_num, 8, '.', '' );
		
	return $coins_num;
}