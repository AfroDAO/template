<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Countdown
 * 
 * Access original fields: $mod_settings
 */

if( method_exists( $GLOBALS['ThemifyBuilder'], 'load_templates_js_css' ) ) {
	$GLOBALS['ThemifyBuilder']->load_templates_js_css();
}

$fields_default = array(
	'mod_title_countdown' => '',
	'mod_date_countdown' => '',
	'done_action_countdown' => '',
	'content_countdown' => '',
	'redirect_countdown' => '',
	'color_countdown' => '',
	'label_days' => __( 'Days', 'builder-countdown' ),
	'label_hours' => __( 'Hours', 'builder-countdown' ),
	'label_minutes' => __( 'Minutes', 'builder-countdown' ),
	'label_seconds' => __( 'Seconds', 'builder-countdown' ),
	'add_css_countdown' => '',
	'counter_background_color' => '',
	'animation_effect' => '',
);

$fields_args = wp_parse_args( $mod_settings, $fields_default );
extract( $fields_args, EXTR_SKIP );
$animation_effect = $this->parse_animation_effect( $animation_effect, $fields_args );

$container_class = implode(' ', 
	apply_filters( 'themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID, $add_css_countdown, $animation_effect
	), $mod_name, $module_ID, $fields_args )
);

// get target date based on user timezone
$epoch = strtotime( $mod_date_countdown . ' ' . get_option( 'timezone_string' ) );
$next_year = strtotime( '+1 year' );

$container_props = apply_filters( 'themify_builder_module_container_props', array(
	'id' => $module_ID,
	'class' => $container_class
), $fields_args, $mod_name, $module_ID );
?>

<!-- module countdown -->
<div <?php echo $this->get_element_attributes( $container_props ); ?>>

	<?php if( '' != $counter_background_color ) : ?>
		<style>#<?php echo $module_ID; ?> .ui { background-color: <?php echo $this->stylesheet->get_rgba_color( $counter_background_color ); ?>; }</style>
	<?php endif; ?>

	<?php do_action( 'themify_builder_before_template_content_render' ); ?>

	<?php
	if( $epoch <= time() ) {
		if( $done_action_countdown == 'revealo' ) : ?>

			<div class="countdown-finished ui <?php echo $color_countdown; ?>">
				<?php echo apply_filters( 'themify_builder_module_content', $content_countdown ); ?>
			</div>

		<?php elseif( $done_action_countdown == 'redirect' && ! Themify_Builder_Model::is_frontend_editor_page() ) : ?>

			<script>
				window.location = '<?php echo esc_url( $redirect_countdown ); ?>';
			</script>

		<?php endif;

	} else { ?>

		<?php if ( $mod_title_countdown != '' ): ?>
			<?php echo $mod_settings['before_title'] . wp_kses_post( apply_filters( 'themify_builder_module_title', $mod_title_countdown, $fields_args ) ) . $mod_settings['after_title']; ?>
		<?php endif; ?>

		<div class="builder-countdown-holder" data-target-date="<?php echo $epoch; ?>">

			<?php if( $next_year < $epoch ) : ?>
				<div class="years ui <?php echo $color_countdown; ?>" data-leading-zeros="2">
					<span class="date-counter"></span>
					<span class="date-label"><?php _e( 'Years', 'builder-countdown' ); ?></span>
				</div>
			<?php endif; ?>

			<div class="days ui <?php echo $color_countdown; ?>" data-leading-zeros="2">
				<span class="date-counter"></span>
				<span class="date-label"><?php echo $label_days; ?></span>
			</div>
			<div class="hours ui <?php echo $color_countdown; ?>" data-leading-zeros="2">
				<span class="date-counter"></span>
				<span class="date-label"><?php echo $label_hours; ?></span>
			</div>
			<div class="minutes ui <?php echo $color_countdown; ?>" data-leading-zeros="2">
				<span class="date-counter"></span>
				<span class="date-label"><?php echo $label_minutes; ?></span>
			</div>
			<div class="seconds ui <?php echo $color_countdown; ?>" data-leading-zeros="2">
				<span class="date-counter"></span>
				<span class="date-label"><?php echo $label_seconds; ?></span>
			</div>
		</div>

	<?php } ?>

	<?php do_action( 'themify_builder_after_template_content_render' ); ?>
</div>
<!-- /module countdown -->
