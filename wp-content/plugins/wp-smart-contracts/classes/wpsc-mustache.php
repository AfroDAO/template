<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Include Mustache Lib into PHP and JS
 */

// Load PHP Mustache
require_once("vendor/autoload.php");


// JS Mustache Logic

new WPSC_Mustache();

class WPSC_Mustache {

    function __construct() {

        // Load admin scripts
        add_action('admin_enqueue_scripts' , [$this, 'loadMustacheJS'], 10, 2 );
        add_action('in_admin_footer', [$this, 'loadTemplatesForCurrentPage']);

        // Load FE scripts
        add_action('wp_enqueue_scripts' , [$this, 'loadMustacheJS'], 10, 2 );
        add_action('wp_footer', [$this, 'loadTemplatesForCurrentPageFE']);

    }

    // Enqueue Mustache JS
    public function loadMustacheJS($hook) {
        
        // enqueue mustache library
        wp_enqueue_script( 'wp-smart-contracts-mustache', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/mustache.min.js' );

    }

    // this will load the templates used in admin
    public function loadTemplatesForCurrentPage($hook) {

        // post edition and creation
        if ( 'post.php' != $hook and 'post-new.php' != $hook) {
            $this->showMustacheTemplates();
        } else {
            $this->showTemplate("wp-admin-bar");
        }

    }

    public function showMustacheTemplates() {
        // messages and warning
        $this->showTemplate("msg-box");
        // disclaimers and deploy conditions
        $this->showTemplate("deploy-warning-message");
        // Token Confirmation modal
        $this->showTemplate("token-info");
        // Crowdfunding Confirmation modal
        $this->showTemplate("crowdfunding-info");
        // ICO Confirmation modal
        $this->showTemplate("ico-info");
        $this->showTemplate("ico-info-ico");
        $this->showTemplate("ico-info-token");
        // Deploy animation
        $this->showTemplate("deploy-animation");
        // Connect Metamask button for Msg box 
        $this->showTemplate("wp-admin-bar-connect-ethereum");        
        // admin bar
        $this->showTemplate("wp-admin-bar");
    }

    // this will load the templates used in frontend
    public function loadTemplatesForCurrentPageFE() {

        // wpsc_coin shortcode JS templates
        if (get_post_field( 'post_type', get_the_ID() ) == "coin" or 
            get_post_field( 'post_type', get_the_ID() ) == "crowdfunding" or 
            get_post_field( 'post_type', get_the_ID() ) == "ico" or 
            has_shortcode( get_post_field('post_content', get_the_ID()), 'wpsc_coin' ) or 
            has_shortcode( get_post_field('post_content', get_the_ID()), 'wpsc_crowdfunding' ) or 
            has_shortcode( get_post_field('post_content', get_the_ID()), 'wpsc_ico' )
        ) {
            $this->showTemplate("coin-view-block-explorer-rows");
            $this->showTemplate("coin-view-block-explorer-loader");
            $this->showTemplate("coin-view-block-explorer-balance");
            $this->showTemplate("coin-view-block-explorer-role");
            $this->showTemplate("coin-view-block-explorer-txid");
            $this->showTemplate("coin-view-block-explorer-txid-detail");
            $this->showTemplate("msg-box");
            $this->showTemplate("crowdfunding-info");
            $this->showTemplate("ico-info");
            $this->showTemplate("ico-info-ico");
            $this->showTemplate("ico-info-token");
            $this->showTemplate("crowd-view-request");
            $this->showTemplate("crowd-view-request-finalized");
        }

    }

    // load the translation texts for JS
    private function loadTranslationsForTag($tag) {

        $json = false;

        $network = get_post_meta(get_the_ID(), "wpsc_network", true);

        if ($network==77) {
            $native_coin = "xDai";
        } else {
            $native_coin = "Ether";
        }

        switch ($tag) {
            case 'wp-admin-bar':
                $json = json_encode([
                    'METAMASK_NO' => __('Unable to connect to the Ethereum Network. Please install and run MetaMask.', 'wp-smart-contracts'),
                    'METAMASK_NO_SHORT' => __('MetaMask not found.', 'wp-smart-contracts'),
                    'NETWORK_ETHEREUM' => __('Main Ethereum Network', 'wp-smart-contracts'),
                    'NETWORK_ROPSTEN' => __('Ropsten Test Network', 'wp-smart-contracts'),
                    'NETWORK_KOVAN' => __('Kovan Test Network', 'wp-smart-contracts'),
                    'NETWORK_RINKEBY' => __('Rinkeby Test Network', 'wp-smart-contracts'),
                    'METAMASK_YOU_SELECTED' => __('You have selected', 'wp-smart-contracts'),
                    'METAMASK_NOT_LOGGED_IN' => __('but looks like you are not logged in with MetaMask, please Log In if you want to deploy Smart Contracts', 'wp-smart-contracts'),
                    'METAMASK_YOU_ARE_CONNECTED' => __('You are connected to', 'wp-smart-contracts'),
                    'METAMASK_WITH_ACCOUNT' => __('with the account', 'wp-smart-contracts'),
                    'ERROR' => __('ERROR, PLEASE CONNECT TO METAMASK', 'wp-smart-contracts'),
                    'METAMASK_NOT_LOGGED_IN' => __('Do you see a "Connect to Ehereum Network" in WP admin bar? if so, click there to connect. Are you logged in to Metamask? if not, please log in. If the error persist try reloading this page.', 'wp-smart-contracts'), 
                    'NETWORK_ERROR' => __("Network error", 'wp-smart-contracts'),
                    'UNKNOWN_NETWORK' => __("Unknown network selected", 'wp-smart-contracts'),
                    'TRANSIENT_CACHE' => __("Transient Cache", 'wp-smart-contracts'),
                    'TRANSIENT_CACHE_FLUSHED' => __("Transient Cache flushed", 'wp-smart-contracts'),
                    'FOX' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                    'CONNECT_WITH_METAMASK' => __('Connect to Ethereum Network', 'wp-smart-contracts'),
                    'WPST_LOGO' => plugins_url( "assets/img/wp-smart-token.png", dirname(__FILE__) ),
                    'AUTHORIZE_WPST' => __("Authorize the use of WPIC funds", 'wp-smart-contracts'),
                    'DEPLOY_USING_WPST' => __("Deploy using WP Ice Cream", 'wp-smart-contracts'),
                    'ETHEREUM_DEPLOY' => __('Network Deploy', 'wp-smart-contracts'),
                    'ETHEREUM_DEPLOY_DESC' => __('Are you ready to deploy your Coin to the currently active Ethereum Network?', 'wp-smart-contracts'),
          
                ]);
                break;
            case 'deploy-warning-message':
                $tos = <<<TOS
<div class="wpsc-tos">
The following clauses will govern the use of the WPSmartContracts Plugin:<br /><br />
<strong>I. TOS Agreement</strong><br /><br />
By accessing the website at http://www.wpsmartcontracts.com and using the WPSmartContracts.com plugin, you agree to be bound by these terms of service, all applicable laws, and regulations. You agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using this plugin or accessing this site. The materials contained in this website are protected by applicable copyright and trademark law.<br /><br />
The customer accepts that this WordPress Plugin, hereinafter referred to as WPSmartContracts.com, is subject to the following Terms of Service.<br /><br />
<strong>II. Technical requirements</strong><br /><br />
Any user that interacts with the Ethereum network using the plugin will be on their own and using their own technical resources. The website will offer reasonable help documentation as to how to use this plugin.<br /><br />
Users accept that it is their sole responsibility to meet the necessary technical requirements to interact with the platform.<br /><br />
WPSmartContracts.com encourages users to test the Smart Contracts in any available test networks before deploying on the mainnet.<br /><br />
<strong>III. Payments</strong><br /><br />
Using this plugin to deploy Smart Contracts in the Ethereum network, the user may have to pay a fee in Ether, the native cryptocurrency of the Ethereum network. Also, the user will need to pay a gas fee for the transaction to be processed.<br /><br />
The deployment fee is a one-time payment to deploy Smart Contracts in the Ethereum network using the plugin. It doesn’t include any other service like support, design, or bug fixes on your site.<br /><br />
All payments are non-refundable.<br /><br />
The deployment fees are available for consultation on the <a href="https://wpsmartcontracts.com/" target="_blank">WPSmartContracts.com</a> website, and gas fees are available <a href="https://ethgasstation.info/" target="_blank">here</a>.<br /><br />
<strong>IV. Support</strong><br /><br />
The support WPSmartContracts.com offer is limited to:<br /><br />
* Documentation on the WPSmartContracts.com/docs website<br /><br />
* Free community support on the <a href="https://wordpress.org/support/plugin/wp-smart-contracts/" target="_blank">WordPress site</a>.<br /><br />
WPSmartContracts.com offers premium support, which can be requested <a href="https://forms.gle/GLbiTTLJNYGoXAa67" target="_blank">here</a>.<br /><br />
The premium support rate is 0.3 ether per hour, and this is a non-refundable payment in advance.<br /><br />
We reserve the right to accept or deny the request for premium support. Do not send any payment for support until you get confirmation from the WPSmartContracts.com official sources.<br /><br />
<strong>V. Irreversible transactions</strong><br /><br />
The customer accepts that the nature of blockchain immutability makes all payments made to the smart contract irreversible. It also accepts that WPSmartContracts.com has no control over this and holds no responsibility for the user’s actions and the latter’s breach of duty of care.<br /><br />
WPSmartContracts.com is not responsible for failed transactions, high fees, slow network response, or any other issue you might experience while interacting with the Ethereum network.<br /><br />
<strong>VI. Compliance with the law of origin of the user</strong><br /><br />
The user accepts that WPSmartContracts.com is a venture located in the Republic of Costa Rica, respectful of international laws; consequently, when the client accepts the terms of services that rule this TOS.<br /><br />
The user is also accepting not to use this plugin to violate any international law or to use it in a country where the use of such technologies are banned or forbidden.<br /><br />
You may not use this service and may not accept the Terms if you are not of legal age.<br /><br />
<strong>VII. Privacy</strong><br /><br />
The user accepts that WPSmartContracts.com is respectful of privacy and acts under the principle of data minimization. All data will be handled only when they are pertinent, adequate, and limited to the purposes they were collected for.<br /><br />
<strong>VIII. Transparency in the code</strong><br /><br />
WordPress users’ final contracts will be open-sourced, and its code will be available on etherscan.io.<br /><br />
<strong>IX. Technical functioning</strong><br /><br />
WPSmartContracts.com commits to carry out all technical function tests on the software; however, the program, which comprises a set of codes, is offered as it has been developed without any further obligation to perform functions or services other than those contained therein.<br /><br />
<strong>X. Availability of technical service</strong><br /><br />
WPSmartContracts.com commits to make every effort to ensure its technology platform is available for its users. Nonetheless, the customer accepts that the nature of the ICTs is to be under constant growth, numerous informatics incidences/issues, and continuous improvement; hence WPSmartContracts.com cannot commit to maintaining a 100% availability of its services, which the customer accepts and won’t hold WPSmartContracts.com responsible for the unavailability of such technological platform and/or the Ethereum platform, which administration is in no way the responsibility of WPSmartContracts.com.<br /><br />
<strong>XI. Disclaimer</strong><br /><br />
The materials on the WPSmartContracts.com website and plugin are provided on an ‘as is’ basis. WPSmartContracts.com makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights. Further, WPSmartContracts.com does not warrant or make any representations concerning the accuracy, likely results, or reliability of using the materials on its website or otherwise relating to such materials or on any sites linked to this site.<br /><br />
<strong>XII. Limitations </strong><br /><br />
In no event shall WPSmartContracts.com or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on WPSmartContracts.com’ website or plugin, even if WPSmartContracts.com or a WPSmartContracts.com authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you. <br /><br />
<strong>XIII. Accuracy of materials</strong><br /><br />
The materials appearing on the WPSmartContracts.com website and plugin could include technical, typographical, or photographic errors. WPSmartContracts.com does not warrant that any of the website’s materials are accurate, complete, or current. WPSmartContracts.com may make changes to the materials contained on its website or plugin at any time without notice. However, WPSmartContracts.com does not make any commitment to update the materials.<br /><br />
<strong>XIV. Links</strong><br /><br />
WP Smart Contracts has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by WPSmartContracts.com of the site. Use of any such linked website is at the user’s own risk. <br /><br />
<strong>XV. Modifications</strong><br /><br />
WPSmartContracts.com may revise these terms of service for its website at any time without notice. Using this website, you agree to be bound by the current version of these terms of service.<br /><br />
<strong>XVI. Governing Law </strong><br /><br />
These terms and conditions are governed by and construed following the laws of Costa Rica. You irrevocably submit to the exclusive jurisdiction of the courts in that State or location.<br /><br />
</div>
TOS;

                $json = json_encode([
                    'TOS' => $tos,
                    'ARE_YOU_USING_MAINNET' => __('Terms of Service (TOS)', 'wp-smart-contracts'),
                    'CONFIRM' => __('By ticking this box I confirm that I have read, consent and agree to the Terms of Service (TOS)', 'wp-smart-contracts'), 
                    'CONFIRM2' => __('By ticking this box I confirm that I am of legal age', 'wp-smart-contracts'), 
                    'DEPLOY_COST_MONEY' => __('I understand that deploying a contract in the Main Ethereum Network can cost me real money (in Ether).', 'wp-smart-contracts'),
                    'FEES_AND_GAS_EXPENSES' => __('I understand what are the fees and gas expenses for deploying a contract.', 'wp-smart-contracts'),
                    'WPST_DISCLAIMER' => __("I understand that I am going to Authorize WP Smart Contracts factory to spend my WPIC tokens deploying contracts", 'wp-smart-contracts'),
                    'FEES_AND_GAS_EXPENSES_WPST' => __("I understand what are the fees and gas expenses of this operation.", 'wp-smart-contracts'),
                    'ACTION_IRREVERSIBLE' => __('I understand that this action is irreversible.', 'wp-smart-contracts'),
                    'ACCEPT_TERMS' => __('I accept the terms of service explained here.', 'wp-smart-contracts'),
                    'DEPLOY_TAKES_TIME' => __('Deploy can take several minutes (in any network used). Please don\'t cancel this operation once initiated.', 'wp-smart-contracts'),
                    'FOOTER' => __('If you are not sure of what you are doing click <strong>Cancel</strong> and learn more in the tutorials section before proceeding', 'wp-smart-contracts'),
                    'MANDATORY_FIELD' => __("Mandatory Field", 'wp-smart-contracts'),
                    'WRITE_CONTRACT_ADDRESS' => __("Please write the contract address", 'wp-smart-contracts'),
                    'CONTRACT_ADDRESS_BAD_FORMAT' => __("Invalid address", 'wp-smart-contracts'),
                    'WRONG_TYPE' => __("Wrong Type or Value", 'wp-smart-contracts'),
                    'PLEASE_ACCEPT_TERMS' => __('To deploy please accept all the terms and conditions, otherwise click "Cancel"', 'wp-smart-contracts'), 
                    'PLEASE_SELECT_FLAVOR' => __('Please select a flavor.'),
                    'FILL_DEFINITION' => __('Please fill in all definition fields (name, symbol, decimals and supply)', "wp-smart-contracts"), 
                    'FILL_DEFINITION2' => __('Please fill in all definition fields (name, symbol and decimals)', "wp-smart-contracts"), 
                    'FILL_RATE' => __('Please fill in the rate with an Integer number', "wp-smart-contracts"), 
                    'FILL_TOKEN2SELL' => __('Please fill in the address of the token you want to sell', "wp-smart-contracts"), 
                    'FILL_WALLET' =>  __('Please fill in the wallet address', 'wp-smart-contracts'), 
                    'BLUEMOON_UNSTOP' =>  __('It is strongly recommended that you set a way of finalizing the ICO, it can be as a hardcapped or timed ICO. Are you sure you want continue without setting a hardcap or a timed option?', 'wp-smart-contracts'), 
                    'POSITIVE_NUMBER' => __('It must be a positive number', 'wp-smart-contracts'), 
                    'TIMED_DATES' => __('Error in Timed Options', 'wp-smart-contracts'),
                    'TIMED_DATES_WRONG' => __('Please check that opening and closing dates are set and that closing date is greater than the opening date', 'wp-smart-contracts'),
                    'TIMED_DATES_TOMM' => __('Opening date has to start tomorrow (in GMT time). It cannot be set for today GMT time.', 'wp-smart-contracts'),
                    'DECIMAL_NUMBER' => __('Decimals must be a number, greater than or equal than 0 or less than 18.', 'wp-smart-contracts'), 
                    'INITIAL_SUPPLY_NUMBER' => __('Initial supply must be a number', 'wp-smart-contracts'), 
                    'HARD_CAP_NUMBER' => __('Hard cap must be a number greater than zero', 'wp-smart-contracts'), 
                    'RATE_NUMBER' => __('Rate must be a positive Integer number', 'wp-smart-contracts'), 
                    'CAP_TOKEN_NUMBER' => __('Mintable Cap must be 0 for unlimited cap or positive number for a limited cap', 'wp-smart-contracts'), 
                    'TOTAL_SUPPLY_NUMBER' => __('Total supply must be a number.', 'wp-smart-contracts'), 
                    'MINTABLE_CAP_NUMBER' => __('If the token is mintable, the mintable cap must be a number greater or equal than 0', 'wp-smart-contracts'),
                ]);
                break;
            case 'deploy-animation':
                $json = json_encode([
                    'DEPLOY_IN_PROGRESS' => __('Transaction in progress', 'wp-smart-contracts'), 
                    'CLICK_CONFIRM' => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'), 
                    'PLEASE_PATIENCE' => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
                    'ANIMATED_GIF' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/deploy-identicon.gif',
                ]);
                break;
            case "token-info":
                $json = json_encode([
                    'IMG_URL' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/',
                    'SYMBOL'  => __('Symbol', 'wp-smart-contracts'), 
                    'NAME'  => __('Name', 'wp-smart-contracts'), 
                    'DECIMALS'  => __('Decimals', 'wp-smart-contracts'), 
                    'INITIAL_SUPPLY'  => __('Initial Supply', 'wp-smart-contracts'), 
                    'BURNABLE'  => __('Burnable', 'wp-smart-contracts'), 
                    'MINTABLE'  => __('Mintable', 'wp-smart-contracts'), 
                    'MAX_CAP'  => __('Max. cap', 'wp-smart-contracts'), 
                    'PAUSABLE'  => __('Pausable', 'wp-smart-contracts'),
                ]);
                break;
            case "crowdfunding-info":
            case "ico-info":
            case "ico-info-ico":
            case "ico-info-token":
                $json = json_encode([
                    'IMG_URL' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/',
                    'ERROR2' => __('Error', 'wp-smart-contracts'),
                    'NO_CONTRACT' => __('No contract found', 'wp-smart-contracts'),
                    'NATIVE_COIN' => $native_coin,
                    'POSITIVE_INTEGER' => __('Funds has to be a positive integer', 'wp-smart-contracts'),
                    'WRITE_AMOUNT' => __("Please specify a valid amount greater than zero", 'wp-smart-contracts'),
                    'WRITE_ADDRESS' => __("Please specify a valid address in the \"Destination address\" field", 'wp-smart-contracts'),
                    'WRITE_DESC' => __("Please specify a description in the \"Add a description\" field", 'wp-smart-contracts'),
                    'METAMASK_ERROR' => __('Unable to connect to the Ethereum Network. Please install and run MetaMask.', 'wp-smart-contracts'),
                    'WRONG_NETWORK' => __('Looks like you are connected to a different network.', 'wp-smart-contracts'),
                    'ACCOUNT_NOT_FOUND' => __('Do you see a "Connect to Ehereum Network" in WP admin bar? if so, click there to connect. Are you logged in to Metamask? if not, please log in. If the error persist try reloading this page.', 'wp-smart-contracts'), 
                    'THANKS' => __('Thanks for your contribution!', 'wp-smart-contracts'),
                    'CONTRIBUTION_DONE' => __('Your contribution was completed', 'wp-smart-contracts'),
                    'CLOSE' => __('Close', 'wp-smart-contracts'),
                    'APPROVAL_DONE' => __('Your approval was successfully completed.', 'wp-smart-contracts'),
                    'FINALIZATION_DONE' => __('Your request was successfully finalized', 'wp-smart-contracts'),
                    'TO' => __('To', 'wp-smart-contracts'),
                    'APPROVE' => __('Approve', 'wp-smart-contracts'),
                    'FINALIZE' => __('Finalize', 'wp-smart-contracts'),
                    'TRANSFERRED' => __('Transferred', 'wp-smart-contracts'),
                    'APPROVERS' => __('Approvers', 'wp-smart-contracts'),
                    'APPROVERS_LABEL' => __('Approvers Percentage', 'wp-smart-contracts'),
                    'MINIMUM_LABEL' => __('Minimum', 'wp-smart-contracts'),

                    'HARD_CAPPED_LABEL' => __('Hard Capped', 'wp-smart-contracts'),
                    'PAUSABLE_LABEL' => __('Pausable', 'wp-smart-contracts'),
                    'TIMED_LABEL' => __('Timed', 'wp-smart-contracts'),
                    'SYMBOL_LABEL' => __('Symbol', 'wp-smart-contracts'),
                    'NAME_LABEL' => __('Name', 'wp-smart-contracts'),
                    'SUPPLY_LABEL' => __('Initial Supply', 'wp-smart-contracts'),
                    'RATE_LABEL' => __('Rate', 'wp-smart-contracts'),
                    'WALLET_LABEL' => __('Wallet', 'wp-smart-contracts'),
                
                    'TOKEN2SELL' =>  __('Token to sell', 'wp-smart-contracts'),

                    'INFURA_API_KEY' => WPSC_helpers::valArrElement(
                        isset($infura)?$infura:null, 
                        "infura_api_key"
                    )?trim($infura["infura_api_key"]):"",
                    'INFURA_MNEMONIC' => WPSC_helpers::valArrElement(
                        isset($infura)?$infura:null, 
                        "infura_mnemonic"
                    )?trim($infura["infura_mnemonic"]):"",
                ]);
                break;
            case "crowd-view-request":
            case "crowd-view-request-finalized":
                $infura = get_option('etherscan_api_key_option');
                $json = json_encode([
                    'TO' => __('To', 'wp-smart-contracts'),
                    'APPROVE' => __('Approve', 'wp-smart-contracts'),
                    'FINALIZE' => __('Finalize', 'wp-smart-contracts'),
                    'TRANSFERRED' => __('Transferred', 'wp-smart-contracts'),
                    'APPROVERS' => __('Approvers', 'wp-smart-contracts'),
                    'INFURA_API_KEY' => trim($infura["infura_api_key"]),
                    'INFURA_MNEMONIC' => trim($infura["infura_mnemonic"]),
                    'NATIVE_COIN' => $native_coin
                ]);
                break;
            case "coin-view-block-explorer-balance":
                $json = json_encode([
                    'BALANCE' => __('Balance', 'wp-smart-contracts'),
                    'ADD_MINTER' => __('Add Minter Role', 'wp-smart-contracts'),
                    'ADD_PAUSER' => __('Add Pauser Role', 'wp-smart-contracts'),
                    'PAUSE' => __('Pause', 'wp-smart-contracts'),
                    'CANCEL' => __('Cancel', 'wp-smart-contracts'),
                    'FILTERED' => __('Filtered by', 'wp-smart-contracts'),
                    'ERROR' => __('Error search data field', 'wp-smart-contracts'),
                    'NOT_VALID' => __('It doesn\'t look as a valid address or transaction ID', 'wp-smart-contracts'),
                    'ERROR2' => __('Error', 'wp-smart-contracts'),
                    'NOT_VALID_ETH_ADDRESS' => __('This is not a valid Ethereum address', 'wp-smart-contracts'),
                    'NO_RESULTS' => __('No transactions found', 'wp-smart-contracts'),
                    'FAILED' => __('FAILED', 'wp-smart-contracts'),
                    'CONFIRMED' => __('CONFIRMED', 'wp-smart-contracts'),
                    'TRANSFER' => __('TRANSFER', 'wp-smart-contracts'),
                    'TRANSFER_FROM' => __('TRANSFER FROM', 'wp-smart-contracts'),
                    'MINT' => __('MINT', 'wp-smart-contracts'),
                    'BURN' => __('BURN', 'wp-smart-contracts'),
                    'BURN_FROM' => __('BURN FROM', 'wp-smart-contracts'),
                    'APPROVE' => __('APPROVE', 'wp-smart-contracts'),
                    'RESUME' => __('RESUME', 'wp-smart-contracts'),
                    'PAUSE' => __('PAUSE', 'wp-smart-contracts'),
                    'CLICK_TO_FILTER' => __('Click to filter', 'wp-smart-contracts'),
                    'ALLOWANCE_TO_SPEND' => __("Your account has an allowance to spend from this account of:", 'wp-smart-contracts'),
                    'LATEST' => __("Latest account transactions", 'wp-smart-contracts'),
                    'ALL' => __("All transactions", 'wp-smart-contracts'),
                    'DETAIL' => __("Transaction detail", 'wp-smart-contracts'),
                    'WRITE_ADDRESS' => __("Please specify a valid address in the \"To address\" field", 'wp-smart-contracts'),
                    'WRITE_ADDRESS_FROM' => __("Please specify a valid address in the \"From address\" field", 'wp-smart-contracts'),
                    'WRITE_AMOUNT' => __("Please specify a valid amount greater than zero", 'wp-smart-contracts'),
                    'METAMASK_ERROR' => __('Unable to connect to the Ethereum Network. Please install and run MetaMask.', 'wp-smart-contracts'),
                    'WRONG_NETWORK' => __('Looks like you are connected to a different network.', 'wp-smart-contracts'),
                    'ACCOUNT_NOT_FOUND' => __('Do you see a "Connect to Ehereum Network" in WP admin bar? if so, click there to connect. Are you logged in to Metamask? if not, please log in. If the error persist try reloading this page.', 'wp-smart-contracts'), 
                    'NO_CONTRACT' => __('No contract found', 'wp-smart-contracts'),
                    'TRANSFER_CONFIRM' => __('Are you sure you want to transfer the specified amount to this address?', 'wp-smart-contracts'),
                    'APPROVE_CONFIRM' => __('Are you sure you want to approve the specified amount to this address?', 'wp-smart-contracts'),
                    'MINT_CONFIRM' => __('Are you sure you want to create the specified amount of tokens and add them to this address?', 'wp-smart-contracts'),
                    'BURN_CONFIRM' => __('Are you sure you want to destroy the specified amount of tokens from your account?', 'wp-smart-contracts'),
                    'TXID_ERROR_MESSAGE' =>  __('An error occurred processing this transaction', 'wp-smart-contracts'),
                    'IS_MINTER' =>  __('Minter', 'wp-smart-contracts'),
                    'IS_PAUSER' =>  __('Pauser', 'wp-smart-contracts'),
                    'BURN_FROM_CONFIRM' => __('Are you sure you want to destroy previously approved from this account?', 'wp-smart-contracts'),
                    'PAUSE_CONFIRM' => __('Are you sure you want to pause all the token activity?', 'wp-smart-contracts'),
                    'RESUME_CONFIRM' => __('Are you sure you want to resume all the token activity?', 'wp-smart-contracts'),
                    'ADD_PAUSER_CONFIRM' => __('Are you sure you give this account the priviledge to pause token activity?', 'wp-smart-contracts'),
                    'ADD_MINTER_CONFIRM' => __('Are you sure you give this account the priviledge to create new tokens?', 'wp-smart-contracts'),
                    'RENOUNCE_PAUSER_CONFIRM' => __('Are you sure you want to remove your pauser privilege from your account?', 'wp-smart-contracts'),
                    'RENOUNCE_MINTER_CONFIRM' => __('Are you sure you want to remove your minter privilege from your account?', 'wp-smart-contracts'),
                    'RENOUNCE_PAUSER' => __('Renounce Pauser', 'wp-smart-contracts'),
                    'CONTRACT_CREATION' => __('Contract Creation', 'wp-smart-contracts'),
                    'ICO_BUY_TOKENS' => __('ICO Buy Tokens', 'wp-smart-contracts'),
                    'ICO_DIRECT_TRANSFER' => __('ICO Direct Transfer', 'wp-smart-contracts'),
                    'RENOUNCE_MINTER' => __('Renounce Minter', 'wp-smart-contracts'),
                ]);
                break;

        }

        if ($json) {

            $wpsc_js_object_name = self::createJSObjectNameFromTag($tag);

            ?>
            <script type='text/javascript'>
            /* <![CDATA[ */
            var <?=$wpsc_js_object_name?> = <?=$json?>;
            /* ]]> */
            </script>
            <?php 

//            echo $wpsc_js_object_name;
        }
    }

    // create a JS object name,
    // i.e. turns wp-admin-bar into WPSC_WP_ADMIN_BAR
    static public function createJSObjectNameFromTag($tag) {
        return 'WPSC_' . strtoupper(str_replace('-', '_', $tag));
    }

    // print the JS Mustache template
    private function showTemplate($tempid) {
        $this->loadTranslationsForTag($tempid);
        echo '<script id="'.$tempid.'" type="x-tmpl-mustache">';
        echo WPSC_Mustache::getTemplate($tempid);
        echo '</script>';
    }

    // get template content
    public static function getTemplate($tempid) {

        // set template file name
        $template = $tempid . '.mustache';

        // if template exists locally in the theme, replace it
        if ( !file_exists( $template_path = get_stylesheet_directory() . '/wpsc-views/' . $template ) ) {
            $template_path = dirname(dirname(__FILE__)) . '/views/' . $template;
        }

        return file_get_contents($template_path);

    }

}
