<?php
/**
 * Shop / product category archive: category sidebar + product grid.
 *
 * Uses WooCommerce's own loop and hooks (result count, sorting, add-to-cart,
 * pagination, no-products notice), so existing WooCommerce behaviour is unchanged.
 *
 * @package LD_Embroidery
 */

?>
<div class="container container--wide shop-layout">
	<?php ldemb_shop_sidebar(); ?>

	<section class="shop-main" aria-labelledby="shop-title">
		<header class="shop-head">
			<h1 id="shop-title" class="shop-head__title"><?php woocommerce_page_title(); ?></h1>
			<?php do_action( 'woocommerce_archive_description' ); ?>
		</header>

		<?php if ( woocommerce_product_loop() ) : ?>
			<div class="shop-toolbar">
				<?php do_action( 'woocommerce_before_shop_loop' ); ?>
			</div>

			<?php
			woocommerce_product_loop_start();
			while ( have_posts() ) {
				the_post();
				do_action( 'woocommerce_shop_loop' );
				wc_get_template_part( 'content', 'product' );
			}
			woocommerce_product_loop_end();

			do_action( 'woocommerce_after_shop_loop' );
			?>
		<?php else : ?>
			<?php do_action( 'woocommerce_no_products_found' ); ?>
		<?php endif; ?>
	</section>
</div>
