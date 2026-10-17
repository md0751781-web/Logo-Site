<?php
/**
 * Homepage: hero, trust badges, services, launch offer.
 *
 * Content is edited under Appearance → Customize → "LD: …" sections.
 * If a static front page has content, it is shown after the services.
 *
 * @package LD_Embroidery
 */

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/trust' );
get_template_part( 'template-parts/home/services' );
get_template_part( 'template-parts/home/offer' );

if ( 'page' === get_option( 'show_on_front' ) ) :
	while ( have_posts() ) :
		the_post();
		if ( get_the_content() ) :
			?>
			<section class="section page-content">
				<div class="container entry-content"><?php the_content(); ?></div>
			</section>
			<?php
		endif;
	endwhile;
endif;

get_footer();
