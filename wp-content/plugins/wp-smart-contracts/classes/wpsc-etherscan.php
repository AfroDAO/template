<?php

if( ! defined( 'ABSPATH' ) ) die;

new WPSC_Etherscan();

/**
 * Handle etherscan api queries for block explorer view
 */

class WPSC_Etherscan {

    // prefix name for the transient variable
    const transientPrefix = 'wpsc_';

    const paginationOffset = 25;

    // define endpoints
    function __construct() {

        // get token supply
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/ping/', [
                'methods' => 'GET',
                'callback' => [ $this, 'ping' ],
            ]);
        });

        // search transactions for one token and one account address
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get_tx_contract_account/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)/(?P<address>[a-zA-Z0-9-]+)/(?P<page>\d+)/(?P<internal>\d+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getTxAccountInContract' ],
            ]);
        });

        // search all transactions for one token
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get_tx_contract/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)/(?P<page>\d+)/(?P<internal>\d+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getTxFromContract' ],
            ]);
        });

        // get token supply
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/total_supply/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getTotalSupply' ],
            ]);
        });

        // get account balance
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/balance/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)/(?P<address>[a-zA-Z0-9-]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getBalance' ],
            ]);
        });

        // get tx per id
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get_tx/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)/(?P<txid>[a-zA-Z0-9-]+)/(?P<ignore_contract>[0-9-]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getTxId' ],
            ]);
        });

        // get tx per id
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/remove_cache/', [
                'methods' => 'GET',
                'callback' => [ $this, 'removeCache' ],
            ]);
        });

        // get code per contract
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get_code/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getCode' ],
            ]);
        });

        // format numbers
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/format_float/(?P<float>[0-9\,\.e\+]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'formatFloat' ],
            ]);
        });

    }

    // endpoint callbacks

    public static function ping($params) {
        return new WP_REST_Response(true);
    }

    public static function getTxAccountInContract($params) {
        
        check_ajax_referer('wp_rest', '_wpnonce');
        
        return new WP_REST_Response(
            self::getTx($params['network'], $params['contract'], $params['address'], $params['page'], $params['internal'])
        );
        
    }

    public static function getTxFromContract($params) {

        check_ajax_referer('wp_rest', '_wpnonce');
        
        return new WP_REST_Response(
            self::getTx($params['network'], $params['contract'], null, $params['page'], $params['internal'])
        );

    }

    static private function processInput($input) {

        if ($input) {

            $hashFunction = $type = $from = $to = $value = null;

            $hashFunction = substr($input, 0, 10);
            switch ($hashFunction) {
                case "0xa9059cbb":
                    $type="transfer";
                    $to = "0x" . substr($input, 34, 40);
                    $value = hexdec(substr($input, 75, 63));
                    break;
                case "0x23b872dd":
                    $type="transferFrom";
                    $from = "0x" . substr($input, 34, 40);
                    $to = "0x" . substr($input, 98, 40);
                    $value = hexdec(substr($input, 138));
                    break;
                case "0x095ea7b3":
                    $type="approve";
                    $to = "0x" . substr($input, 34, 40);
                    $value = hexdec(substr($input, 74));
                    break;
                case "0x40c10f19":
                    $type="mint";
                    $to = "0x" . substr($input, 34, 40);
                    $value = hexdec(substr($input, 74));
                    break;
                case "0x42966c68":
                    $type="burn";
                    $value = hexdec(substr($input, 10));
                    break;
                case "0x79cc6790":
                    $type="burnFrom";
                    $from = "0x" . substr($input, 34, 40);
                    $value = hexdec(substr($input, 74));
                    break;
                case "0x8456cb59":
                    $type="pause";
                    break;
                case "0x3f4ba83a":
                    $type="resume";
                    break;
                case "0x983b2d56":
                    $type="addMinter";
                    break;
                case "0x82dc1ec4":
                    $type="addPauser";
                    break;
                case "0x98650275":
                    $type="renounceMinter";
                    break;
                case "0x6ef8d66d":
                    $type="renouncePauser";
                    break;
                case "0x80a70e5f": // raspberry
                case "0xe6c9f1f6": // raspberry wpst
                case "0x45c2e176": // bluemoon
                case "0x3e517ed1": // bluemoon wpst
                case "0x5b060530": // vanilla and pistachio
                case "0x558d4657": // chocolate
                case "0x37d325a1": // vanilla and pistachio wpst
                case "0x95d38e11": // chocolate wpst
                case "0x772d0f3c": // mango
                case "0xc19afa14": // mango wpst
                    $type="contractCreation";
                break;
                case "0xec8ac4d8":
                    $type="icoBuyTokens";
                    break;
                case "0x":
                    $type="icoDirectTransfer";
                    break;
            }
            if ($value) {
                // convert wei like units to ether like
                $value = $value / 1000000000000000000;
                $value = $value;
            }
            return [$hashFunction, $type, $from, $to, $value];
        } else {
            return [];
        }

    }

    // filter tx based on contract / account address
    private static function getTx($network, $contract, $address, $page, $internal) {

        if (!$subdomain = self::getNetworkSubdomain($network)) {
            return [];
        }

        if (!$page) $page = 1;

        // if we have a transient stored, return it
        if ($txs = self::getTransientResponse($network, $contract, $address, $page)) {

            return $txs;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            // filter by contract and account
            if ($address) {
                $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=account&action=tokentx&address=' . $address . 
                    '&sort=desc&page=' . $page . '&offset=' . self::paginationOffset . '&apikey=' . trim($etherscan_api_key["api_key"]) . 
                        '&contractAddress=' . $contract;
            // filter by contract
            } else {

                // list internal txs
                if ($internal) {
                    $txlist_endpoint = "txlistinternal";
                    $offset = 10; // internal txs are slower, so lets show less
                // list regular txs
                } else {
                    $txlist_endpoint = "txlist";
                    $offset = self::paginationOffset;
                }
                $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=account&action=' . $txlist_endpoint . '&address=' . $contract . 
                '&page=' . $page . '&offset=' . $offset . '&sort=desc&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key
            }

//            return $etherscan_url;

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == "200") {

                $txlist = json_decode($response["body"], true);

                if (is_array($txlist) and array_key_exists('result', $txlist)) {

                    $txs=[];
                    $localeconv = localeconv();

                    // filter transactions with the contract
                    foreach ($txlist['result'] as $res) {

                        $txs_column = array_column($txs, 'txid');
                        if (array_search($res["hash"], $txs_column)!==false) continue;

                        // parse etherscan input response
                        $hashFunction = '';
                        $to = '';
                        $value = 0;

                        list($hashFunction, $type, $transferFrom, $to, $value) = self::processInput($res["input"]);

                        // find the date format in settings or set default
                        $settings = WPSCSettingsPage::get();
                        if (!$date_format = $settings["date_format"]) {
                            $date_format = 'Y-m-d';
                        }
                        
                        $from = $res["from"];

                        if ($type=="transferFrom" or $type=="burnFrom") {
                            $from = $transferFrom;
                        }

                        // if this is an internal request then  find internal details
                        if ($internal) {
                            $txs[] = current(self::getTxId([
                                "network"=>$network, 
                                "contract"=>$contract, 
                                "txid"=>$res["hash"],
                                "ignore_contract"=>true // in this case we dont want to filter by contract
                            ]));
                        // otherwise return regular fields
                        } else {

                            // use default to address if not in input
                            if (!$to) {
                                $to = $res["to"];
                            }
                            
                            // ad it to the tx list
                            $txs[] = [
                                'blockNumber' => $res["blockNumber"],
                                'timeStamp' => ($res["timeStamp"])?date($date_format, $res["timeStamp"]):'',
                                'txid' => $res["hash"],
                                'txid_short' => WPSC_helpers::shortify($res["hash"]),
                                'from' => $from,
                                'from_short' => WPSC_helpers::shortify($from),
                                'transfer_from' => $transferFrom,
                                'transfer_from_short' => WPSC_helpers::shortify($transferFrom),
                                'hashFunction' => $hashFunction,
                                'type' => $type,
                                'to' => $to,
                                'to_short' => WPSC_helpers::shortify($to),
                                'value' => $value?WPSC_helpers::formatNumber($value):
                                    WPSC_helpers::formatNumber($res["value"]/1000000000000000000),
                                'isError' => WPSC_helpers::valArrElement($res, 'isError')?$res["isError"]:false,
                                'subdomain' => self::getNetworkSubdomain($network, ''),
/*
                                'response' => $res,
                                'internal_data' => $internal_data,
                                'internal' => $internal,
*/
                            ];

                        }

                    }

                    // save the wp transient api response
                    if (!empty($txs)) {
                        self::saveTransientResponse($network, $contract, $address, $page, $txs);
                    }

                    return $txs;
                }

            }

        }

        return [];

    }

    // filter tx based on contract / account address
    public static function getTxId($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        $network = $params['network'];
        $contract = $params['contract'];
        $txid = $params['txid'];
        $ignore_contract = $params['ignore_contract'];

        if (!$subdomain = self::getNetworkSubdomain($network)) {
            return [];
        }

        // if we have a transient stored, return it
        if ($txs = self::getTransientResponse($network, $txid, null, null)) {

            return $txs;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=proxy&action=eth_getTransactionByHash&txhash=' . 
                $txid . '&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                $res = json_decode($response["body"], true);

                if (is_array($res) and array_key_exists('result', $res)) {
                    $res = $res["result"];
                }

                if (is_array($res) and array_key_exists('hash', $res)) {

                    // filtering contract interaction comparing unchecksummed addresses
                    if ($ignore_contract or strtolower($res["to"]) == strtolower($contract)) {

                        // parse etherscan input response
                        $hashFunction = '';
                        $to = '';
                        $type = false;
                        $value = 0;

                        list($hashFunction, $type, $transferFrom, $to, $value) = self::processInput($res["input"]);

                        // now try to get the block info to get the timestamp
                        $time_stamp = null;
                        if ($res["blockNumber"]) {

                            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=block&action=getblockreward&blockno=' . 
                                hexdec( substr($res["blockNumber"], 2) ) . '&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key


                            // hit the api
                            $response = wp_remote_get( $etherscan_url );

                            // successful?
                            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                                $body = json_decode($response["body"], true);
                                $time_stamp = $body["result"]["timeStamp"];

                            }

                        }

                        $txs[] = [
                            'blockNumber' => $res["blockNumber"],
                            'timeStamp' => ($time_stamp)?date('Y-m-d', $time_stamp):'',
                            'txid' => $res["hash"],
                            'txid_short' => WPSC_helpers::shortify($res["hash"]),
                            'from' => $res["from"],
                            'from_short' => WPSC_helpers::shortify($res["from"]),
                            'transfer_from' => $transferFrom,
                            'transfer_from_short' => WPSC_helpers::shortify($transferFrom),
                            'hashFunction' => $hashFunction,
                            'type' => $type,
                            'to' => $to,
                            'to_short' => WPSC_helpers::shortify($to),
                            'value' => $value?WPSC_helpers::formatNumber($value):
                                WPSC_helpers::formatNumber(hexdec($res["value"])/1000000000000000000),
                            'isError' => WPSC_helpers::valArrElement($res, 'isError')?$res["isError"]:false,
                            'subdomain' => self::getNetworkSubdomain($network, ''),
//                            'response' => $res,
                        ];
                    }

                    // save the wp transient api response
                    if (!empty($txs)) {
                        self::saveTransientResponse($network, $txid, null, null, $txs);
                    }

                    return $txs;
                }

            }

        }

        return [];

    }

    // filter tx based on contract / account address
    public static function getCode($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        $network = $params['network'];
        $contract = $params['contract'];

        if (!$subdomain = self::getNetworkSubdomain($network)) {
            return [];
        }

        // if we have a transient stored, return it
        if ($txs = self::getTransientResponse($network, $contract, 'source_code', null)) {

            return $txs;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=contract&action=getsourcecode&address=' . 
                $contract . '&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                $res = json_decode($response["body"], true);

                if (is_array($res) and array_key_exists('result', $res)) {
                    self::saveTransientResponse($network, $contract, 'source_code', null, $res["result"]);
                    return $res["result"];
                }

            }

        }

        return [];

    }

    // get total token supply
    public static function getTotalSupply($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        if (!$subdomain = self::getNetworkSubdomain($params['network'])) {
            return [];
        }

        // if we have a transient stored, return it
        if ($supply = self::getTransientResponse($params['network'], $params['contract'], "total_supply", null)) {

            return $supply;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=stats&action=tokensupply&contractaddress=' . $params['contract'] . '&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                $supply = json_decode($response["body"], true);

                if (is_array($supply) and WPSC_helpers::valArrElement($supply, 'result')) {

                    $formatted_result = WPSC_helpers::formatNumber($supply["result"] / 1000000000000000000);

                    // save the wp transient api response
                    self::saveTransientResponse($params['network'], $params['contract'], "total_supply", null, $formatted_result);

                    return $formatted_result;
                    
                }

            }

        }

        return [];

    }

    // get balance of one holder
    public static function getBalance($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        if (!$subdomain = self::getNetworkSubdomain($params['network'])) {
            return [];
        }

        // if we have a transient stored, return it
        if ($balance = self::getTransientResponse($params['network'], $params['contract'], $params['address'] . "_balance", null)) {

            return $balance;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=account&action=tokenbalance&contractaddress=' . $params['contract'] . '&address=' . $params['address'] . '&tag=latest&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                $balance = json_decode($response["body"], true);

                if (is_array($balance) and array_key_exists('result', $balance)) {

                    $final_balance = WPSC_helpers::formatNumber($balance["result"] / 1000000000000000000);

                    // save the wp transient api response
                    self::saveTransientResponse($params['network'], $params['contract'], $params['address'] . "_balance", null, $final_balance);

                    return $final_balance;

                }

            }

        }

        return [];

    }

    // filter tx based on contract / account address
    public static function removeCache() {

        check_ajax_referer('wp_rest', '_wpnonce');
        global $wpdb;

        $current_user = wp_get_current_user();
        if (user_can( $current_user, 'administrator' )) {

            $wpdb->query("DELETE FROM `wp_options` WHERE option_name LIKE '%_transient_" . self::transientPrefix . "%'");
            return new WP_REST_Response(true);

        }

    }

    // format float
    public static function formatFloat($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        $float = $params['float'];

        if ($float) {
            return WPSC_helpers::formatNumber($float / 1000000000000000000);            
        } else {
            return 0;
        }
        
    }

    static private function getNetworkSubdomain($network, $prefix="api") {

        // translate network

        if ( $arr = WPSC_helpers::getNetworks() ) {

            if ($network==1) {
                return $prefix;
            } else {
                if ($prefix) return $prefix . '-' . $arr[$network]["name"];
                else  return $arr[$network]["name"];
            }

        }

    }

    // get txs stored in wp transient
    private static function getTransientResponse($network, $contract, $address, $page) {

        // cache is activated?
        if (!$expiration_time = WPSCSettingsPage::get('expiration_time')) {
            return false;
        }

        $transient_name = self::transientPrefix . $network . "_" . $contract . "_" . $address . "_" . $page;

        if ($t = get_transient($transient_name)) {
            return $t;
        } else {
            return false;
        }

    }

    // store txs to wp transient
    private static function saveTransientResponse($network, $contract, $address, $page, $txs) {

        // cache is activated?
        if (!$expiration_time = WPSCSettingsPage::get('expiration_time')) {
            return false;
        }

        $transient_name = self::transientPrefix . $network . "_" . $contract . "_" . $address . "_" . $page;

        set_transient($transient_name, $txs, $expiration_time);

    }

}
