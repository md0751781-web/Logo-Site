<?php
/**
 * Post card used in listings.
 *
 * @package Logo_Site
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="post-card-thumb"><?php the_post_thumbnail( 'logo-site-portfolio' ); ?></a>
	<?php endif; ?>
	<h2 class="post-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	<div class="post-card-excerpt"><?php the_excerpt(); ?></div>
</article>
