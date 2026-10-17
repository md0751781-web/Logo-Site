<?php
/**
 * Checkout without customer details.
 *
 * With "Checkout without customer details" on (Customize → LD: Checkout, on by default) the checkout page shows only the
 * order summary and the payment methods: no billing, shipping, account or order-note fields. Optionally it still asks
 * for an email address so WooCommerce can email the download link.
 *
 * WooCommerce's block-based Checkout (the default on new stores) cannot remove fields through the classic
 * `woocommerce_checkout_fields` filter, so the checkout page is rendered with the classic `[woocommerce_checkout]`
 * shortcode instead while this mode is on.
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * none = no customer fields, email = only an email field, full = WooCommerce's standard checkout.
 */
function ldemb_checkout_mode() {
	if ( ! ldemb_mod( 'checkout_no_details' ) ) {
		return 'full';
	}
	return ldemb_mod( 'checkout_ask_email' ) ? 'email' : 'none';
}

function ldemb_checkout_slim() {
	return 'full' !== ldemb_checkout_mode();
}

/**
 * True when every item in the cart is a virtual/digital product. Anything physical needs the customer's address, even
 * on a store whose shipping zones are not set up yet (WooCommerce's needs_shipping() would then wrongly say "no").
 */
function ldemb_cart_is_digital() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		return true;
	}
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( empty( $item['data'] ) || ! $item['data']->is_virtual() ) {
			return false;
		}
	}
	return true;
}

/**
 * Render the classic checkout in place of the Checkout block.
 */
function ldemb_classic_checkout_content( $content ) {
	if ( ! ldemb_checkout_slim() || ! function_exists( 'is_checkout' ) || ! is_checkout() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	if ( ! has_block( 'woocommerce/checkout', $content ) ) {
		return $content;
	}

	$blocks = parse_blocks( $content );
	foreach ( $blocks as $i => $block ) {
		if ( 'woocommerce/checkout' === $block['blockName'] ) {
			$blocks[ $i ] = array(
				'blockName'    => null,
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '[woocommerce_checkout]',
				'innerContent' => array( '[woocommerce_checkout]' ),
			);
		}
	}
	return serialize_blocks( $blocks );
}
add_filter( 'the_content', 'ldemb_classic_checkout_content', 5 );

/**
 * Remove the customer fields. Carts with a physical product keep the standard fields (an address is required).
 */
function ldemb_slim_checkout_fields( $fields ) {
	if ( ! ldemb_checkout_slim() ) {
		return $fields;
	}
	if ( ! ldemb_cart_is_digital() ) {
		return $fields;
	}

	$keep = array();
	if ( 'email' === ldemb_checkout_mode() && isset( $fields['billing']['billing_email'] ) ) {
		$email             = $fields['billing']['billing_email'];
		$email['class']    = array( 'form-row-wide' );
		$email['required'] = true;
		$email['priority'] = 10;
		$keep['billing_email'] = $email;
	}

	$fields['billing']  = $keep;
	$fields['shipping'] = array();
	$fields['account']  = array();
	$fields['order']    = array();
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'ldemb_slim_checkout_fields', 99 );

/**
 * Guests must be able to order, and the login/registration prompts are not shown.
 */
function ldemb_checkout_guest_options( $pre ) {
	return ldemb_checkout_slim() ? 'yes' : $pre;
}
add_filter( 'pre_option_woocommerce_enable_guest_checkout', 'ldemb_checkout_guest_options' );

function ldemb_checkout_no_signup( $pre ) {
	return ldemb_checkout_slim() ? 'no' : $pre;
}
add_filter( 'pre_option_woocommerce_enable_signup_and_login_from_checkout', 'ldemb_checkout_no_signup' );
add_filter( 'pre_option_woocommerce_enable_checkout_login_reminder', 'ldemb_checkout_no_signup' );
add_filter( 'woocommerce_enable_order_notes_field', function ( $show ) {
	return ldemb_checkout_slim() ? false : $show;
} );

/**
 * Body class so the stylesheet can hide the empty customer-details column.
 */
function ldemb_checkout_body_class( $classes ) {
	if ( function_exists( 'is_checkout' ) && is_checkout() && ldemb_checkout_slim() ) {
		$classes[] = 'ldemb-slim-checkout';
		if ( 'none' === ldemb_checkout_mode() && ldemb_cart_is_digital() ) {
			$classes[] = 'ldemb-checkout-nofields';
		}
	}
	return $classes;
}
add_filter( 'body_class', 'ldemb_checkout_body_class' );

/**
 * Downloads are tied to the order's email address: WooCommerce refuses a download link when the order has none
 * ("Invalid download link"). Without asking the customer, the order therefore gets a reserved, undeliverable address
 * (".invalid" never resolves, RFC 2606), and no email is ever sent to it.
 */
define( 'LDEMB_GUEST_DOMAIN', 'guest.invalid' );

function ldemb_is_guest_email( $email ) {
	$suffix = '@' . LDEMB_GUEST_DOMAIN;
	return is_string( $email ) && strlen( $email ) > strlen( $suffix ) && substr( $email, -strlen( $suffix ) ) === $suffix;
}

function ldemb_assign_guest_email( $order ) {
	if ( 'none' !== ldemb_checkout_mode() || $order->get_billing_email() || ! ldemb_cart_is_digital() ) {
		return;
	}
	$order->set_billing_email( 'guest-' . strtolower( wp_generate_password( 14, false, false ) ) . '@' . LDEMB_GUEST_DOMAIN );
}
add_action( 'woocommerce_checkout_create_order', 'ldemb_assign_guest_email' );

/**
 * Never try to mail the placeholder address (it would only bounce).
 */
function ldemb_block_guest_mail( $short_circuit, $atts ) {
	$to = isset( $atts['to'] ) ? ( is_array( $atts['to'] ) ? implode( ',', $atts['to'] ) : (string) $atts['to'] ) : '';
	return false !== stripos( $to, '@' . LDEMB_GUEST_DOMAIN ) ? true : $short_circuit;
}
add_filter( 'pre_wp_mail', 'ldemb_block_guest_mail', 10, 2 );

/**
 * Without an email the order-received page is the only place the files can be fetched: say so.
 */
function ldemb_guest_order_notice( $order_id ) {
	$order = $order_id ? wc_get_order( $order_id ) : false;
	if ( $order && ldemb_is_guest_email( $order->get_billing_email() ) && $order->has_downloadable_item() ) {
		echo '<div class="woocommerce-info ldemb-save-notice">' . esc_html__( 'Please download your files now and bookmark this page. No email address was given, so we cannot email you the download link.', 'ld-embroidery' ) . '</div>';
	}
}
add_action( 'woocommerce_thankyou', 'ldemb_guest_order_notice', 5 );
