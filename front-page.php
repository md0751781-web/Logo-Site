<?php
/**
 * Front page: hero, services, portfolio, contact.
 *
 * If a static front page is set, its content is shown below the hero.
 *
 * @package Logo_Site
 */

get_header();

$hero_link = get_theme_mod( 'hero_link', '#contact' );
$email     = get_theme_mod( 'contact_email', get_option( 'admin_email' ) );
$phone     = get_theme_mod( 'contact_phone', '' );
?>

<section class="hero">
	<div class="container">
		<h1><?php echo esc_html( get_theme_mod( 'hero_title', __( 'Logos that make your brand unforgettable', 'logo-site' ) ) ); ?></h1>
		<p class="hero-subtitle"><?php echo esc_html( get_theme_mod( 'hero_subtitle', __( 'Professional logo & brand identity design for businesses of every size.', 'logo-site' ) ) ); ?></p>
		<a class="button" href="<?php echo esc_url( $hero_link ); ?>"><?php echo esc_html( get_theme_mod( 'hero_button', __( 'Get Your Logo', 'logo-site' ) ) ); ?></a>
	</div>
</section>

<?php if ( 'page' === get_option( 'show_on_front' ) ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( get_the_content() ) : ?>
			<section class="section page-content">
				<div class="container entry-content"><?php the_content(); ?></div>
			</section>
		<?php endif; ?>
	<?php endwhile; ?>
<?php endif; ?>

<section id="services" class="section services">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Our Services', 'logo-site' ); ?></h2>
		<div class="grid grid-3">
			<?php
			$services = array(
				array( '✏️', __( 'Logo Design', 'logo-site' ), __( 'Unique, memorable logos crafted around your brand story.', 'logo-site' ) ),
				array( '🎨', __( 'Brand Identity', 'logo-site' ), __( 'Colors, typography and guidelines that keep your brand consistent.', 'logo-site' ) ),
				array( '📇', __( 'Print & Social Kits', 'logo-site' ), __( 'Business cards, social media assets and more, ready to use.', 'logo-site' ) ),
			);
			foreach ( $services as $service ) :
				?>
				<div class="card">
					<div class="card-icon" aria-hidden="true"><?php echo esc_html( $service[0] ); ?></div>
					<h3><?php echo esc_html( $service[1] ); ?></h3>
					<p><?php echo esc_html( $service[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section id="portfolio" class="section portfolio">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Recent Work', 'logo-site' ); ?></h2>
		<?php
		$portfolio = new WP_Query(
			array(
				'posts_per_page'      => 6,
				'ignore_sticky_posts' => true,
				'meta_key'            => '_thumbnail_id',
			)
		);
		if ( $portfolio->have_posts() ) :
			?>
			<div class="grid grid-3 portfolio-grid">
				<?php
				while ( $portfolio->have_posts() ) :
					$portfolio->the_post();
					?>
					<a class="portfolio-item" href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'logo-site-portfolio' ); ?>
						<span class="portfolio-title"><?php the_title(); ?></span>
					</a>
				<?php endwhile; ?>
			</div>
			<?php
			wp_reset_postdata();
		else :
			?>
			<p class="empty-note"><?php esc_html_e( 'Add posts with a featured image to show your logo work here.', 'logo-site' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section id="contact" class="section contact">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Let’s Create Your Logo', 'logo-site' ); ?></h2>
		<p><?php esc_html_e( 'Tell us about your brand and we will get back to you within 24 hours.', 'logo-site' ); ?></p>
		<div class="contact-actions">
			<?php if ( $email ) : ?>
				<a class="button" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<a class="button button-outline" href="https://wa.me/<?php echo esc_attr( preg_replace( '/\D+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
get_footer();
