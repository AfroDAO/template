<?php

class Themify_Tiles_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'setup_options' ), 100 );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function setup_options() {
		$hook = add_submenu_page(
			'edit.php?post_type=themify_tile',
			__( 'Themify Tiles Settings', 'themify-tiles' ),
			__( 'Settings', 'themify-tiles' ),
			'manage_options',
			'themify-tiles-settings',
			array( $this, 'create_admin_page' )
		);
	}

    public function create_admin_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Themify Tiles', 'themify-tiles' ); ?></h2>           
			<form method="post" action="options.php">
				<?php
				do_action( 'themify_tiles_settings_page' );
				// This prints out all hidden setting fields
				settings_fields( 'themify_tiles' );   
				do_settings_sections( 'themify-tiles-settings' );
				submit_button(); 
				?>
			</form>
		</div>
		<?php
    }

	/**
	 * Register and add settings
	 */
	public function page_init() {        
		register_setting(
			'themify_tiles', // Option group
			'themify_tiles' // Option name
		);

		add_settings_section(
			'tile_integration', // ID
			__( 'Integration', 'themify-tiles' ), // Title
			null, // Callback
			'themify-tiles-settings' // Page
		);

		add_settings_field(
			'google_maps_key', // ID
			__( 'Google Maps API Key', 'builder-contact' ), // Title 
			array( $this, 'google_maps_key_callback' ), // Callback
			'themify-tiles-settings', // Page
			'tile_integration' // Section           
		);
    }

	public function google_maps_key_callback() {
		$options = get_option( 'themify_tiles' );
		$key = isset( $options['google_maps_key'] ) ? $options['google_maps_key'] : '';
		printf(
			'<input type="text" class="regular-text" id="title" name="themify_tiles[google_maps_key]" value="%s" />',
			$key
		);
		printf( '<p class="description">' . __( '<a href="#">Generate an API</a> key and insert it here.', 'themify-tiles' ) . '</p>', 'http://developers.google.com/maps/documentation/javascript/get-api-key#key' );
	}
}
new Themify_Tiles_Admin;