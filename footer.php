<?php
/**
 * Site footer (reusable across all pages).
 *
 * @package LD_Embroidery
 */

$ldemb_url      = ldemb_mod( 'footer_url' );
$ldemb_email    = ldemb_mod( 'footer_email' );
$ldemb_support  = ldemb_mod( 'footer_support' );
$ldemb_payments = ldemb_mod( 'footer_payments_show' ) ? ldemb_payment_methods() : array();
?>
</main>

<footer id="site-footer" class="site-footer">
	<div class="container">
		<div class="footer-bar">
			<p class="footer-copy">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>

			<?php if ( $ldemb_url || $ldemb_email || $ldemb_support ) : ?>
				<ul class="footer-contact">
					<?php if ( $ldemb_url ) : ?>
						<li><?php echo ldemb_icon( 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="<?php echo esc_url( $ldemb_url ); ?>"><?php echo esc_html( untrailingslashit( preg_replace( '#^https?://(www\.)?#', '', $ldemb_url ) ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $ldemb_email ) : ?>
						<li><?php echo ldemb_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="mailto:<?php echo esc_attr( antispambot( $ldemb_email ) ); ?>"><?php echo esc_html( antispambot( $ldemb_email ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $ldemb_support ) : ?>
						<li><?php echo ldemb_icon( 'support' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $ldemb_support ); ?></span></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $ldemb_payments ) : ?>
				<ul class="footer-payments" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'ld-embroidery' ); ?>">
					<?php foreach ( $ldemb_payments as $ldemb_method ) : ?>
						<li class="payment-chip">
							<?php echo $ldemb_method['icon'] ? wp_kses_post( $ldemb_method['icon'] ) : esc_html( $ldemb_method['title'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => 'nav',
				'container_aria_label' => __( 'Footer', 'ld-embroidery' ),
				'menu_class'     => 'footer-menu',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
