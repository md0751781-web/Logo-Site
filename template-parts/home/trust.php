<?php
/**
 * Trust badges. Hidden until enabled in Customize → LD: Trust Badges,
 * because they are factual claims the owner must confirm.
 *
 * @package LD_Embroidery
 */

if ( ! ldemb_mod( 'trust_show' ) ) {
	return;
}

$ldemb_badges = array(
	array( ldemb_mod( 'trust_1' ), 'check' ),
	array( ldemb_mod( 'trust_2' ), 'check' ),
	array( ldemb_mod( 'trust_3' ), 'shield' ),
	array( ldemb_mod( 'trust_4' ), 'stars' ),
);
$ldemb_badges  = array_filter(
	$ldemb_badges,
	function ( $badge ) {
		return '' !== trim( (string) $badge[0] );
	}
);
$ldemb_tagline = ldemb_mod( 'trust_tagline' );

if ( ! $ldemb_badges && ! $ldemb_tagline ) {
	return;
}
?>
<section class="trust" aria-label="<?php esc_attr_e( 'Why customers choose us', 'ld-embroidery' ); ?>">
	<div class="container">
		<?php if ( $ldemb_badges ) : ?>
			<ul class="trust__list">
				<?php foreach ( $ldemb_badges as $ldemb_badge ) : ?>
					<li class="trust__item">
						<?php if ( 'stars' === $ldemb_badge[1] ) : ?>
							<span class="trust__stars" aria-hidden="true"><?php echo str_repeat( ldemb_icon( 'star' ), 5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php else : ?>
							<span class="trust__icon"><?php echo ldemb_icon( $ldemb_badge[1] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>
						<span class="trust__label"><?php echo esc_html( $ldemb_badge[0] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $ldemb_tagline ) : ?>
			<p class="trust__tagline"><?php echo esc_html( $ldemb_tagline ); ?></p>
		<?php endif; ?>
	</div>
</section>
