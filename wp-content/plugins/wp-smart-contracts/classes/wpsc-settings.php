<?php

if( ! defined( 'ABSPATH' ) ) die;

class WPSCSettingsPage {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            __("WP Smart Contracts", 'wp-smart-contracts'), 
            'manage_options', 
            'etherscan-api-key-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'etherscan_api_key_option' );

        $m = new Mustache_Engine;

        $option_group = 'etherscan_option_group';

        $atts = [
          "title" => __("WP Smart Contracts Settings", 'wp-smart-contracts'),
          "learn-more" => __("Learn More", 'wp-smart-contracts'),
          "subtitle" => __("Internal settings for the Block Explorer sections and others", 'wp-smart-contracts'),
          "option-group" => esc_attr( $option_group ),
          "wp-nonce-field" => wp_nonce_field( "$option_group-options", '_wpnonce', true, false ),
          "api-key-token" => __("Etherscan API Key Token", 'wp-smart-contracts'),
          "free-registration" =>  __("Register for free, to get an", 'wp-smart-contracts'),
          "api-key-text" => __("Etherscan API Key", 'wp-smart-contracts'),
          "api-key-tooltip" => __("Optimize the Coin Block Explorer section with a free Etherscan API Key.", 'wp-smart-contracts'),
          "api-key-value" => (WPSC_helpers::valArrElement($this->options, "api_key") and !empty($this->options["api_key"]))?$this->options["api_key"]:' ',
          "infura-api-key-text" => __("Infura PROJECT ID", 'wp-smart-contracts'),
          "infura-api-key-tooltip" => __("If you are using Crowdfundings you will need an Infura PROJECT ID (please do not confuse this with the PROJECT SECRET) ", 'wp-smart-contracts'),
          "infura-api-key" => __("If you are using Crowdfundings you will need an Infura PROJECT ID", 'wp-smart-contracts'),
          "infura-api-key-value" => (WPSC_helpers::valArrElement($this->options, "infura_api_key") and !empty($this->options["infura_api_key"]))?$this->options["infura_api_key"]:' ',
          "infura-mnemonic" => (WPSC_helpers::valArrElement($this->options, "infura_mnemonic") and !empty($this->options["infura_mnemonic"]))?$this->options["infura_mnemonic"]:' ',
          "infura-api-key-warning" => __("Please do not confuse this with the PROJECT SECRET ", 'wp-smart-contracts'),
          "coin-settings" => __("Coin settings", 'wp-smart-contracts'),
          "crowd-settings" => __("Infura", 'wp-smart-contracts'),
          "separator-format" => __("Separator Format", 'wp-smart-contracts'),
          "thousands-decimals-separator" => __("Thousands and decimals separator", 'wp-smart-contracts'),
          "thousands-decimals-separator-tooltip" => __("Choose an option to override system number separator (comma or point)", 'wp-smart-contracts'),
          "choose-separators" => __("Choose separators", 'wp-smart-contracts'),
          "choose-expiration-time" => __("Choose expiration time", 'wp-smart-contracts'),
          "number-separators" => [
            ['value' => ",.", 'selected' => $this->options["number_separators"]==",."?"selected":"", 'label' => '1,000,000.00'],
            ['value' => ".,", 'selected' => $this->options["number_separators"]==".,"?"selected":"", 'label' => '1.000.000,00'],
            ['value' => "_.", 'selected' => $this->options["number_separators"]=="_."?"selected":"", 'label' => '1 000 000.00'],
            ['value' => "_,", 'selected' => $this->options["number_separators"]=="_,"?"selected":"", 'label' => '1 000 000,00'],
            ['value' => "x.", 'selected' => $this->options["number_separators"]=="x."?"selected":"", 'label' => '1000000.00'],
            ['value' => "x,", 'selected' => $this->options["number_separators"]=="x,"?"selected":"", 'label' => '1000000,00']
          ],
          "expiration-time-list" => [
            ['value' => "0",      'selected' => $this->options["expiration_time"]=="0"?"selected":"",       'label' =>          __('Cache deactivated', 'wp-smart-contracts')],
            ['value' => "10",     'selected' => $this->options["expiration_time"]=="10"?"selected":"",      'label' => '5 ' .   __('seconds', 'wp-smart-contracts')],
            ['value' => "30",     'selected' => $this->options["expiration_time"]=="30"?"selected":"",      'label' => '30 ' .  __('seconds', 'wp-smart-contracts')],
            ['value' => "60",     'selected' => $this->options["expiration_time"]=="60"?"selected":"",      'label' => '1 ' .   __('minute', 'wp-smart-contracts')],
            ['value' => "300",    'selected' => $this->options["expiration_time"]=="300"?"selected":"",     'label' => '5 ' .   __('minutes', 'wp-smart-contracts')],
            ['value' => "900",    'selected' => $this->options["expiration_time"]=="900"?"selected":"",     'label' => '15 ' .  __('minutes', 'wp-smart-contracts')],
            ['value' => "1800",   'selected' => $this->options["expiration_time"]=="1800"?"selected":"",    'label' => '30 ' .  __('minutes', 'wp-smart-contracts')],
            ['value' => "3600",   'selected' => $this->options["expiration_time"]=="3600"?"selected":"",    'label' => '1 ' .   __('hour', 'wp-smart-contracts')],
            ['value' => "10800",  'selected' => $this->options["expiration_time"]=="10800"?"selected":"",   'label' => '3 ' .   __('hours', 'wp-smart-contracts')],
            ['value' => "21600",  'selected' => $this->options["expiration_time"]=="21600"?"selected":"",   'label' => '6 ' .   __('hours', 'wp-smart-contracts')],
            ['value' => "43200",  'selected' => $this->options["expiration_time"]=="43200"?"selected":"",   'label' => '12 ' .  __('hours', 'wp-smart-contracts')],
            ['value' => "86400",  'selected' => $this->options["expiration_time"]=="86400"?"selected":"",   'label' => '1 ' .   __('day', 'wp-smart-contracts')],
            ['value' => "259200", 'selected' => $this->options["expiration_time"]=="259200"?"selected":"",  'label' => '3 ' .   __('days', 'wp-smart-contracts')],
            ['value' => "604800", 'selected' => $this->options["expiration_time"]=="604800"?"selected":"",  'label' => '1 ' .   __('week', 'wp-smart-contracts')],
          ],
          "date-format-in" => __("Date Format in", 'wp-smart-contracts'),
          "php-style" => __("PHP style", 'wp-smart-contracts'),
          "date-format-in-sub" => __("Specify the date format to show the timestamp of transactions", 'wp-smart-contracts'),
          "date-format-in-sub-tooltip" => __("The date / time can be specified using PHP Date/Time standard notation", 'wp-smart-contracts'),
          "clear-cache" => __("Block Explorer Cache", 'wp-smart-contracts'),
          "cache-dur-in-secs" => __("Etherscan API cache duration in seconds.", 'wp-smart-contracts'),
          "cache-dur-in-secs-tooltip" => __("Duration time to store API responses.", 'wp-smart-contracts'),
          "flush-now" => __("Flush transient cache now", 'wp-smart-contracts'),
          "save" => __("Save Changes", 'wp-smart-contracts'),
          "date-format-value" => WPSC_helpers::valArrElement($this->options, "date_format")?$this->options["date_format"]:'',
          "expiration-time-value" => WPSC_helpers::valArrElement($this->options, "expiration_time")?$this->options["expiration_time"]:'',
          "decimals-to-show" => WPSC_helpers::valArrElement($this->options, "decimals_to_show")?$this->options["decimals_to_show"]:'',
          "decimals-to-show-text" => __("Decimals to show in the value of transactions", "wp-smart-contracts"),
          "decimals-to-show-sub" => __("This is an integer between 0 decimals and a maximum of 18", "wp-smart-contracts"),
          "decimals-to-show-tooltip" => __("The number of decimals by default is 4", "wp-smart-contracts"),
        ];
        echo $m->render(WPSC_Mustache::getTemplate('settings-admin'), $atts);

    }

    // get thousands and decimals number separation settings
    static private function getSeparators() {

      global $wp_locale;

      // if set get from user settings
      $settings = self::get();
      if (WPSC_helpers::valArrElement($settings, 'number_separators')) {
        return $settings["number_separators"];

      // if not get from WP settings
      } elseif (isset($wp_locale)) {
        return $wp_locale->number_format['thousands_sep'] . $wp_locale->number_format['decimal_point'];

      // if not return english format
      } else {
        return ',.';
      }

    }

    static public function numberOfDecimalsToShow() {

      $dts = self::get('decimals_to_show');

      if ($dts!==false) {
        return $dts;
      } else {
        return 2;
      }

    }

    static public function numberFormatDecimals() {
      $separators = self::getSeparators();
      if (strlen($separators)==2) {
        return substr($separators, 1);
      } else {
        return '.';
      }
    }

    static public function numberFormatThousands() {
      $separators = self::getSeparators();
      if (strlen($separators)==2) {
        $sep = substr($separators, 0, 1);
        if ($sep=="_") return " ";
        if ($sep=="x") return "";
        return $sep;
      } else {
        return ',';
      }
    }

    static public function get($option=null) {
      $options = get_option( 'etherscan_api_key_option' );
      if ($option) {
        if (WPSC_helpers::valArrElement($options, $option)) {
          return $options[$option]; 
        } else {
          return false;
        }
      }
      return $options;
    }

    /**
     * Register and add settings
     */
    public function page_init() {        
        register_setting(
            'etherscan_option_group',
            'etherscan_api_key_option',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'setting_section_id',
            'Etherscan API Settings',
            array( $this, 'print_section_info' ),
            'etherscan-api-key-setting-admin' 
        );  

        add_settings_field(
            'api_key', 
            'Etherscan API Key Token', 
            array( $this, 'api_key_callback' ), 
            'etherscan-api-key-setting-admin', 
            'setting_section_id'
        );      

        add_settings_field(
            'infura_api_key', 
            'Infura PROJECT ID', 
            array( $this, 'infura_api_key_callback' ), 
            'infura-api-key-setting-admin', 
            'setting_section_id'
        );      
    }

}

if( is_admin() ) new WPSCSettingsPage();