<?php
/**
 * Template part for the billetterie template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package plda
 */


	// Products query

	$output 	   = $filter_div = $filter_services_div = $cat_description = '';
	$filtres_event = $filtres_service = array();
	$idsOfProducts = \Plda\Custom\Transients::get_transient_query_ids( 'ALL_CURRENT_PRODUCTS' );

	$cat_terms = get_terms( array(
	    'taxonomy' => 'type_offre',
	    'hide_empty' => false
	) );
	//todo : changement titre au load et filtre pour tout caché si filtre n'existe pas
	 
	if ( !empty($cat_terms) ) :
	    $cat_description = '<div class="categories-list">';
	    foreach( $cat_terms as $tax ) {
	        if( $tax->parent == 0 ) {
	            $cat_description .= '<div class="category__item" data-category="'. esc_attr( $tax->slug ) .'">';
	            $cat_description .= '<span class="category__titre"><i class="fas fa-'. esc_attr( $tax->slug ) .'"></i> <span class="">' . $tax->name . '</span></span>';
	            $cat_description .= '<div class="category__description">' . $tax->description . '</div>';
	            $cat_description .= '</div>';
	        }
	    }
	    $cat_description.='</div>';
	endif;

	$loop = new \WP_Query( array(
		'post_type' => 'produit',
        'posts_per_page' => -1,
        'orderby' => 'post__in',
        'post__in' => ((!isset($idsOfProducts) || empty($idsOfProducts)) ? array(0) : $idsOfProducts)
	) );

	$output = '<div class="wrapper-products">';

	if( $loop->have_posts() ):


		$output .= '<div class="wrapper__content product-grid">';

		while( $loop->have_posts() ): $loop->the_post();

			$genre_evenement = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'genre_evenement', true );
			$type_offre 	 = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'type_offre', true );
			
			if ( !empty($genre_evenement) )
				$filtres_event[array_key_first($genre_evenement)] = reset($genre_evenement);
			if ( !empty($type_offre) )
				$filtres_service[array_key_first($type_offre)] = reset($type_offre);

			$output .= \Plda\Core\Tags::get_product_card( get_the_ID() );
			
		endwhile;

		$output .= '</div>';

		foreach ($filtres_event as $key => $value) {
			$filter_div .= ( !empty($value) ) ? '<li data-filter="' . $key . '"><span>' . $value .'</span></li>':'';
		}
		foreach ($filtres_service as $key => $value) {
			$filter_services_div .= ( !empty($value) ) ? '<li data-filter="' . $key . '"><span>' . $value .'</span></li>':'';
		}
		$filter_services_div .= '<li data-filter="packages" data-link="https://www.venue-parisladefense-arena.com/?lang=fr_FR"><span>Packages</span></li>';


		if ( !empty($filter_services_div) ){

			$filter_modal = sprintf(
				'<!-- Modal filter 1 -->
				<div id="event-filter" class="uk-flex-top js-close-on-click" uk-modal>
				    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-top">
				        <h2 class="uk-modal-title">Types d\'événements</h2>
				        <ul class="nav-filter filter--events" data-target="product-grid"><li class="selected" data-filter="all"><span>Tout</span></li>%1$s</ul>
				    </div>
				</div>
				<!-- Modal filter 2 -->
				<div id="services-filter" class="uk-flex-top js-close-on-click" uk-modal>
				    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-top">
				        <div class="uk-modal-title">Services</div>
				        <ul class="nav-filter filter--offres" data-target="product-grid">%2$s</ul>
				    </div>
				</div>',
				$filter_div,
				$filter_services_div
			);
			$filter_div = sprintf(
				'<div class="modal-button-wrapper hidden-up-sm"><a href="#" class="back-rubbon"><i class="fas fa-angle-left"></i> RETOUR</a><button class="btn btn-outline-white js-btn-events-list-toggle" uk-toggle="target: #event-filter" type="button">Types d\'événements<span><i class="plda fa-chevron-bas"></i></span></button>
				<button class="btn btn-outline-white js-btn-services-list-toggle" uk-toggle="target: #services-filter" type="button">Services<span><i class="plda fa-chevron-bas"></i></span></button></div>
				<div class="wrapper-filters display-block-up-sm"><ul class="nav-filter filter--events" data-target="product-grid"><li class="selected" data-filter="all"><span>Tout</span></li>%1$s<li data-filter="offres"><span>Services</span></li><li class="nav_indicator"></li></ul><ul class="nav-subfilter filter--offres" data-target="product-grid">%2$s<li class="nav_indicator"></li></ul></div>',
				$filter_div,
				$filter_services_div
			);
		} else {

			$filter_modal = sprintf(
				'<!-- Modal filter 1 -->
				<div id="event-filter"  class="js-close-on-click" uk-modal>
				    <div class="uk-modal-dialog uk-modal-body">
				        <div class="uk-modal-title">Types d\'événements</div>
				        <ul class="nav-filter filter--events" data-target="product-grid"><li class="selected" data-filter="all"><span>Tout</span></li>%1$s</ul>
				    </div>
				</div>',
				$filter_div
			);
			$filter_div = sprintf(
				'<div class="modal-button-wrapper hidden-up-sm"><button class="btn btn-outline-white" uk-toggle="target: #event-filter" type="button">Types d\'événements</button></div>
				<div class="wrapper-filters display-block-up-sm"><ul class="nav-filter filter--events" data-target="product-grid"><li class="selected" data-filter="all"><span>Tout</span></li>%s<li id="nav_indicator"></li></ul></div>',
				$filter_div
			);
		}

		wp_reset_postdata();
		
	else:
		$output .= '<div class="text-white">Aucun événement !</div>';
	endif;
	$output .= '</div>';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="bg-produit content-empty"></div>
	<div class="container">
		<div class="row row-main">
			<div class="col-main col">

				<header class="entry-header">
					<h1 class="titre-categorie">BILLETTERIE</h1>
					<?php echo $filter_div; ?>
					<?php echo $cat_description; ?>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php
						the_content();

						echo $output;
					?>
				</div><!-- .entry-content -->
			</div><!-- .col -->
		</div><!-- .row -->
	</div><!-- .container -->

</article><!-- #post-## -->
<?php echo $filter_modal; ?>
