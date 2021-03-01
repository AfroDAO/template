<?php

/*
  Plugin Name:  Builder Maps Pro
  Plugin URI:   http://themify.me/addons/maps-pro
  Version:      1.2.7 
  Author:       Themify
  Description:  Maps Pro module allows you to insert Google Maps with multiple location markers with custom icons, tooltip text, and various map styles. It requires to use with the latest version of any Themify theme or the Themify Builder plugin.
  Text Domain:  builder-maps-pro
  Domain Path:  /languages
 */

defined('ABSPATH') or die('-1');

class Builder_Maps_Pro {

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
        if ($instance === null) {
            $instance = new self;
        }
        return $instance;
    }

    private function __construct() {
        $this->constants();
        $this->i18n();
        add_action('themify_builder_setup_modules', array($this, 'register_module'));
		add_filter( 'plugin_row_meta', array( $this, 'themify_plugin_meta'), 10, 2 );
        if (is_admin()) {
            add_action('themify_builder_admin_enqueue', array($this, 'admin_enqueue'));
            add_action('init', array($this, 'updater'));
        } else {
            add_filter('themify_styles_top_frame', array($this, 'frontend_style_enqueue'));
            add_action('themify_builder_frontend_enqueue', array($this, 'admin_enqueue'), 15);
        }
    }

    public function constants() {
        $data = get_file_data(__FILE__, array('Version'));
        $this->version = $data[0];
        $this->url = trailingslashit(plugin_dir_url(__FILE__));
        $this->dir = trailingslashit(plugin_dir_path(__FILE__));
    }

	public function themify_plugin_meta( $links, $file ) {
		if ( plugin_basename( __FILE__ ) == $file ) {
			$row_meta = array(
			  'changelogs'    => '<a href="' . esc_url( 'https://themify.me/changelogs/' ) . basename( dirname( $file ) ) .'.txt" target="_blank" aria-label="' . esc_attr__( 'Plugin Changelogs', 'builder-maps-pro' ) . '">' . esc_html__( 'View Changelogs', 'builder-maps-pro' ) . '</a>'
			);
	 
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}
    public function i18n() {
        load_plugin_textdomain('builder-maps-pro', false, '/languages');
    }

    private function localization() {
        $map_styles = array();
        foreach ($this->get_map_styles() as $key => $value) {
            $name = str_replace('.json', '', $key);
            $map_styles[$name] = $this->get_map_style($name);
        }
        return array(
            'key' => Themify_Builder_Model::getMapKey(),
            'styles' => $map_styles,
            'labels' => array(
                'add_marker' => __('Add Location Marker', 'builder-maps-pro'),
        ));
    }

    public function admin_enqueue() {
        //temp code for compatibility  builder new version with old version of addon to avoid the fatal error, can be removed after updating(2017.07.20)
        if(!class_exists('Themify_Builder_Component_Module')){
            return;
        }
        wp_enqueue_script('themify-builder-maps-pro-admin', themify_enque($this->url . 'assets/admin.js'), array(), $this->version, false);
        if(is_admin()){
            wp_enqueue_style('themify-builder-maps-pro-admin', themify_enque($this->url . 'assets/admin.css'));
        }
        wp_localize_script('themify-builder-maps-pro-admin', 'builderMapsPro',$this->localization());
    }

    public function frontend_style_enqueue($styles) {
        $styles[] = themify_enque($this->url . 'assets/admin.css');
        return $styles;
    }

    public function register_module() {
        //temp code for compatibility  builder new version with old version of addon to avoid the fatal error, can be removed after updating(2017.07.20)
        if (class_exists('Themify_Builder_Component_Module')) {
            Themify_Builder_Model::register_directory('templates', $this->dir . 'templates');
            Themify_Builder_Model::register_directory('modules', $this->dir . 'modules');
        }
    }

    public function updater() {
        if (class_exists('Themify_Builder_Updater')) {
            if (!function_exists('get_plugin_data')) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }
            $plugin_basename = plugin_basename(__FILE__);
            $plugin_data = get_plugin_data(trailingslashit(plugin_dir_path(__FILE__)) . basename($plugin_basename));
            new Themify_Builder_Updater(array(
                'name' => trim(dirname($plugin_basename), '/'),
                'nicename' => $plugin_data['Name'],
                'update_type' => 'addon',
                    ), $this->version, trim($plugin_basename, '/'));
        }
    }

    public function get_map_styles() {
        $theme_styles = is_dir(get_stylesheet_directory() . '/builder-maps-pro/styles/') ? self::list_dir(get_stylesheet_directory() . '/builder-maps-pro/styles/') : array();

        return array_merge(self::list_dir($this->dir . 'styles/'), $theme_styles);
    }

    private static function list_dir($path) {
        $dh = opendir($path);
        $files = array();
        while (false !== ( $filename = readdir($dh) )) {
            if ($filename !== '.' && $filename !== '..') {
                $files[$filename] = $filename;
            }
        }

        return $files;
    }

    public function get_map_style($name) {
        $file = get_stylesheet_directory() . '/builder-maps-pro/styles/' . $name . '.json';
        if(!file_exists($file)){
            $file =  $this->dir . 'styles/' . $name . '.json';
            if (!file_exists($file)) {
                return '';
            }
        }
        ob_start();
        include $file;
        return json_decode(ob_get_clean());
    }

}
Builder_Maps_Pro::get_instance();
