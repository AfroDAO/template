<?php
/*
Plugin Name:  Builder Image Pro
Plugin URI:   http://themify.me/addons/image-pro
Version:      1.2.4
Author:       Themify
Description:  Builder addon to display cool image effects with overlay animation. It requires to use with the latest version of any Themify theme or the Themify Builder plugin.
Text Domain:  builder-image-pro
Domain Path:  /languages
*/

defined( 'ABSPATH' ) or die( '-1' );

class Builder_Image_Pro {

	public $url;
	private $dir;
	public $version;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return	A single instance of this class.
	 */
	public static function get_instance() {
            static $instance = null;
            if($instance===null){
                $instance = new self;
            }
            return $instance;
	}

	private function __construct() {
		$this->constants();
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 5 );
		add_action( 'themify_builder_setup_modules', array( $this, 'register_module' ) );
		add_filter( 'themify_builder_script_vars', array( $this, 'themify_builder_script_vars' ) );
		add_action( 'init', array( $this, 'updater' ) );
		add_filter( 'plugin_row_meta', array( $this, 'themify_plugin_meta'), 10, 2 );
	}

	public function constants() {
		$data = get_file_data( __FILE__, array( 'Version' ) );
		$this->version = $data[0];
		$this->url = trailingslashit( plugin_dir_url( __FILE__ ) );
		$this->dir = trailingslashit( plugin_dir_path( __FILE__ ) );
	}

	public function themify_plugin_meta( $links, $file ) {
		if ( plugin_basename( __FILE__ ) == $file ) {
			$row_meta = array(
			  'changelogs'    => '<a href="' . esc_url( 'https://themify.me/changelogs/' ) . basename( dirname( $file ) ) .'.txt" target="_blank" aria-label="' . esc_attr__( 'Plugin Changelogs', 'builder-image-pro' ) . '">' . esc_html__( 'View Changelogs', 'builder-image-pro' ) . '</a>'
			);
	 
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}
	public function i18n() {
		load_plugin_textdomain( 'builder-image-pro', false, '/languages' );
	}



	public function register_module() {
            //temp code for compatibility  builder new version with old version of addon to avoid the fatal error, can be removed after updating(2017.07.20)
            if(class_exists('Themify_Builder_Component_Module')){
                Themify_Builder_Model::register_directory( 'templates', $this->dir . 'templates' );
		Themify_Builder_Model::register_directory( 'modules', $this->dir . 'modules' );
            }
	}

	/**
	 * Load animate.css library when Image Pro module is used in the page
	 *
	 * @since 1.1.3
	 */
	public function themify_builder_script_vars( $vars ) {
		$vars['animationInviewSelectors'][] = '.module.module-pro-image';
		return $vars;
	}

	public function updater() {
		if( class_exists( 'Themify_Builder_Updater' ) ) {
			if ( ! function_exists( 'get_plugin_data') ){
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                        }
			$plugin_basename = plugin_basename( __FILE__ );
			$plugin_data = get_plugin_data( trailingslashit( plugin_dir_path( __FILE__ ) ) . basename( $plugin_basename ) );
			new Themify_Builder_Updater( array(
				'name' => trim( dirname( $plugin_basename ), '/' ),
				'nicename' => $plugin_data['Name'],
				'update_type' => 'addon',
			), $this->version, trim( $plugin_basename, '/' ) );
		}
	}
}
Builder_Image_Pro::get_instance();
