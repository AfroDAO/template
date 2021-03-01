<?php

class Builder_Contact_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'setup_options' ), 100 );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function setup_options() {
		add_submenu_page(
			'edit.php?post_type=contact_messages',
			__( 'Captcha Settings', 'builder-contact' ),
			__( 'Captcha Settings', 'builder-contact' ),
			'manage_options',
			'builder-contact',
			array( $this, 'create_admin_page' )
		);

	}

    public function create_admin_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e( 'Builder Contact Captcha', 'builder-contact' ); ?></h2>
			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'builder_contact' );   
				do_settings_sections( 'builder-contact' );
				$this->show_recaptcha_guide();
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
			'builder_contact', // Option group
			'builder_contact' // Option name
		);

		add_settings_section(
			'builder-contact-recaptcha', // ID
			__( 'reCAPTCHA Settings', 'builder-contact' ), // Title
			null, // Callback
			'builder-contact' // Page
		);

		add_settings_field(
			'recapthca_public_key', // ID
			__( 'ReCaptcha Public Key', 'builder-contact' ), // Title 
			array( $this, 'recapthca_public_key_callback' ), // Callback
			'builder-contact', // Page
			'builder-contact-recaptcha' // Section           
		);

		add_settings_field(
			'recapthca_private_key', // ID
			__( 'ReCaptcha Private Key', 'builder-contact' ), // Title 
			array( $this, 'recapthca_private_key_callback' ), // Callback
			'builder-contact', // Page
			'builder-contact-recaptcha' // Section           
		);
    }

	public function recapthca_public_key_callback() {
		printf(
			'<input type="text" class="regular-text" id="title" name="builder_contact[recapthca_public_key]" value="%s" />',
			esc_attr( Builder_Contact::get_instance()->get_option( 'recapthca_public_key' ) )
		);
	}

	public function recapthca_private_key_callback() {
		printf(
			'<input type="text" class="regular-text" id="title" name="builder_contact[recapthca_private_key]" value="%s" />',
			esc_attr( Builder_Contact::get_instance()->get_option( 'recapthca_private_key' ) )
		);
	}

	public function show_recaptcha_guide() { ?>
		<h3>To set up your Captcha:</h3>
		<p>Go to <a href="http://www.google.com/recaptcha/intro/">http://www.google.com/recaptcha/intro/</a>, click the &quot;Get reCAPTCHA&quot; button. You may need to log in to your Google account in order for this to work.</p>
		<p>On the register a new site box, enter the Domains that you would like the reCaptcha form to appear (your website's URL address), then copy the ReCaptcha Public Key and the Secret key to this page.</p>
	<?php }
}