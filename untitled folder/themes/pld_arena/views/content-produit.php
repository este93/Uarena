<?php
/**
 * Template part for displaying page content in single-produit.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package plda
 */


$genre_evenement = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'genre_evenement' );
$classGenre   	 =  array_key_first($genre_evenement);
$is_offre_vip 	= has_term( 'vip', 'type_offre' );


$themecouleur_evenement = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'plda_thematique_couleur', true );
$dataCouleur   	 		=  array_key_first($themecouleur_evenement);

$dataCouleur = ($dataCouleur=='') ? ( ($is_offre_vip)? 'vip-theme' : ( ($classGenre == 'genre_evenement_racing92')? 'racing92-theme' : 'green-theme')  ) : $dataCouleur;

$imageTag 		 = '';
$classBgContent  = ' content-empty';

if ( $image_fond_id = carbon_get_the_post_meta( 'product_img_bg' ) ) {
	$imageTag = wp_get_attachment_image( $image_fond_id, 'full' );
	$classBgContent = '';
}


$productController = \PldaPlugin\Base\ProductController::getInstance();



?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="bg-produit<?php echo $classBgContent; ?>"><?php echo $imageTag; ?></div>
	<div class="container">
		<div class="row row-main">
			<div class="col-main col col-w-auto">
				<div class="col-main__inner">
					<header class="entry-header">
						<?php 
						if ( has_post_thumbnail() ) {
							echo '<div class="entry-header__affiche col-xs-5 col-md-4">';
						    	the_post_thumbnail( 'vignette_affiche', array( 'class' => 'thumbnail', 'sizes' => '(max-width:1024px) 30vw, 200px' ) );
								echo (carbon_get_the_post_meta( 'product_alert' )) ? '<div class="icon-pastille"><span class="pastille__label">' . carbon_get_the_post_meta( 'product_alert' ) . '</span>' . inlineSvg('icon-pastille', true) . '</div>':'';
							echo "</div>";
						} 
						?>
						<div class="entry-header__body col-xs-7 col-md-8">
							<?php 
							echo '<p class="entry-header__category text-grad-' . $dataCouleur . '">' . implode(' ', $genre_evenement) . '</p>';
							the_title( '<h1 class="entry-header__title">', '</h1>' );
							if ( $soustitre = carbon_get_the_post_meta('product_subtitle') )
								echo '<div class="entry-header__subtitle subh1">' . $soustitre . '</div>';
							
							echo '<div class="entry-header__date">' . \Plda\Core\Tags::event_display_date() . '</div>';

							?>
						</div>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<?php
							the_content();
						?>
						<?php 
						//services
							//$pages_service = \Plda\Custom\Transients::get_pages_services();
							$product_services = carbon_get_the_post_meta( 'product_services' );
							
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
			<aside class="sidebar col-md-4">
				<div class="sidebar-mobile uk-position-fixed uk-position-bottom hidden-up-sm">
					<div class="sm__header header--contact-form bg-grad-or"><button class="btn btn-fullwidth" uk-toggle="target: .sm__content-contactform; animation: uk-animation-slide-bottom">ÊTRE CONTACTÉ<span class="dropdown__icon-container"><i class="plda fa-chevron-bas"></i></span></button></div>
					<div class="sm__content sm__content-contactform" aria-hidden="true" hidden=""></div>
					<div class="sm__header bg-grad-green"><button class="btn btn-fullwidth" uk-toggle="target: .sm__content-reserver; animation: uk-animation-slide-bottom"><i class="plda fa-ticket"></i> RÉSERVEZ<span class="dropdown__icon-container"><i class="plda fa-chevron-bas"></i></span></button></div>
					<div class="sm__content sm__content-reserver" aria-hidden="true" hidden=""></div>
				</div>
				<div class="sd__content display-block-up-sm">
					<?php 
					if ( $is_offre_vip )
						\Plda\Core\Tags::get_the_produit_contact_form( implode(' ', $genre_evenement) . '/' . get_the_title());
					?>

					<section class="widget-meetings widget">
						<?php echo \Plda\Core\Tags::get_produit_billets( get_the_ID() ) ?>
					</section>
					<section class="widget-offres widget">
						<?php echo \Plda\Core\Tags::get_produit_offres( get_the_ID() ) ?>
					</section>
				</div>
			</aside>
		</div>
		<div class="row">
			<div class="col-12">				
				<?php echo \Plda\Core\Tags::the_related_posts(8) ?>
			</div>
		</div>
	</div>

</article><!-- #post-## -->
