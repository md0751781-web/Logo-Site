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
 * Brand logo: uploaded custom logo, or a text + SVG fallback.
 */
function ldemb_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	$name  = get_bloginfo( 'name' );
	$parts = explode( ' ', $name, 2 );
	?>
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<svg class="brand__mark" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
			<circle cx="22" cy="26" r="17" fill="none" stroke="#B8202B" stroke-width="4"/>
			<circle cx="22" cy="26" r="11.5" fill="none" stroke="#B8202B" stroke-width="1.6" stroke-dasharray="2.4 2.4"/>
			<path d="M8 40 41 7" stroke="#101729" stroke-width="3" stroke-linecap="round"/>
			<ellipse cx="38.5" cy="9.5" rx="1.6" ry="3.6" transform="rotate(45 38.5 9.5)" fill="#fff" stroke="#101729" stroke-width="1.4"/>
			<path d="M39 8c6 3 6 12-2 15s-17 2-20 9" fill="none" stroke="#278D48" stroke-width="2.4" stroke-linecap="round"/>
		</svg>
		<span class="brand__text">
			<span class="brand__first"><?php echo esc_html( $parts[0] ); ?></span>
			<?php if ( isset( $parts[1] ) ) : ?>
				<span class="brand__rest"><?php echo esc_html( $parts[1] ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<?php
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
 * Hero image: uploaded attachment, or the bundled placeholder illustration.
 */
function ldemb_hero_image() {
	$id = absint( ldemb_mod( 'hero_image' ) );
	if ( $id && wp_attachment_is_image( $id ) ) {
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		echo wp_get_attachment_image(
			$id,
			'ldemb-hero',
			false,
			array(
				'class'         => 'hero__img',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decoding'      => 'async',
				'sizes'         => '(max-width: 900px) 100vw, 55vw',
				'alt'           => $alt ? $alt : __( 'Industrial embroidery machine stitching a design', 'ld-embroidery' ),
			)
		);
		return;
	}

	// Placeholder: replace via Appearance → Customize → LD: Hero Banner → Hero image.
	printf(
		'<img class="hero__img hero__img--placeholder" src="%s" width="1200" height="900" alt="%s" fetchpriority="high" decoding="async">',
		esc_url( LDEMB_URI . '/assets/images/hero-placeholder.svg' ),
		esc_attr__( 'Illustration of a multi-needle embroidery machine stitching a floral design on blue fabric', 'ld-embroidery' )
	);
}
