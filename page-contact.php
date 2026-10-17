<?php
/**
 * Template Name: Contact
 *
 * Used automatically for the page with the slug "contact".
 * Contact details, hours and social links: Customize → LD Page: Contact (empty items are hidden).
 *
 * @package LD_Embroidery
 */

get_header();

get_template_part( 'template-parts/page-hero', null, array( 'intro' => ldemb_mod( 'ct_intro' ) ) );

$ldemb_status = ldemb_form_status( 'contact' );
$ldemb_email  = ldemb_mod( 'ct_email' ) ? ldemb_mod( 'ct_email' ) : ldemb_mod( 'footer_email' );
$ldemb_phone  = ldemb_mod( 'ct_phone' );
$ldemb_wa     = ldemb_mod( 'ct_whatsapp' );
$ldemb_addr   = ldemb_mod( 'ct_address' );

$ldemb_cards = array();
if ( $ldemb_phone ) {
	$ldemb_cards[] = array( 'phone', __( 'Call Us', 'ld-embroidery' ), $ldemb_phone, 'tel:' . preg_replace( '/[^\d+]/', '', $ldemb_phone ), ldemb_mod( 'ct_phone_note' ) );
}
if ( $ldemb_wa ) {
	$ldemb_cards[] = array( 'whatsapp', __( 'WhatsApp Us', 'ld-embroidery' ), $ldemb_wa, 'https://wa.me/' . preg_replace( '/\D+/', '', $ldemb_wa ), ldemb_mod( 'ct_whatsapp_note' ) );
}
if ( $ldemb_email ) {
	$ldemb_cards[] = array( 'mail', __( 'Email Us', 'ld-embroidery' ), antispambot( $ldemb_email ), 'mailto:' . antispambot( $ldemb_email ), ldemb_mod( 'ct_email_note' ) );
}
if ( $ldemb_addr ) {
	$ldemb_cards[] = array( 'pin', __( 'Our Location', 'ld-embroidery' ), $ldemb_addr, 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $ldemb_addr ), ldemb_mod( 'ct_address_note' ) );
}

$ldemb_hours  = ldemb_lines( 'ct_hours' );
$ldemb_social = array_filter(
	array(
		'instagram' => array( ldemb_mod( 'ct_instagram' ), 'Instagram' ),
		'facebook'  => array( ldemb_mod( 'ct_facebook' ), 'Facebook' ),
		'pinterest' => array( ldemb_mod( 'ct_pinterest' ), 'Pinterest' ),
	),
	function ( $s ) {
		return (bool) $s[0];
	}
);
$ldemb_privacy = get_privacy_policy_url();
?>

<div class="contact-page">
	<div class="container">
		<?php if ( $ldemb_cards ) : ?>
			<ul class="contact-cards contact-cards--<?php echo count( $ldemb_cards ); ?>">
				<?php foreach ( $ldemb_cards as $ldemb_card ) : ?>
					<li class="contact-card">
						<span class="contact-card__icon"><?php echo ldemb_icon( $ldemb_card[0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<h2 class="contact-card__title"><?php echo esc_html( $ldemb_card[1] ); ?></h2>
						<a class="contact-card__value" href="<?php echo esc_url( $ldemb_card[3] ); ?>"<?php echo 'pin' === $ldemb_card[0] || 'whatsapp' === $ldemb_card[0] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $ldemb_card[2] ); ?></a>
						<?php if ( $ldemb_card[4] ) : ?>
							<p class="contact-card__note"><?php echo esc_html( $ldemb_card[4] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="contact-main<?php echo ( $ldemb_hours || $ldemb_social ) ? '' : ' contact-main--single'; ?>">
			<div id="ldemb-contact" class="contact-form-card">
				<h2 class="contact-form-card__title"><?php esc_html_e( 'Send us a Message', 'ld-embroidery' ); ?></h2>

				<?php if ( $ldemb_status ) : ?>
					<div class="form-notice form-notice--<?php echo esc_attr( $ldemb_status[0] ); ?>" role="<?php echo 'error' === $ldemb_status[0] ? 'alert' : 'status'; ?>">
						<?php echo ldemb_icon( 'success' === $ldemb_status[0] ? 'check' : 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<p><?php echo esc_html( $ldemb_status[1] ); ?></p>
					</div>
				<?php endif; ?>

				<form class="contact-form" method="post" action="<?php echo esc_url( ldemb_form_action( 'contact' ) ); ?>">
					<?php ldemb_form_fields( 'contact' ); ?>
					<div class="field">
						<label for="ct-name"><?php esc_html_e( 'Name', 'ld-embroidery' ); ?></label>
						<input type="text" id="ct-name" name="name" placeholder="<?php esc_attr_e( 'Your name', 'ld-embroidery' ); ?>" autocomplete="name" maxlength="100" required>
					</div>
					<div class="field">
						<label for="ct-email"><?php esc_html_e( 'Email', 'ld-embroidery' ); ?></label>
						<input type="email" id="ct-email" name="email" placeholder="your.email@example.com" autocomplete="email" maxlength="120" required>
					</div>
					<div class="field">
						<label for="ct-message"><?php esc_html_e( 'Message', 'ld-embroidery' ); ?></label>
						<textarea id="ct-message" name="message" rows="5" maxlength="5000" placeholder="<?php esc_attr_e( 'Tell us about your embroidery project…', 'ld-embroidery' ); ?>" required></textarea>
					</div>
					<button type="submit" class="btn btn--red btn--lg btn--block"><?php echo esc_html( ldemb_mod( 'ct_button' ) ); ?></button>
					<?php if ( $ldemb_privacy ) : ?>
						<p class="form-legal">
							<?php
							/* translators: %s: privacy policy link */
							printf( esc_html__( 'By submitting, you agree to our %s.', 'ld-embroidery' ), '<a href="' . esc_url( $ldemb_privacy ) . '">' . esc_html__( 'Privacy Policy', 'ld-embroidery' ) . '</a>' );
							?>
						</p>
					<?php endif; ?>
				</form>
			</div>

			<?php if ( $ldemb_hours || $ldemb_social ) : ?>
				<aside class="contact-aside">
					<?php if ( $ldemb_hours ) : ?>
						<h2 class="contact-aside__title"><?php esc_html_e( 'Studio Hours', 'ld-embroidery' ); ?></h2>
						<ul class="hours">
							<?php
							foreach ( $ldemb_hours as $ldemb_line ) :
								$ldemb_parts = explode( ':', $ldemb_line, 2 );
								?>
								<li>
									<?php if ( isset( $ldemb_parts[1] ) ) : ?>
										<strong><?php echo esc_html( trim( $ldemb_parts[0] ) ); ?>:</strong> <?php echo esc_html( trim( $ldemb_parts[1] ) ); ?>
									<?php else : ?>
										<?php echo esc_html( $ldemb_line ); ?>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $ldemb_social ) : ?>
						<h2 class="contact-aside__title"><?php esc_html_e( 'Follow Us', 'ld-embroidery' ); ?></h2>
						<ul class="social">
							<?php foreach ( $ldemb_social as $ldemb_icon => $ldemb_s ) : ?>
								<li><a href="<?php echo esc_url( $ldemb_s[0] ); ?>" target="_blank" rel="noopener"><?php echo ldemb_icon( $ldemb_icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php echo esc_html( $ldemb_s[1] ); ?></span></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</aside>
			<?php endif; ?>
		</div>

		<?php
		while ( have_posts() ) :
			the_post();
			if ( get_the_content() ) :
				?>
				<div class="entry-content page-extra"><?php the_content(); ?></div>
				<?php
			endif;
		endwhile;
		?>
	</div>
</div>

<?php
get_footer();
