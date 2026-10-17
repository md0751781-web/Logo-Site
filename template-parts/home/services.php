<?php
/**
 * Embroidery digitizing services: four cards.
 *
 * @package LD_Embroidery
 */

$ldemb_icons    = array( 1 => 'cap', 2 => 'puff', 3 => 'tshirt', 4 => 'jacket' );
$ldemb_subtitle = ldemb_mod( 'services_subtitle' );
?>
<section id="services" class="section services" aria-labelledby="services-title">
	<div class="container">
		<header class="section-head">
			<h2 id="services-title" class="section-title"><?php echo esc_html( ldemb_mod( 'services_title' ) ); ?></h2>
			<?php if ( $ldemb_subtitle ) : ?>
				<p class="section-subtitle"><?php echo esc_html( $ldemb_subtitle ); ?></p>
			<?php endif; ?>
		</header>

		<div class="services__grid">
			<?php
			foreach ( $ldemb_icons as $ldemb_i => $ldemb_icon ) :
				$ldemb_title = ldemb_mod( "service_{$ldemb_i}_title" );
				if ( ! $ldemb_title ) {
					continue;
				}
				$ldemb_tone = 1 === $ldemb_i % 2 ? 'red' : 'green';
				?>
				<article class="service-card service-card--<?php echo esc_attr( $ldemb_tone ); ?>">
					<div class="service-card__icon"><?php echo ldemb_icon( $ldemb_icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<h3 class="service-card__title"><?php echo esc_html( $ldemb_title ); ?></h3>
					<p class="service-card__text"><?php echo esc_html( ldemb_mod( "service_{$ldemb_i}_text" ) ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
