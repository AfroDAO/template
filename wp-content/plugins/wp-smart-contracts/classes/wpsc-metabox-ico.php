<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Create Metaboxes for CPT ICO
 */

new WPSC_MetaboxICO();

class WPSC_MetaboxICO {

  function __construct() {

    // load all custom fields
    add_action('admin_init', [$this, 'loadMetaboxes'], 2);

    // save repeatable fields
    add_action('save_post', [$this, 'saveRepeatableFields'], 10, 3);

  }

  public function loadMetaboxes() {

    // check if we need to load specifications of the contract

    $load_spec = true;

    $post_id = WPSC_helpers::valArrElement($_GET, "post")?sanitize_text_field($_GET["post"]):false;

    if (is_numeric($post_id) and get_post_meta($post_id, 'wpsc_contract_address', true)) {
      $load_spec = false;
    }

    if ($load_spec) {
      add_meta_box(
        'wpsc_ico_metabox', 
        'WPSmartContracts: ICO Specification', 
        [$this, 'wpscSmartontractSpecification'], 
        'ico', 
        'normal', 
        'default'
      );
    }
    
    add_meta_box(
      'wpsc_smart_contract', 
      'WPSmartContracts: Smart Contract', 
      [$this, 'wpscSmartContract'], 
      'ico', 
      'normal', 
      'default'
    );
    
    add_meta_box(
      'wpsc_code_crowd', 
      'WPSmartContracts: Source Code', 
      [$this, 'wpscSourceCode'], 
      'ico', 
      'normal', 
      'default'
    );

    add_meta_box(
      'wpsc_reminder_crowd', 
      'WPSmartContracts: Friendly Reminder', 
      [__CLASS__, 'wpscReminder'], 
      'ico', 
      'normal', 
      'default'
    );

    add_meta_box(
      'wpsc_sidebar', 
      'WPSmartContracts: Tutorials & Tools', 
      [$this, 'wpscSidebar'], 
      'ico', 
      'side', 
      'default'
    );

  }

  public function saveRepeatableFields($post_id, $post, $update) {

    if ($post->post_type == "ico") {

      if ( ! isset( $_POST['wpsc_repeatable_meta_box_nonce'] ) ||
      ! wp_verify_nonce( $_POST['wpsc_repeatable_meta_box_nonce'], 'wpsc_repeatable_meta_box_nonce' ) )
          return;

      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
          return;

      if (!current_user_can('edit_post', $post_id))
          return;

      // if the contract was not deployed yet, save the ico definitions
      if (!$_POST["wpsc-readonly"]) {
        self::saveICOMetaData($post_id, $_POST);
      } 

    }

  }

  public static function saveICOMetaData($post_id, $arr) {

    // get clean and update all inputs
    $wpsc_flavor = WPSC_Metabox::cleanUpText($arr["wpsc-flavor"]);
    update_post_meta($post_id, 'wpsc_flavor', $wpsc_flavor);

    if ($wpsc_flavor=="raspberry") {

      $wpsc_adv_pause_ico = WPSC_Metabox::cleanUpText($arr["wpsc-adv-pause-ico"]);
      update_post_meta($post_id, 'wpsc_adv_pause_ico', $wpsc_adv_pause_ico);

      $wpsc_adv_pause = WPSC_Metabox::cleanUpText($arr["wpsc-adv-pause"]);
      $wpsc_adv_burn = WPSC_Metabox::cleanUpText($arr["wpsc-adv-burn"]);

    } elseif ($wpsc_flavor=="bubblegum") {

      // the token to sell is equivalent to the token contract address
      $arr["wpsc-token-contract-address"] = WPSC_Metabox::cleanUpText($arr["wpsc-token-to-sell"]);

    } else {

      $wpsc_adv_hard = WPSC_Metabox::cleanUpText($arr["wpsc-adv-hard"]);
      update_post_meta($post_id, 'wpsc_adv_hard', $wpsc_adv_hard);
  
      $wpsc_adv_cap = WPSC_Metabox::cleanUpText($arr["wpsc-adv-cap"]);
      update_post_meta($post_id, 'wpsc_adv_cap', $wpsc_adv_cap);
  
      $wpsc_adv_timed = WPSC_Metabox::cleanUpText($arr["wpsc-adv-timed"]);
      update_post_meta($post_id, 'wpsc_adv_timed', $wpsc_adv_timed);
      
      $wpsc_adv_opening = WPSC_Metabox::cleanUpText($arr["wpsc-adv-opening"]);
      update_post_meta($post_id, 'wpsc_adv_opening', $wpsc_adv_opening);
  
      $wpsc_adv_closing = WPSC_Metabox::cleanUpText($arr["wpsc-adv-closing"]);
      update_post_meta($post_id, 'wpsc_adv_closing', $wpsc_adv_closing);
    
    }

    $wpsc_coin_name = WPSC_Metabox::cleanUpText($arr["wpsc-coin-name"]);
    update_post_meta($post_id, 'wpsc_coin_name', $wpsc_coin_name);

    $wpsc_coin_decimals_tmp = WPSC_Metabox::cleanUpText($arr["wpsc-coin-decimals"]);
    if (!$wpsc_coin_decimals_tmp) {
      $wpsc_coin_decimals_tmp = 18;
    }

    $wpsc_coin_symbol = WPSC_Metabox::cleanUpText($arr["wpsc-coin-symbol"]);
    update_post_meta($post_id, 'wpsc_coin_symbol', $wpsc_coin_symbol);

    $wpsc_adv_cap_token = WPSC_Metabox::cleanUpText($arr["wpsc-adv-cap-token"]);
    update_post_meta($post_id, 'wpsc_adv_cap_token', $wpsc_adv_cap_token);

    $wpsc_total_supply = WPSC_Metabox::cleanUpText($arr["wpsc-total-supply"]);
    update_post_meta($post_id, 'wpsc_total_supply', $wpsc_total_supply);

    $wpsc_rate = WPSC_Metabox::cleanUpText($arr["wpsc-rate"]);
    update_post_meta($post_id, 'wpsc_rate', $wpsc_rate);

    $wpsc_wallet = WPSC_Metabox::cleanUpText($arr["wpsc-wallet"]);
    $wpsc_to_sell = WPSC_Metabox::cleanUpText($arr["wpsc-token-to-sell"]);
    $wpsc_to_receive = WPSC_Metabox::cleanUpText($arr["wpsc-tokens-to-receive"]);
    
    $erc20_payments = $arr["erc20_payments"];
    $erc20_rates = $arr["erc20_rates"];

    update_post_meta($post_id, 'wpsc_wallet', $wpsc_wallet);
    update_post_meta($post_id, 'wpsc_to_sell', $wpsc_to_sell);
    update_post_meta($post_id, 'erc20_payments', $erc20_payments);
    update_post_meta($post_id, 'erc20_rates', $erc20_rates);
    update_post_meta($post_id, 'wpsc_to_receive', $wpsc_to_receive);

    $wpsc_network = WPSC_Metabox::cleanUpText($arr["wpsc-network"]);
    $wpsc_txid = WPSC_Metabox::cleanUpText($arr["wpsc-txid"]);
    $wpsc_owner = WPSC_Metabox::cleanUpText($arr["wpsc-owner"]);
    $wpsc_contract_address = WPSC_Metabox::cleanUpText($arr["wpsc-contract-address"]);
    $wpsc_token_contract_address = WPSC_Metabox::cleanUpText($arr["wpsc-token-contract-address"]);
    $wpsc_factory = $arr["wpsc-factory"];
    $wpsc_blockie = WPSC_Metabox::cleanUpText($arr["wpsc-blockie"]);
    $wpsc_blockie_token = WPSC_Metabox::cleanUpText($arr["wpsc-blockie-token"]);
    $wpsc_blockie_owner = WPSC_Metabox::cleanUpText($arr["wpsc-blockie-owner"]);
    $wpsc_qr_code = WPSC_Metabox::cleanUpText($arr["wpsc-qr-code"]);
    $wpsc_token_qr_code = WPSC_Metabox::cleanUpText($arr["wpsc-token-qr-code"]);

    // if set, save the contract info meta
    if ($wpsc_network) update_post_meta($post_id, 'wpsc_network', $wpsc_network);
    if ($wpsc_txid) update_post_meta($post_id, 'wpsc_txid', $wpsc_txid);
    if ($wpsc_owner) update_post_meta($post_id, 'wpsc_owner', $wpsc_owner);
    if ($wpsc_contract_address) update_post_meta($post_id, 'wpsc_contract_address', $wpsc_contract_address);
    if ($wpsc_token_contract_address) update_post_meta($post_id, 'wpsc_token_contract_address', $wpsc_token_contract_address);
    if ($wpsc_factory) update_post_meta($post_id, 'wpsc_factory', $wpsc_factory);
    if ($wpsc_blockie) update_post_meta($post_id, 'wpsc_blockie', $wpsc_blockie);
    if ($wpsc_blockie_token) update_post_meta($post_id, 'wpsc_blockie_token', $wpsc_blockie_token);
    if ($wpsc_blockie_owner) update_post_meta($post_id, 'wpsc_blockie_owner', $wpsc_blockie_owner);
    if ($wpsc_qr_code) update_post_meta($post_id, 'wpsc_qr_code', $wpsc_qr_code);
    if ($wpsc_token_qr_code) update_post_meta($post_id, 'wpsc_token_qr_code', $wpsc_token_qr_code);

    // add the token as a separate CPT only on deployment

    $the_token_id = get_post_meta($post_id, 'token_id', true);

    if ($wpsc_token_contract_address and !$the_token_id) {

      $token_id = wp_insert_post([
        'post_title' => $wpsc_coin_name,
        'post_name' => $wpsc_token_contract_address,
        'post_type' => "coin",
        'post_status' => "publish"
      ]);
      
      // add metadata for the token CPT
      update_post_meta($token_id, 'wpsc_flavor', 'chocolate');
      update_post_meta($token_id, 'wpsc_adv_mint', 'mintable');
      update_post_meta($token_id, 'wpsc_coin_name', $wpsc_coin_name);
      update_post_meta($token_id, 'wpsc_coin_symbol', $wpsc_coin_symbol);

      if ($wpsc_flavor=="raspberry") {
        update_post_meta($token_id, 'wpsc_adv_burn', $wpsc_adv_burn);
        update_post_meta($token_id, 'wpsc_adv_pause', $wpsc_adv_pause);
        update_post_meta($token_id, 'wpsc_adv_cap', $wpsc_adv_cap_token);
      } else {
        update_post_meta($token_id, 'wpsc_adv_cap', 0);
      }

      update_post_meta($token_id, 'wpsc_coin_decimals', $wpsc_coin_decimals_tmp);
      update_post_meta($token_id, 'wpsc_network', $wpsc_network);
      update_post_meta($token_id, 'wpsc_txid', $wpsc_txid);
      update_post_meta($token_id, 'wpsc_owner', $wpsc_owner);
      update_post_meta($token_id, 'wpsc_contract_address', $wpsc_token_contract_address);
      update_post_meta($token_id, 'wpsc_factory', $wpsc_factory);
      update_post_meta($token_id, 'wpsc_blockie', $wpsc_blockie_token);
      update_post_meta($token_id, 'wpsc_blockie_owner', $wpsc_blockie_owner);
      update_post_meta($token_id, 'wpsc_qr_code', $wpsc_token_qr_code);

      // link contract to token
      update_post_meta($post_id, 'token_id', $token_id);

    }

  }

  public function wpscSmartontractSpecification() {

    wp_nonce_field( 'wpsc_repeatable_meta_box_nonce', 'wpsc_repeatable_meta_box_nonce' );

    $m = new Mustache_Engine;

    $args =  self::getMetaboxICOArgs();

    echo $m->render(
      WPSC_Mustache::getTemplate('metabox-ico'),
      $args
    );

  }

  static public function getMetaboxICOArgs() {
    
    $m = new Mustache_Engine;

    $wpsc_flavor = get_post_meta(get_the_ID(), 'wpsc_flavor', true);
    $wpsc_adv_hard = get_post_meta(get_the_ID(), 'wpsc_adv_hard', true);
    $wpsc_adv_cap = get_post_meta(get_the_ID(), 'wpsc_adv_cap', true);
    $wpsc_adv_white = get_post_meta(get_the_ID(), 'wpsc_adv_white', true);
    $wpsc_adv_pause = get_post_meta(get_the_ID(), 'wpsc_adv_pause', true);
    $wpsc_adv_timed = get_post_meta(get_the_ID(), 'wpsc_adv_timed', true);
    $wpsc_adv_pause_ico = get_post_meta(get_the_ID(), 'wpsc_adv_pause_ico', true);

    $wpsc_adv_opening = get_post_meta(get_the_ID(), 'wpsc_adv_opening', true);
    $wpsc_adv_closing = get_post_meta(get_the_ID(), 'wpsc_adv_closing', true);
    $wpsc_coin_name = get_post_meta(get_the_ID(), 'wpsc_coin_name', true);
    $wpsc_coin_symbol = get_post_meta(get_the_ID(), 'wpsc_coin_symbol', true);
    $wpsc_adv_burn = get_post_meta(get_the_ID(), 'wpsc_adv_burn', true);
    $wpsc_adv_pause = get_post_meta(get_the_ID(), 'wpsc_adv_pause', true);
    $wpsc_adv_cap_token = get_post_meta(get_the_ID(), 'wpsc_adv_cap_token', true);
    $wpsc_total_supply = get_post_meta(get_the_ID(), 'wpsc_total_supply', true);
    $wpsc_rate = get_post_meta(get_the_ID(), 'wpsc_rate', true);
    $wpsc_wallet = get_post_meta(get_the_ID(), 'wpsc_wallet', true);
    $wpsc_to_sell = get_post_meta(get_the_ID(), 'wpsc_to_sell', true);
    $erc20_payments = get_post_meta(get_the_ID(), 'erc20_payments', true);
    $erc20_rates = get_post_meta(get_the_ID(), 'erc20_rates', true);

    if (is_array($erc20_payments)) {
      foreach ($erc20_payments as $key => $value) {
        $erc20_payments_rates[] = ["payment_token"=>$erc20_payments[$key], "payment_rate"=>$erc20_rates[$key]];
      }
    } else {
      $erc20_payments_rates = [];
    }

    $wpsc_to_receive = get_post_meta(get_the_ID(), 'wpsc_to_receive', true);

    $args = [
      'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
      'learn-more' =>  __('Learn More', 'wp-smart-contracts'),
      'ico-donation' =>  __('Pausable Initial Coin Offering', 'wp-smart-contracts'),
      'ico-donation-desc' =>  __('A simple Initial Coin Offering that can receive contributions in Ether. Contributors automatically receive an amount of tokens in return.', 'wp-smart-contracts'),
      'crowdsale-advanced' =>  __('Advanced Initial Coin Offering', 'wp-smart-contracts'),
      'crowdsale-advanced-desc' =>  __('An advanced crowdsale to create Initial Coin Offerings with multiple features.', 'wp-smart-contracts'),
      'crowdsale-with-tokens' =>  __('ICO with payments in Tokens', 'wp-smart-contracts'),
      'crowdsale-with-tokens-desc' =>  __('A Crowdsale that allows you to sell an existing token, and also receive payments in any ERC-20 Token', 'wp-smart-contracts'),
      'custom-token' =>  __('You can sell an existing token of yours'),
      'dynamic-cap' =>  __('The maximum cap will be determined by the number of tokens you approve to sell'),
      'payments-in-token' =>  __('You can receive contributions in ERC-20 tokens'),

      'custom-token-tooltip' =>  __('The rest of the ICO contracts create the token for you. This one works with existing tokens you own.'),
      'dynamic-cap-tooltip' =>  __('You sell only the tokens you approve to the ICO'),
      'payments-in-token-tooltip' =>  __('Your ICO can sell tokens in Ether and in ERC-20 Tokens'),

      'flavor' =>  __('Flavor', 'wp-smart-contracts'),
      'ico-spec' =>  __('ICO Specification', 'wp-smart-contracts'), 
      'ico-spec-desc' =>  __('Choose the type of Smart Contract that better suit your needs and define your ICO attributes.', 'wp-smart-contracts'),
      'features' =>  __('Features', 'wp-smart-contracts'),

      "wpsc_flavor" => $wpsc_flavor,
      "wpsc_adv_hard" => $wpsc_adv_hard,
      "wpsc_adv_cap" => $wpsc_adv_cap,
      "wpsc_adv_white" => $wpsc_adv_white,
      "wpsc_adv_pause" => $wpsc_adv_pause,
      "wpsc_adv_timed" => $wpsc_adv_timed,
      "wpsc_adv_pause_ico" => $wpsc_adv_pause_ico,

      'img-custom' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/custom-card.png',
      'erc-20-custom' => __('Looking for something else?', 'wp-smart-contracts'),
      'custom-message' => __('If you need to create a smart contract with custom features we can help', 'wp-smart-contracts'),
      'contact-us' => __('Contact us', 'wp-smart-contracts'),

      "wpsc_adv_opening" => $wpsc_adv_opening,
      "wpsc_adv_closing" => $wpsc_adv_closing,
      "wpsc_coin_name" => $wpsc_coin_name,
      "wpsc_coin_symbol" => $wpsc_coin_symbol,
      "wpsc_adv_burn" => $wpsc_adv_burn,
      "wpsc_adv_pause" => $wpsc_adv_pause,
      "wpsc_adv_cap_token" => $wpsc_adv_cap_token,
      "wpsc_total_supply" => $wpsc_total_supply,
      "wpsc_rate" => $wpsc_rate,
      "wpsc_wallet" => $wpsc_wallet,
      "wpsc_to_sell" => $wpsc_to_sell,
      "erc20_payments_rates" => $erc20_payments_rates,
      "wpsc_to_receive" => $wpsc_to_receive,

      'contributions-ether' =>  __('Receive contributions in Ether', 'wp-smart-contracts'),
      'contributions-ether-tooltip' => __('Supporters can send contributions in Ether directly to your Contract Address', 'wp-smart-contracts'),

      'contributions-sent' =>  __('Supporters can send contributions using an GUI', 'wp-smart-contracts'),
      'contributions-sent-tooltip' => __('Your supporters can use your ICO page to contribute using Metamask', 'wp-smart-contracts'),

      'rate' =>  __('Distribute tokens to supporters automatically', 'wp-smart-contracts'),
      'rate-tooltip' => __('Define the number of tokens that a buyer gets per each contribution', 'wp-smart-contracts'),

      'wallet' =>  __('Receive the funds directly in your ETH wallet', 'wp-smart-contracts'),
      'wallet-tooltip' => __('Funds are automatically sent to your custom Ethereum address for every contribution', 'wp-smart-contracts'),

      'token' =>  __('Standard ERC20 Token emission', 'wp-smart-contracts'),
      'token-tooltip' => __('Your token will a Pistachio token flavor', 'wp-smart-contracts'),

      'token_adv' =>  __('Advanced ERC20 Token emission', 'wp-smart-contracts'),
      'token-tooltip_adv' => __('Your token will a Chocolate token', 'wp-smart-contracts'),

      'token-mint' =>  __('New tokens are minted on every contribution', 'wp-smart-contracts'),
      'token-mint-tooltip' => __('Everytime a supporter sends a contribution the tokens will be minted and assigned to supporters account', 'wp-smart-contracts'),

      'adv-cap' =>  __('Add a hard cap', 'wp-smart-contracts'),
      'adv-cap-tooltip' => __('Crowdsale with a limit for total contributions.', 'wp-smart-contracts'),

      'adv-white' =>  __('Whitelist your contributors', 'wp-smart-contracts'),
      'adv-white-tooltip' => __('Only whitelisted accounts can contribute to your ICO.', 'wp-smart-contracts'),

      'adv-pause' =>  __('Ability to pause/unpause', 'wp-smart-contracts'),
      'adv-pause-tooltip' => __('Admins can pause/unpause the ICO.', 'wp-smart-contracts'),

      'adv-timed' =>  __('Opening and closing dates', 'wp-smart-contracts'),
      'adv-timed-tooltip' => __('Time lapse for the ICO to be open', 'wp-smart-contracts'),
      
      'token-rate' => __('Token distribution rate', 'wp-smart-contracts'),
      'token-rate-desc' => __('The ratio of distribution per each ether contributed.', 'wp-smart-contracts'),
      'token-rate-desc-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('If you want to give 1 Token per ether, then your radio will be 1. If you want to give 100 Tokens per ether, your ratio is 100.', 'wp-smart-contracts')]),
      // https://docs.openzeppelin.com/contracts/2.x/crowdsales.html#crowdsale-rate

      'img-bubblegum' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/bubblegum-card.png',
      'img-bluemoon' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/bluemoon-card.png',
      'img-raspberry' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/raspberry-card.png',

      'advanced-options' => __('ICO Advanced Options', 'wp-smart-contracts'),
      'hard-cap' => __('Hard capped', 'wp-smart-contracts'),
      'hard-cap-desc' => __('Strict limit for total contributions (in ethers)', 'wp-smart-contracts'),
      'hard-cap-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("Your ICO automatically will be closed if the hard cap in ethers is reached. Also it will not accept contributions if they exceed the maximum cap.", 'wp-smart-contracts')]),

      'hard-cap-amount' => __('Hard cap amount (in ether)', 'wp-smart-contracts'),
      'hard-cap-amount-desc' => __('Max amount of ether to be contributed', 'wp-smart-contracts'),
      'hard-cap-amount-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("Specify the maximum amount, in ethers, that your ICO can reach", 'wp-smart-contracts')]),
      'maximum-cap' => __('ethers', 'wp-smart-contracts'),

      'bluemoon-warning' => __('It is strongly recommended that you set a way of finalizing the ICO, it can be as a hardcapped or timed ICO', 'wp-smart-contracts'),

      'pausable' => __("Pausable", 'wp-smart-contracts'),
      'pausable-desc' => __("Ability to pause/unpause your ICO", 'wp-smart-contracts'),
      'pausable-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("The owner of the ICO will be able to pause or resume the ICO activity at any time", 'wp-smart-contracts')]),

      'timed' => __("Timed", 'wp-smart-contracts'),
      'timed-desc' => __("Accept contributions only within a time frame.", 'wp-smart-contracts'),
      'timed-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("Dates are in UTC format, which is equivalent to GMT time", 'wp-smart-contracts')]),
      
      'opening' => __("Opening", 'wp-smart-contracts'),
      'opening-desc' => __("ICO opening time", 'wp-smart-contracts'),
      'opening-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("ICO opening timestamp in Coordinated Universal Time (UTC)", 'wp-smart-contracts')]),

      'closing' => __("Closing", 'wp-smart-contracts'),
      'closing-desc' => __("ICO closing time", 'wp-smart-contracts'),
      'closing-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("ICO closing timestamp in Coordinated Universal Time (UTC)", 'wp-smart-contracts')]),

      'token-definition' =>  __('Token definition', 'wp-smart-contracts'),
      'wallet' =>  __('Wallet', 'wp-smart-contracts'),
      'wallet-desc' =>  __('Ether wallet address to receive funds', 'wp-smart-contracts'),
      'wallet-desc-tooltip' =>  __('The beneficiary ether account that will receive the ICO contributions', 'wp-smart-contracts'),

      'token-to-sell' => __("Token to sell", 'wp-smart-contracts'),
      'token-address' => __("Address", 'wp-smart-contracts'),
      'token-address-desc' => __("Address of the token you want to sell. It has to be an ERC-20 Token address. Otherwise it will fail.", 'wp-smart-contracts'),
      'token-address-desc-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('If you have a token of your own, you can run an ICO with it. You will need to deploy the ICO from the same account you own the tokens.', 'wp-smart-contracts')]),

      'tokens-to-receive' => __("Token payments", 'wp-smart-contracts'),
      'tokens-to-receive-address' => __("Token address"),
      'tokens-to-receive-address-desc' => __("ERC-20 address token to receive payments. It has to be an ERC-20 Token address. Otherwise it will fail."),
      'tokens-to-receive-rate' => __("Token rate"),
      'tokens-to-receive-rate-desc' => __("How many tokens you want to give for every token received."),
      'tokens-to-receive-addresses-desc-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('For example, if you want to sell 100 of your tokens per DAI, then you need to specify "0x6B175474E89094C44Da98b954EedeAC495271d0F" as token and "100" as rate.', 'wp-smart-contracts')]),
      'rate-per-token' => __('Rate per token', 'wp-smart-contracts'),

      'name' =>  __('Name', 'wp-smart-contracts'),
      'name-desc' =>  __('The name of the coin', 'wp-smart-contracts'),
      'name-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('By default the post title will be used if not defined here. Once the contract is deployed this name will be frozen.', 'wp-smart-contracts')]),
      'symbol' =>  __('Symbol', 'wp-smart-contracts'),
      'symbol-desc' =>  __('The symbol of the coin. Keep it short - e.g. "HIX"', 'wp-smart-contracts'),
      'initial-supply' =>  __('Initial Supply', 'wp-smart-contracts'),
      'initial-supply-desc' =>  __('The initial amount of coins that the owner will have at the beginning of the ICO.', 'wp-smart-contracts'),
      'initial-supply-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("The tokens of contributors are going to be minted, so, this is the initial supply of tokens for owners.", 'wp-smart-contracts')]),

      'burnable' =>  __('Burnable', 'wp-smart-contracts'),
      'burnable-desc' =>  __('Ability to irreversibly burn (destroy) coins you own.', 'wp-smart-contracts'),
      'pausable' =>  __('Pausable', 'wp-smart-contracts'),
      'pausable-desc' =>  __('Ability to pause all the activity of the coins.', 'wp-smart-contracts'),
      'mintable_mandatory' =>  __('Mintable (this is mandatory for an ICO)', 'wp-smart-contracts'),
      'mitable-desc' =>  __('Ability to create (mint) new coins for any account.', 'wp-smart-contracts'),
      'mintable-cap' =>  __('Mintable Cap', 'wp-smart-contracts'),
      'mintable-desc' =>  __('If Mintable, this will be the maximum supply the coin can reach.', 'wp-smart-contracts'),
      'burnable-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('Holders will have the ability to burn a specific amount of tokens. This will reduce the total supply of the coin', 'wp-smart-contracts')]),
      'pausable-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('Authorized accounts will have the ability to pause/unpause all transfer and all activity of the coins.', 'wp-smart-contracts')]),
      'mintable-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('Authorized accounts will have the ability to create or mint a specific amount of coins. This will increment the total supply of the coins.', 'wp-smart-contracts')]),
      'mintable-tooltip-cap' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('The maximum capitalization your coin can have. This is an integer number, do not include decimal representation or wei like numbers. 0 cap means unlimited capitalization.', 'wp-smart-contracts')]),

      'pause-ico' =>  __('Pausable ICO', 'wp-smart-contracts'),
      'pause-ico-desc' =>  __('Ability to pause and resume ICO', 'wp-smart-contracts'),
      'pause-ico-tooltip' =>  __('This option is mandatory, otherwise your ICO will sell tokens forever', 'wp-smart-contracts'),

    ];

    if ($wpsc_flavor=="raspberry") $args["is-raspberry"] = true;
    if ($wpsc_flavor=="bluemoon") $args["is-bluemoon"] = true;
    if ($wpsc_flavor=="bubblegum") $args["is-bubblegum"] = true;

    $wpsc_contract_address = get_post_meta(get_the_ID(), 'wpsc_contract_address', true);

    // show contract definition
    if ($wpsc_contract_address) {
      $args["readonly"] = true;
    }

    if ($wpsc_adv_burn=="burnable") {
      $args["is-burnable"]=true;
    }
    if ($wpsc_adv_pause=="pausable") {
      $args["is-pausable"]=true;
    }
    
    return $args;

  }

  static public function getNetworkInfo($wpsc_network) {

    if ($wpsc_network and $arr = WPSC_helpers::getNetworks()) {

      return [
        $arr[$wpsc_network]["color"],
        $arr[$wpsc_network]["icon"],
        $arr[$wpsc_network]["url2"],
        __($arr[$wpsc_network]["title"], 'wp-smart-contracts')
      ];

    }

    return ["", "", "", ""];

  }

  public function wpscSmartContract() {

    $wpsc_network = get_post_meta(get_the_ID(), 'wpsc_network', true);
    $wpsc_txid = get_post_meta(get_the_ID(), 'wpsc_txid', true);
    $wpsc_owner = get_post_meta(get_the_ID(), 'wpsc_owner', true);
    $wpsc_contract_address = get_post_meta(get_the_ID(), 'wpsc_contract_address', true);
    $wpsc_token_contract_address = get_post_meta(get_the_ID(), 'wpsc_token_contract_address', true);
    $wpsc_blockie = get_post_meta(get_the_ID(), 'wpsc_blockie', true);
    $wpsc_blockie_token = get_post_meta(get_the_ID(), 'wpsc_blockie_token', true);
    $wpsc_blockie_owner = get_post_meta(get_the_ID(), 'wpsc_blockie_owner', true);
    $wpsc_qr_code = get_post_meta(get_the_ID(), 'wpsc_qr_code', true);
    $token_id = get_post_meta(get_the_ID(), 'token_id', true);
    $wpsc_token_qr_code = get_post_meta(get_the_ID(), 'wpsc_token_qr_code', true);

    list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);

    $m = new Mustache_Engine;

    // show contract
    if ($wpsc_contract_address) {

      $wpsc_flavor   = get_post_meta(get_the_ID(), 'wpsc_flavor', true);
      $wpsc_adv_hard   = get_post_meta(get_the_ID(), 'wpsc_adv_hard', true);
      $wpsc_adv_cap  = get_post_meta(get_the_ID(), 'wpsc_adv_cap', true);
      $wpsc_adv_white  = get_post_meta(get_the_ID(), 'wpsc_adv_white', true);
      $wpsc_adv_pause  = get_post_meta(get_the_ID(), 'wpsc_adv_pause', true);
      $wpsc_adv_timed  = get_post_meta(get_the_ID(), 'wpsc_adv_timed', true);
      $wpsc_adv_pause_ico  = get_post_meta(get_the_ID(), 'wpsc_adv_pause_ico', true);
      
      $wpsc_adv_opening  = get_post_meta(get_the_ID(), 'wpsc_adv_opening', true);
      $wpsc_adv_closing  = get_post_meta(get_the_ID(), 'wpsc_adv_closing', true);      
      $wpsc_coin_name = get_post_meta(get_the_ID(), 'wpsc_coin_name', true);
      $wpsc_coin_symbol = get_post_meta(get_the_ID(), 'wpsc_coin_symbol', true);

      $wpsc_adv_burn = get_post_meta(get_the_ID(), 'wpsc_adv_burn', true);
      $wpsc_adv_pause = get_post_meta(get_the_ID(), 'wpsc_adv_pause', true);
      $wpsc_adv_cap_token = get_post_meta(get_the_ID(), 'wpsc_adv_cap_token', true);
      
      $wpsc_total_supply = get_post_meta(get_the_ID(), 'wpsc_total_supply', true);
      $wpsc_rate = get_post_meta(get_the_ID(), 'wpsc_rate', true);
      $wpsc_wallet = get_post_meta(get_the_ID(), 'wpsc_wallet', true);
      $wpsc_to_sell = get_post_meta(get_the_ID(), 'wpsc_to_sell', true);
      $erc20_rates = get_post_meta(get_the_ID(), 'erc20_rates', true);
      $erc20_payments = get_post_meta(get_the_ID(), 'erc20_payments', true);
      $wpsc_to_receive = get_post_meta(get_the_ID(), 'wpsc_to_receive', true);

      if (is_array($erc20_payments)) {
        foreach ($erc20_payments as $key => $value) {
          $erc20_payments_rates[] = ["payment_token"=>$erc20_payments[$key], "payment_rate"=>$erc20_rates[$key]];
        }
      } else {
        $erc20_payments_rates = [];
      }

      $icoInfo = [
        "type" => $wpsc_flavor,
        "hardcapped" => $wpsc_adv_hard,
        "hardcap" => $wpsc_adv_cap,
        "whitelist" => $wpsc_adv_white,
        "pause" => $wpsc_adv_pause,
        "timed" => $wpsc_adv_timed,
        "pause_ico" => $wpsc_adv_pause_ico,
        "opening" => $wpsc_adv_opening,
        "closing" => $wpsc_adv_closing,
        "name" => $wpsc_coin_name,
        "symbol" => $wpsc_coin_symbol,
        "burn" => $wpsc_adv_burn,
        "pause" => $wpsc_adv_pause,
        "cap_token" => $wpsc_adv_cap_token,
        "supply" => $wpsc_total_supply,
        "rate" => $wpsc_rate,
        "wallet" => $wpsc_wallet,
        "to_sell" => $wpsc_to_sell,
        "erc20_payments_rates" => $erc20_payments_rates,
        "to_receive" => $wpsc_to_receive,
        "hard_capped_label" => __("Hard capped", "wp-smart-contracts"),
        "pausable_label" => __("Pausable", "wp-smart-contracts"),
        "timed_label" => __("Timed", "wp-smart-contracts"),
        "symbol_label" => __("Symbol", "wp-smart-contracts"),
        "name_label" => __("Name", "wp-smart-contracts"),
        "supply_label" => __("Supply", "wp-smart-contracts"),
        "rate_label" => __("Rate", "wp-smart-contracts"),
        "wallet_label" => __("Wallet", "wp-smart-contracts"),
      ];

      if ($wpsc_flavor=="raspberry") $icoInfo["color"] = "red";
      if ($wpsc_flavor=="bluemoon") {
        $icoInfo["color"] = "teal";
        $icoInfo["is_bluemoon"] = true;
      }
      if ($wpsc_flavor=="bubblegum") {
        $icoInfo["color"] = "purple";
        $icoInfo["is_bubblegum"] = true;
      }

      $icoInfo["imgUrl"] = dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/';

      $atts = [
        'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
        'learn-more' => __('Learn More', 'wp-smart-contracts'),
        'smart-contract-desc' => __('Go live with your ICO. You can publish your ICO in a test net or in the main Ethereum network.', 'wp-smart-contracts'),
        'ico-deployed-smart-contract' => __('ICO Smart Contract', 'wp-smart-contracts'),
        'token-deployed-smart-contract' => __('Token Smart Contract', 'wp-smart-contracts'),
        'ethereum-network' => $network_val,
        'ethereum-color' => $color,
        'ethereum-icon' => $icon,
        'contract-address' => $wpsc_contract_address,
        'token-contract-address' => $wpsc_token_contract_address,
        'etherscan' => $etherscan,
        'contract-address-text' => __('Contract Address', 'wp-smart-contracts'),
        'contract-address-desc' => __('The Smart Contract Address of your ico', 'wp-smart-contracts'),
        'txid-text' => __('Transaction ID', 'wp-smart-contracts'),
        'owner-text' => __('Owner Account', 'wp-smart-contracts'),
        'ico-name' => ucwords(get_post_meta(get_the_ID(), 'wpsc_coin_name', true)),
        'ico-symbol' => strtoupper(get_post_meta(get_the_ID(), 'wpsc_coin_symbol', true)), 

        'ico-burn' => strtoupper(get_post_meta(get_the_ID(), 'wpsc_adv_burn', true)), 
        'ico-pause' => strtoupper(get_post_meta(get_the_ID(), 'wpsc_adv_pause', true)), 
        'ico-cap-token' => strtoupper(get_post_meta(get_the_ID(), 'wpsc_adv_cap_token', true)), 
        
        'qr-code-data' => $wpsc_qr_code,
        'edit_token_link' => get_edit_post_link($token_id),
        'token-qr-code-data' => $wpsc_token_qr_code,
        'blockie' => $m->render(WPSC_Mustache::getTemplate('blockies'), ['blockie' => $wpsc_blockie]),
        'blockie-token' => $m->render(WPSC_Mustache::getTemplate('blockies'), ['blockie' => $wpsc_blockie_token]),
        'blockie-owner' => $m->render(WPSC_Mustache::getTemplate('blockies'), ['blockie' => $wpsc_blockie_owner]),
        'ico-info-ico' => $m->render(WPSC_Mustache::getTemplate('ico-info-ico'), $icoInfo),
        'ico-info-token' => $m->render(WPSC_Mustache::getTemplate('ico-info-token'), $icoInfo),
        'ico-logo' => get_the_post_thumbnail_url(get_the_ID()),
        'contract-address-short' => WPSC_helpers::shortify($wpsc_contract_address),
        'token-contract-address-short' => WPSC_helpers::shortify($wpsc_token_contract_address),
        'txid' => $wpsc_txid,
        'txid-short' => WPSC_helpers::shortify($wpsc_txid),
        'owner' => $wpsc_owner,
        'owner-short' => WPSC_helpers::shortify($wpsc_owner),
        'block-explorer' => __('Block Explorer', 'wp-smart-contracts'),
        'color' => $icoInfo["color"],
        'approve-funds' => __("Approve Funds", 'wp-smart-contracts'),
        'funds-approved' => __("Remaining tokens to sell", 'wp-smart-contracts'),
        'tokens-to-sell' => __("Tokens to sell", 'wp-smart-contracts'),
        'tokens-to-sell-desc' => __("You need to approve funds to your ICO to be able to sell tokens", 'wp-smart-contracts'),
        'text' => __('To approve funds to your ICO you need to be connected to an Ethereum Network. Please connect to Metamask and choose the right network to continue.', 'wp-smart-contracts'),
        'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
        'connect-to-metamask' => __('Connect to Metamask', 'wp-smart-contracts'),

      ];

      if ($wpsc_txid) {
        $atts["txid_exists"] = true;
      }

      if ($wpsc_flavor=="bubblegum") {
        $atts["is_bubblegum"] = true;
      }

      echo $m->render(WPSC_Mustache::getTemplate('metabox-smart-contract-ico'), $atts);

    // show buttons to load or create a contract
    } else {

      echo $m->render(
        WPSC_Mustache::getTemplate('metabox-smart-contract-buttons-ico'),
        self::getSmartContractButtons()
      );

    }

  }

  static public function getSmartContractButtons() {

    $m = new Mustache_Engine;
    
    return [
      'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
      'learn-more' => __('Learn More', 'wp-smart-contracts'),
      'smart-contract-desc' => __('Go live with your ICO. You can publish your ICO in a test net or in the main Ethereum network.', 'wp-smart-contracts'),
      'new-smart-contract' => __('New Smart Contract', 'wp-smart-contracts'),
      'text' => __('To deploy your Smart Contracts you need to be connected to an Ethereum Network. Please connect to Metamask and choose the right network to continue.', 'wp-smart-contracts'),
      'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
      'connect-to-metamask' => __('Connect to Metamask', 'wp-smart-contracts'),
      'deploy-with-wpst' => $m->render(
        WPSC_Mustache::getTemplate('metabox-smart-contract-buttons-wpst'),
        [
          'deploy' => __('Deploy', 'wp-smart-contracts'),
          'deploy-desc' => __('Deploy your Smart Contract to the Blockchain using Ether', 'wp-smart-contracts'),
          'deploy-desc-token' => __('Deploy your Smart Contract to the Blockchain using WPIC is a two step process:', 'wp-smart-contracts'),
          'deploy-desc-token-1' => __('First you need to authorize the factory to use the WPIC funds', 'wp-smart-contracts'),
          'deploy-desc-token-2' => __('Then you can deploy your contract using WPIC', 'wp-smart-contracts'),
          'no-wpst' => __('No WPIC found', 'wp-smart-contracts'),
          'not-enough-wpst' => __('Not enough WPIC found', 'wp-smart-contracts'),
          'authorize' => __('Authorize', 'wp-smart-contracts'),
          'authorize-complete' => __('Authorization was successful, click "Deploy" to proceed', 'wp-smart-contracts'),
          'deploy-token' => __('Deploy using WP Ice Cream (WPIC)', 'wp-smart-contracts'),
          'deploy-token-image' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/wp-smart-token.png',
          'deploy-using-ether' => __('Deploy using Ether', 'wp-smart-contracts'),
          'learn-how-to-get-wpst' => __('Learn how to get WPIC', 'wp-smart-contracts'),
          'learn-how-to-get-ether' => __('Learn how to get Ether', 'wp-smart-contracts'),
          'do-you-have-an-erc20-address' => __('Do you already have an ERC20 token address?', 'wp-smart-contracts'),
          'wpst-balance' => __('WPIC Balance', 'wp-smart-contracts'),
          'button-id' => "wpsc-deploy-contract-button-ico",
          'authorize-button-id' => "wpsc-deploy-contract-button-wpst-authorize-ico",
          'deploy-button-wpst' => "wpsc-deploy-contract-button-wpst-deploy-ico",
        ]
      ),
      
      'ethereum-address' => __('Ethereum Network Contract Address', 'wp-smart-contracts'),
      'ethereum-address-desc' => __('Please fill out the contract address you want to import', 'wp-smart-contracts'),
      'ethereum-address-important' => __('Important', 'wp-smart-contracts'),
      'ethereum-address-important-message' => __('Keep in mind that the contract is going to be loaded using the current network and current ethereum account as owner', 'wp-smart-contracts'),
      'active-net-account' => __('Currently active Ethereum Network and account:', 'wp-smart-contracts'),
      'smart-contract-address' => __('Smart Contract Address'),
      'load' => __('Load', 'wp-smart-contracts'),
      'ethereum-deploy' => __('Network Deploy', 'wp-smart-contracts'),
      'ethereum-deploy-desc' => __('Are you ready to deploy your ICO to the currently active Ethereum Network?', 'wp-smart-contracts'),
      'cancel' => __('Cancel', 'wp-smart-contracts'),
      'yes-proceed' => __('Yes, please proceed', 'wp-smart-contracts'),
      'deployed-smart-contract' => __('Deployed Smart Contract', 'wp-smart-contracts'),
    ];

  }

  public function wpscSourceCode() {

      // load the contract technical atts
      $atts = WPSC_Metabox::wpscGetMetaSourceCodeAtts();

      if (!empty($atts)) {

        $m = new Mustache_Engine;
        echo $m->render(
          WPSC_Mustache::getTemplate('metabox-source-code'),
          $atts
        );

      }

  }

  // with great powers... 
  public static function wpscReminder() {
    echo WPSC_Metabox::wpscReminder();
  }

  public function wpscSidebar() {

    $m = new Mustache_Engine;
    echo $m->render(
      WPSC_Mustache::getTemplate('metabox-sidebar-ico'),
      [
        'white-logo' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/wpsc-white-logo.png',
        'tutorials-tools' => __('Tutorials', 'wp-smart-contracts'),
        'tutorials-tools-desc' => __('Here you can find a few tutorials that might be useful to deploy, test and use your Smart Contracts.', 'wp-smart-contracts'),
        'screencasts' => __('Screencasts'),
        'deploy' => __('How to deploy an ICO using Ether?'),
        'deploy_raspberry' => 's7UNeWVbR60',
        'deploy_bluemoon' => 'nKurlOCvDn8',
        'deploy_bubblegum' => 'nKurlOCvDn8',
        'wpic_info' => WPSC_helpers::renderWPICInfo(),
        'learn-more' => __('Learn More', 'wp-smart-contracts'),
        'docs' => __('Documentation', 'wp-smart-contracts'),
        'doc' => "https://wpsmartcontracts.com/doc-ico.php",
        'wpsc-logo' => dirname( plugin_dir_url( __FILE__ )) . '/assets/img/wpsc-logo.png',
      ]
    );

  }

}
