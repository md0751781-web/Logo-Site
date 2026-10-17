<?php
/**
 * Template Name: Order / Upload Artwork
 *
 * Used automatically for the page with the slug "order".
 *
 * @package LD_Embroidery
 */

get_header();

$ldemb_status     = ldemb_form_status( 'order' );
$ldemb_categories = ldemb_lines( 'order_categories' );
$ldemb_types      = ldemb_lines( 'order_types' );
$ldemb_selected   = isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

// Preselect the closest category for deep links such as ?category=Hoodie%20Logo.
$ldemb_match = '';
$ldemb_stem = function ( $text ) {
	$word = strtolower( strtok( trim( $text ), ' ' ) );
	return rtrim( $word, 's' );
};
foreach ( $ldemb_categories as $ldemb_cat ) {
	if ( '' !== $ldemb_selected && ( false !== stripos( $ldemb_selected, $ldemb_cat ) || false !== stripos( $ldemb_cat, $ldemb_selected ) || $ldemb_stem( $ldemb_cat ) === $ldemb_stem( $ldemb_selected ) ) ) {
		$ldemb_match = $ldemb_cat;
		break;
	}
}
?>

<section id="ldemb-order" class="section order-page">
	<div class="container">
		<h1 class="screen-reader-text"><?php the_title(); ?></h1>

		<?php if ( $ldemb_status ) : ?>
			<div class="form-notice form-notice--<?php echo esc_attr( $ldemb_status[0] ); ?>" role="<?php echo 'error' === $ldemb_status[0] ? 'alert' : 'status'; ?>">
				<?php echo ldemb_icon( 'success' === $ldemb_status[0] ? 'check' : 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<p><?php echo esc_html( $ldemb_status[1] ); ?></p>
			</div>
		<?php endif; ?>

		<form class="order-form" method="post" action="<?php echo esc_url( ldemb_form_action( 'order' ) ); ?>" enctype="multipart/form-data">
			<?php ldemb_form_fields( 'order' ); ?>

			<div class="order-form__upload">
				<h2 class="order-form__title"><?php echo esc_html( ldemb_mod( 'order_upload_title' ) ); ?></h2>

				<div class="dropzone" data-dropzone data-max="<?php echo esc_attr( LDEMB_MAX_UPLOAD ); ?>">
					<span class="dropzone__icon"><?php echo ldemb_icon( 'cloud' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<p class="dropzone__title"><?php esc_html_e( 'Drag & drop your file here', 'ld-embroidery' ); ?></p>
					<p class="dropzone__hint" id="artwork-hint"><?php esc_html_e( 'PNG, JPG, SVG, PDF — up to 10MB', 'ld-embroidery' ); ?></p>
					<input class="dropzone__input" type="file" id="artwork" name="artwork" accept=".png,.jpg,.jpeg,.svg,.pdf" aria-describedby="artwork-hint artwork-status" required>
					<label class="btn btn--outline-red" for="artwork">
						<?php echo ldemb_icon( 'upload' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php esc_html_e( 'Upload File', 'ld-embroidery' ); ?></span>
					</label>
					<p class="dropzone__status" id="artwork-status" aria-live="polite"></p>
				</div>
			</div>

			<div class="order-form__details">
				<h2 class="order-form__title"><?php echo esc_html( ldemb_mod( 'order_details_title' ) ); ?></h2>

				<?php if ( $ldemb_categories ) : ?>
					<div class="field">
						<label for="order-category"><?php esc_html_e( 'Select Design Category', 'ld-embroidery' ); ?></label>
						<select id="order-category" name="category" required>
							<?php foreach ( $ldemb_categories as $ldemb_cat ) : ?>
								<option value="<?php echo esc_attr( $ldemb_cat ); ?>" <?php selected( $ldemb_cat === $ldemb_match ); ?>><?php echo esc_html( $ldemb_cat ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>

				<?php if ( $ldemb_types ) : ?>
					<fieldset class="field">
						<legend><?php esc_html_e( 'Embroidery Type', 'ld-embroidery' ); ?></legend>
						<div class="radio-group">
							<?php foreach ( $ldemb_types as $ldemb_i => $ldemb_type ) : ?>
								<label class="radio">
									<input type="radio" name="type" value="<?php echo esc_attr( $ldemb_type ); ?>" <?php checked( 0 === $ldemb_i ); ?> required>
									<span><?php echo esc_html( $ldemb_type ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
					</fieldset>
				<?php endif; ?>

				<div class="field">
					<label for="order-size"><?php esc_html_e( 'Logo Size', 'ld-embroidery' ); ?></label>
					<input type="text" id="order-size" name="size" placeholder="<?php esc_attr_e( 'e.g. 3in x 2in', 'ld-embroidery' ); ?>" maxlength="60">
				</div>
				<div class="field">
					<label for="order-name"><?php esc_html_e( 'Your Name', 'ld-embroidery' ); ?> <span class="req" aria-hidden="true">*</span></label>
					<input type="text" id="order-name" name="name" placeholder="<?php esc_attr_e( 'Enter your name', 'ld-embroidery' ); ?>" autocomplete="name" maxlength="100" required>
				</div>
				<div class="field">
					<label for="order-email"><?php esc_html_e( 'Email', 'ld-embroidery' ); ?> <span class="req" aria-hidden="true">*</span></label>
					<input type="email" id="order-email" name="email" placeholder="you@example.com" autocomplete="email" maxlength="120" required>
				</div>
				<div class="field">
					<label for="order-whatsapp"><?php esc_html_e( 'WhatsApp Number', 'ld-embroidery' ); ?></label>
					<input type="tel" id="order-whatsapp" name="whatsapp" placeholder="+1 (555) 000-0000" autocomplete="tel" maxlength="30">
				</div>
				<div class="field">
					<label for="order-notes"><?php esc_html_e( 'Notes (optional)', 'ld-embroidery' ); ?></label>
					<textarea id="order-notes" name="notes" rows="3" maxlength="2000" placeholder="<?php esc_attr_e( 'Fabric, thread colours, placement, deadline…', 'ld-embroidery' ); ?>"></textarea>
				</div>

				<button type="submit" class="btn btn--red btn--lg btn--block order-form__submit"><?php echo esc_html( ldemb_mod( 'order_button' ) ); ?></button>
			</div>
		</form>
	</div>
</section>

<?php
while ( have_posts() ) :
	the_post();
	if ( get_the_content() ) :
		?>
		<section class="page-content"><div class="container container-narrow entry-content"><?php the_content(); ?></div></section>
		<?php
	endif;
endwhile;

get_footer();
