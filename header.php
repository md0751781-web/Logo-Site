<?php
/**
 * Site header (reusable across all pages).
 *
 * @package LD_Embroidery
 */

$ldemb_account = ldemb_account_url();
$ldemb_cart    = ldemb_cart_url();
$ldemb_badge   = ldemb_mod( 'header_badge_show' ) ? ldemb_mod( 'header_badge_text' ) : '';
$ldemb_cta     = ldemb_mod( 'header_cta_label' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'ld-embroidery' ); ?></a>

<header class="site-header">
	<div class="container">
		<div class="header-bar">
			<div class="site-branding">
				<?php ldemb_logo(); ?>
			</div>

			<nav id="site-nav" class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'ld-embroidery' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_id'        => 'primary-menu-list',
						'depth'          => 1,
						'fallback_cb'    => 'ldemb_fallback_menu',
					)
				);
				?>
				<div class="site-nav__extras">
					<?php if ( $ldemb_badge ) : ?>
						<span class="turnaround-badge"><?php echo ldemb_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $ldemb_badge ); ?></span>
					<?php endif; ?>
					<?php if ( $ldemb_account ) : ?>
						<a class="site-nav__account" href="<?php echo esc_url( $ldemb_account ); ?>"><?php echo ldemb_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'My Account', 'ld-embroidery' ); ?></a>
					<?php endif; ?>
					<?php if ( $ldemb_cta ) : ?>
						<a class="btn btn--green btn--block" href="<?php echo esc_url( ldemb_quote_url() ); ?>"><?php echo esc_html( $ldemb_cta ); ?></a>
					<?php endif; ?>
				</div>
			</nav>

			<div class="header-actions">
				<?php if ( $ldemb_badge ) : ?>
					<span class="turnaround-badge turnaround-badge--desktop"><?php echo ldemb_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $ldemb_badge ); ?></span>
				<?php endif; ?>

				<?php if ( $ldemb_account ) : ?>
					<a class="icon-link icon-link--account" href="<?php echo esc_url( $ldemb_account ); ?>">
						<?php echo ldemb_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="screen-reader-text"><?php esc_html_e( 'My account', 'ld-embroidery' ); ?></span>
					</a>
				<?php endif; ?>

				<?php if ( $ldemb_cart ) : ?>
					<a class="icon-link icon-link--cart" href="<?php echo esc_url( $ldemb_cart ); ?>">
						<?php echo ldemb_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'ld-embroidery' ); ?>,</span>
						<?php echo ldemb_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endif; ?>

				<?php if ( $ldemb_cta ) : ?>
					<a class="btn btn--green header-cta" href="<?php echo esc_url( ldemb_quote_url() ); ?>"><?php echo esc_html( $ldemb_cta ); ?></a>
				<?php endif; ?>

				<button class="menu-toggle" type="button" aria-controls="site-nav" aria-expanded="false">
					<?php echo ldemb_icon( 'menu', 'menu-toggle__open' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo ldemb_icon( 'close', 'menu-toggle__close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'ld-embroidery' ); ?></span>
				</button>
			</div>
		</div>
	</div>
</header>

<main id="main" class="site-main">
