<?php

/**
 * Customer email customer details (plain text)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (RightPress_WC_Legacy::order_get_billing_email($order)) {
    echo __('Email:', 'subscriptio');
    echo RightPress_WC_Legacy::order_get_billing_email($order) . "\n";
}
if (RightPress_WC_Legacy::order_get_billing_phone($order)) {
    echo __('Tel:', 'subscriptio');
    echo RightPress_WC_Legacy::order_get_billing_phone($order) . "\n";
}

wc_get_template('emails/plain/email-addresses.php', array('order' => $order));
