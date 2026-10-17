<?php
/**
 * Launch offer banner + supported file formats.
 * Both are hidden until enabled in Customize → LD: Launch Offer.
 *
 * @package LD_Embroidery
 */

$ldemb_offer   = ldemb_mod( 'offer_show' ) && ldemb_mod( 'offer_text' );
$ldemb_formats = ldemb_mod( 'formats_show' ) ? ldemb_mod( 'formats_text' ) : '';

if ( ! $ldemb_offer && ! $ldemb_formats ) {
	return;
}

$ldemb_title = ldemb_mod( 'offer_title' );
$ldemb_btn   = ldemb_mod( 'offer_btn_label' );
?>
<section id="offer" class="offer" aria-label="<?php esc_attr_e( 'Offer', 'ld-embroidery' ); ?>">
	<div class="container">
		<?php if ( $ldemb_offer ) : ?>
			<div class="offer__banner">
				<p class="offer__text">
					<?php if ( $ldemb_title ) : ?>
						<strong><?php echo esc_html( $ldemb_title ); ?></strong>
					<?php endif; ?>
					<?php echo esc_html( ldemb_mod( 'offer_text' ) ); ?>
				</p>
				<?php if ( $ldemb_btn ) : ?>
					<a class="btn btn--white" href="<?php echo esc_url( ldemb_offer_url() ); ?>"><?php echo esc_html( $ldemb_btn ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $ldemb_formats ) : ?>
			<p class="offer__formats"><span><?php esc_html_e( 'File formats:', 'ld-embroidery' ); ?></span> <?php echo esc_html( $ldemb_formats ); ?></p>
		<?php endif; ?>
	</div>
</section>
