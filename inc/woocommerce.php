<?php
/**
 * WooCommerce integration: cart count, account link, live cart fragments.
 *
 * Every function here degrades gracefully when WooCommerce is inactive.
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ldemb_wc_active() {
	return class_exists( 'WooCommerce' ) && function_exists( 'WC' );
}

function ldemb_cart_count() {
	if ( ! ldemb_wc_active() || ! WC()->cart ) {
		return 0;
	}
	return (int) WC()->cart->get_cart_contents_count();
}

function ldemb_cart_url() {
	return ldemb_wc_active() ? wc_get_cart_url() : '';
}

/**
 * My Account URL, or empty string when the page is not configured.
 */
function ldemb_account_url() {
	if ( ! ldemb_wc_active() || wc_get_page_id( 'myaccount' ) <= 0 ) {
		return '';
	}
	return wc_get_page_permalink( 'myaccount' );
}

/**
 * Cart count badge. Replaced by WooCommerce cart fragments after AJAX add-to-cart.
 */
function ldemb_cart_count_html() {
	$count = ldemb_cart_count();
	return sprintf(
		'<span class="cart-count" data-count="%1$d"><span class="cart-count__num" aria-hidden="true">%1$d</span><span class="screen-reader-text">%2$s</span></span>',
		$count,
		/* translators: %d: number of items in cart */
		esc_html( sprintf( _n( '%d item in cart', '%d items in cart', $count, 'ld-embroidery' ), $count ) )
	);
}

function ldemb_cart_fragments( $fragments ) {
	$fragments['span.cart-count'] = ldemb_cart_count_html();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'ldemb_cart_fragments' );

/**
 * WooCommerce no longer loads cart fragments on every page; the header count needs it.
 */
function ldemb_enqueue_cart_fragments() {
	if ( wp_script_is( 'wc-cart-fragments', 'registered' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
}
add_action( 'wp_enqueue_scripts', 'ldemb_enqueue_cart_fragments', 20 );

/**
 * Enabled payment gateways for the footer: array of array( 'title', 'icon_html' ).
 */
function ldemb_payment_methods() {
	if ( ! ldemb_wc_active() || ! WC()->payment_gateways() ) {
		return array();
	}

	$methods = array();
	foreach ( WC()->payment_gateways()->payment_gateways() as $gateway ) {
		if ( 'yes' !== $gateway->enabled ) {
			continue;
		}
		$methods[] = array(
			'title' => wp_strip_all_tags( $gateway->get_title() ),
			'icon'  => $gateway->get_icon(),
		);
	}
	return $methods;
}

/**
 * Remove WooCommerce's default wrappers in favour of the theme's container (woocommerce.php).
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
