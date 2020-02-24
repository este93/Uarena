<?php
/**
 * Template part for the billetterie template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package plda
 */


	// Products query

	$output 	   = $filter_div = $filter_events = $filter_services_div = $filter_temporalites_div = '';
	$filtres_event = $filtres_service = $filtres_temporalite = array();
	$idsOfProducts = \Plda\Custom\Transients::get_transient_query_ids( 'ALL_CURRENT_VIP' );

	$loop = new \WP_Query( array(
		'post_type' => 'produit',
        'posts_per_page' => -1,
        'orderby' => 'post__in',
        'post__in' => ((!isset($idsOfProducts) || empty($idsOfProducts)) ? array(0) : $idsOfProducts)
	) );


	$output = '<div class="wrapper-offres">';

	if( $loop->have_posts() ):

		$output .= '<div class="wrapper__content offres-grid">';

		while( $loop->have_posts() ): $loop->the_post();

			$genre_evenement 	= \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'genre_evenement', true );
			$type_offre 		= \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'type_offre', true );
			$temporalite_offre  = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'temporalite_offre', true );

			$temporalite_offre = array_filter(
				array_map( function( $cat ) {
				        if ( $cat != 'vip' ) {
				            return $cat;
				        }
				     },
				     $temporalite_offre
				)
			);
			

			if ( !empty($genre_evenement) )
				$filtres_event[array_key_first($genre_evenement)] = reset($genre_evenement);
			if ( !empty($type_offre) )
				$filtres_service[array_key_first($type_offre)] = reset($type_offre);
			if ( !empty($temporalite_offre) )
				$filtres_temporalite[array_key_first($temporalite_offre)] = reset($temporalite_offre);

			$output .= \Plda\Core\Tags::get_offre_card( get_the_ID() );

		endwhile;

		$output .= '</div>';

		foreach ($filtres_event as $key => $value) {
			$filter_events .= ( !empty($value) ) ? '<li data-filter="' . $key . '"><input type="checkbox" value="' . $key . '" class="uk-checkbox" id="'.$key.'%1$s"><label for="'.$key.'%1$s">' . $value .'</label></li>':'';
		}
		foreach ($filtres_service as $key => $value) {
			$filter_services_div .= ( !empty($value) ) ? '<li data-filter="' . $key . '"><input type="checkbox" value="' . $key . '" class="uk-checkbox" id="'.$key.'%1$s"><label for="'.$key.'%1$s">' . $value .'</label></li>':'';
		}
		foreach ($filtres_temporalite as $key => $value) {
			$filter_temporalites_div .= ( !empty($value) ) ? '<li data-filter="' . $key . '"><input type="checkbox" value="' . $key . '" class="uk-checkbox" id="'.$key.'%1$s"><label for="'.$key.'%1$s">' . $value .'</label></li>':'';
		}

		$filter_modal = sprintf(
			'<!-- Modal filter 1 -->
			<div id="filter-offres-xs" class="uk-flex-top" uk-modal>
			    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-top">
			        <h2 class="uk-modal-title js_close-modal"><i class="fas fa-sliders-h"></i> FILTRES<span class="dropdown__icon-container">OK</span></h2>
			        <ul class="nav-filter" data-target="offres-grid">%1$s %2$s %3$s</ul>
			    </div>
			</div>',
			(!empty($filter_events))? 			'<li class="filter__group" data-group="genre_evenement"><span class="filter__title">Type d\'événement</span><ul class="filter__options">' . sprintf($filter_events, '_xs') . '</ul></li>' : '',
			(!empty($filter_services_div))? 	'<li class="filter__group" data-group="type_offre"><span class="filter__title">Type d\'espace</span><ul class="filter__options">' . sprintf($filter_services_div, '_xs') . '</ul></li>' : '',
			(!empty($filter_temporalites_div))? '<li class="filter__group" data-group="temporalite_offre"><span class="filter__title">Type d\'offre</span><ul class="filter__options">' . sprintf($filter_temporalites_div, '_xs') . '</ul></li>' : ''
		);

		$filter_div = sprintf(
				'<div class="wrapper-filters display-block-up-sm"><ul class="nav-filter" data-target="offres-grid">%1$s %2$s %3$s</ul></div>',
				(!empty($filter_events))? 			'<li class="filter__group" data-group="genre_evenement"><span class="filter__title">TYPES D\'ÉVÉNEMENTS</span><ul class="filter__options">' . sprintf($filter_events, '_lg') . '</ul></li>' : '',
				(!empty($filter_services_div))? 	'<li class="filter__group" data-group="type_offre"><span class="filter__title">TYPE D\'ESPACE</span><ul class="filter__options">' . sprintf($filter_services_div, '_lg') . '</ul></li>' : '',
				(!empty($filter_temporalites_div))? '<li class="filter__group" data-group="temporalite_offre"><span class="filter__title">TYPE D\'OFFRE</span><ul class="filter__options">' . sprintf($filter_temporalites_div, '_lg') . '</ul></li>' : ''
			);

		wp_reset_postdata();
		
	else:
		$output .= '<div class="text-white">Aucune offre actuellement !</div>';
	endif;
	$output .= '</div>';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="bg-produit content-empty"></div>
	<div class="container">
		<div class="row row-main">
			<div class="col-main col">

				<header class="entry-header">
					<?php the_title( '<h1 class="titre-categorie">', '</h1>' ); ?>
				</header><!-- .entry-header -->

				<div class="entry-content wrapper__content--contracted">
					<?php
						the_content();

						echo $filter_div;

						echo $output;
					?>
				</div><!-- .entry-content -->
			</div><!-- .col -->
		</div><!-- .row -->
	</div><!-- .container -->

</article><!-- #post-## -->
<div class="btn-toggle-modal-filter uk-position-fixed uk-position-bottom hidden-up-sm bg-black">
	<button class="btn btn-fullwidth text-white" uk-toggle="target: #filter-offres-xs; animation: uk-animation-slide-top"><i class="fas fa-sliders-h"></i> FILTRES<span class="dropdown__icon-container"><i class="plda fa-chevron-bas"></i></span></button>
</div>
<?php echo $filter_modal; ?>
