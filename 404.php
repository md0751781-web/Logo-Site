<?php
/**
 * 404 page.
 *
 * @package LD_Embroidery
 */

get_header();
?>

<div class="container container-narrow section text-center">
	<h1 class="page-title"><?php esc_html_e( 'Page not found', 'ld-embroidery' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for does not exist.', 'ld-embroidery' ); ?></p>
	<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'ld-embroidery' ); ?></a>
</div>

<?php
get_footer();
