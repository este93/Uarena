<?php 
/**
 * @package  PldaPlugin
 */
namespace Plda\Custom;

use Plda\Custom\Transients;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
* 
*/
class CarbonFieldsController
{

	public function register()
	{

		add_action( 'after_setup_theme', array($this, 'cbfdBoot'));

		add_action('carbon_fields_register_fields', array($this, 'createFields'));
        add_filter( 'carbon_fields_association_field_options_slider_news_hp_post_produit', array($this, 'filter_hp_slider_choice_list') );

	}

    public static function createFields()
    {
        $this->createFieldsArticle();
        $this->createFieldsPage();
        $this->createFieldsFlashInfo();
        $this->createFieldsTaxonomy();
        $this->createThemeOptionsPage();
    }

   	public static function cbfdBoot()
    {
		\Carbon_Fields\Carbon_Fields::boot();
    }

    private static function createThemeOptionsPage()
    {

        Container::make( 'theme_options', __( 'PLDA Options' ) )
            ->set_icon( "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='15' viewBox='0 0 471 355'%3E%3Cg fill='%2316FF26' fill-rule='nonzero'%3E%3Cpath d='M0 202C0 117.498 57.305 49 128 49v306C57.305 355 0 286.568 0 202'/%3E%3Cpath d='M124 177.489C124 79.409 206.874 0 309 0v355c-102.17 0-185-79.542-185-177.511'/%3E%3Cpath d='M306 189c0-90.04 73.898-163 165-163v326c-91.168 0-165-72.982-165-163'/%3E%3C/g%3E%3C/svg%3E" )
            ->add_fields( array(
                Field::make( 'association', 'slider_news_hp', 'Contenus du slider HP (limité à 7)' )
                    ->set_max( 7 )
                    ->set_types( array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'produit',
                        ),
                        array(
                            'type'      => 'post',
                            'post_type' => 'post',
                        )
                    ) )
            ) );

    }

    /**
     * Function to filter posts in hp slider config in theme page options 
     * it returns an array of product ID's
     * 
     * @param query_arguments array 
     * @return array of query_arguments
     */

    public function filter_hp_slider_choice_list( $query_arguments ) {

        $idsOfProducts = Transients::get_transient_query_ids( 'ALL_CURRENT_EVENTS' );

        $query_arguments['post__in'] = ((!isset($idsOfProducts) || empty($idsOfProducts)) ? array(0) : $idsOfProducts);

        return $query_arguments;
    }

    private static function createFieldsTaxonomy()
    {

        Container::make( 'term_meta', __( 'Page service associée' ) )
            ->where( 'term_taxonomy', '=', 'plda_service' )
            ->add_fields( array(
                Field::make( 'text', 'service_page_url', 'URL de la page service associée' ),
            ) );

    }

    public static function get_service_terms()
    {
          $categories = get_terms( 'plda_service', 'orderby=name&hide_empty=0' );
            $cats = array();

            if ( ! empty( $categories ) && ! is_wp_error( $categories ) )
              foreach ( $categories as $cat ) 
                  $cats[$cat->term_id] = esc_html( $cat->name );

            return $cats;
    }

    private static function createFieldsArticle()
    {

        Container::make('post_meta', 'Compléments article')
            ->where('post_type', '=', 'post')
            ->add_fields([
                Field::make('text', 'article_subtitle', 'Sous-titre'),
                Field::make('date_time', 'article_date', 'Date de l\'article')
                    ->set_width(50),
                Field::make('image', 'article_img_bg', 'Image fond article'),
                Field::make( "multiselect", "article_services", "Services associés" )
                    ->add_options( array( __CLASS__, 'get_service_terms' ) ),
            ]);

    }

    private static function createFieldsFlashInfo()
    {
        $oConfigPlda = \Plda\Api\ConfigPlda::getInstance();
        $icones = is_array($oConfigPlda->get_key('Plda/icones/flashinfo_icones'))? $oConfigPlda->get_key('Plda/icones/flashinfo_icones') : array();

        Container::make('post_meta', 'Paramètres du Flash Info')
            ->where('post_type', '=', 'flashinfo')
            ->add_fields([
                Field::make('text', 'flashinfo_description', 'Description courte'),
                Field::make('date', 'flashinfo_date_debut', 'Date de début d\'affichage')
                    ->set_width(50),
                Field::make('date', 'flashinfo_date_fin', 'Date de suppression du flash')
                    ->set_width(50),
                Field::make( 'radio', 'flashinfo_couleur', 'Couleur du bandeau' )
                    ->set_options( array(
                        'vert' => 'vert',
                        'violet' => 'violet',
                    ) ),
                Field::make( 'complex', 'flashinfo_icones' )
                    ->set_layout('tabbed-horizontal')
                    ->add_fields( 'icone', array(
                        Field::make( 'text', 'flashinfo_icone_url', 'Url de destination' ),
                        Field::make( 'select', 'flashinfo_icone_svg', 'Icone' )
                            ->set_options(array(''=>'Choisissez...') + $icones),
                    ) )
            ]);

    }
    private static function createFieldsPage()
    {
        $oConfigPlda = \Plda\Api\ConfigPlda::getInstance();
        $icones = is_array($oConfigPlda->get_key('Plda/icones/entete_pages'))? $oConfigPlda->get_key('Plda/icones/entete_pages') : array();

        Container::make('post_meta', 'Options de page')
            ->where('post_type', '=', 'page')
            //->where('post_template', '=', 'template-offres-vip.php')
            ->add_fields([
                Field::make('select', 'page_filtre_offre_init', 'Choix du filtre type offre VIP par défaut')
                    ->add_options( 'Plda\Custom\CarbonFieldsController::liste_offres_vip_getter_function' ),
                Field::make('select', 'page_filtre_icone_header', 'Icone associée à la page')
                    ->set_options(array(''=>'Choisissez...') + $icones),
            ]);

    }
    public static function liste_offres_vip_getter_function() {
        $optionList = array();
        $taxonomy_name = 'type_offre';
        $term = get_term_by( 'slug', 'vip', $taxonomy_name ); // get parent term 'VIP'
        $termchildren = get_term_children($term->term_id, $taxonomy_name); // get VIP children
        if((sizeof($termchildren)>0)) {
            $optionList[""] = 'Aucune option';
            foreach ( $termchildren as $child ) {
                $childTerm = get_term_by( 'id', $child, $taxonomy_name );
                $optionList[$childTerm->slug] = $childTerm->name;
            }
        }
        return $optionList;
    }
}