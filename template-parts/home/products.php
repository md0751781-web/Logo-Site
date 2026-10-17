<?php
/**
 * Homepage products: newest WooCommerce products.
 * Hidden when switched off, when WooCommerce is inactive, or when there are no purchasable products.
 *
 * @package LD_Embroidery
 */

if ( ! ldemb_mod( 'products_show' ) ) {
	return;
}

$ldemb_products = ldemb_home_products();
if ( ! $ldemb_products ) {
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
								'woocommerce_thumbnail',
								array(
									'loading'  => $ldemb_i < 4 ? 'eager' : 'lazy',
									'decoding' => 'async',
									'class'    => 'product-card__img',
								)
							)
						);
						?>
					</a>
					<div class="product-card__body">
						<h3 class="product-card__title"><a href="<?php echo esc_url( $ldemb_product->get_permalink() ); ?>"><?php echo esc_html( $ldemb_product->get_name() ); ?></a></h3>
						<p class="product-card__price"><?php echo wp_kses_post( $ldemb_product->get_price_html() ); ?></p>
						<?php if ( $ldemb_cart && $ldemb_product->is_purchasable() && $ldemb_product->is_in_stock() ) : ?>
							<a class="btn btn--red btn--sm btn--block" href="<?php echo esc_url( $ldemb_product->add_to_cart_url() ); ?>" rel="nofollow">
								<?php echo esc_html( $ldemb_cart ); ?><span class="screen-reader-text">: <?php echo esc_html( $ldemb_product->get_name() ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $ldemb_shop && $ldemb_all ) : ?>
			<p class="products__more"><a class="btn btn--outline-green btn--lg" href="<?php echo esc_url( $ldemb_shop ); ?>"><?php echo esc_html( $ldemb_all ); ?><?php echo ldemb_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		<?php endif; ?>
	</div>
</section>
