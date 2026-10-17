<?php
/**
 * Homepage products: newest WooCommerce products.
 * Hidden when switched off, when WooCommerce is inactive, or when there are no purchasable products.
 *
 * @package LD_Embroidery
 */

$ldemb_products = ldemb_mod( 'products_show' ) ? ldemb_home_products() : array();
if ( ! $ldemb_products ) {
	// Visitors see nothing; site admins get a note explaining why the section is hidden.
	if ( current_user_can( 'edit_theme_options' ) ) {
		if ( ! ldemb_mod( 'products_show' ) ) {
			$ldemb_hint = __( 'The products section is switched off (Customize → LD: Home Products).', 'ld-embroidery' );
		} elseif ( ! ldemb_wc_active() ) {
			$ldemb_hint = __( 'WooCommerce is not active, so no products can be shown.', 'ld-embroidery' );
		} else {
			$ldemb_hint = __( 'No visible products yet. A product appears here when it is Published, visible in the catalog and has a regular price. If you just imported, check WooCommerce → Products.', 'ld-embroidery' );
		}
		echo '<section class="section home-products"><div class="container"><p class="products-hint">' . esc_html( $ldemb_hint ) . ' <em>' . esc_html__( '(Only site admins see this note.)', 'ld-embroidery' ) . '</em></p></div></section>';
	}
	return;
}

$ldemb_subtitle = ldemb_mod( 'products_subtitle' );
$ldemb_shop     = ldemb_shop_url();
$ldemb_all      = ldemb_mod( 'products_button' );
?>
<section id="products" class="section home-products" aria-labelledby="products-title">
	<?php /* Same layout and the same WooCommerce product card as the shop page (see template-parts/shop/archive.php). */ ?>
	<div class="container container--wide shop-layout woocommerce">
		<?php ldemb_shop_sidebar(); ?>

		<div class="shop-main">
			<header class="shop-head">
				<h2 id="products-title" class="shop-head__title"><?php echo esc_html( ldemb_mod( 'products_title' ) ); ?></h2>
				<?php if ( $ldemb_subtitle ) : ?>
					<p class="shop-head__text"><?php echo esc_html( $ldemb_subtitle ); ?></p>
				<?php endif; ?>
			</header>

			<?php
			woocommerce_product_loop_start();
			foreach ( $ldemb_products as $ldemb_product ) {
				// WooCommerce's card prints the title and link of the *current post* and reads the $product global,
				// exactly as in the shop loop, so both must point at this product.
				$GLOBALS['post']    = get_post( $ldemb_product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
				$GLOBALS['product'] = $ldemb_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
				setup_postdata( $GLOBALS['post'] );
				wc_get_template_part( 'content', 'product' );
			}
			woocommerce_product_loop_end();
			wp_reset_postdata();
			unset( $GLOBALS['product'] );
			?>

			<?php if ( $ldemb_shop && $ldemb_all ) : ?>
				<p class="products__more"><a class="btn btn--outline-green btn--lg" href="<?php echo esc_url( $ldemb_shop ); ?>"><?php echo esc_html( $ldemb_all ); ?><?php echo ldemb_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
			<?php endif; ?>
		</div>
	</div>
</section>
