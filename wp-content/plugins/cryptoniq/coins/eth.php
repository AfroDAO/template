<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}
	
function cryptoniq_coin_txs_eth( $wallet = '', $amount = '', $id = '', $print = '' )
{		
	$api_change = cryptoniq_get_option( 'apikey_eth' );
	$api_def = 'Z3RSPYD5E7Y84C7REJEA4HPVGFWW85CXTD';
	$key = $api_change ? $api_change : $api_def; 
		
	$url = 'http://api.etherscan.io/api?module=account&action=txlist&sort=desc&page=1&offset=100&apikey=' . $key . '&address=' . $wallet;
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

	$result = $content['result'];  
		
	$order = wc_get_order( $id );
	if ( empty( $order ) || $order->get_status() == 'failed' || $order->get_status() == 'cancelled' ) {
		return; 
	}		     
			
	// time props
	$time_start = get_post_meta( $id, 'cryptoniq_time_start', true );
	$time_counter = 60 * 60 * 12;
	$time_little_frame = 60 * 30;
	$time_end = $time_start + $time_counter;
	$time_current = current_time( 'timestamp', 0 );
 
	// number without decimal
	$num_without_decimal = str_replace( '.', '', $amount );

	// number after decimal
	if ( strpos( $amount, '.' ) !== false ) {
    	$num_after_decimal = explode( '.', $amount );
    	$num_after_decimal = strlen( $num_after_decimal[1] );
	} else {
    	$num_after_decimal = 0;
	}

	// number multiply
	$num_multiply = 18 - $num_after_decimal;

	// number final
	$num_final = $num_without_decimal * pow( 10, $num_multiply );  
			
	if ( $result ) {
    	if ( is_array( $result ) ) {
    		foreach ( $result as $index => $tx ) {
           		if ( $tx['value'] == $num_final && $wallet == $tx['to'] ) {							
               		$output = array(
                  		'found' => 1,
						'tx' => $tx['hash'],
                   		'conf' => $tx['confirmations'],
                   		'completed' => 0,
						'expire' => 0
                	);
                
					// check it time expired
					if ( $tx['timeStamp'] < ( $time_start - $time_little_frame ) || $time_current > $time_end || cryptoniq_check_txid( $tx['hash'], $id ) == 1 ) {
						if ( $time_current > $time_end ) {
							// time left
							$output['expire'] = 1;
									
							// update status
							Cryptoniq_AJAX::order_status_update( $id, 'failed' );
						} elseif ( $tx['timeStamp'] < ( $time_start - $time_little_frame ) || cryptoniq_check_txid( $tx['hash'], $id ) == 1 ) {
							// old transaction
							$output['expire'] = -1;
						}
					} else {
						if ( $tx['confirmations'] >= 1 ) {
							// update tx meta field
							cryptoniq_tx_data_update( $tx['hash'], $id );						
						}
						
                		if ( $tx['confirmations'] >= 6 && $tx['txreceipt_status'] == 1 ) {							
							$output['completed'] = 1;
									
							// update status
							Cryptoniq_AJAX::order_status_update( $id, 'processing' );

							$output['status'] = 1;
							$output['redirect'] = WC_Payment_Gateway::get_return_url( $order );
                		}
					}
           		}
       		}
    	} else {
           	if ( $result['value'] == $num_final && $wallet == $result['to'] ) {						
               	$output = array(
					'found' => 1,
					'tx' => $result['hash'],
                   	'conf' => $result['confirmations'],
                   	'completed' => 0,
					'expire' => 0
                );
                
				// check it time expired
				if ( $tx['timeStamp'] < ( $time_start - $time_little_frame ) || $time_current > $time_end || cryptoniq_check_txid( $result['hash'] ) == 1 ) {
					if ( $time_current > $time_end ) {
						// time left
						$output['expire'] = 1;
								
						// update status
						Cryptoniq_AJAX::order_status_update( $id, 'failed' );
					} elseif ( $tx['timeStamp'] < ( $time_start - $time_little_frame ) || cryptoniq_check_txid( $result['hash'], $id ) == 1 ) {
						// old transaction
						$output['expire'] = -1;
					}
				} else {
					if ( $result['confirmations'] >= 1 ) {
						// update tx meta field
						cryptoniq_tx_data_update( $result['hash'], $id );						
					}
					
                	if ( $result['confirmations'] >= 6 && $result['txreceipt_status'] == 1 ) {						
						$output['completed'] = 1;								
								
						// update status
						Cryptoniq_AJAX::order_status_update( $id, 'processing' );

						$output['status'] = 1;
						$output['redirect'] = WC_Payment_Gateway::get_return_url( $order );
					}
                }
           	}
    	}
	} else {
    	$output = $empty;
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