<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

function cryptoniq_coin_txs_zec( $wallet = '', $amount = '', $id = '', $print = '' )
{			
	$url = 'https://chain.so/api/v2/address/ZEC/' . $wallet;
	$request = wp_remote_get( $url, array( 
		'timeout' => 100, 'sslverify' => false
	) );
		
	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
		return;
	}
		
	$body = wp_remote_retrieve_body( $request );
	$content = json_decode( $body, true );
		
	$empty = 0;
	$output = array( 'found' => 0 );
		
	$order = wc_get_order( $id );
	if ( empty( $order ) || $order->get_status() == 'failed' || $order->get_status() == 'cancelled' ) {
		return; 
	}			
			
	// time props
	$time_start = get_post_meta( $id, 'cryptoniq_time_start', true );
	$time_counter = 60 * 60 * 12;
	$time_little_frame = 60 * 30;
	$time_new_start = $time_start - $time_little_frame;
	$time_end = $time_start + $time_counter;
	$time_current = current_time( 'timestamp', 0 );
		   
	// amount make 8 numbers after digit
	$amount = str_replace( ',', '', $amount );
	$amount = number_format( (float)$amount, 8, '.', '' );
		
	$txs = $content['data']['txs'];
			
	if ( is_array( $txs ) ) { 
		foreach ( $txs as $tx ) {
        	if ( !empty( $tx['incoming']['value'] ) && $tx['incoming']['value'] == $amount ) {						
				$output = array(
					'found' => 1,
					'tx' => $tx['txid'],
					'conf' => $tx['confirmations'],
					'completed' => 0,
					'expire' => 0
				);				
						
				// check if the time expired
				if ( $tx['time'] < $time_new_start || $time_current > $time_end || cryptoniq_check_txid( $tx['txid'], $id ) == 1 ) {
					if ( $time_current > $time_end ) {
						// time left
						$output['expire'] = 1;
								
						// update status
						Cryptoniq_AJAX::order_status_update( $id, 'failed' );								
					} elseif ( $tx['time'] < $time_new_start || cryptoniq_check_txid( $tx['txid'], $id ) == 1 ) {
						// old transaction
						$output['expire'] = -1;								
					}					
				} else {
					if ( $tx['confirmations'] >= 1 ) {
						// update tx meta field
						cryptoniq_tx_data_update( $tx['txid'], $id );						
					}
					
					if ( $tx['confirmations'] >= 2 ) {						
						$output['completed'] = 1;	
								
						// update status
						Cryptoniq_AJAX::order_status_update( $id, 'processing' );

						$output['status'] = 1;
						$output['redirect'] = WC_Payment_Gateway::get_return_url( $order );
					}
				}
			}
		}
	}
		
	if ( $time_current > $time_end && $output['found'] != 1 ) {
		// time left
		$output['expire'] = 1;
								
		// update status
		Cryptoniq_AJAX::order_status_update( $id, 'failed' );					
	}
		
	if ( $print != 'no' ) {
		print_r( json_encode( $output ) );
	}
}