<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

if ( !class_exists( 'Redux' ) ) {
	return;
}

if ( !empty( Redux::getOption( 'cryptoniq_option', 'cryptoniq_panel_activate' ) ) && Redux::getOption( 'cryptoniq_option', 'cryptoniq_panel_activate' ) != 'yes' ) {
	return;
}

$opt_name = 'cryptoniq_option';

// Set Arguments
// ======================================================

Redux::setArgs( $opt_name,array(
	'opt_name' => $opt_name,
	'display_name' => 'Cryptoniq',
	'display_version' => CRYPTONIQ_VER,
	'menu_type' => 'menu',
	'allow_sub_menu' => true,
	'menu_title' => 'Cryptoniq',
	'page_title' => 'Cryptoniq',
	'google_api_key' => '',
	'google_update_weekly' => false,
	'async_typography' => true,
	'admin_bar' => true,
	'admin_bar_icon' => 'dashicons-portfolio',
	'admin_bar_priority' => 51,
	'global_variable' => '',
	'dev_mode' => false,
	'show_options_object' => false,
	'update_notice' => true,
	'customizer' => false,
	'page_priority' => 51,
	'page_parent' => 'themes.php',
	'page_permissions' => 'manage_options',
	'menu_icon' => '',
	'last_tab' => '',
	'page_icon' => 'icon-themes',
	'page_slug' => 'cryptoniq',
	'save_defaults' => true,
	'default_show' => false,
	'default_mark' => '',
	'show_import_export' => true,
	'transient_time' => 60 * MINUTE_IN_SECONDS,
	'output' => true,
	'output_tag' => false,
	'database' => '',
	'use_cdn' => true,
	'hints' => array(
		'icon' => 'el el-question-sign',
		'icon_position' => 'right',
		'icon_color' => 'lightgray',
		'icon_size' => 'normal',
		'tip_style' => array(
			'color' => 'red',
			'shadow' => true,
			'rounded' => false,
			'style' => '',
		),
		'tip_position' => array(
			'my' => 'top left',
			'at' => 'bottom right',
		),
		'tip_effect' => array(
			'show' => array(
				'effect' => 'slide',
				'duration' => '500',
				'event' => 'mouseover',
			) ,
			'hide' => array(
				'effect' => 'slide',
				'duration' => '500',
				'event' => 'click mouseleave',
			),
		),
	)
) );

// Add Sections
// ======================================================

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Payment', 'cryptoniq' ),
	'id' => 'tab_payment',
	'icon' => 'icon ion-md-cart',
	'fields' => array(
		array(
			'id' => 'payment_coins',
			'type' => 'select',
			'multi' => true,
			'sortable' => true,
			'title' => esc_html__( 'Payment Coins', 'cryptoniq' ),
			'options' => array(
				'BTC' => 'BTC',
				'ETH' => 'ETH',
				'LTC' => 'LTC',
				'DOGE' => 'DOGE',
				'ZEC' => 'ZEC',
				'DASH' => 'DASH',
			) ,
			'default' => array(	'BTC', 'ETH', 'LTC', 'DOGE', 'ZEC', 'DASH' )
		),
		array(
			'id' => 'order_status',
			'type' => 'select',
			'title' => esc_html__( 'Order Status', 'cryptoniq' ),
			'subtitle' => esc_html__( 'Select status for order after completed payment', 'cryptoniq' ),
			'options' => array(
				'processing' => 'Processing',
				'completed' => 'Completed',
				'on-hold' => 'On Hold'
			) ,
			'default' => 'processing',
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Wallets', 'cryptoniq' ),
	'desc' => sprintf( esc_html__( 'Add your wallets addresses for each payment coin. Try to add as much as possible. Read more %1$s.', 'cryptoniq' ) , '<a href="https://divengine.ticksy.com/article/13482/" target="_blank">' . esc_html__( 'here', 'cryptoniq' ) . '</a>' ),
	'id' => 'tab_wallets',
	'icon' => 'icon ion-md-wallet',
	'fields' => array(
		array(
			'id' => 'wallets_btc',
			'type' => 'multi_text',
			'title' => esc_html__( 'Wallet', 'cryptoniq' ) . ': BTC',
			'subtitle' => esc_html__( 'Add your BTC addresses (at least 10-20).', 'cryptoniq' ),
			'add_text' => esc_html__( 'Add address', 'cryptoniq' )
		),
		array(
			'id' => 'wallets_eth',
			'type' => 'multi_text',
			'title' => esc_html__( 'Wallet', 'cryptoniq' ) . ': ETH',
			'subtitle' => esc_html__( 'Add your ETH addresses (at least 10-20).', 'cryptoniq' ),
			'add_text' => esc_html__( 'Add wallet', 'cryptoniq' )
		),
		array(
			'id' => 'wallets_ltc',
			'type' => 'multi_text',
			'title' => esc_html__( 'Wallet', 'cryptoniq' ) . ': LTC',
			'subtitle' => esc_html__( 'Add your LTC addresses (at least 10-20).', 'cryptoniq' ),
			'add_text' => esc_html__( 'Add wallet', 'cryptoniq' )
		),
		array(
			'id' => 'wallets_doge',
			'type' => 'multi_text',
			'title' => esc_html__( 'Wallet', 'cryptoniq' ) . ': DOGE',
			'subtitle' => esc_html__( 'Add your DOGE addresses (at least 10-20).', 'cryptoniq' ),
			'add_text' => esc_html__( 'Add wallet', 'cryptoniq' )
		),
		array(
			'id' => 'wallets_zec',
			'type' => 'multi_text',
			'title' => esc_html__( 'Wallet', 'cryptoniq' ) . ': ZEC',
			'subtitle' => esc_html__( 'Add your ZEC addresses (at least 10-20).', 'cryptoniq' ),
			'add_text' => esc_html__( 'Add wallet', 'cryptoniq' )
		),
		array(
			'id' => 'wallets_dash',
			'type' => 'multi_text',
			'title' => esc_html__( 'Wallet', 'cryptoniq' ) . ': DASH',
			'subtitle' => esc_html__( 'Add your DASH addresses (at least 10-20).', 'cryptoniq' ),
			'add_text' => esc_html__( 'Add wallet', 'cryptoniq' )
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Prices', 'cryptoniq' ),
	'id' => 'tab_prices',
	'icon' => 'icon ion-md-cash',
	'fields' => array(
		array(
			'id' => 'price_coin_show',
			'type' => 'checkbox',
			'title' => esc_html__( 'Show coin prices', 'cryptoniq' ),
			'subtitle' => esc_html__( 'Add coin calculated prices near products prices.', 'cryptoniq' ),
			'default' => '1'
		),
		array(
			'id' => 'price_coin_name',
			'type' => 'select',
			'title' => esc_html__( 'Coin', 'cryptoniq' ),
			'options' => array(
				'BTC' => 'BTC',
				'ETH' => 'ETH',
				'LTC' => 'LTC',
				'DOGE' => 'DOGE',
				'ZEC' => 'ZEC',
				'DASH' => 'DASH'
			),
			'default' => 'BTC',
			'validate' => 'not_empty',
			'required' => array( 'price_coin_show', '=', '1' )
		),
		array(
			'id' => 'price_coin_sign',
			'type' => 'select',
			'title' => esc_html__( 'Sign', 'cryptoniq' ),
			'options' => array(
				'text' => esc_html__( 'Text', 'cryptoniq' ),
				'icon' => esc_html__( 'Icon', 'cryptoniq' ),
				'none' => esc_html__( 'None', 'cryptoniq' )
			),
			'default' => 'icon',
			'validate' => 'not_empty',
			'required' => array( 'price_coin_show',	'=', '1' )
		),
		array(
			'id' => 'price_coin_divider',
			'type' => 'text',
			'title' => esc_html__( 'Divider', 'cryptoniq' ),
			'default' => '/',
			'required' => array( 'price_coin_show',	'=', '1' )
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Titles', 'cryptoniq' ),
	'id' => 'tab_titles',
	'icon' => 'icon ion-md-list-box',
	'fields' => array(
		array(
			'id' => 'description',
			'type' => 'textarea',
			'title' => esc_html__( 'Description', 'cryptoniq' ),
			'subtitle' => esc_html__( 'Write some description for this payment type. It will be shown in \'Checkout\' page.', 'cryptoniq' ),
			'default' => esc_html__( 'Pay with cryptocurrencies.', 'cryptoniq' ),
			'validate' => 'not_empty',
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Api Keys', 'cryptoniq' ),
	'id' => 'tab_keys',
	'icon' => 'icon ion-md-key',
	'fields' => array(
		array(
			'id' => 'apikey_eth',
			'type' => 'text',
			'title' => esc_html__( 'ETH Api Key', 'cryptoniq' ),
			'subtitle' => sprintf( esc_html__( 'See how to get api-key %1$s.', 'cryptoniq' ), '<a href="https://divengine.ticksy.com/article/13483/" target="_blank">' . esc_html__( 'here', 'cryptoniq' ) . '</a>' )
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'License', 'cryptoniq' ),
	'id' => 'tab_license',
	'icon' => 'icon ion-md-key',
	'fields' => array(
		array(
			'id' => 'license_key',
			'type' => 'text',
			'title' => esc_html__( 'License Key', 'cryptoniq' ),
			'subtitle' => sprintf( esc_html__( 'See how to get license key %1$s.', 'cryptoniq' ), '<a href="https://divengine.ticksy.com/article/13647/" target="_blank">' . esc_html__( 'here', 'cryptoniq' ) . '</a>' ),
			'validate_callback' => 'cryptoniq_license_validate'
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Documentation', 'cryptoniq' ),
	'id' => 'tab_doc',
	'icon' => 'icon ion-md-book',
	'desc' => sprintf( esc_html__( 'For documentation please visit %1$s.', 'cryptoniq' ) , '<a href="https://divengine.ticksy.com/articles/100013199/" target="_blank">' . esc_html__( 'here', 'cryptoniq' ) . '</a>' )
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Support', 'cryptoniq' ),
	'id' => 'tab_help',
	'icon' => 'icon ion-md-help-buoy',
	'desc' => sprintf( esc_html__( 'For support please open a ticket %1$s.', 'cryptoniq' ) , '<a href="https://divengine.ticksy.com/submit/" target="_blank">' . esc_html__( 'here', 'cryptoniq' ) . '</a>' )
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Import / Export', 'cryptoniq' ),
	'id' => 'tab_import_export',
	'icon' => 'icon ion-md-git-compare',
	'fields' => array(
		array(
			'id' => 'opt-import-export',
			'type' => 'import_export',
			'full_width' => true,
		),
	),
) );


// Add custom icons
// ======================================================

function cryptoniq_custom_icons()
{
	wp_enqueue_style( 'cryptoniq-libs-ionicons' );
}
add_action( 'redux/page/' . $opt_name . '/enqueue', 'cryptoniq_custom_icons' );