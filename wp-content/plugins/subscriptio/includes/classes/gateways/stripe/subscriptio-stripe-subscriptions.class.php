<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to subscriptions
 *
 * @class Subscriptio_Stripe_Subscriptions
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Stripe_Subscriptions')) {

class Subscriptio_Stripe_Subscriptions
{

    /**
     * Constructor class
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function __construct($id = null)
    {
        // Process payment
        add_filter('subscriptio_automatic_payment_subscriptio_stripe', array($this, 'process_payment'), 10, 3);
    }

    /**
     * Process automatic subscription payment
     *
     * @access public
     * @param bool $payment_successful
     * @param array $order
     * @param array $subscription
     * @return bool
     */
    public function process_payment($payment_successful, $order, $subscription)
    {
        $customer_id = RightPress_WC_Legacy::customer_get_meta($subscription->user_id, '_subscriptio_stripe_customer_id', true);
        $default_card = RightPress_WC_Legacy::customer_get_meta($subscription->user_id, '_subscriptio_stripe_customer_default_card', true);

        if (empty($customer_id) || empty($default_card)) {
            return false;
        }

        // Load payment gateway object to access its methods
        $gateway = new Subscriptio_Stripe_Gateway();

        // Send request
        $response = $gateway->charge(array(
            'amount'        => RightPress_WC_Legacy::order_get_total($order) * Subscriptio_Stripe_Gateway::get_currency_multiplier($order),
            'currency'      => strtolower(RightPress_WC_Legacy::order_get_currency($order)),
            'description'   => apply_filters('subscriptio_stripe_payment_description', esc_html(get_bloginfo('name')) . ' - ' . __('Order', 'subscriptio-stripe') . ' ' . $order->get_order_number() . ' (' . __('Subscription', 'subscriptio-stripe') . ' ' . $subscription->get_subscription_number() . ')', $order),
            'metadata'      => array(
                'order_id'          => RightPress_WC_Legacy::order_get_id($order),
                'subscription_id'   => $subscription->id,
                'email'             => RightPress_WC_Legacy::order_get_billing_email($order),
            ),
            'statement_descriptor' => substr(sprintf(__('Subscr %s', 'subscriptio-stripe'), $subscription->get_subscription_number()), 0, 15),
            'customer'      => $customer_id,
            'source'          => $default_card,
        ));

        // Received error?
        if (is_string($response)) {
            $order->add_order_note(__('Automatic subscription payment failed (Stripe).', 'subscriptio-stripe') . ' ' . $response);
            return false;
        }

        // Add payment method
        RightPress_WC_Legacy::order_update_meta_data($order, '_payment_method', 'subscriptio_stripe');

        // Save charge id
        RightPress_WC_Legacy::order_update_meta_data($order, '_subscriptio_stripe_charge_id', $response->id);

        // Save capture status
        RightPress_WC_Legacy::order_update_meta_data($order, '_subscriptio_stripe_charge_captured', ($response->captured ? 'yes' : 'no'));

        // Charge captured?
        if ($response->captured) {
            $order->add_order_note(sprintf(__('Stripe charge %s captured.', 'subscriptio-stripe'), $response->id));
            $order->payment_complete();
        }

        // Only authorized
        else {
            $order->add_order_note(sprintf(__('Stripe charge %s authorized and will be charged as soon as you start processing this order. Authorization will expire in 7 days.', 'subscriptio-stripe'), $response->id));
            $order->update_status('on-hold');
            RightPress_WC_Legacy::order_reduce_stock_levels($order);
        }

        return true;
    }

}

new Subscriptio_Stripe_Subscriptions();

}
