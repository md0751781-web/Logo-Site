<?php
/**
 * Logo Site theme functions.
 *
 * @package Logo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LOGO_SITE_VERSION', '1.0.0' );

function logo_site_setup() {
	load_theme_textdomain( 'logo-site', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'logo-site' ),
			'footer'  => __( 'Footer Menu', 'logo-site' ),
		)
	);

	add_image_size( 'logo-site-portfolio', 600, 600, true );
}
add_action( 'after_setup_theme', 'logo_site_setup' );

function logo_site_assets() {
	wp_enqueue_style( 'logo-site-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'logo-site-style', get_stylesheet_uri(), array(), LOGO_SITE_VERSION );
	wp_enqueue_style( 'logo-site-main', get_template_directory_uri() . '/assets/css/main.css', array( 'logo-site-style' ), LOGO_SITE_VERSION );
	wp_enqueue_script( 'logo-site-main', get_template_directory_uri() . '/assets/js/main.js', array(), LOGO_SITE_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'logo_site_assets' );

function logo_site_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer Widgets', 'logo-site' ),
			'id'            => 'footer-1',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'logo_site_widgets_init' );

/**
 * Customizer: editable hero text and contact info.
 */
function logo_site_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'logo_site_home',
		array(
			'title'    => __( 'Home Page', 'logo-site' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'hero_title'    => array( __( 'Hero Title', 'logo-site' ), __( 'Logos that make your brand unforgettable', 'logo-site' ), 'text' ),
		'hero_subtitle' => array( __( 'Hero Subtitle', 'logo-site' ), __( 'Professional logo & brand identity design for businesses of every size.', 'logo-site' ), 'textarea' ),
		'hero_button'   => array( __( 'Hero Button Text', 'logo-site' ), __( 'Get Your Logo', 'logo-site' ), 'text' ),
		'hero_link'     => array( __( 'Hero Button Link', 'logo-site' ), '#contact', 'url' ),
		'contact_email' => array( __( 'Contact Email', 'logo-site' ), get_option( 'admin_email' ), 'email' ),
		'contact_phone' => array( __( 'Contact Phone / WhatsApp', 'logo-site' ), '', 'text' ),
	);

	foreach ( $fields as $id => $field ) {
		$sanitize = 'sanitize_text_field';
		if ( 'textarea' === $field[2] ) {
			$sanitize = 'sanitize_textarea_field';
		} elseif ( 'url' === $field[2] ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'email' === $field[2] ) {
			$sanitize = 'sanitize_email';
		}

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $field[1],
				'sanitize_callback' => $sanitize,
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $field[0],
				'section' => 'logo_site_home',
				'type'    => $field[2],
			)
		);
	}
}
add_action( 'customize_register', 'logo_site_customize_register' );

/**
 * Fallback menu when no menu is assigned.
 */
function logo_site_fallback_menu() {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'logo-site' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/#services' ) ) . '">' . esc_html__( 'Services', 'logo-site' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/#portfolio' ) ) . '">' . esc_html__( 'Portfolio', 'logo-site' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/#contact' ) ) . '">' . esc_html__( 'Contact', 'logo-site' ) . '</a></li>';
	echo '</ul>';
}
