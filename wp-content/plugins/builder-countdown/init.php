<?php
/*
Plugin Name:  Builder Countdown
Plugin URI:   http://themify.me/addons/countdown
Version:      1.1.0
Author:       Themify
Description:  It requires to use with the latest version of any Themify theme or the Themify Builder plugin.
Text Domain:  builder-countdown
Domain Path:  /languages
*/

defined( 'ABSPATH' ) or die( '-1' );

class Builder_Countdown {

	private static $instance = null;
	private $url;
	private $dir;
	private $version;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return	A single instance of this class.
	 */
	public static function get_instance() {
		return null == self::$instance ? self::$instance = new self : self::$instance;
	}

	private function __construct() {
		$this->constants();
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 5 );
		add_action( 'themify_builder_setup_modules', array( $this, 'register_module' ) );
		add_action( 'themify_builder_admin_enqueue', array( $this, 'admin_enqueue' ), 15 );
                add_filter('themify_builder_addons_assets',array($this,'assets'),10,1);
		add_action( 'init', array( $this, 'updater' ) );
	}

	public function constants() {
		$data = get_file_data( __FILE__, array( 'Version' ) );
		$this->version = $data[0];
		$this->url = trailingslashit( plugin_dir_url( __FILE__ ) );
		$this->dir = trailingslashit( plugin_dir_path( __FILE__ ) );
	}

	public function i18n() {
		load_plugin_textdomain( 'builder-countdown', false, '/languages' );
	}


	public function admin_enqueue() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'builder-countdown' );
		wp_enqueue_script( 'jquery-ui-timepicker', $this->url . 'assets/jquery-ui-timepicker.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, true );
		wp_enqueue_script( 'builder-countdown-admin', $this->url . 'assets/admin.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-timepicker' ), $this->version, true );
		wp_localize_script( 'builder-countdown-admin', 'BuilderCountdownAdmin', apply_filters( 'builder_stopwatch_admin_script_vars', array(
			'closeButton' => __( 'Close', 'builder-countdown' ),
			'buttonText' => __( 'Pick Date', 'builder-countdown' ),
			'dateFormat' => 'yy-mm-dd',
			'timeFormat' => 'HH:mm:ss',
			'separator' => ' ',
		) ) );
		wp_enqueue_style( 'builder-countdown-admin', $this->url . 'assets/admin.css' );
	}

	public function register_module( $ThemifyBuilder ) {
		$ThemifyBuilder->register_directory( 'templates', $this->dir . 'templates' );
		$ThemifyBuilder->register_directory( 'modules', $this->dir . 'modules' );
	}
        
        public function assets($assets){
            global $wp_scripts;
            $assets['builder-countdown']=array(
                                        'selector'=>'.module-countdown',
                                        'css'=>$this->url.'assets/style.css',
                                        'js'=>$this->url.'assets/script.js',
                                        'ver'=>$this->version,
                                        'external'=>Themify_Builder_Model::localize_js('builderCountDown', array(
                                            'url' =>  includes_url('js/jquery/ui/'),
                                            'ver'=>$wp_scripts->query('jquery-ui-core'),
                                        ))
                            );
            return $assets;
        }

	public function updater() {
		if( class_exists( 'Themify_Builder_Updater' ) ) {
			if ( ! function_exists( 'get_plugin_data') ) 
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
			$plugin_basename = plugin_basename( __FILE__ );
			$plugin_data = get_plugin_data( trailingslashit( plugin_dir_path( __FILE__ ) ) . basename( $plugin_basename ) );
			new Themify_Builder_Updater( array(
				'name' => trim( dirname( $plugin_basename ), '/' ),
				'nicename' => $plugin_data['Name'],
				'update_type' => 'addon',
			), $this->version, trim( $plugin_basename, '/' ) );
		}
	}
	/**
	 * Get a module ID and returns it's data
	 */
	public function get_module_data( $module_id ) {
		if( $module_id ) {
			$parts = explode( '-', $module_id );
			list( $module_name, $node_id, $rows, $columns, $modules ) = $parts;

			$builder_data = get_post_meta( $node_id, apply_filters( 'themify_builder_meta_key', '_themify_builder_settings' ), true );
			$builder_data = stripslashes_deep( maybe_unserialize( $builder_data ) );

			if( isset( $builder_data[$rows]['cols'][$columns]['modules'][$modules] ) && $builder_data[$rows]['cols'][$columns]['modules'][$modules]['mod_name'] == $module_name ) {
				return $builder_data[$rows]['cols'][$columns]['modules'][$modules]['mod_settings'];
			}
		}

		return false;
	}
}
Builder_Countdown::get_instance();