<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Module Name: Contact
 */
class TB_Contact_Module extends Themify_Builder_Component_Module {
	public function __construct() {
		parent::__construct(array(
			'name' => __('Contact', 'builder-contact'),
			'slug' => 'contact'
		));
	}

	public function get_assets() {
		$instance = Builder_Contact::get_instance();
		return array(
			'selector' => '.module-contact',
			'css' => themify_enque($instance->url . 'assets/style.css'),
			'js' => themify_enque($instance->url . 'assets/scripts.js'),
			'external' => Themify_Builder_Model::localize_js( 'BuilderContact', array(
				'admin_url' => admin_url( 'admin-ajax.php' )
			) ),
			'ver' => $instance->version,
		);
	}

	public function get_options() {
                $url = Builder_Contact::get_instance()->url;
		return array(
			array(
				'id' => 'mod_title_contact',
				'type' => 'text',
				'label' => __('Module Title', 'builder-contact'),
				'class' => 'large',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'layout_contact',
				'type' => 'layout',
				'label' => __('Layout', 'builder-contact'),
				'options' => array(
					array('img' => $url . 'assets/style1.png', 'value' => 'style1', 'label' => __('Style 1', 'builder-contact')),
					array('img' => $url . 'assets/style2.png', 'value' => 'style2', 'label' => __('Style 2', 'builder-contact')),
					array('img' => $url . 'assets/style3.png', 'value' => 'style3', 'label' => __('Style 3', 'builder-contact')),
					array('img' => $url . 'assets/style4.png', 'value' => 'animated-label', 'label' => __('Animated Label', 'builder-contact')),
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'mail_contact',
				'type' => 'text',
				'label' => __('Send to', 'builder-contact'),
				'class' => 'large',
				'after' => '<br><small>' . __( 'To send the form to multiple recipients, comma-separate the mail addresses.', 'builder-contact' ) . '</small>',
				'required' => array(
					'rule' => 'email',
					'message' => esc_html__( 'Please enter valid email address.', 'builder-contact' )
				),
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'post_type',
				'type' => 'checkbox',
				'label' => ' ',
				'options' => array(
					array( 'name' => 'enable', 'value' => __('Enable submissions as Contact posts', 'themify') )
				),
				'binding' => array(
					'checked' => array( 'show' => array( 'post_author', 'gdpr', 'gdpr_label' ) ),
					'not_checked' => array( 'hide' => array( 'post_author', 'gdpr', 'gdpr_label' ) ),
				),
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'post_author',
				'type' => 'checkbox',
				'label' => ' ',
				'wrap_with_class' => '_tf-hide',
				'options' => array(
					array( 'name' => 'add', 'value' => __('Assign "send to" email address as post author', 'themify') )
				),
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'gdpr',
				'type' => 'checkbox',
				'label' => ' ',
				'option_js' => true,
				'options' => array(
					array( 'name' => 'accept', 'value' => __('Show required consent checkbox to comply with GDPR', 'themify') )
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'gdpr_label',
				'type' => 'textarea',
				'label' => ' ',
				'default' => 'I consent to my submitted data being collected and stored',
				'option_js' => true,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'success_url',
				'type' => 'text',
				'label' => __( 'Success URL', 'builder-contact' ),
				'class' => 'large',
				'after' => '<br><small>' . __( 'Redirect to this URL when the form is successfully sent.', 'builder-contact' ) . '</small>',
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'success_message_text',
				'type' => 'text',
				'label' => __( 'Success Message', 'builder-contact' ),
				'class' => 'large',
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'auto_respond',
				'type' => 'checkbox',
				'label' => ' ',
				'options' => array(
					array( 'name' => 'enable', 'value' => __( 'Enable auto respond to submission', 'themify' ) )
				),
				'binding' => array(
					'checked' => array( 'show' => array( 'auto_respond_message', 'auto_respond_subject' ) ),
					'not_checked' => array( 'hide' => array( 'auto_respond_message', 'auto_respond_subject' ) ),
				),
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'auto_respond_subject',
				'type' => 'text',
				'label' => __( 'Auto Respond Subject', 'builder-contact' ),
				'class' => 'large',
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'auto_respond_message',
				'type' => 'textarea',
				'label' => __( 'Auto Respond Message', 'builder-contact' ),
				'class' => 'large',
				'render_callback' => array(
					'binding' => false
				)
			),
			array(
				'id' => 'default_subject',
				'type' => 'text',
				'label' => __( 'Default Subject', 'builder-contact' ),
				'class' => 'large',
				'after' => '<br><small>' . __( 'This will be used as the subject of the mail if the Subject field is not shown on the contact form.', 'builder-contact' ) . '</small>',
                                'render_callback' => array(
					'binding' => ''
				)
			),
			array(
				'id' => 'fields_contact',
				'type' => 'contact_fields',
				'class' => 'large',
				'render_callback' => array(
					'binding' => 'live',
					'control_type' => 'fields_contact'
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_class_contact',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'builder-contact'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'builder-contact') ),
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
	}

	public function get_default_settings() {
		return array(
			'field_name_label' => esc_html__( 'Your Name', 'builder-contact' ),
			'field_email_label' => esc_html__( 'Your Email', 'builder-contact' ),
			'field_subject_label' => esc_html__( 'Subject', 'builder-contact' ),
			'field_subject_active' => 'yes',
			'field_subject_require' => 'yes',
			'field_message_label' => esc_html__( 'Message', 'builder-contact' ),
			'field_sendcopy_label' => __( 'Send a copy to myself', 'builder-contact' ),
			'field_send_label' => esc_html__( 'Send', 'builder-contact' ),
			'field_send_align' => 'left',
			'field_extra' => '{ "fields": [] }',
			'field_order' => '{}',
		);
	}

	public function get_styling() {
		$general = array(
                        //bacground
                        self::get_seperator('image_bacground', __('Background', 'themify'), false),
                        self::get_color('.module-contact', 'background_color', __('Background Color', 'themify'), 'background-color'),
			// Font
                        self::get_seperator('font', __('Font', 'themify')),
                        self::get_font_family('.module-contact'),
                        self::get_color('.module-contact', 'font_color', __('Font Color', 'themify')),
                        self::get_font_size('.module-contact'),
                        self::get_line_height('.module-contact'),
                        self::get_text_align('.module-contact'),
                        // Padding
                        self::get_seperator('padding', __('Padding', 'themify')),
                        self::get_padding('.module-contact'),
                        // Margin
                        self::get_seperator('margin', __('Margin', 'themify')),
                        self::get_margin('.module-contact'),
                        // Border
                        self::get_seperator('border', __('Border', 'themify')),
                        self::get_border('.module-contact')
		);

		$labels = array(
			// Font
                        self::get_seperator('font', __('Font', 'themify'),false),
                        self::get_font_family('.module-contact .control-label','font_family_labels'),
                        self::get_color('.module-contact .control-label', 'font_color_labels', __('Font Color', 'themify')),
                        self::get_font_size('.module-contact .control-label','font_size_labels')
		);

		$inputs = array(
                        //bacground
                        self::get_seperator('image_bacground', __('Background', 'themify'), false),
                        self::get_color(array( '.module-contact input[type="text"]', '.module-contact textarea' ), 'background_color_inputs', __('Background Color', 'themify'), 'background-color'),
			// Font
                        self::get_seperator('font', __('Font', 'themify')),
                        self::get_font_family(array( '.module-contact input[type="text"]', '.module-contact textarea' ),'font_family_inputs'),
                        self::get_color(array( '.module-contact input[type="text"]', '.module-contact textarea' ), 'font_color_inputs', __('Font Color', 'themify')),
                        self::get_font_size(array( '.module-contact input[type="text"]', '.module-contact textarea' ),'font_size_inputs'),
			// Border
                        self::get_seperator('border', __('Border', 'themify')),
                        self::get_border(array( '.module-contact input[type="text"]', '.module-contact textarea' ),'border_inputs')
		);

		$send_button = array(
                        //bacground
                        self::get_seperator('image_bacground', __('Background', 'themify'), false),
                        self::get_color('.module-contact .builder-contact-field-send button', 'background_color_send', __('Background Color', 'themify'), 'background-color'),
			// Font
                        self::get_seperator('font', __('Font', 'themify')),
                        self::get_font_family('.module-contact .builder-contact-field-send button' ,'font_family_send'),
                        self::get_color( '.module-contact .builder-contact-field-send button', 'font_color_send', __('Font Color', 'themify')),
                        self::get_font_size( '.module-contact .builder-contact-field-send button','font_size_send'),
			// Border
                        self::get_seperator('border', __('Border', 'themify')),
                        self::get_border('.module-contact .builder-contact-field-send button','border_send')
		);

		$success_message = array(
                        //bacground
                        self::get_seperator('success', __('Background', 'themify'), false),
                        self::get_color('.module-contact .contact-success', 'background_color_success_message', __('Background Color', 'themify'), 'background-color'),
			// Font
                        self::get_seperator('font', __('Font', 'themify')),
                        self::get_font_family('.module-contact .contact-success','font_family_success_message'),
                        self::get_color('.module-contact .contact-success', 'font_color_success_message', __('Font Color', 'themify')),
                        self::get_font_size('.module-contact .contact-success','font_size_success_message'),
                        self::get_line_height('.module-contact .contact-success','line_height_success_message'),
                        self::get_text_align('.module-contact .contact-success','text_align_success_message'),
                        // Padding
                        self::get_seperator('padding', __('Padding', 'themify')),
                        self::get_padding('.module-contact .contact-success','padding_success_message'),
                        // Margin
                        self::get_seperator('margin', __('Margin', 'themify')),
                        self::get_margin('.module-contact .contact-success','margin_success_message'),
                        // Border
                        self::get_seperator('border', __('Border', 'themify')),
                        self::get_border('.module-contact .contact-success','border_success_message')
		);

		$error_message = array(
                         //bacground
                        self::get_seperator('success', __('Background', 'themify'), false),
                        self::get_color('.module-contact .contact-error', 'background_color_error_message', __('Background Color', 'themify'), 'background-color'),
			// Font
                        self::get_seperator('font', __('Font', 'themify')),
                        self::get_font_family('.module-contact .contact-error','font_family_error_message'),
                        self::get_color('.module-contact .contact-error', 'font_color_error_message', __('Font Color', 'themify')),
                        self::get_font_size('.module-contact .contact-error','font_size_error_message'),
                        self::get_line_height('.module-contact .contact-error','line_height_error_message'),
                        self::get_text_align('.module-contact .contact-error','text_align_error_message'),
                        // Padding
                        self::get_seperator('padding', __('Padding', 'themify')),
                        self::get_padding('.module-contact .contact-error','padding_error_message'),
                        // Margin
                        self::get_seperator('margin', __('Margin', 'themify')),
                        self::get_margin('.module-contact .contact-error','margin_error_message'),
                        // Border
                        self::get_seperator('border', __('Border', 'themify')),
                        self::get_border('.module-contact .contact-error','border_error_message')
		);

		return array(
			array(
				'type' => 'tabs',
				'id' => 'module-styling',
				'tabs' => array(
					'general' => array(
						'label' => __('General', 'themify'),
						'fields' => $general
					),
					'labels' => array(
						'label' => __('Field Labels', 'themify'),
						'fields' => $labels
					),
					'inputs' => array(
						'label' => __('Input Fields', 'themify'),
						'fields' => $inputs
					),
					'send_button' => array(
						'label' => __('Send Button', 'themify'),
						'fields' => $send_button
					),
					'success_message' => array(
						'label' => __('Success Message', 'themify'),
						'fields' => $success_message
					),
					'error_message' => array(
						'label' => __('Error Message', 'themify'),
						'fields' => $error_message
					)
				)
			),
		);

	}

	protected function _visual_template() {
		$module_args = self::get_module_args();?>
		<#
		try{
			field_extra = JSON.parse(data.field_extra);
			field_extra = field_extra.fields;
		} catch( e ){
			field_extra = {};
		}
		try{
			field_order = JSON.parse(data.field_order);
		} catch( e ){
			field_order = {};
		}
		#>
		<div class="module module-<?php echo $this->slug; ?> {{ data.css_class_contact }} <# data.layout_contact ? print('contact-' + data.layout_contact) : ''; #>">
			<!--insert-->
                        <# if( data.mod_title_contact ) { #>
				<?php echo $module_args['before_title']; ?>
				{{{ data.mod_title_contact }}}
				<?php echo $module_args['after_title']; ?>
			<# } #>

			<?php do_action( 'themify_builder_before_template_content_render' ); ?>

			<form class="builder-contact" method="post">
				<div class="contact-message"></div>

				<div class="builder-contact-fields">
					<div class="builder-contact-field builder-contact-field-name builder-contact-text-field" data-order="{{ field_order.field_name_label }}">
						<label class="control-label"><# data.field_name_label != '' ? print(data.field_name_label) : print('Name') #> <# if( data.field_name_label != '' ) { #><span class="required">*</span><# } #></label>
						<div class="control-input">
							<input type="text" name="contact-name" placeholder="{{{ data.field_name_placeholder }}}" value="" class="form-control" required />
						</div>
					</div>

					<div class="builder-contact-field builder-contact-field-email builder-contact-text-field" data-order="{{ field_order.field_email_label }}">
						<label class="control-label"><# data.field_email_label != '' ? print(data.field_email_label) : print('Email') #> <# if( data.field_email_label != '' ) { #><span class="required">*</span><# } #></label>
						<div class="control-input">
							<input type="text" name="contact-email" placeholder="{{{ data.field_email_placeholder }}}" value="" class="form-control" required />
						</div>
					</div>

					<# if( data.field_subject_active === 'yes' ) { #>
					<div class="builder-contact-field builder-contact-field-subject builder-contact-text-field" data-order="{{ field_order.field_subject_label }}">
						<label class="control-label"><# data.field_subject_label != '' ? print(data.field_subject_label) : print('Subject') #> <# if( data.field_subject_require ){ #><span class="required">*</span><# } #></label>
						<div class="control-input">
							<input type="text" name="contact-subject" placeholder="{{{ data.field_subject_placeholder }}}" value="" class="form-control" <# true === data.field_subject_require && print( 'required' ) #> />
						</div>
					</div>
					<# } #>

					<div class="builder-contact-field builder-contact-field-message builder-contact-textarea-field" data-order="{{ field_order.field_message_label }}">
						<label class="control-label"><# data.field_message_label != '' ? print(data.field_message_label) : print('Message') #> <# if( data.field_message_label != '' ) { #><span class="required">*</span><# } #></label>
						<div class="control-input">
							<textarea name="contact-message" placeholder="{{{ data.field_message_placeholder }}}" rows="8" cols="45" class="form-control" required></textarea>
						</div>
					</div>

					<# _.each( field_extra, function( field, field_index ){ #>
						<div class="builder-contact-field builder-contact-field-extra builder-contact-{{ field.type }}-field" data-order="{{ field_order[field.label] }}">
							<label class="control-label">{{{ field.label }}} <# if( field.required ){ #><span class="required">*</span><# } #></#></label>
							<div class="control-input">
							<# if( 'textarea' == field.type ){ #>
								<textarea name="field_extra_{{ field_index }}" placeholder="{{ field.value }}" rows="8" cols="45" class="form-control" <# true === field.required && print( 'required' ) #>></textarea>
							<# } else if( 'text' == field.type ){ #>
								<input type="text" name="field_extra_{{ field_index }}" placeholder="{{ field.value }}" class="form-control" <# true === field.required && print( 'required' ) #> />
							<# } else if( 'static' == field.type ){ #>
								{{{ field.value }}}
							<# } else if( 'radio' == field.type ){ #>
								<# _.each( field.value, function( value, index ){ #>
									<label>
										<input type="radio" name="field_extra_{{ field_index }}" value="{{ value }}" class="form-control" <# true === field.required && print( 'required' ) #> /> {{ value }}
									</label>
								<# }) #>
							<# } else if( 'select' == field.type ){ #>
								<select name="field_extra_{{ field_index }}" class="form-control" <# true === field.required && print( 'required' ) #>>
									<# _.each( field.value, function( value, index ){ #>
										<option value="{{ value }}"> {{ value }} </option>
									<# }) #>
								</select>
							<# } else if( 'checkbox' == field.type ){ #>
								<# _.each( field.value, function( value, index ){ #>
									<label>
										<input type="checkbox" name="field_extra_{{ field_index }}[]" value="{{ value }}" class="form-control"/> {{ value }}
									</label>
								<# }) #>
							<# } #>
							</div>
						</div>
					<# }) #>

					<# if( data.field_captcha_active == 'yes' ) { #>
						<div class="builder-contact-field builder-contact-field-captcha">
							<label class="control-label">{{{ data.field_captcha_label }}} <span class="required">*</span></label>
							<div class="control-input">
								 <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( Builder_Contact::get_instance()->get_option( 'recapthca_public_key' ) ); ?>"></div>
							</div>
						</div>
					<# } #>

					<# if( data.field_sendcopy_active ) { #>
					<div class="builder-contact-field builder-contact-field-sendcopy">
						<div class="control-label">
							<div class="control-input checkbox">
								<label class="send-copy">
									<input type="checkbox" name="send-copy" value="1" /> <# data.field_sendcopy_label != '' ? print(data.field_sendcopy_label) : print('Send a copy to myself') #>
								</label>
							</div>
						</div>
					</div>
					<# } #>

					<# if( data.gdpr ) { #>
					<div class="builder-contact-field builder-contact-field-gdpr">
						<div class="control-label">
							<div class="control-input checkbox">
								<label class="field-gdpr">
									<input type="checkbox" name="gdpr" value="1" required> <# data.gdpr_label != '' ? print(data.gdpr_label) : print('I consent to my submitted data being collected and stored') #>
								</label>
							</div>
						</div>
					</div>
					<# } #>


					<div class="builder-contact-field builder-contact-field-send">
						<div class="control-input builder-contact-field-send-{{ data.field_send_align }}">
							<button type="submit" class="btn btn-primary"> <i class="fa fa-cog fa-spin"></i> <# if( data.field_send_label != '' ) { #> {{{ data.field_send_label }}} <# }else{ #> Send <# } #></button>
						</div>
					</div>
				</div>
			</form>

			<?php do_action( 'themify_builder_after_template_content_render' ); ?>
		</div>
	<?php
	}
}

function themify_builder_field_contact_fields( $field, $mod_name ) {
	?>
	<div class="themify_builder_field builder_contact_fields">
		<div class="themify_builder_input">
		<table class="contact_fields">
		<thead>
			<tr>
				<th><?php _e( 'Field', 'builder-contact' ); ?></th>
				<th><?php _e( 'Label', 'builder-contact' ); ?></th>
				<th><?php _e( 'Placeholder', 'builder-contact' ); ?></th>
				<th><?php _e( 'Show', 'builder-contact' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php _e( 'Name', 'builder-contact' ) ?><span class="ti-split-v"></span></td>
				<td><input type="text" id="field_name_label" name="field_name_label" value="" class="tb_lb_option large" placeholder="<?php _e( 'Name', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td><input type="text" id="field_name_placeholder" name="field_name_placeholder" value="" class="tb_lb_option large" placeholder="<?php _e( 'Placeholder', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td></td>
			</tr>
			<tr>
				<td><?php _e( 'Email', 'builder-contact' ) ?><span class="ti-split-v"></span></td>
				<td><input type="text" id="field_email_label" name="field_email_label" value="" class="tb_lb_option large" placeholder="<?php _e( 'Email', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td><input type="text" id="field_email_placeholder" name="field_email_placeholder" value="" class="tb_lb_option large" placeholder="<?php _e( 'Placeholder', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td></td>
			</tr>
			<tr>
				<td><?php _e( 'Subject', 'builder-contact' ) ?><span class="ti-split-v"></span></td>
				<td>
					<input type="text" id="field_subject_label" name="field_subject_label" value="" class="tb_lb_option large" placeholder="<?php _e( 'Subject', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" />
					<div class="tb_lb_option themify-checkbox" id="field_subject_require" data-control-binding="live" data-control-type="checkbox"><input type="checkbox" name="field_subject_require" value="yes" class="tb-checkbox" /> <?php _e( 'Required', 'builder-contact' ); ?></div>
				</td>
				<td><input type="text" id="field_subject_placeholder" name="field_subject_placeholder" value="" class="tb_lb_option large" placeholder="<?php _e( 'Placeholder', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td class="tb_lb_option themify-checkbox" id="field_subject_active" data-control-binding="live" data-control-type="checkbox"><input type="checkbox" name="field_subject_active" value="yes" class="tb-checkbox" /></td>
			</tr>
			<tr>
				<td><?php _e( 'Message', 'builder-contact' ) ?></td>
				<td><input type="text" id="field_message_label" name="field_message_label" value="" class="tb_lb_option large" placeholder="<?php _e( 'Message', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td><input type="text" id="field_message_placeholder" name="field_message_placeholder" value="" class="tb_lb_option large" placeholder="<?php _e( 'Placeholder', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td class=""></td>
			</tr>
			<tr class="tb-no-sort tb-new-field-row">
				<td>
					<a href="#" class="tb-new-field-action"><span class="ti-plus"></span><?php esc_html_e( 'Add Field', 'builder-contact' ); ?></a>
				</td>
				<td><input type="text" id="field_extra" name="field_extra" value="" class="tb_lb_option hidden-all" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td><?php _e( 'Captcha', 'builder-contact' ) ?></td>
				<td><input type="text" id="field_captcha_label" name="field_captcha_label" value="" class="tb_lb_option large" placeholder="<?php _e( 'Captcha', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" />
				<p class="description"><?php printf( __( 'Requires Captcha keys entered at: <a href="%s">reCAPTCHA settings</a>.', 'builder-contact' ), admin_url( 'admin.php?page=builder-contact' ) ); ?></p>
				</td>
				<td></td>
				<td class="tb_lb_option themify-checkbox" id="field_captcha_active" data-control-binding="live" data-control-type="checkbox"><input type="checkbox" name="field_captcha_active" value="yes" class="tb-checkbox" /></td>
			</tr>
			<tr>
				<td><?php _e( 'Send Copy', 'builder-contact' ) ?></td>
				<td><input type="text" id="field_sendcopy_label" name="field_sendcopy_label" value="" class="tb_lb_option large" placeholder="<?php _e( 'Send Copy', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td></td>
				<td class="tb_lb_option themify-checkbox" id="field_sendcopy_active" data-control-binding="live" data-control-type="checkbox"><input type="checkbox" name="field_sendcopy_active" value="yes" class="tb-checkbox" /></td>
			</tr>
			<tr class="hidden-all">
				<td><input type="text" id="field_order" name="field_order" value="" class="tb_lb_option hidden-desktop hidden-tablet hidden-mobile" data-control-binding="live" data-control-event="keyup" data-control-type="change" /></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td><?php _e( 'Send Button', 'builder-contact' ) ?></td>
				<td>
					<input type="text" id="field_send_label" name="field_send_label" value="" class="tb_lb_option large" placeholder="<?php _e( 'Send', 'builder-contact' ) ?>" data-control-binding="live" data-control-event="keyup" data-control-type="change" />
					<div class="selectwrapper">
						<select id="field_send_align" name="field_send_align" class="tb_lb_option module-widget-select-field" data-control-binding="live" data-control-event="change" data-control-type="change">
							<option value="left"><?php _e( 'Left', 'builder-contact' ); ?></option>
							<option value="right"><?php _e( 'Right', 'builder-contact' ); ?></option>
							<option value="center"><?php _e( 'Center', 'builder-contact' ); ?></option>
						</select>
					</div><?php _e( 'Button Alignment', 'builder-contact' ); ?>
				</td>
				<td></td>
				<td>&nbsp;</td>
			</tr>

		</tfoot>
		</table>
		</div>
	</div>
	<?php
}

Themify_Builder_Model::register_module( 'TB_Contact_Module' );