<?php
/**
 * Template Name: Services
 *
 * Used automatically for the page with the slug "services".
 * Cards come from wp-admin → Services; starter cards are shown until the first one is published.
 *
 * @package LD_Embroidery
 */

get_header();

get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'badge' => ldemb_mod( 'svc_badge' ),
		'intro' => ldemb_mod( 'svc_intro' ),
	)
);

$ldemb_groups  = ldemb_get_services();
$ldemb_details = ldemb_mod( 'svc_details' );
$ldemb_cta     = ldemb_mod( 'svc_cta_text' );
?>

<div class="services-page">
	<div class="container">
		<?php
		foreach ( array( 'apparel' => 'g1', 'software' => 'g2' ) as $ldemb_group => $ldemb_key ) :
			if ( empty( $ldemb_groups[ $ldemb_group ] ) ) {
				continue;
			}
			$ldemb_text = ldemb_mod( "svc_{$ldemb_key}_text" );
			?>
			<section class="svc-group svc-group--<?php echo esc_attr( $ldemb_group ); ?>" aria-labelledby="svc-<?php echo esc_attr( $ldemb_group ); ?>">
				<header class="svc-group__head">
					<h2 id="svc-<?php echo esc_attr( $ldemb_group ); ?>" class="svc-group__title"><?php echo esc_html( ldemb_mod( "svc_{$ldemb_key}_title" ) ); ?></h2>
					<?php if ( $ldemb_text ) : ?>
						<p class="svc-group__text"><?php echo esc_html( $ldemb_text ); ?></p>
					<?php endif; ?>
				</header>

				<div class="svc-grid">
					<?php foreach ( $ldemb_groups[ $ldemb_group ] as $ldemb_svc ) : ?>
						<article class="svc-card">
							<span class="svc-card__icon"><?php echo ldemb_icon( $ldemb_svc[0] ? $ldemb_svc[0] : 'pencil' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<h3 class="svc-card__title"><?php echo esc_html( $ldemb_svc[1] ); ?></h3>
							<?php if ( $ldemb_svc[2] ) : ?>
								<p class="svc-card__text"><?php echo esc_html( $ldemb_svc[2] ); ?></p>
							<?php endif; ?>
							<div class="svc-card__foot">
								<?php if ( $ldemb_svc[3] ) : ?>
									<span class="svc-card__price"><?php echo esc_html( $ldemb_svc[3] ); ?></span>
								<?php endif; ?>
								<?php if ( $ldemb_details ) : ?>
									<a class="btn btn--outline-red btn--sm" href="<?php echo esc_url( $ldemb_svc[4] ); ?>">
										<?php echo esc_html( $ldemb_details ); ?><span class="screen-reader-text">: <?php echo esc_html( $ldemb_svc[1] ); ?></span>
									</a>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>

		<?php if ( $ldemb_cta ) : ?>
			<div class="cta-band">
				<p><?php echo esc_html( $ldemb_cta ); ?></p>
				<a class="btn btn--red" href="<?php echo esc_url( ldemb_page_url( 'contact' ) ? ldemb_page_url( 'contact' ) : ldemb_quote_url() ); ?>"><?php echo esc_html( ldemb_mod( 'svc_cta_label' ) ); ?><?php echo ldemb_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</div>
		<?php endif; ?>

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
