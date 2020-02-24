<?php

namespace Plda\Core;

use PldaPlugin\Base\ProductController;
use Plda\Custom\Transients;
use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Exception\RuntimeException;

/**
 * Tags.
 */
class Tags
{

	use ConfigTrait;

	public $productInstance;

    private static $instance = null;


	/**
	 * Main constructor
	 *
	 */
	public function __construct( ConfigInterface $config = null ) 
	{
		if ( $config)
			$this->processConfig( $config );

	}

	/**
	 * register default hooks and actions for WordPress
	 * @return
	 */
	public function register()
	{
		add_action( 'edit_category', array( $this, 'category_transient_flusher' ) );
		add_action( 'save_post', array( $this, 'category_transient_flusher' ) );
	}


	/**
	 * Static constructor / factory
	 */

    public static function getInstance()
    {

		$configGM = ConfigFactory::createSubConfig( ABSPATH . '../project_settings.php', 'Plda\googleMap' );

		if(self::$instance == null)
		{
			self::$instance = new self($configGM);
		}

		return self::$instance;

    }

	public static function posted_on()
	{
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if (get_the_time('U') !== get_the_modified_time('U')) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated hide" datetime="%3$s">%4$s</time>';
		}
		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);
		$posted_on = sprintf(
			esc_html_x('Posted on %s', 'post date', 'awps'),
			'<a href="'.esc_url(get_permalink()).'" rel="bookmark">'.$time_string.'</a>'
		);
		$byline = sprintf(
			esc_html_x('by %s', 'post author', 'awps'),
			'<span class="author vcard"><a class="url fn n" href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'">'.esc_html(get_the_author()).'</a></span>'
		);
		echo '<span class="posted-on">'.$posted_on.'</span><span class="byline"> '.$byline.'</span>'; // WPCS: XSS OK.
	}

	public static function event_display_date()
	{

		if ( ! ($dateText = carbon_get_the_post_meta('product_date_text')) ){
			$dateText = self::get_date_text( get_the_ID() );
			//carbon_set_post_meta( get_the_ID(), 'product_date_text', $dateText );
		}

		return $dateText;
	}

	/**
	 * Ajout des classes ad-hoc pour les types de contenu (fiche/debat/actu/atelier)
	 */
	public static function get_class_of_tax( $post_id = null ) 
	{

		if ( ! $post_id ) {
		    return array();
		}

		return array_merge( array_keys(self::get_post_tax_terms($post_id, 'famille_produit')), array_keys(self::get_post_tax_terms($post_id, 'genre_evenement')), array_keys(self::get_post_tax_terms($post_id, 'type_offre')), array_keys(self::get_post_tax_terms($post_id, 'temporalite_offre')) );

	}

	public static function get_post_tax_terms( $post_id = null, $tax_slug, $remove_tag_slug = false ) 
	{
		$classes = array();

		if ( ! $post_id ) {
		    return $classes;
		}

		$custom_terms = get_the_terms($post_id, $tax_slug);
		$tax_slug = ( !$remove_tag_slug ) ? $tax_slug . '_':'';
		if ( $custom_terms && ! is_wp_error( $custom_terms ) ) {
			foreach ($custom_terms as $custom_term) {
				$classes[$tax_slug . $custom_term->slug] = $custom_term->name;
			}
		}
		return $classes;
	}

	public static function get_the_produit_contact_form( $subject ) 
	{
		global $post;
		$nonce = wp_create_nonce( 'offre_vip_form_' . $post->ID );
	?>
		<section class="widget-contact-form widget" uk-accordion>
			<div class="wrapper-contact-form">
				<a class="uk-accordion-title bg-grad-gold" href="#">ÊTRE CONTACTÉ<i class="fas fa-chevron-down"></i></a>
				<div class="uk-accordion-content">
					<form id="form_vip_offre_contact" class="plda-form" action="#">
					    <fieldset class="uk-fieldset">

					        <legend class="uk-legend">Pour être recontacté, merci de remplir le formulaire ci-dessous :<div class="messages"></div></legend>

					        <div class="form-group uk-margin">
					            <input name="nom" class="uk-input" type="text" required placeholder="NOM">
					        </div>
					        <div class="form-group uk-margin">
					            <input name="prenom" class="uk-input" required type="text" placeholder="PRÉNOM">
					        </div>
					        <div class="form-group uk-margin">
					            <input name="societe" class="uk-input" required type="text" placeholder="SOCIÉTÉ">
					        </div>
					        <div class="form-group uk-margin">
					            <input name="email" class="uk-input" required type="email" placeholder="EMAIL">
					        </div>
					        <div class="form-group uk-margin">
					            <input name="telephone" class="uk-input" required type="text" placeholder="TÉLÉPHONE">
					        	<input type="hidden" name="subject" value="<?php echo $subject ?>">
					        	<input type="hidden" name="post_id" value="<?php echo $post->ID ?>">
					        	<input type="hidden" name="action" value="offre_vip_contact">
					        	<input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
					        </div>
							<div class="help-text">Tous les champs sont obligatoires</div>
					        <div class="uk-margin text-center">
					            <input class="btn btn-grad-gold" required type="submit" value="ÊTRE CONTACTÉ">
					        </div>
					    </fieldset>
					</form>
				</div>
			</div>
		</section>
	<?php
	}

	public static function get_produit_billets( $post_id )
	{
		$output 		= '';
		$classList 		= 'expanded-list';
		$meetings 		= carbon_get_post_meta( $post_id, 'meeting_repeater' );
		$meetingCount 	= 0;
		$jsonData 		= array();

		$productInstance = ProductController::getInstance();

		$header  = '<div class="wrapper-billets">';
		$header .= '<h2>BILLETTERIE</h2>';
		$header .= '<ul class="list-group list-group--billets">';

		if ( $meetings ) 
		{			
			// set french locale
			\Moment\Moment::setLocale('fr_FR');
			\Moment\Moment::setDefaultTimezone('Europe/Paris');

			foreach ($meetings as $meeting) {

				$meeting_publication = ($meeting['meeting_force_publication']) ? ( ($meeting['meeting_force_status']!='unpublished')?'1':'0' ) : $meeting['meeting_publication'];
				$statusCalculated	 = ($meeting['meeting_force_publication']) ? $meeting['meeting_force_status'] : $meeting['meeting_status'];
				$statusArray		 = $productInstance->getStatus($statusCalculated);

				if ( !empty($statusArray) && $meeting_publication == '1' ) {

	            	$meeting_date = $meeting['meeting_date_text'];
		            $meeting_date_object = new \Moment\Moment( $meeting['meeting_date'] );

	            	if ( !$meeting_date ){

		            	$meeting_date = '<time class="meeting__date" datetime="%1$s"><span class="meeting__day">%2$s</span><span class="meeting__month">%3$s</span><span class="meeting__hour">%4$s</span></time>';
						$meeting_date = sprintf( $meeting_date,
							esc_attr( $meeting_date_object->format( 'c' ) ),
							esc_html( $meeting_date_object->format( 'l' ) ),
							esc_html( $meeting_date_object->format( 'd F Y' ) ),
							esc_html( $meeting_date_object->format( 'H\Hi' ) )
						);
					}

		            $meeting_icone 		  = '<span class="plda fa-' . $statusArray['icon'] . '"></span>' ;
		            $meeting_date 		  = '<h3 class="meeting__titre">' . $meeting_date . '</h3>';
		            $meeting_a_partir_de  = ($meeting['meeting_a_partir_de']) ? 'a_partir_de':'';
		            $meeting_prix  		  = ($meeting['meeting_prix']) ? sprintf('<div class="meeting__prix %s">%s</div>', $meeting_a_partir_de, self::formatPrice($meeting['meeting_prix'])) : '';
		            $meeting_url  		  = $meeting['meeting_url'] ? $meeting['meeting_url'] : '#';
		            $meeting_btn 		  = ($meeting['meeting_btn_label']) ? $meeting['meeting_btn_label'] : $statusArray['btnActionLabel'];
		            
		            if ( !$statusArray['affichagePrix'] )
		            	$meeting_prix = '';

		            if ( $statusArray['icon'] == 'notification'){
	            		$meetingLink = '<a href="#" class="btn btn-link btn-xs is-reservable">%s</a>';
	            		$meeting_btn = sprintf($meetingLink, $meeting_btn);
	            	}
	            	elseif ($statusArray['meetingLink']) {
		            	$meetingLink = '<a href="%1$s" class="btn btn-link btn-xs">%2$s</a>';
		            	$meeting_btn = sprintf($meetingLink, $meeting_url, $meeting_btn);
		            	
		            }else{
		            	$meetingLink = '<span class="flag_status">%s</span>';
		            	$meeting_btn = sprintf($meetingLink, $statusArray['btnActionLabel']);
		            }

		            $output .= '<li class="list-item item--meeting ' . $statusArray['classItemBillet'] . '" data-statut="' . $statusCalculated . '">';
		            $output .= '<div class="list-item__contents">';
		            $output .= '  <div class="list-item__contents-positif">';
		            $output .= '  	<div class="list-item__image">' . $meeting_icone . '</div>';
		            $output .= '  	<div class="list-item__content">';
		            $output .= 			$meeting_date;
		            $output .= '  	</div>';
		            $output .= '  </div>';
		            $output .= '  <div class="list-item__contents-negatif">';
		            $output .= '  	<div class="list-item__image">' . $meeting_icone . '</div>';
		            $output .= '  	<div class="list-item__content">';
		            $output .= 			$meeting_date;
		            $output .= '  	</div>';
		            $output .= '  </div>';
		            $output .= '</div>';
		            $output .= '  <div class="list-item__extra">';
		            $output .= 		$meeting_prix;
		            $output .= 		$meeting_btn;
		            $output .= '  </div>';
		            $output .= '  <span class="svg--dgrad">';
		            $output .= 		inlineSvg('dgrad-vert-billetterie', true);
		            $output .= '  </span>';
		            $output .= '</li>';


		            $jsonData[] = array (
			            '@context' 	=> 'http://schema.org',
			            '@type' 	=> 'Event',
			            'location' 	=> array(
			                  "@type" 	=> "Place",
			                  "address" => array (
			                    "@type" 			=> "PostalAddress",
			                    "addressLocality" 	=> "Nanterre",
			                    "postalCode" 		=> "92000",
			                    "streetAddress" 	=> "207 Jardin de l'Arche"
			                  ),
			                  "name" 	=> "Paris La Défense Arena"
			            ),
	                    "name" 		=> esc_attr( get_the_title() ),
	                    "offers" 	=> array('@type'=> 'offer', 'url' => $meeting_url, 'price' => $meeting['meeting_prix'], 'priceCurrency' => 'EUR'),
	                    "startDate" => esc_attr( $meeting_date_object->format( 'Y-m-d' ) ),
	                    "url" 		=> get_the_permalink()	            	
		            );

		            $meetingCount++;
				}

	        }

	        if ( !has_term( 'vip', 'type_offre', $post_id ) && $meetingCount > 3) {

	        	$header = '<div class="wrapper-billets dropdown-list">';
				$header.= '<h2>BILLETTERIE</h2>';
	        	$header.= '<div class="dropdown__header bg-grad-green"><button class="btn btn-fullwidth">Sélectionner une date...<span class="dropdown__icon-container"><i class="plda fa-chevron-bas"></i></span></button></div>';
	        	$header.= '<ul class="list-group list-group--billets no-animation"  uk-dropdown="mode: click">';
	        }

	        $output = $header . $output;
	        $output.= '</ul></div>';

	        
	        $output.= '<script type="application/ld+json">
	        // <![CDATA[
	        ' . json_encode($jsonData) . '
	        // ]]
	        </script>';
		}

		return $output;
	}

	public static function get_produit_offres( $post_id )
	{
		$output = '';
		$offres = carbon_get_post_meta( $post_id, 'offres_repeater' );
		if ( $offres ) 
		{
			$output = '<h2>OFFRES</h2>';
			$output .= '<ul class="list-group list-group--offres">';

			foreach ($offres as $offre) {

	            $offre_icone  		= ($offre['offre_icone'])		? '<span class="' . $offre['offre_icone'] . '"></span>' : '';
	            $offre_titre  		= ($offre['offre_titre'])		? '    <h3 class="offre__titre">' . $offre['offre_titre'] . '</h3>' :'';
	            $offre_description  = ($offre['offre_description']) ? '<small>' . $offre['offre_description'] . '</small>' : '';
	            $offre_a_partir_de  = ($offre['offre_a_partir_de']) ? 'a_partir_de':'';
	            $offre_prix  		= ($offre['offre_prix'])		? sprintf('<div class="offre__prix %s">%s</div>', $offre_a_partir_de, self::formatPrice($offre['offre_prix'])) : '';
	            $offre_url  		= ($offre['offre_url'])		    ? :'#';
	            $offre_btn  		= ($offre['offre_btn_label'])	? $offre['offre_btn_label']:'RÉSERVER';

	            $output .= '<li class="list-item item--offre">';
	            $output .= '<div class="list-item__image">' . $offre_icone . '</div>';
	            $output .= '  <div class="list-item__content">';
	            $output .= 		$offre_titre;
	            $output .= 		$offre_description;
	            $output .= '  </div>';
	            $output .= '  <div class="list-item__extra">';
	            $output .= 		$offre_prix;
	            $output .= 		sprintf('<a href="%1$s" class="btn btn-link btn-xs">%2$s</a>', $offre_url, $offre_btn);
	            $output .= '  </div>';
	            $output .= '  <span class="svg--dgrad">';
	            $output .= 		inlineSvg('dgrad-rose-billetterie', true);
	            $output .= 		inlineSvg('dgrad-racing-billetterie', true);
	            $output .= '  </span>';
	            $output .= '</li>';

	        }
	        $output .= '</ul>';
		}

		return $output;
	}

	public static function get_product_card( $post_id = null )
	{
		if (! $post_id)
			return '';

		$productInstance = ProductController::getInstance();
					
		$genre_evenement 	= self::get_post_tax_terms( $post_id, 'genre_evenement', true );
		$famille_produit 	= self::get_post_tax_terms( $post_id, 'famille_produit', true );
		$type_offre			= self::get_post_tax_terms( $post_id, 'type_offre', true );
		$data_filter 	 	=  ( !empty($genre_evenement) ) ? $genre_evenement : $type_offre;
		$event_status 	 	=  (carbon_get_the_post_meta( 'sell_status' )) ? carbon_get_the_post_meta( 'sell_status' ) : 'in_sell';
		$statusArray		= $productInstance->getStatus($event_status);
		$btnEvent			= '	<div class="card__actions"><a href="%1$s" class="btn %2$s">%3$s</a></div>';

		if ( is_array($famille_produit) && array_key_first($famille_produit) == 'promo')
			$statusArray['btnActionLabel'] = 'PLONGER';

		if ( !empty($statusArray) ) {
			$btnEvent = sprintf(
				$btnEvent,
				get_permalink( $post_id ),
				$statusArray['btnManifestationClass'],
				$statusArray['btnActionLabel']
			);
		} else {
			$btnEvent = sprintf(
				$btnEvent,
				get_permalink( $post_id ),
				'btn-green-theme',
				'RÉSERVER'
			);
		}

		$output = '';
							
		$output .= '<div class="card ' . implode(' ', self::get_class_of_tax( $post_id ) ) . '" data-type="' . array_key_first($genre_evenement) . '" data-famille="' . array_key_first($famille_produit) . '" data-offre="' . array_key_first($type_offre) . '" data-js-filter="' . array_key_first($data_filter) . '">';
		$output .= '<div class="card__block-image">';
		if( has_post_thumbnail( $post_id ) ){
			$output .= ($type_offre) ? '<div class="card__offre">' . implode(' ', $type_offre) . '</div>':'';
			$output .= get_the_post_thumbnail( $post_id, 'vignette_affiche', array('class' => 'card-img-top') );
			$output .= (carbon_get_the_post_meta( 'product_alert' )) ? '<div class="icon-pastille"><span class="pastille__label">' . carbon_get_the_post_meta( 'product_alert' ) . '</span>' . inlineSvg('icon-pastille', true) . '</div>':'';
		}
		$output .= '</div>';

		$output .= '	<div class="card__body">';
		$output .= (!empty($genre_evenement)) ? '		<p class="card__category">' . implode(' ', $genre_evenement) . '</p>':'';
		$output .= '		<h3 class="card__title">' . get_the_title( $post_id ) . '</h3>';
		$output .= '		<p class="card__meta">' . self::event_display_date() . '</p>';
		$output .= '	</div>';
		$output .= $btnEvent;

		$output .= '</div>';

		return $output;

	}
	public static function get_offre_card( $post_id = null )
	{
		if (! $post_id)
			return '';
					
		$genre_evenement   = self::get_post_tax_terms( $post_id, 'genre_evenement', true );
		$temporalite_offre = self::get_post_tax_terms( $post_id, 'temporalite_offre', true );
		$type_offre		   = self::get_post_tax_terms( $post_id, 'type_offre', true );
		$data_filter 	   = ( !empty($genre_evenement) ) ? $genre_evenement : $type_offre;
		$prestationsHtml   = '	<li class="card__item">'
							.	'<span class="card-item__icon fa-stack"><i class="fas fa-circle fa-stack-2x"></i><i class="%1$s fa-stack-1x fa-inverse"></i></span>'
							.	'<span class="card-item__title">%2$s</span>'
							.	'<span class="card-item__description">%3$s</span>'
							.	'</li>';
		$prestation1 	   = carbon_get_post_meta($post_id, 'offre_prestationstar_1_description');
		$prestation2 	   = carbon_get_post_meta($post_id, 'offre_prestationstar_2_description');
		$prestation3 	   = carbon_get_post_meta($post_id, 'offre_prestationstar_3_description');

		$imageTag = $prestationsOutput = '';
		$compteurPrestations = 0;
		
		if ( $prestation1 || $prestation3 || $prestation3 ) {
			$prestationsOutput = '<ul class="card__items-row">';
			$prestationsOutput.= ($prestation1)? sprintf($prestationsHtml, 'plda fa-vip', 'TYPE D\'ESPACE', $prestation1) : '';
			$prestationsOutput.= ($prestation2)? sprintf($prestationsHtml, 'fas fa-concierge-bell', 'PRESTATIONS', $prestation2) : '';
			$prestationsOutput.= ($prestation3)? sprintf($prestationsHtml, 'fas fa-euro-sign', 'TARIF', $prestation3) : '';

			$prestationsOutput .= '</ul>';
		}

		if ( $image_fond_id = carbon_get_the_post_meta( 'product_img_bg' ) ) {
			$imageTag = wp_get_attachment_image( $image_fond_id, 'large' );
		}

		$output = '';
							
		$output .= '<div class="card ' . implode(' ', self::get_class_of_tax( $post_id ) ) . '" data-type="' . array_key_first($genre_evenement) . '" data-famille="' . array_key_first($temporalite_offre) . '" data-offre="' . array_key_first($type_offre) . '" data-js-filter="' . array_key_first($data_filter) . '">';
		$output .= '<div class="card__block-image">';
		$output .= '	<div class="card__block-pano">';
		$output .= $imageTag;
		$output .= '	</div>';
		if( has_post_thumbnail( $post_id ) ){
			$output .= get_the_post_thumbnail( $post_id, 'vignette_affiche', array('class' => 'card-img-top') );
		}
		$output .= '</div>';
		$output .= '<div class="card__block-content">';

		$output .= '	<div class="card__body">';
		$output .= '		<div class="card__tempo text-grad-gold">' . implode(' ', $temporalite_offre) . '</div>';
		$output .= '		<h3 class="card__title">' . get_the_title( $post_id ) . '</h3>';
		$output .= '	</div>';
		$output .= '	<div class="card__items">' . $prestationsOutput . '</div>';
		$output .= '	<div class="card__actions"><a href="' . get_permalink( $post_id ) . '" class="btn btn-offre">DÉCOUVRIR</a></div>';

		$output .= '</div>';
		$output .= '</div>';

		return $output;

	}


	/**
	 * Related Posts par catégorie
	 *
	 * @global array $post
	 *   WP global post.
	 * @param int $related_count
	 *   Nombre de résultats à afficher.
	 */

	public static function the_related_posts( $related_count = -1, $display_title = true ) {
	    global $post;

	    $tax 			= 'category';
	    $titre 			= 'LIRE AUSSI';
	    $modele 		= 'article-cards';
	    $data_perslide 	= '2';
	    $class 			= "articles";

	    $post_type 		= get_post_type( $post->ID );

        $related_args = array(
            'post_type' => $post_type,
            'posts_per_page' => $related_count,
            'ignore_sticky_posts' => 1,
        );
	    

	    if ( $post_type == 'produit' ) {
	        $tax 					  = 'genre_evenement';
	        $titre 					  = 'VOUS AIMEREZ AUSSI';
	        $modele 				  = 'product-cards';
	        $data_perslide 			  = '3';
	        $class 					  = 'events';
			$related_args['post__in'] = array_diff(Transients::get_transient_query_ids( 'ALL_CURRENT_EVENTS' ),array( $post->ID ));
            $related_args['orderby']  = 'post__in';

			// genre_evenement : concert/racing92 / ...

			$terms = get_the_terms( $post->ID, $tax );

			if ( empty( $terms ) ) $terms = array();
			
			$term_list = wp_list_pluck( $terms, 'slug' );

			$related_args['tax_query'] = array(
				'relation' => 'AND',
			    array(
			        'taxonomy' 	=> $tax,
			        'field' 	=> 'slug',
			        'terms' 	=> $term_list
			    )
			);

			//event or offre

	        $tax = 'famille_produit';
			
			$terms = get_the_terms(
			    $post->ID, 
			    $tax
			);

			if (    $terms
			     && !is_wp_error( $terms )
			) {
			    $term_list = wp_list_pluck( $terms, 'term_id' );

			    $related_args['tax_query'][] = array(
	                'taxonomy' => $tax,
	                'terms'    => $term_list,                                       
	            );
			}


	    }else{
            $related_args['orderby']  		= 'date';
            $related_args['order']    		= 'DESC';
            $related_args['post__not_in']   = array($post->ID);
            $related_args['post_status']    = 'publish';
		    
		    $terms = get_the_terms( $post->ID, $tax );

		    if ( empty( $terms ) ) $terms = array();
		    
		    $term_list = wp_list_pluck( $terms, 'slug' );

		    $related_args['tax_query'] = array(
		        array(
		            'taxonomy' => $tax,
		            'field' => 'slug',
		            'terms' => $term_list
		        )
		    );
	    }


	    if ( $post_type == 'page') { // home page or other page
	    	 $related_args['post_type'] = 'post';
	    	 unset($related_args['tax_query']);
	    }

	    $related = new \WP_Query( $related_args );

	    if( $related->have_posts() ):
	    ?>
	    <section class="wrapper-related wrapper-<?php echo $class ?>">
	        <div class="related__container">
	            <?php echo ($display_title)? '<h3 class="titre-categorie">' . $titre . '</h3>' : '' ?>
	            <div class="slider--related swiper-container" data-perslide="<?php echo $data_perslide ?>">
	                <div class="swiper-wrapper">
	                    <?php while( $related->have_posts() ): $related->the_post(); ?>
	                    	<div class="swiper-slide">
						    <?php if ( $post_type == 'produit') { ?>
	                        	<?php echo self::get_product_card( get_the_ID() ); ?>
	                        <?php }else{ ?>
	                        	<?php get_template_part( 'components/article', 'cards' ); ?>
	                        <?php } ?>
	                    	</div>
	                    <?php endwhile; ?>
	                </div>
	            </div>
	            <div class="swiper-buttons-nav container">
	            	<div class="swiper-button-prev"><i class="fas fa-angle-left stack-circle fz-24 plda-icon-context"></i></div>
	            	<div class="swiper-button-next"><i class="fas fa-angle-right stack-circle fz-24 plda-icon-context"></i></div>
	            </div>
	        </div>
	    </section>
	    <?php
	    endif;
	    wp_reset_postdata();
	}

	private static function formatPrice( $price ){
		if (!$price)
			return '';
		$price = str_replace(',', '.', $price);
		$priceArr = explode('.', $price);
		$price = $priceArr[0] . '<sup>&euro;';
		if (array_key_exists(1, $priceArr))
			$price .= $priceArr[1];
		$price .= '</sup>';

		return $price;

	}


	public static function get_date_text( $post_id )
	{

		$meetings = carbon_get_post_meta( $post_id, 'meeting_repeater' );
		$meetings_dates = $meetings_years = $meetings_months = array();


		// set french locale
		\Moment\Moment::setLocale('fr_FR');
		\Moment\Moment::setDefaultTimezone('Europe/Paris');

		foreach ($meetings as $k => $meeting) {

            if ( ($meeting['meeting_publication'] == "1") || ($meeting['meeting_force_publication']) ) {

            	$tmp_date = new \Moment\Moment( $meeting['meeting_date'] );

                $meetings_dates[]  = $tmp_date;
                $meetings_years[]  = $tmp_date->format( 'Y' );
                $meetings_months[] = $tmp_date->format( 'F' );

            }
        }
        
        if ( empty($meetings_dates) )
        	return false;

        if ( count($meetings_dates) === 1 )
        	return $meetings_dates[0]->format('l d F Y H[H]i');

        sort($meetings_dates);

        $distinct_years  = array_unique($meetings_years);
        $distinct_months = array_unique($meetings_months);

        if ( count($distinct_years) === 1 ) {
	        if ( count($distinct_months) === 1 ) {

        		switch ( count($meetings_dates) ) {
        			case 1:
        				return $meetings_dates[0]->format('l d F Y H[H]i');
        				break;
        			case 2:
        				return $meetings_dates[0]->format('d') . " & " . $meetings_dates[1]->format('d F Y') ;
        				break;
        			case 3:
        				return $meetings_dates[0]->format('d') . ", " . $meetings_dates[1]->format('d') . " & " . $meetings_dates[2]->format('d M Y') ;
        				break;
        			default:
        				return 'Du ' . $meetings_dates[0]->format('d') . " au " . $meetings_dates[count($meetings_dates)-1]->format('d M Y') ;
        				break;
        		}

	        }else{

        		if ( count($meetings_dates) == 2 ) {
        				return $meetings_dates[0]->format('d M') . " & " . $meetings_dates[1]->format('d M Y') ;
        		}else{
        				return 'Du ' . $meetings_dates[0]->format('d M') . " au " . $meetings_dates[count($meetings_dates)-1]->format('d M Y') ;
        		}

	        }

		}else{
    		if ( count($meetings_dates) === 2) {
    				return $meetings_dates[0]->format('d M Y') . " & " . $meetings_dates[1]->format('d M Y') ;
    		}else{
    				return 'Du ' . $meetings_dates[0]->format('d M Y') . " au " . $meetings_dates[count($meetings_dates)-1]->format('d M Y') ;
    		}
        }

	}

	public static function the_arena_google_map() {

		$instance = self::getInstance();
		$gApiKey = ( $instance->hasConfigKey( 'api_key' ) )? $instance->getConfigKey( 'api_key' ) : 'none';
		$gDestinations = ( $instance->hasConfigKey( 'destinations' ) )? $instance->getConfigKey( 'destinations' ) : '';
	?>
		<section class="map">
		  <div class="map__wrap">
		    <div
		      class="map__map js-map"
		      data-gApiKey="<?php echo $gApiKey ?>"
		      data-destinations="<?php echo esc_attr( json_encode($gDestinations) ) ?>"
		      data-lat="48.8953801"
		      data-lng="2.2296555"
		      data-address="99 Jardins de l’Arche, 92000 Nanterre, France"
		      data-title="Parvis Arena"
		      data-zoom="7"
		      >
		    </div>
		  </div>
		</section>
	    <script>
	    	document.addEventListener('DOMContentLoaded', () => {
		    	if (document.querySelector('.js-map')) {
				  window.PldaApp.map.LocationMap('.js-map');
				}
	    	})
	           //new Map({"1":{"name":"99 Jardins de l\u2019Arche, 92000 Nanterre, France","label":"Parvis Arena","latitude":"48.8953801","longitude":"2.2296555"},"2":{"name":"Parking coupole-regnault, Paris, France","label":"Parking Coupole-Regnault","latitude":"48.8597898","longitude":"2.3619364"},"3":{"name":"Grande Arche de la Defense, Paris, France","label":"La D\u00e9fense (Grande Arche)","latitude":"48.8953801","longitude":"2.2296555"},"4":{"name":"Parking villon, Paris, France","label":"Parking Villon","latitude":"48.8877092","longitude":"2.2419249"}});
	    </script>
	<?php 
	}

	public static function entry_footer()
	{

		// Hide category and tag text for pages.
		if ('post' === get_post_type()) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list(esc_html__(', ', 'awps'));
			if ($categories_list && self::categorized_blog()) {
				printf('<span class="cat-links">'.esc_html__('Posted in %1$s', 'awps').'</span>', $categories_list); // WPCS: XSS OK.
			}
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list('', esc_html__(', ', 'awps'));
			if ($tags_list) {
				printf('<span class="tags-links">'.esc_html__('Tagged %1$s', 'awps').'</span>', $tags_list); // WPCS: XSS OK.
			}
		}
		if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
			echo '<span class="comments-link">';
			/* translators: %s: post title */
			comments_popup_link(sprintf(wp_kses(__('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'awps'), array('span' => array('class' => array()))), get_the_title()));
			echo '</span>';
		}
		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__('Edit %s', 'awps'),
				the_title('<span class="screen-reader-text">"', '"</span>', false)
			),
			'<span class="edit-link">',
			'</span>'
		);
	}

	public static function categorized_blog()
	{
		if (false === ($all_the_cool_cats = get_transient('awps_categories'))) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories(array(
				'fields' => 'ids',
				'hide_empty' => 1,
				// We only need to know if there is more than one category.
				'number' => 2,
			));
			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count($all_the_cool_cats);
			set_transient('awps_categories', $all_the_cool_cats);
		}
		if ($all_the_cool_cats > 1) {
			// This blog has more than 1 category so awps_categorized_blog should return true.
			return true;
		} else {
			// This blog has only 1 category so awps_categorized_blog should return false.
			return false;
		}
	}

	public function category_transient_flusher()
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		delete_transient('awps_categories');
	}
}
