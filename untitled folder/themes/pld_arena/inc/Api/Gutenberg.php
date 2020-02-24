<?php
/**
 * Build Gutenberg Blocks
 *
 * @package plda
 * @link https://developer.wordpress.org/block-editor/
 * @link https://capitainewp.io/formations/wordpress-creer-blocs-gutenberg/add-theme-support/
 * @link https://www.billerickson.net/full-and-wide-alignment-in-gutenberg/
 */

namespace Plda\Api;

use Carbon_Fields\Block;
use Carbon_Fields\Field;

/**
 * Customizer class
 */
class Gutenberg
{
	/**
	 * Register default hooks and actions for WordPress
	 *
	 * @return WordPress add_action()
	 */
	public function register() 
	{
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'gutenberg_init' ) );

		add_action( 'init', array( $this, 'gutenberg_enqueue' ) );

		add_action( 'enqueue_block_assets', array( $this, 'gutenberg_css_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'gutenberg_js_assets' ) );

		add_action( 'carbon_fields_register_fields', array($this,'gutenberg_services_block') );
		add_action( 'carbon_fields_register_fields', array($this,'gutenberg_itineraire_block') );
		add_action( 'carbon_fields_register_fields', array($this,'gutenberg_plan_salle_block') );

	}

	/**
	 * Custom Gutenberg settings
	 * @return
	 */
	public function gutenberg_init()
	{
		add_theme_support( 'gutenberg', array(
			// Theme supports responsive video embeds
			'responsive-embeds' => true,
            // Theme supports wide images, galleries and videos.
            'wide-images' => true,
		) );

		add_theme_support(
		    'editor-gradient-presets',
		    array(
		        array(
		            'name'     => __( 'Vivid cyan blue to vivid purple', 'themeLangDomain' ),
		            'gradient' => 'linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)',
		            'slug'     => 'vivid-cyan-blue-to-vivid-purple'
		        ),
		        array(
		            'name'     => __( 'Vivid green cyan to vivid cyan blue', 'themeLangDomain' ),
		            'gradient' => 'linear-gradient(135deg,rgba(0,208,132,1) 0%,rgba(6,147,227,1) 100%)',
		            'slug'     =>  'vivid-green-cyan-to-vivid-cyan-blue',
		        ),
		        array(
		            'name'     => __( 'Light green cyan to vivid green cyan', 'themeLangDomain' ),
		            'gradient' => 'linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%)',
		            'slug'     => 'light-green-cyan-to-vivid-green-cyan',
		        ),
		        array(
		            'name'     => __( 'Luminous vivid amber to luminous vivid orange', 'themeLangDomain' ),
		            'gradient' => 'linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%)',
		            'slug'     => 'luminous-vivid-amber-to-luminous-vivid-orange',
		        ),
		        array(
		            'name'     => __( 'Luminous vivid orange to vivid red', 'themeLangDomain' ),
		            'gradient' => 'linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%)',
		            'slug'     => 'luminous-vivid-orange-to-vivid-red',
		        ),
		    )
		);
		
		// add_theme_support( 'editor-color-palette', array(
		// 	array(
		// 		'name'  => __( 'White', 'plda' ),
		// 		'slug'  => 'white',
		// 		'color' => '#ffffff',
		// 	),
		// 	array(
		// 		'name'  => __( 'Black', 'plda' ),
		// 		'slug'  => 'black',
		// 		'color' => '#333333',
		// 	),
		// 	array(
		// 		'name'  => __( 'Gold', 'plda' ),
		// 		'slug'  => 'gold',
		// 		'color' => '#FCBB6D',
		// 	),
		// 	array(
		// 		'name'  => __( 'Pink', 'plda' ),
		// 		'slug'  => 'pink',
		// 		'color' => '#FF4444',
		// 	),
		// 	array(
		// 		'name'  => __( 'Grey', 'plda' ),
		// 		'slug'  => 'grey',
		// 		'color' => '#b8c2cc',
		// 	),
		// ) );
	}

	/**
	 * Create Gutenberg blocks from CarbonFields
	 * @return
	 */
	public function gutenberg_services_block()
	{
		Block::make( __( 'Liste des Services' ) )
    		->set_description( __( 'Affichage de la liste des services' ) )
		    ->add_fields( array(
		        Field::make( 'html', 'plda_offre_services_placeholder' )
		            ->set_html( '<p>Affichage ici de la liste des services d\'une offre</p>' ),
		    ) )
    		->set_icon( 'screenoptions' )
    		->set_preview_mode( false )
    		->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
    			global $post;
    			$prestationsOutput = '';
    			$prestations = carbon_get_post_meta($post->ID, 'product_prestations');
    			
    			if ( $prestations ) {
    				$prestationsOutput = '<ul class="icons-grid">';
    				foreach ($prestations as $key => $prestation) {
    					$prestationsOutput .= '<li class="grid__item">';
    					$prestationsOutput .= '<span class="grid-item__icon fa-stack"><i class="fas fa-circle fa-stack-2x"></i><i class="' . $prestation['offre_prestation_icone'] . ' fa-stack-1x fa-inverse"></i></span>';
    					$prestationsOutput .= '<span class="grid-item__title">' . $prestation['offre_prestation_label'] . '</span>';
    					$prestationsOutput .= '</li>';

    				}
    				$prestationsOutput .= '</ul>';
		        ?>

		        <div class="block block--prestations-offre">
		            <div class="block__heading">
		                <h3 class="text-center titre-categorie">DÉTAIL DE L'OFFRE</h3>
		            </div><!-- /.block__heading -->

		            <div class="block__content">
		                <?php echo $prestationsOutput; ?>
		            </div><!-- /.block__content -->
		        </div><!-- /.block -->

		        <?php
		    	}
		    } );
	}

	/**
	 * Create Gutenberg blocks from CarbonFields
	 * @return
	 */
	public function gutenberg_plan_salle_block()
	{
		Block::make( __( 'Plan de salle' ) )
    		->set_description( __( 'Plan interactif de la salle' ) )
		    ->add_fields( array(
		        Field::make( 'complex', 'plan_de_salle', 'Eléménts du plan de salle' )
                    ->set_layout('tabbed-horizontal')
                    ->add_fields([
                    	Field::make('text', 'pds_zone_titre', 'Titre de la zone')
                    		->set_width(50),
                    	Field::make('image', 'pds_zone_visuel', 'Visuel de la zone')
                    		->set_width(50),
                    	Field::make('rich_text', 'pds_zone_description', 'Description de la zone'),
                    ]),
		    ) )
    		->set_icon( 'location-alt' )
    		->set_preview_mode( false )
    		->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
    			global $post;

    			$list_zones = $list_images = $list_descriptions = '';

    			foreach( $fields['plan_de_salle'] as $zone) {
    				$list_zones .= '<li><a href="#">' . esc_html( $zone['pds_zone_titre'] ) . '</a></li>';
    				if ( $imageArray = wp_get_attachment_image_src( $zone['pds_zone_visuel'], 'large' ) )
    					$list_images.= '<li class="conteneur_image">'.wp_get_attachment_image( $zone['pds_zone_visuel'], 'large' ) .'</li>';
    				else
    					$list_images.= '<li></li>';
    				$list_descriptions .= '<li><div>' . apply_filters( 'the_content', $zone['pds_zone_description'] ) . '</div></li>';
    			}

		        ?>

		        <div class="block block--plan-salle">
			            <div class="block__heading">
			            	<p class="entete-cell">ZONE : </p>
			                <ul uk-switcher="connect: .in-relation-items; animation: uk-animation-fade"><?php echo $list_zones; ?></ul>
			            </div><!-- /.block__heading -->

			            <div class="block__image">
			                <ul class="uk-switcher in-relation-items"><?php echo $list_images; ?></ul>
			            </div><!-- /.block__image -->

			            <div class="block__content">
			                <ul class="uk-switcher in-relation-items"><?php echo $list_descriptions; ?></ul>
			            </div><!-- /.block__content -->
		        </div><!-- /.block -->

		        <?php
		    	});
	}

	/**
	 * Create Gutenberg itineraire blocks (shortcode)
	 * @return
	 */
	public function gutenberg_itineraire_block()
	{
		Block::make( __( 'Map Itineraire' ) )
    		->set_description( __( 'Affichage de la liste carte itineraire' ) )
		    ->add_fields( array(
		        Field::make( 'html', 'plda_itineraire_block' )
		            ->set_html( '<h3 class="dashicons-before dashicons-location" style="border: 1px dashed;background-color: #fafafa;padding: 0.5rem;"> Affichage du plan d\'accès à l\'Arena</h3>' ),
		    ) )
    		->set_icon( 'location' )
    		->set_preview_mode( false )
    		->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
    			global $post;

		        $oConfigPlda 	= \Plda\Api\ConfigPlda::getInstance();
		        $gApi 			= $oConfigPlda->get_key('Plda/googleMap/api_key_dev_fjo');
		        $gDeplacements 	= $oConfigPlda->get_key('Plda/googleMap/deplacements');
		        $gLat 			= $oConfigPlda->get_key('Plda/googleMap/coordonnees_uarena/lat');
		        $gLng 			= $oConfigPlda->get_key('Plda/googleMap/coordonnees_uarena/lng');

		        ?>

		        <div class="block block--map alignwide" data-mapcanvas="#map_canvas" data-gapikey="<?php echo $gApi; ?>" data-deplacements="<?php echo esc_attr( json_encode($gDeplacements) ) ?>" data-lat="<?php echo $gLat ?>" data-lng="<?php echo $gLng ?>">
		            <div class="block__inner">
			            	<div class="block__header">
				            	<h2>GÉNÉREZ VOTRE ITINERAIRE</h2>
				            	<h3>EN FONCTION DE VOTRE MODE DE TRANSPORT</h3>
								<div class="tabs" id="transports">
								  <!-- TAB 1 -->
								  <input type="radio" name="transportMode" id="tab-01"  data-transport="subway" value="TRANSIT" selected />
								  <label for="tab-01">
								      <i class="fas fa-subway"></i>
								  </label>
								  <!-- TAB 2 -->
								  <input type="radio" name="transportMode" id="tab-02"  data-transport="taxi" value="DRIVING" />
								  <label for="tab-02">
								      <i class="fas fa-taxi"></i>
								  </label>
								  <!-- TAB 3 -->
								  <input type="radio" name="transportMode" id="tab-03"  data-transport="car" value="DRIVING" />
								  <label for="tab-03">
								      <i class="fas fa-car"></i>
								  </label>
								  <!-- TAB 4 -->
								  <input type="radio" name="transportMode" id="tab-04"  data-transport="motorcycle" value="DRIVING" />
								  <label for="tab-04">
								      <i class="fas fa-motorcycle"></i>
								  </label>
								  <!-- TAB 5 -->
								  <input type="radio" name="transportMode" id="tab-05" data-transport="walking" value="WALKING" checked />
								  <label for="tab-05">
								      <i class="fas fa-walking"></i>
								  </label>
								  <!-- TAB 6 -->
								  <input type="radio" name="transportMode" id="tab-06" data-transport="bicycle" value="BICYCLING" />
								  <label for="tab-06">
								      <i class="fas fa-bicycle"></i>
								  </label>
								  <!-- indicator -->
								  <div class="nav-selected-bg"></div>
								</div>
								<div class="row form-transport">
									<div class="col-sm-6">
										<label>ADRESSE DE DÉPART</label>
										<div class="uk-position-relative">
								            <a href="" class="uk-form-icon uk-form-icon-flip js_go-search"><i  class="plda fa-search"></i></a>
											<input type="text" name="routeFrom" id="routeFrom" class="uk-input routeFrom" placeholder="SAISISSEZ UNE ADRESSE" />
								        </div>
									</div>
									<div class="col-sm-6">
										<label>ADRESSE D'ARRIVÉE</label>
										<select class="plda-select routeTo" name="routeTo" id="routeTo">
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<div class="js-map" id="map_canvas"></div>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<div class="directions-display" id="directions"></div>
								</div>
							</div>
		            </div><!-- /.block__inner -->
		        </div><!-- /.block -->

		        <?php
		    } );
	}

	/**
	 * Enqueue scripts and styles of your Gutenberg blocks
	 * @return
	 */
	public function gutenberg_enqueue()
	{
		//wp_register_script( 'gutenberg-plda-script', get_template_directory_uri() . '/assets/js/gutenberg.js', array( 'wp-blocks', 'wp-element', 'wp-editor' ) );

		// register_block_type( 'gutenberg-plda/plda-cta', array(
		// 	'editor_script' => 'gutenberg-plda-script', // Load script in the editor
		// ) );
	}

	/**
	 * Enqueue styles of your Gutenberg blocks in the editor
	 * @return
	 */
	public function gutenberg_css_assets()
	{
		//wp_enqueue_style( 'gutenberg-plda-style', get_template_directory_uri() . '/assets/css/gutenberg.css', null );
	}
	/**
	 * Enqueue scripts of your Gutenberg blocks in the editor
	 * @return
	 */
	public function gutenberg_js_assets()
	{
		wp_register_script( 'gutenberg-plda-script', get_template_directory_uri() . '/assets/js/gutenberg.js', array( 'wp-blocks', 'wp-element', 'wp-editor' ) );
	    wp_enqueue_script( 'gutenberg-plda-script' );
		wp_enqueue_style( 'gutenberg-plda-style', get_template_directory_uri() . '/assets/css/gutenberg.css', null );
	}
}