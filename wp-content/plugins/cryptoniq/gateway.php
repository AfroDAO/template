<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

add_action( 'plugins_loaded', 'cryptoniq_payment_gateway_init' );
function cryptoniq_payment_gateway_init() {
    if ( !class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

    class Cryptoniq_Gateway extends WC_Payment_Gateway
	{
        public function __construct() {
			$description = esc_html__( 'Pay with cryprocurrencies.', 'cryptoniq' );
			if ( class_exists( 'Redux' ) && !empty( Redux::getOption( CRYPTONIQ_OPTION, 'description' ) ) ) {
				$description = Redux::getOption( CRYPTONIQ_OPTION, 'description' );
			} 
			
            $this->id = CRYPTONIQ_PAY_ID;
			
            $this->has_fields = false;
			$this->icon = CRYPTONIQ_DIR_URL . 'assets/images/cryptoniq.logo.png';
            $this->init_form_fields();
            $this->init_settings();
			
            $this->method_title = 'Cryptoniq'; 
            $this->method_description = esc_html__( 'Pay with cryprocurrencies.', 'cryptoniq' );
            $this->title = 'Cryptoniq';
			$this->description = $description;
		
			$this->is_enabled(); 
			
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_filter( 'woocommerce_endpoint_order-pay_title', array( $this, 'pay_title' ), 10, 2 );
        }
    
        public function init_form_fields() {
            $this->form_fields = array (
                'enabled' => array (
                    'title' => esc_html__( 'Online/Offline gateway', 'cryptoniq' ),
                    'type' => 'checkbox',
                    'label' => esc_html__( 'Enable', 'cryptoniq' ),
                    'default' => 'no'
                )
            );
        }

        public function admin_options(){
            echo '<h3>Cryptoniq</h3>';
            echo '<table class="form-table">';
            	$this->generate_settings_html();
            echo '</table>';
        }

        public function payment_fields() {
?>

<script>
(function ($, window, document) {
	$(document).ready(function() {
		var $selector = $( '.cryptoniq-pgateway-selectors' ),
			selector_coin = $selector.data( 'coin' ),
			selector_price = $selector.data( 'price' );
		
		$( '#cryptoniq-pgateway-coin-price' ).text( selector_coin + ': ' + selector_price );
		$( '#cryptoniq-pgateway' ).find( '.cryptoniq-pgateway-input-item' ).click( function() {
			$( '#cryptoniq-pgateway-coin-price' ).text( $(this).data('coin') + ': ' + $(this).data('price') );
		});
	});
}(jQuery, window, document));
</script>

<?php
			global $woocommerce;
		    $pay_coins = cryptoniq_get_option( 'payment_coins' );
			
		    if ( is_array( $pay_coins ) ) {
			    echo '<div id="cryptoniq-pgateway" class="cryptoniq-border-box">';
					$coin_data = '';
				    if ( cryptoniq_get_option( 'price_coin_show' ) == 1 && cryptoniq_get_option( 'price_coin_name' ) ) {
						$coin_data = ' data-coin="' . cryptoniq_get_option( 'price_coin_name' ) . '" data-price="' . cryptoniq_get_price( cryptoniq_get_option( 'price_coin_name' ), $woocommerce->cart->total ) . '"';
					}

					echo '<div class="cryptoniq-pgateway-selectors"' . $coin_data . '>';
			    
			    	foreach ( $pay_coins as $pay_num => $pay_coin ) {
						if ( cryptoniq_get_option( 'price_coin_show' ) == 1 && cryptoniq_get_option( 'price_coin_name' ) == $pay_coin ) {
							$checked = ' checked="checked"';
						} else {
							$checked = ( $pay_num == 0 ) ? ' checked="checked"' : '';
						}
				
				    	echo '<input' . $checked . ' id="cryptoniq-pgateway-type-' . strtolower( $pay_coin ) . '" class="cryptoniq-pgateway-input" type="radio" name="cryptoniq_coin_name" value="' . $pay_coin . '" />';
				    	echo '<label class="cryptoniq-pgateway-input-item cryptoniq-pgateway-input-item-' . strtolower( $pay_coin ) . '" for="cryptoniq-pgateway-type-' . strtolower( $pay_coin ) . '" data-coin="' . $pay_coin . '" . data-price="' . cryptoniq_get_price( $pay_coin, $woocommerce->cart->total ) . '"></label>';			
			    	}
			    
					echo '</div>';
				    echo '<div id="cryptoniq-pgateway-coin-price">BTC: ' . cryptoniq_get_price( 'BTC', $woocommerce->cart->total ) . '</div>';
				
					if ( $this->description ) {
				    	echo '<p class="cryptoniq-pgateway-descr">' . $this->description . '</p>';
					}
			    echo '</div>';		 
		    }  
        }
		
     	public function pay_title( $title, $endpoint ) {
			return;
    	}   
  
        public function process_payment( $order_id ){
            $order = new WC_Order( $order_id );

            return array(
				'result' => 'success',
				'redirect' => add_query_arg(
					'order-pay',
					$order->id,
					add_query_arg( 
						'key',
						$order->order_key,
						get_permalink( woocommerce_get_page_id( 'pay' ) )
					)
				)
            );
        }
    
	    public function is_enabled() {
    		if ( !class_exists( 'Redux' ) ) {
        		return;
    		}
			
		    if ( $this->get_option( 'enabled' ) == 'yes' ) {
			    Redux::setOption( CRYPTONIQ_OPTION, 'cryptoniq_panel_activate', 'yes' );
		    } else {
		        Redux::setOption( CRYPTONIQ_OPTION, 'cryptoniq_panel_activate', 'no' );
		    }
	    }    
    }

    // Add Gateway to WooCommerce
    // ======================================================

    function cryptoniq_add_payment_gateway( $methods ) {
        $methods[] = 'Cryptoniq_Gateway';
        return $methods;
    }

    add_filter( 'woocommerce_payment_gateways', 'cryptoniq_add_payment_gateway' );
}