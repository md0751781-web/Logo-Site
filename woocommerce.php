<?php
/**
 * WooCommerce pages wrapper (shop, categories, product, cart, checkout, account).
 *
 * Shop and category archives use the two-column layout (categories + product grid);
 * everything else uses WooCommerce's own templates inside the theme header/footer.
 *
 * @package LD_Embroidery
 */

get_header();

if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) :
	get_template_part( 'template-parts/shop/archive' );
else :
	?>
	<div class="container woocommerce-area">
		<?php woocommerce_content(); ?>
	</div>
	<?php
endif;

get_footer();
