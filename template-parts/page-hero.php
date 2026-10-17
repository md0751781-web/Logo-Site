<?php
/**
 * Centered page heading used by the inner pages.
 *
 * @param array $args { badge, intro }
 * @package LD_Embroidery
 */

$ldemb_badge = isset( $args['badge'] ) ? $args['badge'] : '';
$ldemb_intro = isset( $args['intro'] ) ? $args['intro'] : '';
?>
<header class="page-hero">
	<div class="container">
		<?php if ( $ldemb_badge ) : ?>
			<p class="page-hero__badge"><?php echo esc_html( $ldemb_badge ); ?></p>
		<?php endif; ?>
		<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php if ( $ldemb_intro ) : ?>
			<p class="page-hero__intro"><?php echo esc_html( $ldemb_intro ); ?></p>
		<?php endif; ?>
	</div>
</header>
