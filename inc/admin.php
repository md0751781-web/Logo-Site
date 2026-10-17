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
	if ( ! ldemb_mod( 'quote_url' ) && ! ldemb_page_url( array( 'order', 'get-quote', 'quote', 'contact' ) ) ) {
		$todo[] = __( 'Set the quote page (LD: Header → Quote page URL). "Get Quote" currently points to the services section.', 'ld-embroidery' );
	}
	if ( ! ldemb_mod( 'upload_url' ) && ! ldemb_page_url( array( 'order', 'upload-your-design', 'upload' ) ) ) {
		$todo[] = __( 'Set the upload page (LD: Hero Banner → Upload page URL). "Upload Your Design" currently uses the quote destination.', 'ld-embroidery' );
	}
	$missing = ldemb_missing_pages();
	if ( $missing ) {
		/* translators: %s: comma-separated page names */
		$todo[] = sprintf( __( 'Create the theme pages: %s (use the button below).', 'ld-embroidery' ), implode( ', ', $missing ) );
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
		<p><strong><?php esc_html_e( 'Vector Emb theme setup', 'ld-embroidery' ); ?></strong></p>
		<ul style="list-style:disc;padding-left:20px">
			<?php foreach ( $todo as $item ) : ?>
				<li><?php echo esc_html( $item ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p>
			<a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open Customizer', 'ld-embroidery' ); ?></a>
			<?php if ( $missing ) : ?>
				<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=ldemb_create_pages' ), 'ldemb_create_pages' ) ); ?>"><?php esc_html_e( 'Create missing pages', 'ld-embroidery' ); ?></a>
			<?php endif; ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'ldemb_setup_notice' );

/**
 * Theme pages: slug => title. Each slug has a matching page-{slug}.php template.
 */
function ldemb_theme_pages() {
	return array(
		'services'  => __( 'Services', 'ld-embroidery' ),
		'portfolio' => __( 'Portfolio', 'ld-embroidery' ),
		'order'     => __( 'Order', 'ld-embroidery' ),
		'contact'   => __( 'Contact', 'ld-embroidery' ),
	);
}

function ldemb_missing_pages() {
	$missing = array();
	foreach ( ldemb_theme_pages() as $slug => $title ) {
		if ( ! get_page_by_path( $slug ) ) {
			$missing[ $slug ] = $title;
		}
	}
	return $missing;
}

function ldemb_create_pages() {
	if ( ! current_user_can( 'publish_pages' ) || ! check_admin_referer( 'ldemb_create_pages' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'ld-embroidery' ), 403 );
	}
	foreach ( ldemb_missing_pages() as $slug => $title ) {
		wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => $title,
				'post_name'   => $slug,
			)
		);
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=page' ) );
	exit;
}
add_action( 'admin_post_ldemb_create_pages', 'ldemb_create_pages' );
