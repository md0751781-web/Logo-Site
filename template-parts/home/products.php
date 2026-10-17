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
		echo '<section class="section products"><div class="container"><p class="products-hint">' . esc_html( $ldemb_hint ) . ' <em>' . esc_html__( '(Only site admins see this note.)', 'ld-embroidery' ) . '</em></p></div></section>';
	}
	return;
}

$ldemb_subtitle = ldemb_mod( 'products_subtitle' );
$ldemb_cart     = ldemb_mod( 'products_cart' );
$ldemb_shop     = ldemb_shop_url();
$ldemb_all      = ldemb_mod( 'products_button' );
?>
<section id="products" class="section products" aria-labelledby="products-title">
	<div class="container">
		<header class="section-head">
			<h2 id="products-title" class="section-title"><?php echo esc_html( ldemb_mod( 'products_title' ) ); ?></h2>
			<?php if ( $ldemb_subtitle ) : ?>
				<p class="section-subtitle"><?php echo esc_html( $ldemb_subtitle ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="products__grid">
			<?php foreach ( $ldemb_products as $ldemb_i => $ldemb_product ) : ?>
				<li class="product-card">
					<a class="product-card__media" href="<?php echo esc_url( $ldemb_product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
						<?php
						echo wp_kses_post(
							$ldemb_product->get_image(
								'woocommerce_single',
								array(
									'loading'  => $ldemb_i < 3 ? 'eager' : 'lazy',
									'decoding' => 'async',
									'class'    => 'product-card__img',
									'sizes'    => '(max-width: 640px) 50vw, 340px',
								)
							)
						);
						?>
						<?php if ( $ldemb_product->is_downloadable() ) : ?>
							<span class="product-card__tag"><?php echo ldemb_icon( 'cloud' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Instant download', 'ld-embroidery' ); ?></span>
						<?php endif; ?>
					</a>
					<div class="product-card__body">
						<h3 class="product-card__title"><a href="<?php echo esc_url( $ldemb_product->get_permalink() ); ?>"><?php echo esc_html( $ldemb_product->get_name() ); ?></a></h3>
						<div class="product-card__foot">
							<p class="product-card__price"><?php echo wp_kses_post( $ldemb_product->get_price_html() ); ?></p>
							<?php if ( $ldemb_cart && $ldemb_product->is_purchasable() && $ldemb_product->is_in_stock() ) : ?>
								<a class="btn btn--red" href="<?php echo esc_url( $ldemb_product->add_to_cart_url() ); ?>" rel="nofollow">
									<?php echo esc_html( $ldemb_cart ); ?><span class="screen-reader-text">: <?php echo esc_html( $ldemb_product->get_name() ); ?></span>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $ldemb_shop && $ldemb_all ) : ?>
			<p class="products__more"><a class="btn btn--outline-green btn--lg" href="<?php echo esc_url( $ldemb_shop ); ?>"><?php echo esc_html( $ldemb_all ); ?><?php echo ldemb_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		<?php endif; ?>
	</div>
</section>
