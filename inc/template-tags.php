<?php
/**
 * Template helpers: links, logo, menus, hero image.
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL of the first published page matching one of the slugs, or ''.
 */
function ldemb_page_url( $slugs ) {
	foreach ( (array) $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page && 'publish' === $page->post_status ) {
			return get_permalink( $page );
		}
	}
	return '';
}

/**
 * Quote destination. Configuration point: LD: Header → Quote page URL.
 */
function ldemb_quote_url() {
	$url = ldemb_mod( 'quote_url' );
	if ( ! $url ) {
		$url = ldemb_page_url( array( 'order', 'get-quote', 'quote', 'contact' ) );
	}
	if ( ! $url && ldemb_mod( 'footer_email' ) ) {
		$url = 'mailto:' . ldemb_mod( 'footer_email' );
	}
	return $url ? $url : home_url( '/#services' );
}

/**
 * Upload destination. Configuration point: LD: Hero Banner → Upload page URL.
 */
function ldemb_upload_url() {
	$url = ldemb_mod( 'upload_url' );
	if ( ! $url ) {
		$url = ldemb_page_url( array( 'order', 'upload-your-design', 'upload' ) );
	}
	return $url ? $url : ldemb_quote_url();
}

/**
 * Order page URL, optionally preselecting a design category (?category=…).
 */
function ldemb_order_url( $category = '' ) {
	$url = ldemb_page_url( array( 'order', 'upload-your-design', 'upload' ) );
	if ( ! $url ) {
		$url = ldemb_upload_url();
	}
	if ( $category && 0 !== strpos( $url, 'mailto:' ) && false === strpos( $url, '#' ) ) {
		$url = add_query_arg( 'category', rawurlencode( $category ), $url );
	}
	return $url;
}

/**
 * Primary "Get Started" destination: verified product → custom URL → order/quote page → shop.
 */
function ldemb_primary_url() {
	$product_id = absint( ldemb_mod( 'hero_product_id' ) );
	if ( $product_id && ldemb_wc_active() ) {
		$product = wc_get_product( $product_id );
		if ( $product && 'publish' === $product->get_status() ) {
			return get_permalink( $product_id );
		}
	}

	$url = ldemb_mod( 'hero_btn_url' );
	if ( ! $url ) {
		$url = ldemb_page_url( array( 'order', 'get-quote', 'quote' ) );
	}
	if ( ! $url && ldemb_wc_active() && wc_get_page_id( 'shop' ) > 0 ) {
		$url = wc_get_page_permalink( 'shop' );
	}
	return $url ? $url : ldemb_quote_url();
}

function ldemb_offer_url() {
	$url = ldemb_mod( 'offer_btn_url' );
	return $url ? $url : ldemb_primary_url();
}

/**
 * Brand logo: the logo uploaded under Site Identity, otherwise the bundled Vector Emb logo.
 */
function ldemb_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	// assets/images/logo.png is 320 x 246 (transparent). Replace the file, or upload a logo in Site Identity.
	printf(
		'<a class="brand" href="%1$s" rel="home"><img class="brand__logo" src="%2$s" width="320" height="246" alt="%3$s"></a>',
		esc_url( home_url( '/' ) ),
		esc_url( LDEMB_URI . '/assets/images/logo.png' ),
		esc_attr( ldemb_brand_name() )
	);
}

/**
 * Fallback when no Primary menu is assigned: only links that resolve.
 */
function ldemb_fallback_menu() {
	$items = array(
		array( __( 'Home', 'ld-embroidery' ), home_url( '/' ), is_front_page() ),
	);

	$pages = array(
		'services'     => __( 'Services', 'ld-embroidery' ),
		'pricing'      => __( 'Pricing', 'ld-embroidery' ),
		'how-it-works' => __( 'How It Works', 'ld-embroidery' ),
		'portfolio'    => __( 'Portfolio', 'ld-embroidery' ),
		'contact'      => __( 'Contact', 'ld-embroidery' ),
	);

	foreach ( $pages as $slug => $label ) {
		$url = ldemb_page_url( $slug );
		if ( ! $url && 'services' === $slug ) {
			$url = home_url( '/#services' );
		}
		if ( $url ) {
			$items[] = array( $label, $url, is_page( $slug ) );
		}
	}

	echo '<ul id="primary-menu-list" class="menu">';
	foreach ( $items as $item ) {
		printf(
			'<li class="menu-item%3$s"><a href="%1$s"%4$s>%2$s</a></li>',
			esc_url( $item[1] ),
			esc_html( $item[0] ),
			$item[2] ? ' current-menu-item' : '',
			$item[2] ? ' aria-current="page"' : ''
		);
	}
	echo '</ul>';
}

/**
 * Hero slides: array of arrays( html ) — each Customizer image (LD: Hero Banner), falling back to the bundled photos.
 */
function ldemb_hero_slides() {
	$defaults = array(
		__( 'Row of multi-head embroidery machines stitching a floral design', 'ld-embroidery' ),
		__( 'Embroidery machine stitching a 3D logo on a black cap', 'ld-embroidery' ),
		__( 'Embroidery machine stitching a logo on a black t-shirt', 'ld-embroidery' ),
	);

	$slides = array();
	foreach ( $defaults as $i => $alt_default ) {
		$n  = $i + 1;
		$id = absint( ldemb_mod( "hero_slide_{$n}" ) );
		if ( 1 === $n && ! $id ) {
			$id = absint( ldemb_mod( 'hero_image' ) ); // legacy single-image setting.
		}

		$attr = array(
			'class'    => 'hero-slide__img',
			'decoding' => 'async',
			'sizes'    => '(max-width: 860px) 100vw, 56vw',
		);
		$attr['loading']       = 1 === $n ? 'eager' : 'lazy';
		$attr['fetchpriority'] = 1 === $n ? 'high' : 'low';

		if ( $id && wp_attachment_is_image( $id ) ) {
			$alt         = get_post_meta( $id, '_wp_attachment_image_alt', true );
			$attr['alt'] = $alt ? $alt : $alt_default;
			$html        = wp_get_attachment_image( $id, 'ldemb-hero', false, $attr );
		} else {
			$base = LDEMB_URI . '/assets/images/hero-' . $n;
			$html = sprintf(
				'<img class="%1$s" src="%2$s" srcset="%2$s 1600w, %3$s 800w" sizes="%4$s" width="1600" height="900" alt="%5$s" loading="%6$s" fetchpriority="%7$s" decoding="async">',
				esc_attr( $attr['class'] ),
				esc_url( $base . '-1600.webp' ),
				esc_url( $base . '-800.webp' ),
				esc_attr( $attr['sizes'] ),
				esc_attr( $alt_default ),
				esc_attr( $attr['loading'] ),
				esc_attr( $attr['fetchpriority'] )
			);
		}
		if ( $html ) {
			$slides[] = $html;
		}
	}
	return $slides;
}

/**
 * Favicon fallback: the bundled symbol, only when no Site Icon is set in Customize.
 */
function ldemb_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" href="%s" type="image/png">' . "\n", esc_url( LDEMB_URI . '/assets/images/favicon.png' ) );
}
add_action( 'wp_head', 'ldemb_favicon', 2 );

/**
 * Brand name as plain text (footer copyright, logo alt text).
 */
function ldemb_brand_name() {
	return trim( str_replace( '|', '', ldemb_mod( 'brand_name' ) ) );
}
