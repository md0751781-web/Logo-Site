<?php
/**
 * Site footer.
 *
 * @package Logo_Site
 */
?>
</main>

<footer class="site-footer">
	<div class="container">
		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="footer-widgets"><?php dynamic_sidebar( 'footer-1' ); ?></div>
		<?php endif; ?>

		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => 'nav',
				'menu_class'     => 'footer-menu',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>

		<p class="copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'logo-site' ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
