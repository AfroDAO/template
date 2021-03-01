<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$license = get_option('cryptoniq_license');
$order_id = $order->get_id();
$timestamp = get_post_meta( $order_id, 'cryptoniq_time_start', true );
$wallet = get_post_meta( $order_id, 'cryptoniq_wallet', true );
$step = get_post_meta( $order_id, 'cryptoniq_step', true );
$coins_final_num = get_post_meta( $order_id, 'cryptoniq_coin_amount', true );
$coin = get_post_meta( $order_id, 'cryptoniq_coin_name', true );
$coin = ( $coin == 'BTC' || $coin == 'ETH' || $coin == 'LTC' || $coin == 'DOGE' || $coin == 'ZEC' || $coin == 'DASH' ) ? $coin : 'BTC';

if ( $coin == "ETH" ) {
	$coin_wallets = cryptoniq_get_option( 'wallets_eth' );
	$conf_num = 6;
} elseif ( $coin == "DOGE" ) { 
	$coin_wallets = cryptoniq_get_option( 'wallets_doge' );
	$conf_num = 2;
} elseif ( $coin == "LTC" ) { 
	$coin_wallets = cryptoniq_get_option( 'wallets_ltc' );
	$conf_num = 2;
}  elseif ( $coin == "ZEC" ) { 
	$coin_wallets = cryptoniq_get_option( 'wallets_zec' );
	$conf_num = 2;
}  elseif ( $coin == "DASH" ) { 
	$coin_wallets = cryptoniq_get_option( 'wallets_dash' );
	$conf_num = 2;
} else {
	$coin_wallets = cryptoniq_get_option( 'wallets_btc' );
	$conf_num = 2;
}

$empty = 1;
if ( is_array( $coin_wallets ) ) {
	$wallet_first = preg_replace( '/[^A-Za-z0-9]/', '', $coin_wallets[0] );
	if ( $wallet_first && $wallet ) {
		$empty = -1;
	}
}

if ( $empty === 1 ) {
	$message = esc_html( 'No wallet for', 'cryptoniq' );
	return wc_print_notice( $message . ' ' . $coin, 'error' );
}

if ( $order->get_status() == 'failed' ) {
	$message = esc_html( 'Payment failed', 'cryptoniq' );
	return wc_print_notice( $message, 'error' );
}

$license_activated = -1;
if ( $license && is_array( $license ) ) {
	if ( $license['valid'] == 1 ) {
		$license_activated = 1;
	}
}

if ( $license_activated === -1 ) {
	$message = esc_html( 'License is not activated', 'cryptoniq' );
	return wc_print_notice( $message, 'error' );	
}

?>

<div class="cryptoniq-paybox-layer cryptoniq-paybox-layer-mask cryptoniq-clearfix">
	<div class="cryptoniq-paybox-preloader">
		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube1"></div>
  		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube2"></div>
  		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube3"></div>
  		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube4"></div>
  		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube5"></div>
  		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube6"></div>
  		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube7"></div>
  		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube8"></div>
  		<div class="cryptoniq-paybox-cube cryptoniq-paybox-cube9"></div>		
	</div>
	<div id="cryptoniq-paybox" class="cryptoniq-paybox cryptoniq-border-box cryptoniq-clearfix">
		<div class="cryptoniq-paybox-topbar">
			<div class="cryptoniq-paybox-topbar-wrapper cryptoniq-clearfix">
				<div class="cryptoniq-paybox-topbar-left">
					<?php if ( $step != 2 ) { ?>
					<div id="cryptoniq-paybox-back" class="cryptoniq-paybox-back">
						<a href="<?php echo wc_get_page_permalink( 'checkout' ); ?>"><i></i></a>
					</div>
					<?php } ?>
					<div class="cryptoniq-paybox-heading"><span class="cryptoniq-paybox-heading-pre"><?php esc_html_e( 'Payment via', 'cryptoniq' ); ?></span> <?php if ( $coin == 'ETH' ) { ?>Ethereum<?php } elseif ( $coin == 'DOGE' ) { ?>Dogecoin<?php } elseif ( $coin == 'LTC' ) { ?>Litecoin<?php } elseif ( $coin == 'ZEC' ) { ?>Zcash<?php } elseif ( $coin == 'DASH' ) { ?>Dash<?php } else { ?>Bitcoin<?php } ?></div>
				</div>
				<div class="cryptoniq-paybox-topbar-right">
					<div id="cryptoniq-paybox-loader" class="cryptoniq-paybox-loader">
 						<div class="cryptoniq-paybox-rect1"></div><div class="cryptoniq-paybox-rect2"></div><div class="cryptoniq-paybox-rect3"></div><div class="cryptoniq-paybox-rect4"></div><div class="cryptoniq-paybox-rect5"></div>
					</div>
					<div class="cryptoniq-paybox-coin-icon cryptoniq-paybox-coin-icon-<?php echo strtolower( $coin ); ?>"><i></i></div>
				</div>
			</div>
		</div>
		<div class="cryptoniq-paybox-container">
			<div class="cryptoniq-paybox-section-left">
				<div class="cryptoniq-paybox-section-content">
					<div class="cryptoniq-paybox-section-title">
						<div class="cryptoniq-paybox-section-title-left cryptoniq-paybox-section-title-mask"><?php esc_html_e( 'My Cart', 'cryptoniq' ); ?></div>
					</div>
					<div class="cryptoniq-paybox-items-list">
						<ul>
<?php
    // check if 'woocommerce_thumbnail' size is available			
	$thumbnail_size = in_array( 'woocommerce_thumbnail', get_intermediate_image_sizes() ) ? 'woocommerce_thumbnail' : 'thumbnail';

    $cart_items = $order->get_items();
	if ( is_array( $cart_items ) ) {
    	foreach ( $cart_items as $cart_item_key => $cart_item ) {
			$id = $cart_item['product_id'];
			$product = wc_get_product( $id );
?> 
							<li>
								<div class="cryptoniq-paybox-item-thumbnail"><a target="_blank" href="<?php echo get_the_permalink( $id ); ?>"><img src="<?php echo get_the_post_thumbnail_url( $id, $thumbnail_size ); ?>" /></a>
									<?php if ( $cart_item['quantity'] > 1 ) { ?>
										<span class="cryptoniq-paybox-item-quantity">x<?php echo $cart_item['quantity']; ?></span>
									<?php } ?>
								</div>
								<div class="cryptoniq-paybox-item-details">
									<div class="cryptoniq-paybox-item-title"><a target="_blank" href="<?php echo get_the_permalink( $id ); ?>"><?php echo $product->get_title(); ?></a></div>
									<div class="cryptoniq-paybox-item-price-coin"><?php echo cryptoniq_cpi( 'coin', 'text_dots', 'yes', $coin, 'yes' ) . cryptoniq_get_price( $coin, $product->get_price() ); ?></div>
									<div class="cryptoniq-paybox-item-price-fiat"><?php echo wc_price( $product->get_price() ); ?></div>
								</div>
							</li>
<?php } } ?>		
						</ul>
					</div>
				</div>
			</div>
			<div class="cryptoniq-paybox-section-right">
				<div class="cryptoniq-paybox-section-content">
					<div class="cryptoniq-paybox-data">
						<div class="cryptoniq-paybox-section-title cryptoniq-clearfix">
							<div class="cryptoniq-paybox-section-title-left cryptoniq-paybox-section-title-mask"><?php esc_html_e( 'How to pay', 'cryptoniq' ); ?></div>
							<div id="cryptoniq-paybox-tooltip-button" class="cryptoniq-paybox-section-title-right"><i></i></div>
						</div>
						<div class="cryptoniq-paybox-order-details">
							<ul>
								<li>
									<div class="cryptoniq-paybox-order-title"><?php esc_html_e( 'Please, send', 'cryptoniq' ); ?></div>
									<div class="cryptoniq-paybox-order-text">
										<span class="cryptoniq-paybox-order-text-mask"><?php echo $coin . ': ' . $coins_final_num; ?></span><span class="cryptoniq-paybox-order-price-fiat"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
									</div>
								</li>
								<li>
									<div class="cryptoniq-paybox-order-title"><?php esc_html_e( 'To this address', 'cryptoniq' ); ?> <i id="cryptoniq-paybox-address-copy" data-clipboard-target="#cryptoniq-paybox-address"></i></div>
									<div id="cryptoniq-paybox-address" class="cryptoniq-paybox-order-text cryptoniq-paybox-order-text-address">
										<span class="cryptoniq-paybox-order-text-mask"><?php echo $wallet; ?></span>
									</div>
								</li>
								<li>
									<div class="cryptoniq-paybox-order-title"><?php esc_html_e( 'Scan QR code', 'cryptoniq' ); ?></div>
									<div id="cryptoniq-paybox-qrcode" class="cryptoniq-paybox-order-text cryptoniq-paybox-qrcode"></div>
								</li>
							</ul>
						</div>
						<div class="cryptoniq-paybox-instructions">
							<div class="cryptoniq-paybox-instructions-container">
								<div class="cryptoniq-paybox-instructions-topbar cryptoniq-clearfix">
									<div class="cryptoniq-paybox-instructions-title"><?php esc_html_e( 'Instruction', 'cryptoniq' ); ?></div>
									<div id="cryptoniq-paybox-instructions-close"><i></i></div>
								</div>
								<div class="cryptoniq-paybox-instructions-content">
									<p><?php esc_html_e( 'To make a payment, send required amount of coins to mentioned address. It must be as one transaction (not included the fees).', 'cryptoniq' ); ?></p>
									<p><?php esc_html_e( 'After, click on "Yes, I\'ve sent" button and wait the system to check your payment.', 'cryptoniq' ); ?></p>
									<p><?php esc_html_e( 'System will give you 3 hours, in which time the transaction must be completed. So if you haven\'t sent the coins before clicking the check button, you still can. But note that if the coins are not received within a given time, order will be automatically cancelled and payment will be failed.', 'cryptoniq' ); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="cryptoniq-paybox-bottombar">
			<div class="cryptoniq-paybox-bottombar-left">
				<div class="cryptoniq-paybox-price-bar">
					<div class="cryptoniq-paybox-price-bar-content cryptoniq-clearfix">
						<div class="cryptoniq-paybox-price-coin"><?php echo $coin . ': ' . $coins_final_num; ?></div>
						<div class="cryptoniq-paybox-price-fiat"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></div>
					</div>
				</div>
			</div>
			<div class="cryptoniq-paybox-bottombar-right">
				<div id="cryptoniq-paybox-tx-bar" class="cryptoniq-paybox-tx-form <?php if ( $step != 2 ) { ?>cryptoniq-paybox-tx-sending<?php } ?>" data-id="<?php echo $order_id; ?>" data-coin="<?php echo $coin; ?>" data-step="<?php echo $step; ?>" data-time="<?php echo $timestamp; ?>">
					<?php if ( $step != 2 ) { ?>
						<div id="cryptoniq-paybox-check-button" class="cryptoniq-paybox-check-button"><?php esc_html_e( 'Yes, I\'ve sent', 'cryptoniq' ); ?></div>
					<?php } ?>
					<div id="cryptoniq-paybox-tx-checker" class="cryptoniq-paybox-tx-check-bar">
						<div class="cryptoniq-paybox-tx-content">
							<div class="cryptoniq-paybox-pg-bar"><div id="cryptoniq-paybox-tx-pg-pc" class="cryptoniq-paybox-pg-bar-width"></div></div>
							<div class="cryptoniq-paybox-tx-info">
								<div class="cryptoniq-paybox-line-conf">Confirmations: <span id="cryptoniq-paybox-tx-conf-num">0</span> / <?php echo $conf_num; ?> <span id="cryptoniq-paybox-txid"><a target="_blank" href="#"></a></span></div>
								<div class="cryptoniq-paybox-line-st-time">
									<span id="cryptoniq-paybox-alert" class="cryptoniq-paybox-alert"><?php esc_html_e( 'Checking...', 'cryptoniq' ); ?></span> / <?php esc_html_e( 'Time:', 'cryptoniq' ); ?> <span id="cryptoniq-paybox-tx-counter">00:00:00</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>