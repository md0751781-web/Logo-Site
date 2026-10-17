<?php
/**
 * Main template (blog / archives fallback).
 *
 * @package LD_Embroidery
 */

get_header();
?>

<div class="container section">
	<?php if ( is_home() && ! is_front_page() ) : ?>
		<h1 class="page-title"><?php single_post_title(); ?></h1>
	<?php elseif ( is_archive() ) : ?>
		<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
	<?php elseif ( is_search() ) : ?>
		<h1 class="page-title">
			<?php
			/* translators: %s: search query */
			printf( esc_html__( 'Search results for: %s', 'ld-embroidery' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
			?>
		</h1>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="posts-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'card' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'ld-embroidery' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</div>

<?php
get_footer();
