<?php
/**
 * Theme supports, menus and assets.
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ldemb_setup() {
	load_theme_textdomain( 'ld-embroidery', LDEMB_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'ld-embroidery' ),
			'footer'  => __( 'Footer Menu', 'ld-embroidery' ),
		)
	);

	add_image_size( 'ldemb-hero', 1400, 1050, false );
}
add_action( 'after_setup_theme', 'ldemb_setup' );

function ldemb_assets() {
	wp_enqueue_style( 'ldemb-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'ldemb-style', get_stylesheet_uri(), array(), LDEMB_VERSION );
	wp_enqueue_style( 'ldemb-main', LDEMB_URI . '/assets/css/main.css', array( 'ldemb-style' ), LDEMB_VERSION );
	wp_enqueue_script( 'ldemb-main', LDEMB_URI . '/assets/js/main.js', array(), LDEMB_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ldemb_assets' );

/**
 * Preconnect to Google Fonts.
 */
function ldemb_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'ldemb_resource_hints', 10, 2 );
