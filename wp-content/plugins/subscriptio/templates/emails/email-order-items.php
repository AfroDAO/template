<?php

/**
 * Customer email order items
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
        <tr>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Product', 'subscriptio'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'subscriptio'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Price', 'subscriptio'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php echo RightPress_WC_Legacy::get_email_order_items($order, array('show_download_links' => true, 'show_purchase_note' => true)); ?>
    </tbody>
    <tfoot>
        <?php
            if ($totals = $order->get_order_item_totals()) {

                $i = 0;

                foreach ($totals as $total) {

                    $i++;

                    ?>
                    <tr>
                        <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
                        <td style="text-align:left; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
                    </tr>
                    <?php
                }
            }
        ?>
    </tfoot>
</table>
