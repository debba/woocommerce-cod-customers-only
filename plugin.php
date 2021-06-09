<?php
/*
* Plugin Name: Woocommerce Enable COD for registered customers only
* Plugin Uri: https://github.com/debba/woocommerce-cod-customers-only
* Description: Using this WooCommerce plugin you can disable COD for guests.
* Author: Andrea Debernardi
* Author URI: https://www.dueclic.com
* Version: 1.0
* Tested up: 5.7
* WC requires at least: 5.0.0
* WC tested up to: 5.4.0
* Text Domain: woocommerce-cod-customers-only
* Domain Path: /languages/
* License: GPL v3
*/

if ( ! defined( 'ABSPATH' ) ) {
    die;
}


function ns_payment_gateway_disable_cod( $available_gateways ) {

    if ( isset( $available_gateways['cod'] ) ) {
        unset( $available_gateways['cod'] );
    }

    return $available_gateways;

}

add_action('woocommerce_checkout_update_order_review', 'ns_woocommerce_checkout_update_order_review', 10, 1);

function ns_show_errors_cod_disabled(){
    echo '<p class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( ' ns_show_errors_cod_disabled_message', esc_html__( 'In order to use Cash on delivery method you have to create an account.', 'woocommerce-cod-customers-only' ) ) . '</p>'; // @codingStandardsIgnoreLine
}

function ns_woocommerce_checkout_update_order_review($post_data) {
    parse_str( $post_data,
        $results );
    if (!is_user_logged_in() && isset($results['createaccount']) && intval($results['createaccount']) == 1){
        remove_filter( 'woocommerce_available_payment_gateways', 'ns_payment_gateway_disable_cod' );
        remove_action('woocommerce_review_order_before_submit', 'ns_show_errors_cod_disabled');
    } else {
        if (!is_user_logged_in()) {
            add_filter( 'woocommerce_available_payment_gateways',
                'ns_payment_gateway_disable_cod' );
            add_action( 'woocommerce_review_order_before_submit',
                'ns_show_errors_cod_disabled' );
        }
    }

}
