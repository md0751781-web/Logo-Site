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

/**
 * Products for the homepage section: WC_Product[] (newest first), optionally from one category.
 */
function ldemb_home_products() {
	if ( ! ldemb_wc_active() || ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$args = array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => max( 3, min( 24, absint( ldemb_mod( 'products_count' ) ) ) ),
		'orderby'    => 'date',
		'order'      => 'DESC',
	);

	$category = sanitize_title( ldemb_mod( 'products_category' ) );
	if ( $category ) {
		$args['category'] = array( $category );
	}

	$cache_key = 'ldemb_home_products_' . md5( wp_json_encode( $args ) );
	$ids       = get_transient( $cache_key );
	if ( false === $ids ) {
		$ids = wc_get_products( array_merge( $args, array( 'return' => 'ids' ) ) );
		if ( $ids ) {
			// Never cache "no products": the first visit before an import must not hide the section afterwards.
			set_transient( $cache_key, $ids, 10 * MINUTE_IN_SECONDS );
		}
	}

	$products = array();
	foreach ( (array) $ids as $id ) {
		$product = wc_get_product( $id );
		if ( $product && $product->is_visible() && '' !== $product->get_price() ) {
			$products[] = $product;
		}
	}
	return $products;
}

/**
 * Shop page URL, or '' when no shop page is configured.
 */
function ldemb_shop_url() {
	return ldemb_wc_active() && wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : '';
}

/**
 * Cached product query must refresh when products change.
 */
function ldemb_flush_home_products() {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_ldemb\_home\_products\_%' OR option_name LIKE '\_transient\_timeout\_ldemb\_home\_products\_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}
add_action( 'woocommerce_update_product', 'ldemb_flush_home_products' );
add_action( 'woocommerce_new_product', 'ldemb_flush_home_products' );
add_action( 'woocommerce_delete_product', 'ldemb_flush_home_products' );
add_action( 'customize_save_after', 'ldemb_flush_home_products' );

/**
 * Shop grid: 4 products per row, 24 per page (six full rows).
 */
add_filter( 'loop_shop_columns', function () {
	return 4;
} );
add_filter( 'loop_shop_per_page', function () {
	return 24;
} );

/**
 * Category count: WooCommerce's cached count of visible products (including sub-categories).
 */
function ldemb_category_count( $term ) {
	$count = get_term_meta( $term->term_id, 'product_count_product_cat', true );
	return '' === $count ? (int) $term->count : (int) $count;
}

/**
 * One level of the category list; sub-categories are only expanded inside the active branch.
 */
function ldemb_shop_category_items( $parent, $active_ids, $depth = 0 ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => $parent,
			'hide_empty' => true,
			'pad_counts' => true, // a parent whose products all sit in sub-categories must stay visible.
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			'orderby'    => 'name',
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}

	echo '<ul class="' . ( $depth ? 'shop-cats__sub' : 'shop-cats__list' ) . '">';
	foreach ( $terms as $term ) {
		$is_current = $active_ids && $term->term_id === $active_ids[0];
		$in_branch  = in_array( $term->term_id, $active_ids, true );
		printf(
			'<li class="shop-cats__item%1$s"><a href="%2$s"%3$s><span class="shop-cats__name">%4$s</span> <span class="shop-cats__count">%5$d</span></a>',
			$in_branch ? ' is-active' : '',
			esc_url( get_term_link( $term ) ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( $term->name ),
			(int) ldemb_category_count( $term )
		);
		if ( $in_branch && $depth < 2 ) {
			ldemb_shop_category_items( $term->term_id, $active_ids, $depth + 1 );
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Left sidebar with the product categories (a collapsible panel on phones).
 */
function ldemb_shop_sidebar() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$active_ids = array();
	if ( is_product_category() ) {
		$current = get_queried_object();
		if ( $current && ! is_wp_error( $current ) ) {
			$active_ids = array_merge( array( $current->term_id ), get_ancestors( $current->term_id, 'product_cat', 'taxonomy' ) );
		}
	}

	$shop_url = ldemb_shop_url();
	$total    = wp_count_posts( 'product' );
	$total    = isset( $total->publish ) ? (int) $total->publish : 0;
	?>
	<aside class="shop-sidebar" aria-label="<?php esc_attr_e( 'Product categories', 'ld-embroidery' ); ?>">
		<details class="shop-cats" open>
			<summary class="shop-cats__title"><?php esc_html_e( 'Design Category', 'ld-embroidery' ); ?></summary>
			<?php if ( $shop_url ) : ?>
				<ul class="shop-cats__list shop-cats__list--all">
					<li class="shop-cats__item<?php echo $active_ids || ! is_shop() ? '' : ' is-active'; ?>">
						<a href="<?php echo esc_url( $shop_url ); ?>"<?php echo $active_ids || ! is_shop() ? '' : ' aria-current="page"'; ?>>
							<span class="shop-cats__name"><?php esc_html_e( 'All Designs', 'ld-embroidery' ); ?></span>
							<span class="shop-cats__count"><?php echo (int) $total; ?></span>
						</a>
					</li>
				</ul>
			<?php endif; ?>
			<?php ldemb_shop_category_items( 0, $active_ids ); ?>
		</details>
	</aside>
	<?php
}
