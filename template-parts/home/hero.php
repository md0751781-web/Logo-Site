<?php
/**
 * Homepage hero banner.
 *
 * @package LD_Embroidery
 */

$ldemb_price = ldemb_mod( 'hero_price' );
$ldemb_text  = ldemb_mod( 'hero_text' );
$ldemb_small = ldemb_mod( 'hero_btn_small' );
$ldemb_main  = ldemb_mod( 'hero_btn_label' );
$ldemb_btn2  = ldemb_mod( 'hero_btn2_label' );
?>
<?php $ldemb_slides = ldemb_hero_slides(); ?>
<section class="hero" aria-labelledby="hero-title">
	<div class="hero__media hero-slider" data-hero-slider data-interval="<?php echo esc_attr( max( 0, absint( ldemb_mod( 'hero_interval' ) ) ) ); ?>" role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Embroidery machines at work', 'ld-embroidery' ); ?>">
		<div class="hero-slider__track">
			<?php foreach ( $ldemb_slides as $ldemb_i => $ldemb_slide ) : ?>
				<div class="hero-slide<?php echo 0 === $ldemb_i ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: slide number, 2: slide count */ __( 'Slide %1$d of %2$d', 'ld-embroidery' ), $ldemb_i + 1, count( $ldemb_slides ) ) ); ?>"<?php echo 0 === $ldemb_i ? '' : ' aria-hidden="true"'; ?>>
					<?php echo $ldemb_slide; // phpcs:ignore WordPress.Security.EscapeOutput -- built with esc_* / wp_get_attachment_image. ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( count( $ldemb_slides ) > 1 ) : ?>
			<div class="hero-slider__dots" role="group" aria-label="<?php esc_attr_e( 'Choose slide', 'ld-embroidery' ); ?>">
				<?php foreach ( $ldemb_slides as $ldemb_i => $ldemb_slide ) : ?>
					<button type="button" class="hero-slider__dot" data-slide="<?php echo (int) $ldemb_i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number */ __( 'Show slide %d', 'ld-embroidery' ), $ldemb_i + 1 ) ); ?>"<?php echo 0 === $ldemb_i ? ' aria-current="true"' : ''; ?>></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="container hero__inner">
		<div class="hero__content">
			<h1 id="hero-title" class="hero__title">
				<span class="hero__line"><?php echo esc_html( ldemb_mod( 'hero_line1' ) ); ?></span>
				<span class="hero__line"><?php echo esc_html( ldemb_mod( 'hero_line2' ) ); ?></span>
				<?php if ( $ldemb_price ) : ?>
					<span class="hero__price"><?php echo esc_html( $ldemb_price ); ?></span>
				<?php endif; ?>
			</h1>

			<?php if ( $ldemb_text ) : ?>
				<p class="hero__text"><?php echo esc_html( $ldemb_text ); ?></p>
			<?php endif; ?>

			<div class="hero__actions">
				<?php if ( $ldemb_main ) : ?>
					<a class="btn btn--red btn--lg btn--stacked" href="<?php echo esc_url( ldemb_primary_url() ); ?>">
						<?php if ( $ldemb_small ) : ?>
							<span class="btn__small"><?php echo esc_html( $ldemb_small ); ?></span>
						<?php endif; ?>
						<span class="btn__main"><?php echo esc_html( $ldemb_main ); ?><?php echo ldemb_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</a>
				<?php endif; ?>
				<?php if ( $ldemb_btn2 ) : ?>
					<a class="btn btn--outline-green btn--lg" href="<?php echo esc_url( ldemb_upload_url() ); ?>">
						<?php echo ldemb_icon( 'upload' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( $ldemb_btn2 ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
