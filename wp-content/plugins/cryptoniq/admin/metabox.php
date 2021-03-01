<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

// Add Custom Meta Box For Orders
// ======================================================

function cryptoniq_custom_meta_box() {
    add_meta_box( 'cryptoniq-meta-box', 'Cryptoniq', 'cryptoniq_custom_meta_box_content', 'shop_order', 'advanced', 'high', null );
}
add_action( 'add_meta_boxes', 'cryptoniq_custom_meta_box' );

function cryptoniq_custom_meta_box_content( $object ) {
    wp_nonce_field( basename(__FILE__), 'cryptoniq-meta-box-nonce' );
?>
        <div>
			<label for="cryptoniq-coin-name-field"><?php esc_html_e( 'Coin name', 'cryptoniq' ); ?></label>
			<p><input class="widefat" id="cryptoniq-coin-name-field" name="cryptoniq_coin_name" type="text" value="<?php echo get_post_meta( $object->ID, 'cryptoniq_coin_name', true ); ?>"></p>
		</div>

        <div>
			<label for="cryptoniq-coin-amount-field"><?php esc_html_e( 'Amount of coins', 'cryptoniq' ); ?></label>
			<p><input class="widefat" id="cryptoniq-coin-amount-field" name="cryptoniq_coin_amount" type="text" value="<?php echo get_post_meta( $object->ID, 'cryptoniq_coin_amount', true ); ?>"></p>
		</div>

        <div style="display: none;">
			<label for="cryptoniq-step-field"><?php esc_html_e( 'Step', 'cryptoniq' ); ?></label>
			<p><input class="widefat" id="cryptoniq-step-field" name="cryptoniq_step" type="text" value="<?php echo get_post_meta( $object->ID, 'cryptoniq_step', true ); ?>"></p>
		</div>

        <div>
			<label for="cryptoniq-time-start-field"><?php esc_html_e( 'Start time', 'cryptoniq' ); ?></label>
			<p><input class="widefat" id="cryptoniq-time-start-field" name="cryptoniq_time_start" type="text" value="<?php echo get_post_meta( $object->ID, 'cryptoniq_time_start', true ); ?>"></p>
		</div>

        <div>
			<label for="cryptoniq-tx-field"><?php esc_html_e( 'Transaction ID', 'cryptoniq' ); ?></label>
			<p><input class="widefat" id="cryptoniq-tx-field" name="cryptoniq_tx" type="text" value="<?php echo get_post_meta( $object->ID, 'cryptoniq_tx', true ); ?>"></p>
		</div>

        <div>
			<label for="cryptoniq-wallet-field"><?php esc_html_e( 'Address', 'cryptoniq' ); ?></label>
			<p><input class="widefat" id="cryptoniq-wallet-field" name="cryptoniq_wallet" type="text" value="<?php echo get_post_meta( $object->ID, 'cryptoniq_wallet', true ); ?>"></p>
		</div>
<?php }