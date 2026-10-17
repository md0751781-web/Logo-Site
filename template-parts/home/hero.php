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
<section class="hero" aria-labelledby="hero-title">
	<div class="hero__media">
		<?php ldemb_hero_image(); ?>
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
