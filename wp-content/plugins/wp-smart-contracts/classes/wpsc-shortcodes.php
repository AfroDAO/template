<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Define and render shortcodes
 */

new WPSC_Shortcodes();

class WPSC_Shortcodes {

    private $templates;

    function __construct() {
        add_shortcode( 'wpsc_coin', [$this, "coin"] );
        add_shortcode( 'wpsc_qr_scanner', [$this, "qrScanner"] );
        add_shortcode( 'wpsc_crowdfunding', [$this, "crowdfunding"] );
        add_shortcode( 'wpsc_ico', [$this, "ico"] );
    }

    public function qrScanner($params) {
        
        $atts = [
            "qr-scanner" => plugins_url( "assets/js/qr-scanner.min.js", dirname(__FILE__) ),
            "qr-scanner-worker" => plugins_url( "assets/js/qr-scanner-worker.min.js", dirname(__FILE__) ),
            "align-camera" => __('Align the QR code with the camera', 'wp-smart-contracts')
        ];

        if (array_key_exists('input', $_GET) and $input = $_GET['input'] and $input_sanitized = sanitize_text_field( $input )) {
            $atts["input-name"] = $input_sanitized;
        }

        $m = new Mustache_Engine;
        return $m->render(WPSC_Mustache::getTemplate('qr-scanner'), $atts);

    }

    public function coin($params) {

        $the_id = self::getPostID($params);

        if (is_array($params) and array_key_exists('id', $params) and $params["id"]) {
            $title = get_the_title(absint($params["id"]));
            $the_id = $params['id'];
        }

        if (!$the_id) {
            $the_id = get_the_ID();
        }

        $wpsc_thumbnail = get_the_post_thumbnail_url($the_id);
        $wpsc_title = get_the_title($the_id);
        $wpsc_content = get_post_field('post_content', $the_id);

        $wpsc_network = get_post_meta($the_id, 'wpsc_network', true);
        $wpsc_txid = get_post_meta($the_id, 'wpsc_txid', true);
        $wpsc_owner = get_post_meta($the_id, 'wpsc_owner', true);
        $wpsc_contract_address = get_post_meta($the_id, 'wpsc_contract_address', true);
        $wpsc_blockie = get_post_meta($the_id, 'wpsc_blockie', true);
        $wpsc_blockie_owner = get_post_meta($the_id, 'wpsc_blockie_owner', true);
        $wpsc_qr_code = get_post_meta($the_id, 'wpsc_qr_code', true);

        list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);

        if ($wpsc_network==77) { // xDai testnet
            $networks = WPSC_helpers::getNetworks();
            $xdai_block_explorer = $networks[$wpsc_network]["url2"]."address/".$wpsc_contract_address;
            $xdai=true;
        }

        $wpsc_social_icon = get_post_meta($the_id, 'wpsc_social_icon', true);
        $wpsc_social_link = get_post_meta($the_id, 'wpsc_social_link', true);
        $wpsc_social_name = get_post_meta($the_id, 'wpsc_social_name', true);

        $m = new Mustache_Engine;

        // initialization
        $wpsc_flavor        = null;
        $wpsc_forkdelta     = null;
        $wpsc_uniswap     = null;
        $wpsc_adv_burn      = null;
        $wpsc_adv_pause     = null;
        $wpsc_adv_mint      = null;
        $wpsc_coin_name     = null;
        $wpsc_coin_symbol   = null;
        $wpsc_coin_decimals = null;
        $wpsc_total_supply  = null;
        $wpsc_adv_cap       = null;
        $atts = [];

        // show contract
        if ($wpsc_contract_address) {

            $wpsc_flavor        = get_post_meta($the_id, 'wpsc_flavor', true);
            $wpsc_forkdelta     = get_post_meta($the_id, 'wpsc_forkdelta', true);
            $wpsc_uniswap       = get_post_meta($the_id, 'wpsc_uniswap', true);

            $wpsc_adv_burn      = get_post_meta($the_id, 'wpsc_adv_burn', true);
            $wpsc_adv_pause     = get_post_meta($the_id, 'wpsc_adv_pause', true);
            $wpsc_adv_mint      = get_post_meta($the_id, 'wpsc_adv_mint', true);
            $wpsc_coin_name     = get_post_meta($the_id, 'wpsc_coin_name', true);
            $wpsc_coin_symbol   = get_post_meta($the_id, 'wpsc_coin_symbol', true);
            $wpsc_coin_decimals = get_post_meta($the_id, 'wpsc_coin_decimals', true);
            $wpsc_total_supply  = WPSC_helpers::formatNumber(get_post_meta($the_id, 'wpsc_total_supply', true));

            $the_cap = get_post_meta($the_id, 'wpsc_adv_cap', true);
            if ($the_cap) {
                $wpsc_adv_cap = WPSC_helpers::formatNumber($the_cap);
            } else {
                $wpsc_adv_cap = __('Unlimited', 'wp-smart-contracts');
            }

            $tokenInfo = [
                "type" => $wpsc_flavor,
                "symbol" => $wpsc_coin_symbol,
                "name" => $wpsc_coin_name,
                "decimals" => $wpsc_coin_decimals,
                "supply" => $wpsc_total_supply,
                "size" => "mini",
                "symbol_label" => __('Symbol', 'wp-smart-contracts'),
                "name_label" => __('Name', 'wp-smart-contracts'),
                "decimals_label" => __('Decimals', 'wp-smart-contracts'),
                "initial_label" => __('Initial Supply', 'wp-smart-contracts'),
                "burnable_label" => __('Burnable', 'wp-smart-contracts'),
                "mintable_label" => __('Mintable', 'wp-smart-contracts'),
                "max_label" => __('Max. cap', 'wp-smart-contracts'),
                "pausable_label" => __('Pausable', 'wp-smart-contracts'),    
            ];
            if ($wpsc_flavor=="chocolate") {
                $tokenInfo["color"] = "brown";
                $tokenInfo["cap"] = $wpsc_adv_cap;
                if ($wpsc_adv_burn) $tokenInfo["burnable"] = true;
                if ($wpsc_adv_mint) $tokenInfo["mintable"] = true;
                if ($wpsc_adv_pause) $tokenInfo["pausable"] = true;
            }
            if ($wpsc_flavor=="vanilla") $tokenInfo["color"] = "yellow";
            if ($wpsc_flavor=="pistachio") $tokenInfo["color"] = "olive";
            $tokenInfo["imgUrl"] = dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/';

            $atts = [
                'ethereum-network' => $network_val,
                'ethereum-color' => $color,
                'ethereum-icon' => $icon,
                'contract-address' => $wpsc_contract_address,
                'etherscan' => $etherscan,
                'contract-address-text' => __('Contract Address', 'wp-smart-contracts'),
                'txid-text' => __('Transaction ID', 'wp-smart-contracts'),
                'owner-text' => __('Owner Account', 'wp-smart-contracts'),
                'token-name' => ucwords(get_post_meta($the_id, 'wpsc_coin_name', true)),
                'token-symbol' => strtoupper(get_post_meta($the_id, 'wpsc_coin_symbol', true)), 
                'qr-code' => $wpsc_qr_code,
                'blockie' => $wpsc_blockie,
                'blockie-owner' => $wpsc_blockie_owner,
                'token-info' => $m->render(WPSC_Mustache::getTemplate('token-info'), $tokenInfo),
                'token-logo' => get_the_post_thumbnail_url($the_id),
                'contract-address-short' => WPSC_helpers::shortify($wpsc_contract_address),
                'txid' => $wpsc_txid,
                'txid-short' => WPSC_helpers::shortify($wpsc_txid),
                'owner' => $wpsc_owner,
                'owner-short' => WPSC_helpers::shortify($wpsc_owner)
            ];

            if ($wpsc_txid) {
                $atts["txid-exists"] = true;
            }

        }

        $social_networks = '';
        if (is_array($wpsc_social_link)) {
            foreach ($wpsc_social_link as $sn_i => $social_link) {
                $social_networks .= $m->render(WPSC_Mustache::getTemplate('coin-view-social-networks'), [
                    'link' => $social_link,
                    'icon' => $wpsc_social_icon[$sn_i]
                ]);
            }                   
        }

        $block_explorer_atts = [
            "xdai" => $xdai,
            "xdai_block_explorer" => $xdai_block_explorer,
            "xdai_block_explorer_label" => __('xDai Block Explorer', 'wp-smart-contracts'),
            'block-explorer' => __('Block Explorer', 'wp-smart-contracts'),
            'search-placeholder' => __('Search by Address or Txhash', 'wp-smart-contracts'),
            'transfers' => __('Transfers', 'wp-smart-contracts'),
            'holders' => __('Holders', 'wp-smart-contracts'),
            'page' => __('Page', 'wp-smart-contracts'),
            'date' => __('Date', 'wp-smart-contracts'),
            'from' => __('From', 'wp-smart-contracts'),
            'to' => __('To', 'wp-smart-contracts'),
            'amount_tx' => __('Amount and Transaction', 'wp-smart-contracts'),
            'value' => __('Value', 'wp-smart-contracts'),
            'previous' => __('Previous', 'wp-smart-contracts'),
            'next' => __('Next', 'wp-smart-contracts'),
            'updated' => __('Synced with blockchain every minute', 'wp-smart-contracts'),
            'account-url' => str_replace('acc-add-here', '', home_url() . esc_url( add_query_arg( 'acc', 'acc-add-here' ) ) ),
            'url' => get_permalink(),
            'etherscan' => $etherscan,
            'subdomain' => strtolower($network_val),
            'contract' => $wpsc_contract_address,
            'network' => $wpsc_network,
            'total_supply' => __('Total supply', 'wp-smart-contracts'),
            'symbol' => $wpsc_coin_symbol,
            'internal-transactions' => __('Internal Transactions', 'wp-smart-contracts'),
            'transactions' => __('Transactions', 'wp-smart-contracts'),
        ];

        $the_token_symbol = WPSC_helpers::valArrElement($atts, 'token-symbol')?$atts["token-symbol"]:null;

        $atts_coin_view_token = [
            'blockie' => WPSC_helpers::valArrElement($atts, 'blockie')?$atts["blockie"]:null,
            'token-name' => WPSC_helpers::valArrElement($atts, 'token-name')?$atts["token-name"]:null,
            'token-symbol' => $the_token_symbol,
            'color' => $color,
            'icon' => $icon,
            'ethereum-network' => WPSC_helpers::valArrElement($atts, 'ethereum-network')?$atts["ethereum-network"]:null,
            'token-info' => WPSC_helpers::valArrElement($atts, 'token-info')?$atts["token-info"]:null,
        ];

        $atts_coin_view_addresses = [
            "addresses" => __('Addresses', 'wp-smart-contracts'),
            "contract-address"          => WPSC_helpers::valArrElement($atts, 'contract-address')?$atts["contract-address"]:null,
            "blockie"                   => WPSC_helpers::valArrElement($atts, 'blockie')?$atts["blockie"]:null,
            "contract-address-text"     => WPSC_helpers::valArrElement($atts, 'contract-address-text')?$atts["contract-address-text"]:null,
            "contract-address-short"    => WPSC_helpers::valArrElement($atts, 'contract-address-short')?$atts["contract-address-short"]:null,
            "qr-code"                   => WPSC_helpers::valArrElement($atts, 'qr-code')?$atts["qr-code"]:null,
            "blockie-owner"             => WPSC_helpers::valArrElement($atts, 'blockie-owner')?$atts["blockie-owner"]:null,
            "owner-text"                => WPSC_helpers::valArrElement($atts, 'owner-text')?$atts["owner-text"]:null,
            "owner"                     => WPSC_helpers::valArrElement($atts, 'owner')?$atts["owner"]:null,
            "etherscan"                 => WPSC_helpers::valArrElement($atts, 'etherscan')?$atts["etherscan"]:null,
            "owner-short"               => WPSC_helpers::valArrElement($atts, 'owner-short')?$atts["owner-short"]:null,
            "txid"                      => WPSC_helpers::valArrElement($atts, 'txid')?$atts["txid"]:null,
            "genesis"                   => __('Genesis', 'wp-smart-contracts'),
            "txid-short"                => WPSC_helpers::valArrElement($atts, 'txid-short')?$atts["txid-short"]:null
        ];

        $atts_coin_view_wallet = [
            "xdai" => $xdai,
            "wallet" => __('Wallet', 'wp-smart-contracts'),
            "wallet-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/wallet.svg',
            "balance" => __('Balance', 'wp-smart-contracts'),
            "the-balance" => '', // $m->render(WPSC_Mustache::getTemplate('coin-view-block-explorer-balance'), []),
            "balance-tooltip" => __('Check the balance of specific accounts', 'wp-smart-contracts'),
            "transfer" => __('Transfer', 'wp-smart-contracts'),
            "transfer-tooltip" => __('Transfer an amount of tokens from your account to another', 'wp-smart-contracts'),
            "transfer-from" => __('Transfer from', 'wp-smart-contracts'),
            "transfer-from-tooltip" => __('Expend tokens previously approved from an account', 'wp-smart-contracts'),
            "approve" => __('Approve', 'wp-smart-contracts'),
            "approve-tooltip" => __('Authorize an account to withdraw your tokens up to a specified amount', 'wp-smart-contracts'),
            "burn" => __('Burn', 'wp-smart-contracts'),
            "burn-tooltip" => __('Destroy (burn) an specific amount of tokens from your account', 'wp-smart-contracts'),
            "burn-from" => __('Burn from', 'wp-smart-contracts'),
            "burn-from-tooltip" => __('Burn tokens previously approved from an account', 'wp-smart-contracts'),
            "mint" => __('Mint', 'wp-smart-contracts'),
            "mint-tooltip" => __('Create new tokens and assign them to an account', 'wp-smart-contracts'),
            'add-minter' => __('Add Minter Role', 'wp-smart-contracts'),
            'tooltip-minter' => __('Allow this account to create tokens', 'wp-smart-contracts'),
            'add-pauser' => __('Add Pauser Role', 'wp-smart-contracts'),
            'tooltip-pauser' => __('Allow this account to pause all activity in this contract', 'wp-smart-contracts'),
            "pause" => __('Pause', 'wp-smart-contracts'),
            "pause-tooltip" => __('Pause token activity', 'wp-smart-contracts'),
            "resume" => __('Resume', 'wp-smart-contracts'),
            "resume-tooltip" => __('Resume token activity', 'wp-smart-contracts'),
            "address-from" => __('From address', 'wp-smart-contracts'),
            "address-to" => __('To address', 'wp-smart-contracts'),
            "amount" => __('Amount', 'wp-smart-contracts'),
            "scan" => __('Scan', 'wp-smart-contracts'),
            "deploy-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif',
            "flavor" => $wpsc_flavor,
            "cancel" => __('Cancel', 'wp-smart-contracts'),
            "tx-in-progress" => __('Transaction in progress', 'wp-smart-contracts'),
            "click-confirm" => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'),
            "please-patience" => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
            "renounce-pauser" => __('Renounce Pauser Role', 'wp-smart-contracts'),
            'tooltip-renounce-pauser' => __('Remove the pauser Role from your account', 'wp-smart-contracts'),
            "renounce-minter" => __('Renounce Minter Role', 'wp-smart-contracts'),
            'tooltip-renounce-minter' => __('Remove the minter Role from your account', 'wp-smart-contracts'),
        ];

        $atts_dex = [
            "dex" => __('Exchanges', 'wp-smart-contracts'),
            "exchange" => __('Exchange', 'wp-smart-contracts'),
            "wpsc_forkdelta" => $wpsc_forkdelta,
            "wpsc_uniswap" => $wpsc_uniswap,
            "forkdelta" => "https://forkdelta.app/#!/trade/" . $wpsc_contract_address . "-ETH",
            "forkdelta-logo" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/forkdelta.png',
            "forkdelta-domain" => 'ForkDelta.app',
            "uniswap" => "https://app.uniswap.org/#/swap?outputCurrency=" . $wpsc_contract_address,
            "uniswap-logo" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/uniswap.png',
            "uniswap-domain" => 'Uniswap.org',
            "etherdelta" => "https://etherdelta.com/#" . $wpsc_contract_address . "-ETH",
            "etherdelta-logo" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/etherdelta.png',
            "etherdelta-domain" => 'EtherDelta.com',
            "mcafee" => "https://mcafeedex.com/#" . $wpsc_contract_address . "-ETH",
            "mcafee-logo" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/mcafee.png',
            "mcafee-domain" => 'McAfeeDex.com',
            "token-symbol" => $the_token_symbol,
        ];


        if ($wpsc_flavor == "chocolate") {
            $atts_coin_view_wallet["is_chocolate"] = true;
        }

        if ($wpsc_txid) {
            $atts_coin_view_addresses["txid-exists"] = true;
        }

        if ($wpsc_contract_address) {
            $atts_coin_view_wallet["contract-exists"] = true;
            $atts_coin_view_token["contract-exists"] = true;
            $atts_coin_view_addresses["contract-exists"] = true;
            $block_explorer_atts["contract-exists"] = true;
            $atts_dex["contract-exists"] = true;
        }

        $atts_source_code = WPSC_Metabox::wpscGetMetaSourceCodeAtts($the_id);

        $msg_box = "";

        if (WPSC_helpers::valArrElement($_GET, 'welcome')) {
            $actual_link = strtok("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
            $msg_box = $m->render(
                WPSC_Mustache::getTemplate('msg-box'), 
                [
                    'type' => 'info',
                    'icon' => 'info',
                    'title' => __('Important Information', 'wp-smart-contracts'),
                    'msg' => "<p>Your contract was successfully deployed to the address: " . $atts["contract-address"] . "</p>" .
                        "<p>The URL of your block explorer is: " . $actual_link . "</p>" .
                        "<p>Please store this information for future reference.</p>"
                    ]
                );
        }

        return $m->render(WPSC_Mustache::getTemplate('coin-view'), [

            'msg-box' => $msg_box,

            'view-metamask' => 
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-metamask'), 
                    [
                        'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                        'text' => __('Please connect or choose the right Ethereum Network', 'wp-smart-contracts'),
                    ]
                ),

            'coin-view-brand' => (WPSC_helpers::valArrElement($params, 'hide-brand') and $params['hide-brand'] and $params['hide-brand']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-brand'), 
                    [
                        'title' => $wpsc_title,
                        'social-networks' => $social_networks,
                        'content' => $wpsc_content,
                        'thumbnail' => $wpsc_thumbnail
                    ]
                ),

            'coin-view-token' => (WPSC_helpers::valArrElement($params, 'hide-token') and $params['hide-token'] and $params['hide-token']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-token'), 
                    $atts_coin_view_token
                ),
            
            'coin-view-addresses' => (WPSC_helpers::valArrElement($params, 'hide-address') and $params['hide-address'] and $params['hide-address']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-addresses'), 
                    $atts_coin_view_addresses
                ),
            
            'coin-view-wallet' => (WPSC_helpers::valArrElement($params, 'hide-wallet') and $params['hide-wallet'] and $params['hide-wallet']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-wallet'),
                    $atts_coin_view_wallet
                ),
            
            'coin-view-dex' => (!$wpsc_forkdelta and !$wpsc_uniswap)?'':
                $m->render(
                  WPSC_Mustache::getTemplate('coin-view-dex'),
                  $atts_dex
                ),

            'coin-view-block-explorer' =>  (WPSC_helpers::valArrElement($params, 'hide-block') and $params['hide-block'] and $params['hide-block']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-block-explorer'), 
                    $block_explorer_atts
                ),

            'coin-view-audit' =>   (WPSC_helpers::valArrElement($params, 'hide-audit') and $params['hide-audit'] and $params['hide-audit']=="true")?'':
                $m->render(
                  WPSC_Mustache::getTemplate('coin-view-audit'),
                  $atts_source_code
                ),

        ]);

    }

    public function crowdfunding($params) {

        $the_id = self::getPostID($params);

        $wpsc_thumbnail = get_the_post_thumbnail_url($the_id);
        $wpsc_title = get_the_title($the_id);
        $wpsc_content = get_post_field('post_content', $the_id);

        $wpsc_network = get_post_meta($the_id, 'wpsc_network', true);
        $wpsc_txid = get_post_meta($the_id, 'wpsc_txid', true);
        $wpsc_owner = get_post_meta($the_id, 'wpsc_owner', true);
        $wpsc_contract_address = get_post_meta($the_id, 'wpsc_contract_address', true);
        $wpsc_blockie = get_post_meta($the_id, 'wpsc_blockie', true);
        $wpsc_blockie_owner = get_post_meta($the_id, 'wpsc_blockie_owner', true);
        $wpsc_qr_code = get_post_meta($the_id, 'wpsc_qr_code', true);

        list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);

        $m = new Mustache_Engine;

        // initialization
        $wpsc_flavor        = null;
        $wpsc_minimum       = null;
        $wpsc_approvers     = null;
        $atts = [];

        // show contract
        if ($wpsc_contract_address) {

            $wpsc_flavor        = get_post_meta($the_id, 'wpsc_flavor', true);
            $wpsc_minimum       = get_post_meta($the_id, 'wpsc_minimum', true);
            $wpsc_approvers     = get_post_meta($the_id, 'wpsc_approvers', true);

            $crowdInfo = [
                "type" => $wpsc_flavor,
                "factor" => $wpsc_approvers,
                "minimum" => $wpsc_minimum,
                "size" => "mini",
                "approvers_label" => __("Approvers Percentage", "wp-smart-contracts"),
                "minimum_label" => __("Minimum", "wp-smart-contracts")
            ];
            if ($wpsc_flavor=="mango") $crowdInfo["color"] = "orange";
            if ($wpsc_flavor=="bluemoon") $crowdInfo["color"] = "teal";
            if ($wpsc_flavor=="bubblegum") $crowdInfo["color"] = "purple";
            $crowdInfo["imgUrl"] = dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/';

            $atts = [
                'ethereum-network' => $network_val,
                'ethereum-color' => $color,
                'ethereum-icon' => $icon,
                'contract-address' => $wpsc_contract_address,
                'etherscan' => $etherscan,
                'contract-address-text' => __('Contract Address', 'wp-smart-contracts'),
                'txid-text' => __('Transaction ID', 'wp-smart-contracts'),
                'owner-text' => __('Owner Account', 'wp-smart-contracts'),
                'qr-code' => $wpsc_qr_code,
                'blockie' => $wpsc_blockie,
                'blockie-owner' => $wpsc_blockie_owner,
                'crowd-info' => $m->render(WPSC_Mustache::getTemplate('crowdfunding-info'), $crowdInfo),
                'crowd-logo' => get_the_post_thumbnail_url($the_id),
                'contract-address-short' => WPSC_helpers::shortify($wpsc_contract_address),
                'txid' => $wpsc_txid,
                'txid-short' => WPSC_helpers::shortify($wpsc_txid),
                'owner' => $wpsc_owner,
                'owner-short' => WPSC_helpers::shortify($wpsc_owner)
            ];

            if ($wpsc_txid) {
                $atts["txid-exists"] = true;
            }

        }

        $atts_crowd_view_contract = [
            'blockie' => WPSC_helpers::valArrElement($atts, 'blockie')?$atts["blockie"]:null,
            'contract-name' => __('Crowdfunding', 'wp-smart-contracts'),
            'color' => $color,
            'icon' => $icon,
            'ethereum-network' => WPSC_helpers::valArrElement($atts, 'ethereum-network')?$atts["ethereum-network"]:null,
            'crowd-info' => WPSC_helpers::valArrElement($atts, 'crowd-info')?$atts["crowd-info"]:null,
            'title' => $wpsc_title,
            'content' => $wpsc_content,
            'thumbnail' => $wpsc_thumbnail
        ];

        $atts_crowd_view_panel = [
            'network' => $wpsc_network,
            'minimum' => $wpsc_minimum,
            'minimum-contribution' => __('Minimum contribution', 'wp-smart-contracts'),
            'panel' => __('Contributions', 'wp-smart-contracts'),
            'requests' => __('Requests', 'wp-smart-contracts'),
            'balance' => __('Balance', 'wp-smart-contracts'),
            'contribute' => __('Contribute', 'wp-smart-contracts'),
            'contribute-tooltip' => __('Amount to donate to the campaign', 'wp-smart-contracts'),
            'send' => __('Send', 'wp-smart-contracts'),
            'cancel' => __('Cancel', 'wp-smart-contracts'),
            'amount' => __('Amount', 'wp-smart-contracts'),
            'contributors' => __('Contributors', 'wp-smart-contracts'),
            'approve' => __('Approve', 'wp-smart-contracts'),
            'create-request' => __('Create Request', 'wp-smart-contracts'),
            'request' => __('Create request', 'wp-smart-contracts'),
            'description' => __('Add a description', 'wp-smart-contracts'),
            'create-request-tooltip' => __('A request to withdraw funds from the contract. Requests must be approved by approvers', 'wp-smart-contracts'),
            'finalize-request' => __('Finalize Request', 'wp-smart-contracts'),
            'scan' => __('Scan', 'wp-smart-contracts'),
            'address-to' => __('Destination address', 'wp-smart-contracts'),
            'wpsc-contract-address' => $wpsc_contract_address,
            'wpsc-flavor' => $wpsc_flavor,
            "tx-in-progress" => __('Transaction in progress', 'wp-smart-contracts'),
            "click-confirm" => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'),
            "please-patience" => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
            "deploy-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif',
            "check-contribution" => __('Check contribution', 'wp-smart-contracts'),
        ];

        if ($wpsc_txid) {
            $atts_crowd_view_addresses["txid-exists"] = true;
        }

        if ($wpsc_contract_address) {
            $atts_crowd_view_contract["contract-exists"] = true;
            $atts_crowd_view_addresses["contract-exists"] = true;
            $atts_crowd_view_panel["contract-exists"] = true;
        }

        $atts_source_code = WPSC_Metabox::wpscGetMetaSourceCodeAtts($the_id);

        $msg_box = "";

        if (WPSC_helpers::valArrElement($_GET, 'welcome')) {
            $actual_link = strtok("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
            $msg_box = $m->render(
                WPSC_Mustache::getTemplate('msg-box'), 
                [
                    'type' => 'info',
                    'icon' => 'info',
                    'title' => __('Important Information', 'wp-smart-contracts'),
                    'msg' => "<p>Your contract was successfully deployed to the address: " . $atts["contract-address"] . "</p>" .
                        "<p>The URL of your Crowdfunding is: " . $actual_link . "</p>" .
                        "<p>Please store this information for future reference.</p>"
                    ]
                );
        }

        return $m->render(WPSC_Mustache::getTemplate('crowd-view'), [

            'msg-box' => $msg_box,
            
            'view-metamask' => 
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-metamask'), 
                    [
                        'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                        'text' => __('Please connect or choose the right Ethereum Network', 'wp-smart-contracts'),
                        'text-wrong-net' => __('You are connected to a different network, please choose the right one', 'wp-smart-contracts'),
                    ]
                ),
            
            'crowd-view-brand' => (WPSC_helpers::valArrElement($params, 'hide-brand') and $params['hide-brand'] and $params['hide-brand']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-brand'), 
                    $atts_crowd_view_contract
                ),
            
            'crowd-view-panel' => (WPSC_helpers::valArrElement($params, 'hide-panel') and $params['hide-panel'] and $params['hide-panel']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-panel'), 
                    $atts_crowd_view_panel
                ),

            'crowd-view-audit' =>   (WPSC_helpers::valArrElement($params, 'hide-audit') and $params['hide-audit'] and $params['hide-audit']=="true")?'':
                $m->render(
                  WPSC_Mustache::getTemplate('crowd-view-audit'),
                  $atts_source_code
                ),

        ]);

    }

    // return a timestamp using UTC time
    static public function utc_timestamp($input) {
        $utc_time_zone = new DateTimeZone("UTC");
        $date = new DateTime( $input, $utc_time_zone );            
        return $date->format('U');
    }

    public function ico($params) {

        $the_id = self::getPostID($params);

        $wpsc_thumbnail = get_the_post_thumbnail_url($the_id);
        $wpsc_title = get_the_title($the_id);
        $wpsc_content = get_post_field('post_content', $the_id);

        $wpsc_network = get_post_meta($the_id, 'wpsc_network', true);
        $wpsc_txid = get_post_meta($the_id, 'wpsc_txid', true);
        $wpsc_owner = get_post_meta($the_id, 'wpsc_owner', true);
        $wpsc_contract_address = get_post_meta($the_id, 'wpsc_contract_address', true);
        $wpsc_blockie = get_post_meta($the_id, 'wpsc_blockie', true);
        $wpsc_blockie_owner = get_post_meta($the_id, 'wpsc_blockie_owner', true);
        $wpsc_qr_code = get_post_meta($the_id, 'wpsc_qr_code', true);

        $wpsc_flavor        = null;

        if ($wpsc_network==77) { // xDai testnet
            $native_coin="xDai";
        } else {
            $native_coin="Ether";
        }

        // show contract
        if ($wpsc_contract_address) {
            $wpsc_flavor        = get_post_meta($the_id, 'wpsc_flavor', true);
        }

        $timed = get_post_meta($the_id, 'wpsc_adv_timed', true);

        if ($timed==="false") $timed=false;

        $wpsc_hardcap = get_post_meta($the_id, 'wpsc_adv_hard', true);
        if ($wpsc_hardcap==="false") $wpsc_hardcap=false;

        if ($timed) {

            $utc_now = gmdate('Y-m-d');

            $now = self::utc_timestamp($utc_now);

            $opening_string = get_post_meta($the_id, 'wpsc_adv_opening', true) . " 00:00:00";
            $closing_string = get_post_meta($the_id, 'wpsc_adv_closing', true) . " 23:59:59";

            $opening = self::utc_timestamp($opening_string);
            $closing = self::utc_timestamp($closing_string);

            $opening_human = date("F j, Y, g:i a", $opening) . " GMT";
            $closing_human = date("F j, Y, g:i a", $closing) . " GMT";

            $opening_human_short = date("M j, Y", $opening) . " GMT";
            $closing_human_short = date("M j, Y", $closing) . " GMT";

            if ($now>=$opening and $now<=$closing) {
                $is_open = true;
                // if timed can contribute only if open
                $can_contribute = true;
            }
            if ($utc_now>$closing_string) {
                $is_closed = true;
            }

            // if timed and open or closed show how much is raised
            if ((isset($is_open) and $is_open) or (isset($is_closed) and $is_closed)) {
                $show_raised = true;
            }

        } else {
            // if not timed can contribute any time and always shows raised
            $can_contribute = true;
            $show_raised = true;
        }

        list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo(get_post_meta($the_id, 'wpsc_network', true));

        $is_bubblegum = false;
        if ($wpsc_flavor=="bubblegum") {
            $is_bubblegum = true;
        }

        $atts_ico_view_brand = [
            "wpsc_thumbnail" => $wpsc_thumbnail,
            "wpsc_title" => $wpsc_title,
            "wpsc_content" => $wpsc_content,
            "etherscan" => $etherscan,
            "color" => $color,
            "icon" => $icon,
            "network_val" => $network_val,
            "token-name" => __('Token name', 'wp-smart-contracts'),
            "token-symbol" => __('Token Symbol', 'wp-smart-contracts'),
            "initial-supply" => __('Initial supply', 'wp-smart-contracts'),
            "hard-cap" => __('Hard cap', 'wp-smart-contracts'),
            "rate" => __('Rate', 'wp-smart-contracts'),
            "calendar" => __('Calendar', 'wp-smart-contracts'),
            "ico-begins" => __('ICO Begins', 'wp-smart-contracts'),
            "open-until" => __('Open until', 'wp-smart-contracts'),
            "days" => __('days', 'wp-smart-contracts'),
            "hrs" => __('hrs', 'wp-smart-contracts'),
            "whitelist" => __('Whitelist', 'wp-smart-contracts'),
            "whitelist-desc" => __('Only whitelisted users can contribute', 'wp-smart-contracts'),
            "min" => __('min', 'wp-smart-contracts'),
            "sec" => __('sec', 'wp-smart-contracts'),
            "raised" => __('Raised', 'wp-smart-contracts'),
            "sold" => __('sold!', 'wp-smart-contracts'),
            "hard-cap-reached" => __('Hardcap reached', 'wp-smart-contracts'),
            "send-ether" => __('Contribute by transfering Ether', 'wp-smart-contracts'),
            "send-ether-address" => __('Sending Ethers', 'wp-smart-contracts'),
            "copied" => __('Copied!', 'wp-smart-contracts'),
            "erc20-wallet" => __('You will receive your tokens in the same address you use to send Ether contribution. Please make sure you are using an ERC20 Token compatible wallet.', 'wp-smart-contracts'),
            "no-exchange" => __('Do not send contributions from an exchange', 'wp-smart-contracts'),
            "buy-tokens" => __('Buy Tokens', 'wp-smart-contracts'),
            "buy" => __('Buy', 'wp-smart-contracts'),
            "browser-wallet" => __('Using Metamask', 'wp-smart-contracts'),
            "contribute" => __('Contribute', 'wp-smart-contracts'),
            "text_4" => __('You can receive your tokens in a different Ethereum account', 'wp-smart-contracts'),
            "ico-icon" => plugins_url( "assets/img/ico.png", dirname(__FILE__) ),

            "wpsc_adv_hard" => $wpsc_hardcap,
            "wpsc_adv_cap" => WPSC_helpers::formatNumber(get_post_meta($the_id, 'wpsc_adv_cap', true)),
            "wpsc_adv_white" => get_post_meta($the_id, 'wpsc_adv_white', true),
            "wpsc_adv_pause" => get_post_meta($the_id, 'wpsc_adv_pause', true),
            "wpsc_adv_timed" => $timed,
            "wpsc_adv_opening" => isset($opening)?$opening:null,
            "wpsc_adv_closing" => isset($closing)?$closing:null,
            "wpsc_coin_name" => get_post_meta($the_id, 'wpsc_coin_name', true),
            "wpsc_coin_symbol" => strtoupper(get_post_meta($the_id, 'wpsc_coin_symbol', true)),
            "wpsc_total_supply" => WPSC_helpers::formatNumber(get_post_meta($the_id, 'wpsc_total_supply', true)),
            "wpsc_rate" => WPSC_helpers::formatNumber(get_post_meta($the_id, 'wpsc_rate', true)),
            "wpsc_native_coin" => $native_coin,
            "timed" => __('Timed', 'wp-smart-contracts'),
            "from" => __('From', 'wp-smart-contracts'),
            "to" => __('to', 'wp-smart-contracts'),
            "token-address" => __('Token Address', 'wp-smart-contracts'),
            "or" => __('OR', 'wp-smart-contracts'),
            "wpsc_contract_address" => $wpsc_contract_address,
            "wpsc_contract_address_short" => WPSC_helpers::shortify($wpsc_contract_address, true),
            "wpsc_blockie" => get_post_meta($the_id, 'wpsc_blockie', true),
            "wpsc_blockie_token" => get_post_meta($the_id, 'wpsc_blockie_token', true),
            "wpsc_token_contract_address" => get_post_meta($the_id, 'wpsc_token_contract_address', true),
            "wpsc_token_contract_address_short" => WPSC_helpers::shortify(get_post_meta($the_id, 'wpsc_token_contract_address', true), true),
            "wpsc_qr_code" => get_post_meta($the_id, 'wpsc_qr_code', true),
            "wpsc_token_qr_code" => get_post_meta($the_id, 'wpsc_token_qr_code', true),
            "opening_human_short" => isset($opening_human_short)?$opening_human_short:null,
            "closing_human_short" => isset($closing_human_short)?$closing_human_short:null,
            "block-explorer" => __('Block Explorer', 'wp-smart-contracts'),
            "block-explorer-link" => get_permalink(get_post_meta($the_id, 'token_id', true)),

            "resume" => __('Resume', 'wp-smart-contracts'),
            "pause" => __('Pause', 'wp-smart-contracts'),
            "cancel" => __('Cancel', 'wp-smart-contracts'),

            "tx-in-progress" => __('Transaction in progress', 'wp-smart-contracts'),
            "deploy-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif',
            "click-confirm" => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'),
            "please-patience" => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
            "is_bubblegum" => $is_bubblegum,
            "tokens-to-sell" => __('Total tokens to sell', 'wp-smart-contracts'),

        ];

        $atts_ico_view_panel = [

            "is_open" => isset($is_open)?$is_open:null,
            "is_closed" => isset($is_closed)?$is_closed:null,
            "can_contribute" => isset($can_contribute)?$can_contribute:null,
            "show_raised" => isset($show_raised)?$show_raised:null,
            "opening_human" => isset($opening_human)?$opening_human:null,
            "closing_human" => isset($closing_human)?$closing_human:null,
            "opening" => isset($opening)?$opening * 1000:null,
            "closing" => isset($closing)?$closing * 1000:null,

            'network' => $wpsc_network,
            'wpsc-contract-address' => $wpsc_contract_address,
            'wpsc-flavor' => $wpsc_flavor,
    
            "ico-will-open" => __('ICO Coming Soon', 'wp-smart-contracts'),
            "ico-is-open" => __('ICO Is Open', 'wp-smart-contracts'),
            "ico-is-closed" => __('ICO Is Closed', 'wp-smart-contracts'),
            "ico-closed" => __('Closed on', 'wp-smart-contracts'),

            "ico-contribute" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/ico.png',
            "contribute-tooltip" => __('Purchase tokens directly from ICO contract.', 'wp-smart-contracts'),
            "contribute-help" => __('This is the address where you are going to receive the tokens. The beneficiary account has to be a valid ERC20 token compatible address.', 'wp-smart-contracts'),
            "what-is" => __('What\'s this?', 'wp-smart-contracts'),
            "amount-ether" => __('Amount to spend', 'wp-smart-contracts'),
            "send" => __('Send', 'wp-smart-contracts'),
            "cancel" => __('Cancel', 'wp-smart-contracts'),
            "scan" => __('Scan', 'wp-smart-contracts'),
            "beneficiary" => __('Beneficiary account', 'wp-smart-contracts'),
            "tx-in-progress" => __('Transaction in progress', 'wp-smart-contracts'),
            "deploy-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif',
            "click-confirm" => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'),
            "please-patience" => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
            
        ] + $atts_ico_view_brand;

        if ($wpsc_contract_address) {
            $atts_ico_view_panel["contract-exists"] = true;
        }

        $atts_source_code = WPSC_Metabox::wpscGetMetaSourceCodeAtts($the_id);

        $m = new Mustache_Engine;

        $msg_box = "";

        if (WPSC_helpers::valArrElement($_GET, 'welcome')) {
            $actual_link = strtok("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
            $msg_box = $m->render(
                WPSC_Mustache::getTemplate('msg-box'), 
                [
                    'type' => 'info',
                    'icon' => 'info',
                    'title' => __('Important Information', 'wp-smart-contracts'),
                    'msg' => "<p>Your contract was successfully deployed to the address: " . $wpsc_contract_address . "</p>" .
                        "<p>The URL of your ICO is: " . $actual_link . "</p>" .
                        "<p>Please store this information for future reference.</p>"
                    ]
                );
        }

        return $m->render(WPSC_Mustache::getTemplate('ico-view'), [
    
            'msg-box' => $msg_box,

            'view-metamask' => 
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-metamask'), 
                    [
                        'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                        'text' => __('Please connect or choose the right Ethereum Network', 'wp-smart-contracts'),
                        'text-wrong-net' => __('You are connected to a different network, please choose the right one', 'wp-smart-contracts'),
                    ]
                ),

            'ico-view-brand' => (WPSC_helpers::valArrElement($params, 'hide-brand') and $params['hide-brand'] and $params['hide-brand']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('ico-view-brand'), 
                    $atts_ico_view_brand
                ),

            'ico-view-panel' => (WPSC_helpers::valArrElement($params, 'hide-panel') and $params['hide-panel'] and $params['hide-panel']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('ico-view-panel'), 
                    $atts_ico_view_panel
                ),

            'ico-view-audit' =>   (WPSC_helpers::valArrElement($params, 'hide-audit') and $params['hide-audit'] and $params['hide-audit']=="true")?'':
                $m->render(
                  WPSC_Mustache::getTemplate('crowd-view-audit'),
                  $atts_source_code
                ),

        ]);

    }

    // return the post id from environment or from shortcode
    private static function getPostID($params) {

        $the_id = 0;

        if (is_array($params) and array_key_exists('id', $params) and $params["id"]) {
            $the_id = $params['id'];
        }

        if (!$the_id) {
            $the_id = get_the_ID();
        }

        return $the_id;

    }

    

}

