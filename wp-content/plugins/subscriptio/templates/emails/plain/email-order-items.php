<?php

/**
 * Customer email order items (plain text)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

echo "\n" . RightPress_WC_Legacy::get_email_order_items($order, array('show_sku' => true, 'plain_text' => true));

echo "----------\n\n";

if ($totals = $order->get_order_item_totals()) {
    foreach ($totals as $total) {
        echo $total['label'] . "\t " . $total['value'] . "\n";
    }
}
