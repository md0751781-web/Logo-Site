<?php
/**
 * Admin setup checklist: lists configuration points that still use fallbacks.
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ldemb_setup_notice() {
	$screen = get_current_screen();
	if ( ! current_user_can( 'edit_theme_options' ) || ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes' ), true ) ) {
		return;
	}

	$todo = array();
	if ( ! has_custom_logo() ) {
		$todo[] = __( 'Upload the LD Embroidery logo (Site Identity).', 'ld-embroidery' );
	}
	if ( ! ldemb_mod( 'hero_image' ) ) {
		$todo[] = __( 'Set the hero photo (LD: Hero Banner → Hero image). A placeholder illustration is shown until then.', 'ld-embroidery' );
	}
	if ( ! ldemb_mod( 'quote_url' ) && ! ldemb_page_url( array( 'get-quote', 'quote', 'contact' ) ) ) {
		$todo[] = __( 'Set the quote page (LD: Header → Quote page URL). "Get Quote" currently points to the services section.', 'ld-embroidery' );
	}
	if ( ! ldemb_mod( 'upload_url' ) && ! ldemb_page_url( array( 'upload-your-design', 'upload' ) ) ) {
		$todo[] = __( 'Set the upload page (LD: Hero Banner → Upload page URL). "Upload Your Design" currently uses the quote destination.', 'ld-embroidery' );
	}
	if ( ! has_nav_menu( 'primary' ) ) {
		$todo[] = __( 'Assign a Primary menu (Appearance → Menus).', 'ld-embroidery' );
	}
	if ( ! ldemb_wc_active() ) {
		$todo[] = __( 'Activate WooCommerce to enable the cart and account icons.', 'ld-embroidery' );
	}

	if ( ! $todo ) {
		return;
	}
	?>
	<div class="notice notice-info">
		<p><strong><?php esc_html_e( 'LD Embroidery theme setup', 'ld-embroidery' ); ?></strong></p>
		<ul style="list-style:disc;padding-left:20px">
			<?php foreach ( $todo as $item ) : ?>
				<li><?php echo esc_html( $item ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p><a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open Customizer', 'ld-embroidery' ); ?></a></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'ldemb_setup_notice' );
