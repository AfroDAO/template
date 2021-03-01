<?php

/**
 * Customer email customer details
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php if (RightPress_WC_Legacy::order_get_billing_email($order)): ?>
	<p><strong><?php _e('Email:', 'subscriptio'); ?></strong> <?php echo RightPress_WC_Legacy::order_get_billing_email($order); ?></p>
<?php endif; ?>
<?php if (RightPress_WC_Legacy::order_get_billing_phone($order)): ?>
	<p><strong><?php _e('Tel:', 'subscriptio'); ?></strong> <?php echo RightPress_WC_Legacy::order_get_billing_phone($order); ?></p>
<?php endif; ?>

<?php wc_get_template('emails/email-addresses.php', array('order' => $order)); ?>
