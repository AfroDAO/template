<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}
 
if ( cryptoniq_get_option('price_coin_show') == 1 ) {
	add_filter( 'woocommerce_get_price_html', 'cryptoniq_item_price_filter', 10, 3 );
	add_filter( 'woocommerce_cart_item_price', 'cryptoniq_cart_item_price_filter', 10, 3 );
	add_filter( 'woocommerce_cart_subtotal', 'cryptoniq_subtotal_filter', 10, 3 );
	add_filter( 'woocommerce_cart_total', 'cryptoniq_total_filter', 10, 3 );
}
	
// Get coin product infos
// ======================================================

function cryptoniq_cpi( $select = 'coin', $type = 'text', $tag = 'no', $coin = 'BTC', $change = 'no', $divider = '/', $method = '' ) {
	if ( $change != 'yes' ) {
		$coin = cryptoniq_get_option( 'price_coin_name' ) ? cryptoniq_get_option( 'price_coin_name' ) : $coin;
	}
		 
	$divider = cryptoniq_get_option( 'price_coin_divider' ) ? cryptoniq_get_option( 'price_coin_divider' ) : $divider;
	$divider_final = '<span class="cryptoniq-product-divider">' . $divider . '</span>';
	
    $tag_class = 'cryptoniq-coin-tag';
		
	if ( $select == 'divider' ) {
		$data = $divider_final;
	} else {
		if ( $type == 'icon' ) {
			$data = '<i class="cryptoniq-product-list-icon cryptoniq-product-list-icon-' . strtolower( $coin ) . '"></i>';
		} elseif ( $type == 'none' ) {
			$data = '';
		} elseif ( $type == 'text_space' ) {
			$data = ( $tag == 'yes' ) ? '<span class="' . $tag_class . '">' . $coin . '</span> ' : $coin . ' ';
		}  elseif ( $type == 'text_dots' ) {
			$data = ( $tag == 'yes' ) ? '<span class="' . $tag_class . '">' . $coin . '</span>: ' : $coin . ': ';
		} else {
			$data = ( $tag == 'yes' ) ? '<span class="' . $tag_class . '">' . $coin . '</span>' : $coin;
		}
	}
	
	return $data;
}

function cryptoniq_ctags( $set = 'type' ) {
	$type = cryptoniq_get_option( 'price_coin_sign' ) ? cryptoniq_get_option( 'price_coin_sign' ) : 'text_space';
	if ( $type == 'text' ) $type = 'text_space';	
	
	if ( $set = 'type' ) {
		return $type;
	} else {
		return ( $type == 'text_space' ) ? 'yes' : 'no';	
	}
}

function cryptoniq_item_price_filter( $price, $product ){
	if ( $product->sale_price > 1 ) {
		$priceq = $product->regular_price;
	} else {
		$priceq = null;
	}
		
	$prices = $product->price; 
	$af = strip_tags( $price );
	$s = strlen( $af ) / 2;
	$divider = cryptoniq_cpi( 'divider' );
		
	$aff = '';
	if ( $s > 10 ) {
		$aff = substr( $af, -5 );
	}

	if ( $product->sale_price != null and $product->sale_price > 1 ) { 
		$res = $price . cryptoniq_cpi( 'divider' ) . '<del>' . cryptoniq_cpi( 'coin', cryptoniq_ctags( 'type' ), cryptoniq_ctags( 'tag' ) ) . cryptoniq_get_price( cryptoniq_cpi( 'coin', 'text' ), $priceq ) . '</del>  ' . '<ins>' . cryptoniq_cpi( 'coin', cryptoniq_ctags( 'type' ), cryptoniq_ctags( 'tag' ) ) . cryptoniq_get_price( cryptoniq_cpi( 'coin', 'text' ), $prices ) . '</ins>';
	} else {
		$res =  $price . cryptoniq_cpi( 'divider' ) . cryptoniq_cpi( 'coin', cryptoniq_ctags( 'type' ), cryptoniq_ctags( 'tag' ) ) . cryptoniq_get_price( cryptoniq_cpi( 'coin', 'text' ), $prices );
	}
		
	if ( $aff != '' and $product->sale_price < 2 ) {
		return $res . ' – '. cryptoniq_cpi( 'coin', cryptoniq_ctags( 'type' ), cryptoniq_ctags( 'tag' ) ) . cryptoniq_get_price( cryptoniq_cpi( 'coin', 'text' ), $aff );
	} else {
		return $res;
	}
}

function cryptoniq_cart_item_price_filter( $price, $cart_item, $cart_item_key ) {
	$id = $cart_item['product_id'];
	$product = wc_get_product( $id );
	
	$price .= cryptoniq_cpi( 'divider' ) . cryptoniq_cpi( 'coin', cryptoniq_ctags( 'type' ), cryptoniq_ctags( 'tag' )) . cryptoniq_get_price( cryptoniq_cpi( 'coin', 'text' ), $product->get_price() ) ; 		

	return $price;	
}

function cryptoniq_subtotal_filter( $price ) {
	$divider = cryptoniq_get_option( 'price_coin_divider' ) ? cryptoniq_get_option( 'price_coin_divider' ) : $divider;
	$divider_final = '<b class="cryptoniq-product-divider">' . $divider . '</b>';
		
    $price .= $divider_final . cryptoniq_cpi( 'coin', cryptoniq_ctags( 'type' ), cryptoniq_ctags( 'tag' ) ) . cryptoniq_get_price( cryptoniq_cpi( 'coin', 'text' ), WC()->cart->subtotal );
		
    return $price;
}

function cryptoniq_total_filter( $price ) {
	$divider = cryptoniq_get_option( 'price_coin_divider' ) ? cryptoniq_get_option( 'price_coin_divider' ) : $divider;
	$divider_final = '<b class="cryptoniq-product-divider">' . $divider . '</b>';
		
    $price .= $divider_final . cryptoniq_cpi( 'coin', cryptoniq_ctags( 'type' ), cryptoniq_ctags( 'tag' ) ) . cryptoniq_get_price( cryptoniq_cpi( 'coin', 'text' ), WC()->cart->total );
		
    return $price;
}