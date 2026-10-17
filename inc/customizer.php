<?php
/**
 * Customizer: every piece of homepage content lives here, so nothing
 * business-specific has to be edited in the PHP templates.
 *
 * Claims that the business owner must confirm (trust badges, launch offer,
 * file formats) are switched OFF by default.
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings registry: section => array( title, fields ).
 * Field: id => array( label, default, type, description ).
 */
function ldemb_settings() {
	static $settings = null;
	if ( null !== $settings ) {
		return $settings;
	}

	$settings = array(
		'ldemb_header'   => array(
			'title'  => __( 'LD: Header', 'ld-embroidery' ),
			'fields' => array(
				'header_badge_show' => array( __( 'Show turnaround badge', 'ld-embroidery' ), true, 'checkbox' ),
				'header_badge_text' => array( __( 'Turnaround badge text', 'ld-embroidery' ), __( '24h Turnaround', 'ld-embroidery' ), 'text' ),
				'header_cta_label'  => array( __( 'Header button text', 'ld-embroidery' ), __( 'Get Quote', 'ld-embroidery' ), 'text' ),
				'quote_url'         => array( __( 'Quote page URL', 'ld-embroidery' ), '', 'url', __( 'Used by "Get Quote". Leave empty to use a page with the slug "get-quote", "quote" or "contact" if one exists.', 'ld-embroidery' ) ),
			),
		),
		'ldemb_hero'     => array(
			'title'  => __( 'LD: Hero Banner', 'ld-embroidery' ),
			'fields' => array(
				'hero_line1'       => array( __( 'Heading line 1', 'ld-embroidery' ), __( 'Professional Embroidery', 'ld-embroidery' ), 'text' ),
				'hero_line2'       => array( __( 'Heading line 2', 'ld-embroidery' ), __( 'Digitizing', 'ld-embroidery' ), 'text' ),
				'hero_price'       => array( __( 'Price heading (red)', 'ld-embroidery' ), __( 'Just $5', 'ld-embroidery' ), 'text', __( 'Leave empty to hide.', 'ld-embroidery' ) ),
				'hero_text'        => array( __( 'Supporting paragraph', 'ld-embroidery' ), __( 'Turn your design into a perfect stitch-ready embroidery file in 12–24 hours. Fast, accurate, and affordable digitizing for all garment types.', 'ld-embroidery' ), 'textarea' ),
				'hero_btn_small'   => array( __( 'Primary button: small label', 'ld-embroidery' ), __( '$5 Digitizing', 'ld-embroidery' ), 'text' ),
				'hero_btn_label'   => array( __( 'Primary button: main label', 'ld-embroidery' ), __( 'Get Started Now', 'ld-embroidery' ), 'text' ),
				'hero_product_id'  => array( __( 'Primary button: WooCommerce product ID', 'ld-embroidery' ), 0, 'number', __( 'Optional. If a published WooCommerce product with this ID exists, the button links to it (nothing is added to the cart automatically).', 'ld-embroidery' ) ),
				'hero_btn_url'     => array( __( 'Primary button: URL', 'ld-embroidery' ), '', 'url', __( 'Used when no product ID is set. Empty = order/quote page or the WooCommerce shop.', 'ld-embroidery' ) ),
				'hero_btn2_label'  => array( __( 'Secondary button text', 'ld-embroidery' ), __( 'Upload Your Design', 'ld-embroidery' ), 'text' ),
				'upload_url'       => array( __( 'Upload page URL', 'ld-embroidery' ), '', 'url', __( 'Empty = page with slug "upload-your-design", "upload" or the quote page.', 'ld-embroidery' ) ),
				'hero_image'       => array( __( 'Hero image', 'ld-embroidery' ), 0, 'media', __( 'Landscape photo of an embroidery machine, at least 1400px wide. WebP recommended.', 'ld-embroidery' ) ),
			),
		),
		'ldemb_trust'    => array(
			'title'  => __( 'LD: Trust Badges', 'ld-embroidery' ),
			'fields' => array(
				'trust_show'    => array( __( 'Show trust badges (only after the owner has confirmed every claim)', 'ld-embroidery' ), false, 'checkbox' ),
				'trust_1'       => array( __( 'Badge 1', 'ld-embroidery' ), __( '100% Satisfaction Guaranteed', 'ld-embroidery' ), 'text' ),
				'trust_2'       => array( __( 'Badge 2', 'ld-embroidery' ), __( '12–24h Fast Delivery', 'ld-embroidery' ), 'text' ),
				'trust_3'       => array( __( 'Badge 3', 'ld-embroidery' ), __( 'Secure & Confidential', 'ld-embroidery' ), 'text' ),
				'trust_4'       => array( __( 'Badge 4 (with stars)', 'ld-embroidery' ), __( '10,000+ Happy Customers', 'ld-embroidery' ), 'text' ),
				'trust_tagline' => array( __( 'Line below badges', 'ld-embroidery' ), __( 'Trusted by small businesses, apparel brands & embroiderers worldwide', 'ld-embroidery' ), 'text', __( 'Leave a badge or this line empty to hide it.', 'ld-embroidery' ) ),
			),
		),
		'ldemb_services' => array(
			'title'  => __( 'LD: Services', 'ld-embroidery' ),
			'fields' => array(
				'services_title'    => array( __( 'Heading', 'ld-embroidery' ), __( 'Our Embroidery Digitizing Services', 'ld-embroidery' ), 'text' ),
				'services_subtitle' => array( __( 'Supporting text', 'ld-embroidery' ), __( 'Expert digitizing for every apparel type — optimized for clean, crisp stitches', 'ld-embroidery' ), 'text' ),
				'service_1_title'   => array( __( 'Card 1 title (cap icon)', 'ld-embroidery' ), __( 'Cap Embroidery', 'ld-embroidery' ), 'text' ),
				'service_1_text'    => array( __( 'Card 1 text', 'ld-embroidery' ), __( 'Front, side & 3D puff caps. Clean sharp details for logos & text.', 'ld-embroidery' ), 'textarea' ),
				'service_2_title'   => array( __( 'Card 2 title (3D puff icon)', 'ld-embroidery' ), __( '3D Puff', 'ld-embroidery' ), 'text' ),
				'service_2_text'    => array( __( 'Card 2 text', 'ld-embroidery' ), __( 'Raised textured embroidery for bold, premium look on hats & jackets.', 'ld-embroidery' ), 'textarea' ),
				'service_3_title'   => array( __( 'Card 3 title (T-shirt icon)', 'ld-embroidery' ), __( 'T-shirt', 'ld-embroidery' ), 'text' ),
				'service_3_text'    => array( __( 'Card 3 text', 'ld-embroidery' ), __( 'Perfect digitizing for cotton tees. Clean, soft, wash-ready stitch files.', 'ld-embroidery' ), 'textarea' ),
				'service_4_title'   => array( __( 'Card 4 title (jacket icon)', 'ld-embroidery' ), __( 'Jacket Back', 'ld-embroidery' ), 'text' ),
				'service_4_text'    => array( __( 'Card 4 text', 'ld-embroidery' ), __( 'Large back embroidery for jackets, hoodies & workwear. Detailed precision.', 'ld-embroidery' ), 'textarea' ),
			),
		),
		'ldemb_products' => array(
			'title'  => __( 'LD: Home Products', 'ld-embroidery' ),
			'fields' => array(
				'products_show'     => array( __( 'Show products on the homepage', 'ld-embroidery' ), true, 'checkbox', __( 'Real WooCommerce products. The section hides itself while there are none.', 'ld-embroidery' ) ),
				'products_title'    => array( __( 'Heading', 'ld-embroidery' ), __( 'Latest Embroidery Designs', 'ld-embroidery' ), 'text' ),
				'products_subtitle' => array( __( 'Supporting text', 'ld-embroidery' ), __( 'Ready-to-stitch embroidery files, available to download right after purchase.', 'ld-embroidery' ), 'text' ),
				'products_count'    => array( __( 'Number of products (4–24)', 'ld-embroidery' ), 10, 'number' ),
				'products_category' => array( __( 'Only from category (slug, optional)', 'ld-embroidery' ), '', 'text', __( 'e.g. best-3dpuff-embroidery-logo. Empty = newest products from all categories.', 'ld-embroidery' ) ),
				'products_button'   => array( __( '"View all" button text', 'ld-embroidery' ), __( 'View All Designs', 'ld-embroidery' ), 'text' ),
				'products_cart'     => array( __( 'Add to cart button text', 'ld-embroidery' ), __( 'Add to Cart', 'ld-embroidery' ), 'text' ),
			),
		),
		'ldemb_offer'    => array(
			'title'  => __( 'LD: Launch Offer', 'ld-embroidery' ),
			'fields' => array(
				'offer_show'      => array( __( 'Show offer banner (only after the owner has approved the offer)', 'ld-embroidery' ), false, 'checkbox' ),
				'offer_title'     => array( __( 'Offer label', 'ld-embroidery' ), __( 'Special Launch Offer:', 'ld-embroidery' ), 'text' ),
				'offer_text'      => array( __( 'Offer text', 'ld-embroidery' ), __( 'Only $5 per design • Limited time • Get your first file for $5 today!', 'ld-embroidery' ), 'text' ),
				'offer_btn_label' => array( __( 'Offer button text', 'ld-embroidery' ), __( 'Claim $5 Offer →', 'ld-embroidery' ), 'text' ),
				'offer_btn_url'   => array( __( 'Offer button URL', 'ld-embroidery' ), '', 'url', __( 'Empty = same destination as the hero primary button.', 'ld-embroidery' ) ),
				'formats_show'    => array( __( 'Show supported file formats', 'ld-embroidery' ), false, 'checkbox' ),
				'formats_text'    => array( __( 'File formats (list only formats you actually deliver)', 'ld-embroidery' ), 'DST · PES · JEF · EXP · VP3 · EMB', 'text' ),
			),
		),
		'ldemb_page_order'     => array(
			'title'  => __( 'LD Page: Order / Upload', 'ld-embroidery' ),
			'fields' => array(
				'order_upload_title'  => array( __( 'Upload column heading', 'ld-embroidery' ), __( 'Upload Artwork', 'ld-embroidery' ), 'text' ),
				'order_details_title' => array( __( 'Form column heading', 'ld-embroidery' ), __( 'Order Details', 'ld-embroidery' ), 'text' ),
				'order_categories'    => array( __( 'Design categories (one per line)', 'ld-embroidery' ), "Cap\n3D Puff\nT-shirt\nJacket Back\nHoodie\nVector Art", 'textarea' ),
				'order_types'         => array( __( 'Embroidery types (one per line)', 'ld-embroidery' ), "Flat\n3D Puff", 'textarea' ),
				'order_button'        => array( __( 'Submit button text', 'ld-embroidery' ), __( 'Place Order', 'ld-embroidery' ), 'text' ),
				'order_success'       => array( __( 'Message after a successful submission', 'ld-embroidery' ), __( 'Thank you! Your artwork has been received. We will review it and contact you with the next steps.', 'ld-embroidery' ), 'textarea' ),
				'notify_email'        => array( __( 'Send new orders & messages to', 'ld-embroidery' ), '', 'email', __( 'Empty = the WordPress admin email.', 'ld-embroidery' ) ),
			),
		),
		'ldemb_page_services'  => array(
			'title'  => __( 'LD Page: Services', 'ld-embroidery' ),
			'fields' => array(
				'svc_badge'      => array( __( 'Small badge above the title', 'ld-embroidery' ), __( 'Embroidery & Software Services', 'ld-embroidery' ), 'text' ),
				'svc_intro'      => array( __( 'Intro text', 'ld-embroidery' ), __( 'Professional embroidery solutions and software expertise — custom designs, digitizing, vector work and file services.', 'ld-embroidery' ), 'textarea' ),
				'svc_g1_title'   => array( __( 'Group 1 title', 'ld-embroidery' ), __( 'Apparel Services', 'ld-embroidery' ), 'text' ),
				'svc_g1_text'    => array( __( 'Group 1 text', 'ld-embroidery' ), __( 'Custom embroidery digitizing for apparel — caps, garments, jackets and more.', 'ld-embroidery' ), 'text' ),
				'svc_g2_title'   => array( __( 'Group 2 title', 'ld-embroidery' ), __( 'Software Related Services', 'ld-embroidery' ), 'text' ),
				'svc_g2_text'    => array( __( 'Group 2 text', 'ld-embroidery' ), __( 'Professional software services — digitizing, conversion, editing and file optimization.', 'ld-embroidery' ), 'text' ),
				'svc_cta_text'   => array( __( 'Bottom banner text', 'ld-embroidery' ), __( 'Need a custom service? We can help with bulk and special orders.', 'ld-embroidery' ), 'text' ),
				'svc_cta_label'  => array( __( 'Bottom banner button', 'ld-embroidery' ), __( 'Contact Us Today', 'ld-embroidery' ), 'text' ),
				'svc_details'    => array( __( '"View Details" button text', 'ld-embroidery' ), __( 'View Details', 'ld-embroidery' ), 'text' ),
			),
		),
		'ldemb_page_portfolio' => array(
			'title'  => __( 'LD Page: Portfolio', 'ld-embroidery' ),
			'fields' => array(
				'pf_intro'    => array( __( 'Intro text', 'ld-embroidery' ), __( 'A showcase of our embroidered logo work across apparel — clean, precise, premium.', 'ld-embroidery' ), 'textarea' ),
				'pf_all'      => array( __( '"All" filter label', 'ld-embroidery' ), __( 'All Work', 'ld-embroidery' ), 'text' ),
				'pf_order'    => array( __( 'Quick View order button', 'ld-embroidery' ), __( 'Order a Similar Design', 'ld-embroidery' ), 'text' ),
			),
		),
		'ldemb_page_contact'   => array(
			'title'  => __( 'LD Page: Contact', 'ld-embroidery' ),
			'fields' => array(
				'ct_intro'         => array( __( 'Intro text', 'ld-embroidery' ), __( 'We would love to hear from you. Reach out with any questions about your embroidery digitizing order.', 'ld-embroidery' ), 'textarea' ),
				'ct_phone'         => array( __( 'Phone number', 'ld-embroidery' ), '', 'text', __( 'Each contact card is hidden while its main field is empty.', 'ld-embroidery' ) ),
				'ct_phone_note'    => array( __( 'Phone note (e.g. hours)', 'ld-embroidery' ), '', 'text' ),
				'ct_whatsapp'      => array( __( 'WhatsApp number (with country code)', 'ld-embroidery' ), '', 'text' ),
				'ct_whatsapp_note' => array( __( 'WhatsApp note', 'ld-embroidery' ), '', 'text' ),
				'ct_email'         => array( __( 'Contact email', 'ld-embroidery' ), '', 'email', __( 'Empty = the footer business email.', 'ld-embroidery' ) ),
				'ct_email_note'    => array( __( 'Email note', 'ld-embroidery' ), '', 'text' ),
				'ct_address'       => array( __( 'Location / address', 'ld-embroidery' ), '', 'text' ),
				'ct_address_note'  => array( __( 'Location note', 'ld-embroidery' ), '', 'text' ),
				'ct_hours'         => array( __( 'Opening hours (one per line, "Day: time")', 'ld-embroidery' ), '', 'textarea', __( 'Empty = the hours box is hidden.', 'ld-embroidery' ) ),
				'ct_instagram'     => array( __( 'Instagram URL', 'ld-embroidery' ), '', 'url' ),
				'ct_facebook'      => array( __( 'Facebook URL', 'ld-embroidery' ), '', 'url' ),
				'ct_pinterest'     => array( __( 'Pinterest URL', 'ld-embroidery' ), '', 'url' ),
				'ct_button'        => array( __( 'Form button text', 'ld-embroidery' ), __( 'Send Message', 'ld-embroidery' ), 'text' ),
				'ct_success'       => array( __( 'Message after sending', 'ld-embroidery' ), __( 'Thank you! Your message has been received. We will get back to you soon.', 'ld-embroidery' ), 'textarea' ),
			),
		),
		'ldemb_footer'   => array(
			'title'  => __( 'LD: Footer', 'ld-embroidery' ),
			'fields' => array(
				'footer_url'           => array( __( 'Website address to display', 'ld-embroidery' ), '', 'url', __( 'Empty = hidden.', 'ld-embroidery' ) ),
				'footer_email'         => array( __( 'Business email', 'ld-embroidery' ), '', 'email', __( 'Empty = hidden.', 'ld-embroidery' ) ),
				'footer_support'       => array( __( 'Support information', 'ld-embroidery' ), '', 'text', __( 'e.g. your real support hours. Empty = hidden.', 'ld-embroidery' ) ),
				'footer_payments_show' => array( __( 'Show active WooCommerce payment methods', 'ld-embroidery' ), true, 'checkbox', __( 'Only gateways that are enabled in WooCommerce → Settings → Payments are listed.', 'ld-embroidery' ) ),
			),
		),
	);

	return $settings;
}

/**
 * Get a theme mod with its registered default.
 */
function ldemb_mod( $key ) {
	foreach ( ldemb_settings() as $section ) {
		if ( isset( $section['fields'][ $key ] ) ) {
			return get_theme_mod( $key, $section['fields'][ $key ][1] );
		}
	}
	return get_theme_mod( $key );
}

function ldemb_sanitize_checkbox( $value ) {
	return (bool) $value;
}

function ldemb_customize_register( $wp_customize ) {
	$sanitizers = array(
		'text'     => 'sanitize_text_field',
		'textarea' => 'sanitize_textarea_field',
		'url'      => 'esc_url_raw',
		'email'    => 'sanitize_email',
		'number'   => 'absint',
		'media'    => 'absint',
		'checkbox' => 'ldemb_sanitize_checkbox',
	);

	$priority = 30;
	foreach ( ldemb_settings() as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'    => $section['title'],
				'priority' => $priority++,
			)
		);

		foreach ( $section['fields'] as $id => $field ) {
			$type = $field[2];

			$wp_customize->add_setting(
				$id,
				array(
					'default'           => $field[1],
					'sanitize_callback' => $sanitizers[ $type ],
				)
			);

			$args = array(
				'label'       => $field[0],
				'section'     => $section_id,
				'description' => isset( $field[3] ) ? $field[3] : '',
			);

			if ( 'media' === $type ) {
				$args['mime_type'] = 'image';
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, $args ) );
			} else {
				$args['type'] = $type;
				$wp_customize->add_control( $id, $args );
			}
		}
	}
}
add_action( 'customize_register', 'ldemb_customize_register' );
