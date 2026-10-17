<?php
/**
 * Content types managed in wp-admin:
 * - Portfolio items (ldemb_portfolio) with categories (ldemb_portfolio_cat)
 * - Services (ldemb_service) grouped into Apparel / Software
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ldemb_register_content_types() {
	register_post_type(
		'ldemb_portfolio',
		array(
			'labels'        => array(
				'name'          => __( 'Portfolio', 'ld-embroidery' ),
				'singular_name' => __( 'Portfolio Item', 'ld-embroidery' ),
				'add_new_item'  => __( 'Add Portfolio Item', 'ld-embroidery' ),
				'edit_item'     => __( 'Edit Portfolio Item', 'ld-embroidery' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'rewrite'       => array( 'slug' => 'work' ),
			'menu_icon'     => 'dashicons-format-gallery',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	register_taxonomy(
		'ldemb_portfolio_cat',
		'ldemb_portfolio',
		array(
			'labels'            => array(
				'name'          => __( 'Portfolio Categories', 'ld-embroidery' ),
				'singular_name' => __( 'Portfolio Category', 'ld-embroidery' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'rewrite'           => array( 'slug' => 'work-category' ),
			'show_admin_column' => true,
			'show_in_rest'      => true,
		)
	);

	register_post_type(
		'ldemb_service',
		array(
			'labels'        => array(
				'name'          => __( 'Services', 'ld-embroidery' ),
				'singular_name' => __( 'Service', 'ld-embroidery' ),
				'add_new_item'  => __( 'Add Service', 'ld-embroidery' ),
				'edit_item'     => __( 'Edit Service', 'ld-embroidery' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'rewrite'       => array( 'slug' => 'service' ),
			'menu_icon'     => 'dashicons-admin-tools',
			'menu_position' => 22,
			'supports'      => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);
}
add_action( 'init', 'ldemb_register_content_types' );

/**
 * Meta field definitions per post type: key => array( label, type, options ).
 */
function ldemb_meta_fields( $post_type ) {
	$icons = array(
		'cap'      => __( 'Cap', 'ld-embroidery' ),
		'puff'     => __( '3D Puff', 'ld-embroidery' ),
		'tshirt'   => __( 'T-shirt', 'ld-embroidery' ),
		'jacket'   => __( 'Jacket', 'ld-embroidery' ),
		'hoodie'   => __( 'Hoodie', 'ld-embroidery' ),
		'vector'   => __( 'Vector', 'ld-embroidery' ),
		'digitize' => __( 'Digitizing software', 'ld-embroidery' ),
		'tracing'  => __( 'Vector tracing', 'ld-embroidery' ),
		'file'     => __( 'File conversion', 'ld-embroidery' ),
		'pencil'   => __( 'Editing', 'ld-embroidery' ),
	);

	$fields = array(
		'ldemb_portfolio' => array(
			'_ldemb_subtitle' => array( __( 'Subtitle (e.g. "Cap Logo • Embroidered")', 'ld-embroidery' ), 'text' ),
			'_ldemb_price'    => array( __( 'Price (optional, e.g. "$28.00")', 'ld-embroidery' ), 'text' ),
			'_ldemb_badge'    => array( __( 'Corner badge (optional, e.g. "NEW")', 'ld-embroidery' ), 'text' ),
		),
		'ldemb_service'   => array(
			'_ldemb_group' => array(
				__( 'Group', 'ld-embroidery' ),
				'select',
				array(
					'apparel'  => __( 'Apparel Services', 'ld-embroidery' ),
					'software' => __( 'Software Related Services', 'ld-embroidery' ),
				),
			),
			'_ldemb_icon'  => array( __( 'Icon', 'ld-embroidery' ), 'select', $icons ),
			'_ldemb_price' => array( __( 'Price label (optional, e.g. "Starting at $5")', 'ld-embroidery' ), 'text' ),
			'_ldemb_link'  => array( __( '"View Details" link (optional)', 'ld-embroidery' ), 'url' ),
		),
	);

	return isset( $fields[ $post_type ] ) ? $fields[ $post_type ] : array();
}

function ldemb_add_meta_boxes() {
	foreach ( array( 'ldemb_portfolio', 'ldemb_service' ) as $post_type ) {
		add_meta_box( 'ldemb_details', __( 'Display details', 'ld-embroidery' ), 'ldemb_render_meta_box', $post_type, 'side' );
	}
}
add_action( 'add_meta_boxes', 'ldemb_add_meta_boxes' );

function ldemb_render_meta_box( $post ) {
	wp_nonce_field( 'ldemb_meta', 'ldemb_meta_nonce' );
	foreach ( ldemb_meta_fields( $post->post_type ) as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $field[0] ) . '</strong></label><br>';
		if ( 'select' === $field[1] ) {
			echo '<select class="widefat" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">';
			foreach ( $field[2] as $opt => $label ) {
				echo '<option value="' . esc_attr( $opt ) . '"' . selected( $value, $opt, false ) . '>' . esc_html( $label ) . '</option>';
			}
			echo '</select>';
		} else {
			printf(
				'<input class="widefat" type="%1$s" id="%2$s" name="%2$s" value="%3$s">',
				'url' === $field[1] ? 'url' : 'text',
				esc_attr( $key ),
				esc_attr( $value )
			);
		}
		echo '</p>';
	}
	if ( 'ldemb_portfolio' === $post->post_type ) {
		echo '<p class="description">' . esc_html__( 'Set the photo as the Featured image and choose a Portfolio Category.', 'ld-embroidery' ) . '</p>';
	}
}

function ldemb_save_meta( $post_id, $post ) {
	if ( ! isset( $_POST['ldemb_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ldemb_meta_nonce'] ), 'ldemb_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( ldemb_meta_fields( $post->post_type ) as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( 'url' === $field[1] ) {
			$value = esc_url_raw( $raw );
		} elseif ( 'select' === $field[1] ) {
			$value = array_key_exists( $raw, $field[2] ) ? $raw : '';
		} else {
			$value = sanitize_text_field( $raw );
		}
		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post_ldemb_portfolio', 'ldemb_save_meta', 10, 2 );
add_action( 'save_post_ldemb_service', 'ldemb_save_meta', 10, 2 );

/**
 * Starter services shown until real Service posts are published.
 */
function ldemb_default_services() {
	$p = __( 'Starting at $5', 'ld-embroidery' );
	return array(
		array( 'apparel', 'cap', __( 'Cap Embroidery', 'ld-embroidery' ), __( 'Digitized cap designs with precise detailing and durable stitching.', 'ld-embroidery' ), $p ),
		array( 'apparel', 'puff', __( '3D Puff', 'ld-embroidery' ), __( 'Raised 3D puff digitizing for bold, dimensional logo designs.', 'ld-embroidery' ), $p ),
		array( 'apparel', 'tshirt', __( 'T-Shirt Logo', 'ld-embroidery' ), __( 'Clean logo files for t-shirts — perfect for brands and teams.', 'ld-embroidery' ), $p ),
		array( 'apparel', 'jacket', __( 'Jacket Back', 'ld-embroidery' ), __( 'Large back designs for jackets with premium stitch quality.', 'ld-embroidery' ), $p ),
		array( 'apparel', 'hoodie', __( 'Hoodie Logo', 'ld-embroidery' ), __( 'Embroidery-ready logos for hoodies — clean, lasting, professional.', 'ld-embroidery' ), $p ),
		array( 'apparel', 'vector', __( 'Vector Art', 'ld-embroidery' ), __( 'Custom vector tracing and clean artwork prep for embroidery.', 'ld-embroidery' ), $p ),
		array( 'software', 'digitize', __( 'Wilcom Digitizing', 'ld-embroidery' ), __( 'Professional EMB and DST production files.', 'ld-embroidery' ), $p ),
		array( 'software', 'tracing', __( 'Vector Tracing', 'ld-embroidery' ), __( 'AI, EPS, SVG and PDF artwork for print and embroidery.', 'ld-embroidery' ), $p ),
		array( 'software', 'file', __( 'File Format Conversion', 'ld-embroidery' ), __( 'Convert between embroidery machine formats.', 'ld-embroidery' ), $p ),
		array( 'software', 'pencil', __( 'Editing & Fixing', 'ld-embroidery' ), __( 'Resize, colour changes and stitch corrections.', 'ld-embroidery' ), $p ),
	);
}

/**
 * Services for the Services page, grouped: array( 'apparel' => [...], 'software' => [...] ).
 * Each item: array( icon, title, text, price, url ).
 */
function ldemb_get_services() {
	$groups = array(
		'apparel'  => array(),
		'software' => array(),
	);

	$posts = get_posts(
		array(
			'post_type'      => 'ldemb_service',
			'posts_per_page' => 50,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
		)
	);

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$group = get_post_meta( $post->ID, '_ldemb_group', true );
			$link  = get_post_meta( $post->ID, '_ldemb_link', true );
			if ( ! $link ) {
				$link = $post->post_content ? get_permalink( $post ) : ldemb_order_url( $post->post_title );
			}
			$groups[ 'software' === $group ? 'software' : 'apparel' ][] = array(
				get_post_meta( $post->ID, '_ldemb_icon', true ),
				get_the_title( $post ),
				has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 18 ),
				get_post_meta( $post->ID, '_ldemb_price', true ),
				$link,
			);
		}
		return $groups;
	}

	foreach ( ldemb_default_services() as $s ) {
		$groups[ $s[0] ][] = array( $s[1], $s[2], $s[3], $s[4], ldemb_order_url( $s[2] ) );
	}
	return $groups;
}

/**
 * Make the new content type URLs work right after activation.
 */
function ldemb_flush_rewrites() {
	ldemb_register_content_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'ldemb_flush_rewrites' );
