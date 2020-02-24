<?php
/**
 * Template part for displaying content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package plda
 */


$genre_article = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'category' );
$couleur_article = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'plda_thematique_couleur' );

$imageTag 		 = '';
$classBgContent  = ' content-empty';

if ( $image_fond_id = carbon_get_the_post_meta( 'article_img_bg' ) ) {
	$imageTag = wp_get_attachment_image( $image_fond_id, 'full' );
	$classBgContent = '';
}


if ( $dateText = carbon_get_the_post_meta('article_date') ) {
	// set french locale
	\Moment\Moment::setLocale('fr_FR');
	\Moment\Moment::setDefaultTimezone('Europe/Paris');
	$article_date = new \Moment\Moment( $dateText );
	$dateText = '<div class="entry-header__date">' . $article_date->format( 'd F Y - H\Hi' ) . '</div>';
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="bg-produit<?php echo $classBgContent; ?>" uk-parallax="scale: 1,0.8; y: 0,160px; viewport: 0.5;"><?php echo $imageTag; ?></div>
	<div class="container wrapper__content--contracted">
		<div class="row row-main">
			<div class="col col-main">
				<div class="col-main__inner">
					<header class="entry-header">
						<?php 
						echo '<p class="entry-header__category">' . implode(' ', $genre_article) . '</p>';
						the_title( '<h1 class="entry-header__title">', '</h1>' );
						echo $dateText;
						if ( $soustitre = carbon_get_the_post_meta('article_subtitle') )
							echo '<h2 class="entry-header__subtitle">' . $soustitre . '</h2>';
						?>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<?php
							the_content();
						?>
						<?php 
						//services
							$product_services = carbon_get_the_post_meta( 'article_services' );
							
							if ( !empty($product_services) ) { ?>
								<section class="product-services">
									<h3 class="text-center">LES SERVICES</h3>
									<ul class="service__liste">
									<?php foreach ($product_services as $product_service) { 
										$term = get_term_by('id', $product_service, 'plda_service');
										if ( $term ) {
											echo '<li class="service__item col-xs-4">';
											echo '	<a href="' . carbon_get_term_meta($term->term_id, 'service_page_url') . '" title="' . esc_attr($term->name). '" class="service__link">';
											echo '<span class="service__btn">';
													inlineSvg( 'btn-service' );
											echo '	<span class="service__icon fa-inverse fas fa-' . $term->slug . '"></span>';
											echo '</span>';
											echo '<span class="service__label">' . $term->name . '</span>';
											echo '	</a>';
											echo '</li>';
										}
									} ?>
									</ul>
								</section>
							<?php } ?>
					</div><!-- .entry-content -->
				</div>
			</div>
		</div><!-- .col-main .row-main -->
		
		<div class="row">
			<div class="col-12">				
				<?php echo \Plda\Core\Tags::the_related_posts() ?>
			</div>
		</div>
	</div><!-- .container -->
</article><!-- #post-## -->
