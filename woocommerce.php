<?php
/**
 * WooCommerce pages wrapper (shop, product, cart, checkout, account).
 * These pages use WooCommerce's own templates inside the theme header/footer.
 *
 * @package LD_Embroidery
 */

get_header();
?>

<div class="container woocommerce-area">
	<?php woocommerce_content(); ?>
</div>

<?php
get_footer();
