<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Create Metaboxes for CPT Crowdfunding
 */

new WPSC_MetaboxCrowdfunding();

class WPSC_MetaboxCrowdfunding {

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
        'wpsc_crowdfunding_metabox', 
        'WPSmartContracts: Crowdfunding Specification', 
        [$this, 'wpscSmartontractSpecification'], 
        'crowdfunding', 
        'normal', 
        'default'
      );
    }
    
    add_meta_box(
      'wpsc_smart_contract', 
      'WPSmartContracts: Smart Contract', 
      [$this, 'wpscSmartContract'], 
      'crowdfunding', 
      'normal', 
      'default'
    );
    
    add_meta_box(
      'wpsc_code_crowd', 
      'WPSmartContracts: Source Code', 
      [$this, 'wpscSourceCode'], 
      'crowdfunding', 
      'normal', 
      'default'
    );

    add_meta_box(
      'wpsc_reminder_crowd', 
      'WPSmartContracts: Friendly Reminder', 
      [__CLASS__, 'wpscReminder'], 
      'crowdfunding', 
      'normal', 
      'default'
    );

    add_meta_box(
      'wpsc_sidebar', 
      'WPSmartContracts: Tutorials & Tools', 
      [$this, 'wpscSidebar'], 
      'crowdfunding', 
      'side', 
      'default'
    );

  }

  public function saveRepeatableFields($post_id, $post, $update) {

      if ($post->post_type == "crowdfunding") {

        if ( ! isset( $_POST['wpsc_repeatable_meta_box_nonce'] ) ||
        ! wp_verify_nonce( $_POST['wpsc_repeatable_meta_box_nonce'], 'wpsc_repeatable_meta_box_nonce' ) )
            return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (!current_user_can('edit_post', $post_id))
            return;

        // if the contract was not deployed yet, save the crowdfunding definitions
        if (!$_POST["wpsc-readonly"]) {

          // get and clean all inputs
          $wpsc_flavor = WPSC_Metabox::cleanUpText($_POST["wpsc-flavor"]);
          $wpsc_minimum = WPSC_Metabox::cleanUpText($_POST["wpsc-minimum"]);
          $wpsc_approvers = WPSC_Metabox::cleanUpText($_POST["wpsc-approvers"]);

          $wpsc_network = WPSC_Metabox::cleanUpText($_POST["wpsc-network"]);
          $wpsc_txid = WPSC_Metabox::cleanUpText($_POST["wpsc-txid"]);
          $wpsc_owner = WPSC_Metabox::cleanUpText($_POST["wpsc-owner"]);
          $wpsc_contract_address = WPSC_Metabox::cleanUpText($_POST["wpsc-contract-address"]);
          $wpsc_factory = $_POST["wpsc-factory"];
          $wpsc_blockie = WPSC_Metabox::cleanUpText($_POST["wpsc-blockie"]);
          $wpsc_blockie_owner = WPSC_Metabox::cleanUpText($_POST["wpsc-blockie-owner"]);
          $wpsc_qr_code = WPSC_Metabox::cleanUpText($_POST["wpsc-qr-code"]);

          update_post_meta($post_id, 'wpsc_flavor', $wpsc_flavor);
          update_post_meta($post_id, 'wpsc_minimum', $wpsc_minimum);
          update_post_meta($post_id, 'wpsc_approvers', $wpsc_approvers);

          // if set, save the contract info meta
          if ($wpsc_network) update_post_meta($post_id, 'wpsc_network', $wpsc_network);
          if ($wpsc_txid) update_post_meta($post_id, 'wpsc_txid', $wpsc_txid);
          if ($wpsc_owner) update_post_meta($post_id, 'wpsc_owner', $wpsc_owner);
          if ($wpsc_contract_address) update_post_meta($post_id, 'wpsc_contract_address', $wpsc_contract_address);
          if ($wpsc_factory) update_post_meta($post_id, 'wpsc_factory', $wpsc_factory);
          if ($wpsc_blockie) update_post_meta($post_id, 'wpsc_blockie', $wpsc_blockie);
          if ($wpsc_blockie_owner) update_post_meta($post_id, 'wpsc_blockie_owner', $wpsc_blockie_owner);
          if ($wpsc_qr_code) update_post_meta($post_id, 'wpsc_qr_code', $wpsc_qr_code);

        } 

      }

  }

  static public function getMetaboxCrodfundingArgs() {

    $wpsc_flavor        = get_post_meta(get_the_ID(), 'wpsc_flavor', true);
    $wpsc_minimum       = get_post_meta(get_the_ID(), 'wpsc_minimum', true);
    $wpsc_approvers     = get_post_meta(get_the_ID(), 'wpsc_approvers', true);

    $m = new Mustache_Engine;

    $args = [
      'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
      'learn-more' =>  __('Learn More', 'wp-smart-contracts'),
      'crowdfunding-donation' =>  __('Safe Crowdfunding', 'wp-smart-contracts'),
      'crowdfunding-donation-desc' =>  __('A simple crowdfunding campaign that can receive contributions in Ether. The owner can spend the donations only on contributors approval.', 'wp-smart-contracts'),
      'flavor' =>  __('Flavor', 'wp-smart-contracts'),
      'crowdfunding-spec' =>  __('Crowdfunding Specification', 'wp-smart-contracts'), 
      'crowdfunding-spec-desc' =>  __('Choose the type of Smart Contract that better suit your needs and define your Crowdfunding attributes.', 'wp-smart-contracts'),
      'features' =>  __('Features', 'wp-smart-contracts'),
      'contributions-ether' =>  __('Can receive contributions in Ether', 'wp-smart-contracts'),
      'contributions-ether-tooltip' => __('Supporters can send contributions in Ether', 'wp-smart-contracts'),
      'safe-minimum' =>  __('Minimum amount for contributions', 'wp-smart-contracts'),
      'safe-minimum-tooltip' => __('The crowdfunding will not accept contributions for less than this minimum', 'wp-smart-contracts'),
      'safe-approval' =>  __('Funds release will depend on contributos approval.', 'wp-smart-contracts'),
      'safe-approval-tooltip' => __('Request to spend the funds must be approved by contributors', 'wp-smart-contracts'),
      'wpsc-minimum' => $wpsc_minimum,

      'img-custom' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/custom-card.png',
      'erc-20-custom' => __('Looking for something else?', 'wp-smart-contracts'),
      'custom-message' => __('If you need to create a smart contract with custom features we can help', 'wp-smart-contracts'),
      'contact-us' => __('Contact us', 'wp-smart-contracts'),

      'Minted-Crowdsale' => __('Minted Crowdsale', 'wp-smart-contracts'),
      'Minted-Crowdsale-tooltip' => __('Minted Crowdsale', 'wp-smart-contracts'),
      'Capped-Crowdsale' => __('Capped Crowdsale', 'wp-smart-contracts'),
      'Capped-Crowdsale-tooltip' => __('Capped Crowdsale', 'wp-smart-contracts'),
      'Timed-Crowdsale' => __('Timed Crowdsale', 'wp-smart-contracts'),
      'Timed-Crowdsale-tooltip' => __('Timed Crowdsale', 'wp-smart-contracts'),
      'Whitelisted-Crowdsale' => __('Whitelisted Crowdsale', 'wp-smart-contracts'),
      'Whitelisted-Crowdsale-tooltip' => __('Whitelisted Crowdsale', 'wp-smart-contracts'),
      'Refundable-Crowdsale' => __('Refundable Crowdsale', 'wp-smart-contracts'),
      'Refundable-Crowdsale-tooltip' => __('Refundable Crowdsale', 'wp-smart-contracts'),
      'ICO-Presale' => __('ICO Presale', 'wp-smart-contracts'),
      'ICO-Presale-tooltip' => __('ICO Presale', 'wp-smart-contracts'),
      'Finalize-Crowdsale' => __('Finalize Crowdsale', 'wp-smart-contracts'),
      'Finalize-Crowdsale-tooltip' => __('Finalize Crowdsale', 'wp-smart-contracts'),
      'Token-Distribution' => __('Token Distribution', 'wp-smart-contracts'),
      'Token-Distribution-tooltip' => __('Token Distribution', 'wp-smart-contracts'),
      'safecrowd-options' => __('Safe Crowdfunding Options', 'wp-smart-contracts'),
      'minimum' => __('Minimum contribution', 'wp-smart-contracts'),
      'minimum-desc' =>  __('The crowdfunding will not accept contributions for less than this minimum', 'wp-smart-contracts'),
      'minimum-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('Minimum contributions are set in Ethers, you can specify a minimum of less than 1 Ether using points as a decimal separator, i.e: 0.01', 'wp-smart-contracts')]),
      'approvers' => __('Percentage of approvers required', 'wp-smart-contracts'),
      'approvers-desc' =>  __('% of approvers required to release a request of the owner', 'wp-smart-contracts'),
      'approvers-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __('Owner can\'t release the funds unless this minimum percentage of contributors approves the transfer.' , 'wp-smart-contracts')]),
      'img-mango' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/mango-card.png',
    ];

    // create an index array with the percent label, like [percent-25] => true
    $args["percent-" . str_replace('%', '', $wpsc_approvers)] = true;
    $args["percent"] = $wpsc_approvers;

    if ($wpsc_flavor=="mango") $args["is-mango"] = true;
    if ($wpsc_flavor=="bluemoon") $args["is-bluemoon"] = true;
    if ($wpsc_flavor=="bubblegum") $args["is-bubblegum"] = true;

    $wpsc_contract_address = get_post_meta(get_the_ID(), 'wpsc_contract_address', true);

    // show contract definition
    if ($wpsc_contract_address) {
      $args["readonly"] = true;
    }

    return $args;
    
  }

  public function wpscSmartontractSpecification() {

    wp_nonce_field( 'wpsc_repeatable_meta_box_nonce', 'wpsc_repeatable_meta_box_nonce' );

    $m = new Mustache_Engine;

    $args = self::getMetaboxCrodfundingArgs();
    
    echo $m->render(
      WPSC_Mustache::getTemplate('metabox-crowdfunding'),
      $args
    );

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
    $wpsc_blockie = get_post_meta(get_the_ID(), 'wpsc_blockie', true);
    $wpsc_blockie_owner = get_post_meta(get_the_ID(), 'wpsc_blockie_owner', true);
    $wpsc_qr_code = get_post_meta(get_the_ID(), 'wpsc_qr_code', true);

    list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);

    $m = new Mustache_Engine;

    // show contract
    if ($wpsc_contract_address) {

      $wpsc_flavor     = get_post_meta(get_the_ID(), 'wpsc_flavor', true);
      $wpsc_minimum    = get_post_meta(get_the_ID(), 'wpsc_minimum', true);
      $wpsc_approvers  = get_post_meta(get_the_ID(), 'wpsc_approvers', true);

      $crowdInfo = [
        "type" => $wpsc_flavor,
        "factor" => $wpsc_approvers,
        "minimum" => $wpsc_minimum,
        "approvers_label" => __("Approvers Percentage", "wp-smart-contracts"),
        "minimum_label" => __("Minimum", "wp-smart-contracts")
        
      ];
      if ($wpsc_flavor=="mango") $crowdInfo["color"] = "orange";
      if ($wpsc_flavor=="bluemoon") $crowdInfo["color"] = "teal";
      if ($wpsc_flavor=="bubblegum") $crowdInfo["color"] = "purple";

      $crowdInfo["imgUrl"] = dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/';

       $atts = [
        'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
        'learn-more' => __('Learn More', 'wp-smart-contracts'),
        'smart-contract-desc' => __('Go live with your Crowdfunding. You can publish your Crowdfunding in a test net or in the main Ethereum network.', 'wp-smart-contracts'),
        'deployed-smart-contract' => __('Deployed Smart Contract', 'wp-smart-contracts'),
        'ethereum-network' => $network_val,
        'ethereum-color' => $color,
        'ethereum-icon' => $icon,
        'contract-address' => $wpsc_contract_address,
        'etherscan' => $etherscan,
        'contract-address-text' => __('Contract Address', 'wp-smart-contracts'),
        'contract-address-desc' => __('The Smart Contract Address of your crowdfunding', 'wp-smart-contracts'),
        'txid-text' => __('Transaction ID', 'wp-smart-contracts'),
        'owner-text' => __('Owner Account', 'wp-smart-contracts'),
        'crowdfunding-name' => ucwords(get_post_meta(get_the_ID(), 'wpsc_coin_name', true)),
        'crowdfunding-symbol' => strtoupper(get_post_meta(get_the_ID(), 'wpsc_coin_symbol', true)), 
        'qr-code-data' => $wpsc_qr_code,
        'blockie' => $m->render(WPSC_Mustache::getTemplate('blockies'), ['blockie' => $wpsc_blockie]),
        'blockie-owner' => $m->render(WPSC_Mustache::getTemplate('blockies'), ['blockie' => $wpsc_blockie_owner]),
        'crowdfunding-info' => $m->render(WPSC_Mustache::getTemplate('crowdfunding-info'), $crowdInfo),
        'crowdfunding-logo' => get_the_post_thumbnail_url(get_the_ID()),
        'contract-address-short' => WPSC_helpers::shortify($wpsc_contract_address),
        'txid' => $wpsc_txid,
        'txid-short' => WPSC_helpers::shortify($wpsc_txid),
        'owner' => $wpsc_owner,
        'owner-short' => WPSC_helpers::shortify($wpsc_owner),
        'block-explorer' => __('Block Explorer', 'wp-smart-contracts'),
      ];

      if ($wpsc_txid) {
        $atts["txid_exists"] = true;
      }

      echo $m->render(WPSC_Mustache::getTemplate('metabox-smart-contract'), $atts);

    // show buttons to load or create a contract
    } else {

      echo $m->render(
        WPSC_Mustache::getTemplate('metabox-smart-contract-buttons-crowdfunding'),
        self::getSmartContractButtons(false)
      );

    }

  }

  static public function getSmartContractButtons($show_load=true) {

    $m = new Mustache_Engine;

    return [

      'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
      'learn-more' => __('Learn More', 'wp-smart-contracts'),
      'smart-contract-desc' => __('Go live with your Crowdfunding. You can publish your Crowdfunding in a test net or in the main Ethereum network.', 'wp-smart-contracts'),
      'new-smart-contract' => __('New Smart Contract', 'wp-smart-contracts'),
      'text' => __('To deploy your Smart Contracts you need to be connected to an Ethereum Network. Please connect to Metamask and choose the right network to continue.', 'wp-smart-contracts'),
      'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
      'connect-to-metamask' => __('Connect to Metamask', 'wp-smart-contracts'),
      'deploy-with-wpst' => $m->render(
        WPSC_Mustache::getTemplate('metabox-smart-contract-buttons-wpst'),
        [
          'show_load' => $show_load,
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
          'load' => __('Load', 'wp-smart-contracts'),
          'load-desc' => __('Load an existing Smart Contract', 'wp-smart-contracts'),
          'button-id' => "wpsc-deploy-contract-button-crowdfunding",
          'authorize-button-id' => "wpsc-deploy-contract-button-wpst-authorize-crowd",
          'deploy-button-wpst' => "wpsc-deploy-contract-button-wpst-deploy-crowd",
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
      'ethereum-deploy-desc' => __('Are you ready to deploy your Crowdfunding to the currently active Ethereum Network?', 'wp-smart-contracts'),
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
      WPSC_Mustache::getTemplate('metabox-sidebar-crowdfunding'),
      [
        'white-logo' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/wpsc-white-logo.png',
        'tutorials-tools' => __('Tutorials', 'wp-smart-contracts'),
        'tutorials-tools-desc' => __('Here you can find a few tutorials that might be useful to deploy, test and use your Smart Contracts.', 'wp-smart-contracts'),
        'screencasts' => __('Screencasts'),
        'deploy' => __('How to deploy a crowdfunding using Ether?'),
        'deploy_mango' => 'VIxQ2s8076g',
        'wpic_info' => WPSC_helpers::renderWPICInfo(),
        'learn-more' => __('Learn More', 'wp-smart-contracts'),
        'docs' => __('Documentation', 'wp-smart-contracts'),
        'doc' => "https://wpsmartcontracts.com/doc-crowdfundings-mango.php",
        'wpsc-logo' => dirname( plugin_dir_url( __FILE__ )) . '/assets/img/wpsc-logo.png',
      ]
    );

  }

}
