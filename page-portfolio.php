<?php
/**
 * Template Name: Portfolio Gallery
 *
 * Used automatically for the page with the slug "portfolio".
 * Items come from wp-admin → Portfolio (featured image + Portfolio Category).
 *
 * @package LD_Embroidery
 */

get_header();

get_template_part( 'template-parts/page-hero', null, array( 'intro' => ldemb_mod( 'pf_intro' ) ) );

$ldemb_items = get_posts(
	array(
		'post_type'      => 'ldemb_portfolio',
		'posts_per_page' => 120,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
	)
);
$ldemb_terms = get_terms(
	array(
		'taxonomy'   => 'ldemb_portfolio_cat',
		'hide_empty' => true,
	)
);
$ldemb_terms = is_wp_error( $ldemb_terms ) ? array() : $ldemb_terms;
$ldemb_total = count( $ldemb_items );
?>

<section class="portfolio-page" data-portfolio>
	<div class="container">
		<?php if ( $ldemb_items ) : ?>
			<?php if ( $ldemb_terms ) : ?>
				<div class="pf-filters" role="group" aria-label="<?php esc_attr_e( 'Filter portfolio', 'ld-embroidery' ); ?>">
					<button type="button" class="pf-filter" data-filter="*" aria-pressed="true"><?php echo esc_html( ldemb_mod( 'pf_all' ) ); ?></button>
					<?php foreach ( $ldemb_terms as $ldemb_term ) : ?>
						<button type="button" class="pf-filter" data-filter="<?php echo esc_attr( $ldemb_term->slug ); ?>" aria-pressed="false"><?php echo esc_html( $ldemb_term->name ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<ul class="pf-grid">
				<?php
				foreach ( $ldemb_items as $ldemb_item ) :
					$ldemb_slugs    = wp_get_post_terms( $ldemb_item->ID, 'ldemb_portfolio_cat', array( 'fields' => 'slugs' ) );
					$ldemb_names    = wp_get_post_terms( $ldemb_item->ID, 'ldemb_portfolio_cat', array( 'fields' => 'names' ) );
					$ldemb_subtitle = get_post_meta( $ldemb_item->ID, '_ldemb_subtitle', true );
					$ldemb_price    = get_post_meta( $ldemb_item->ID, '_ldemb_price', true );
					$ldemb_badge    = get_post_meta( $ldemb_item->ID, '_ldemb_badge', true );
					$ldemb_full     = get_the_post_thumbnail_url( $ldemb_item, 'large' );
					$ldemb_cat      = is_wp_error( $ldemb_names ) || ! $ldemb_names ? '' : $ldemb_names[0];
					?>
					<li class="pf-item" data-cats="<?php echo esc_attr( is_wp_error( $ldemb_slugs ) ? '' : implode( ' ', $ldemb_slugs ) ); ?>">
						<article class="pf-card">
							<div class="pf-card__media">
								<?php
								if ( has_post_thumbnail( $ldemb_item ) ) {
									echo get_the_post_thumbnail( $ldemb_item, 'medium_large', array( 'loading' => 'lazy', 'sizes' => '(max-width: 560px) 50vw, (max-width: 1024px) 33vw, 20vw' ) );
								} else {
									echo '<span class="pf-card__noimg">' . ldemb_icon( 'tshirt' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
								}
								?>
								<?php if ( $ldemb_badge ) : ?>
									<span class="pf-card__badge"><?php echo esc_html( $ldemb_badge ); ?></span>
								<?php endif; ?>
								<?php if ( $ldemb_full ) : ?>
									<button type="button" class="pf-card__quick"
										data-quick-view
										data-img="<?php echo esc_url( $ldemb_full ); ?>"
										data-title="<?php echo esc_attr( get_the_title( $ldemb_item ) ); ?>"
										data-subtitle="<?php echo esc_attr( $ldemb_subtitle ); ?>"
										data-price="<?php echo esc_attr( $ldemb_price ); ?>"
										data-order="<?php echo esc_url( ldemb_order_url( $ldemb_cat ) ); ?>">
										<?php echo ldemb_icon( 'eye' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<?php esc_html_e( 'Quick View', 'ld-embroidery' ); ?><span class="screen-reader-text">: <?php echo esc_html( get_the_title( $ldemb_item ) ); ?></span>
									</button>
								<?php endif; ?>
							</div>
							<h2 class="pf-card__title"><a href="<?php echo esc_url( get_permalink( $ldemb_item ) ); ?>"><?php echo esc_html( get_the_title( $ldemb_item ) ); ?></a></h2>
							<?php if ( $ldemb_subtitle ) : ?>
								<p class="pf-card__sub"><?php echo esc_html( $ldemb_subtitle ); ?></p>
							<?php endif; ?>
							<?php if ( $ldemb_price ) : ?>
								<p class="pf-card__price"><?php echo esc_html( $ldemb_price ); ?></p>
							<?php endif; ?>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>

			<p class="pf-count" aria-live="polite" data-total="<?php echo esc_attr( $ldemb_total ); ?>">
				<?php
				/* translators: 1: visible items, 2: total items */
				printf( esc_html__( 'Showing %1$s of %2$s designs', 'ld-embroidery' ), '<span data-count>' . (int) $ldemb_total . '</span>', (int) $ldemb_total );
				?>
			</p>

			<dialog class="quick-view" aria-labelledby="qv-title">
				<button type="button" class="quick-view__close" data-close><?php echo ldemb_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'Close', 'ld-embroidery' ); ?></span></button>
				<img class="quick-view__img" src="" alt="">
				<div class="quick-view__body">
					<h2 id="qv-title" class="quick-view__title"></h2>
					<p class="quick-view__sub"></p>
					<p class="quick-view__price"></p>
					<a class="btn btn--red quick-view__order" href="#"><?php echo esc_html( ldemb_mod( 'pf_order' ) ); ?></a>
				</div>
			</dialog>
		<?php else : ?>
			<div class="empty-state">
				<?php echo ldemb_icon( 'cap' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<p><?php esc_html_e( 'Our portfolio is being updated. Please check back soon.', 'ld-embroidery' ); ?></p>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<a class="btn btn--outline-red" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=ldemb_portfolio' ) ); ?>"><?php esc_html_e( 'Add a portfolio item', 'ld-embroidery' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
