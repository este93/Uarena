<?php
/**
 * AjaxExchange Class
 *
 * @package plda
 */

namespace Plda\Api;


use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Exception\RuntimeException;

/**
 * AjaxExchange API Class
 */
class AjaxExchange
{

	use ConfigTrait;



	/**
	 * Constructor
	 */
	public function __construct(ConfigInterface $config = null)
	{

        $config = ConfigFactory::create( ABSPATH . '../project_settings.php' );

        $this->processConfig( $config );

	}

	public function register()
	{
		// Ajax loadmore on HP
		add_action( 'wp_enqueue_scripts', array($this, 'plda_load_more_article_scripts') );
		add_action('wp_ajax_loadmore_articles', array($this, 'plda_loadmore_articles_ajax_handler') ); 
		add_action('wp_ajax_nopriv_loadmore_articles', array($this, 'plda_loadmore_articles_ajax_handler') ); 

		// Contact Offre VIP Ajax submission
		add_action('wp_ajax_offre_vip_contact', array($this, 'plda_offre_vip_contact_action') ); 
		add_action('wp_ajax_nopriv_offre_vip_contact', array($this, 'plda_offre_vip_contact_action') ); 

	}

	/**
	 * Enqueue script for 'load more articles' news page
	 *
	 */
	public function plda_load_more_article_scripts() {
 
		global $wp_query; 

		if (is_home())
	 	{
	 		// register main script but not enqueue it yet
	 		wp_register_script( 'plda_loadmore_articles', get_stylesheet_directory_uri() . '/assets/js/ajax.js' );
	 	 
	 		// php parameters to loadmorearticles.js script
	 		// wp_localize_script() : define javascript variables
	 		wp_localize_script( 'plda_loadmore_articles', 'plda_loadmore_articles_params', array(
	 			'ajaxurl' => admin_url( 'admin-ajax.php' ), // WordPress AJAX
	 			'posts' => json_encode( $wp_query->query_vars ), // data
	 			'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
	 			'max_page' => $wp_query->max_num_pages
	 		) );
	 	 
	 	 	wp_enqueue_script( 'plda_loadmore_articles' );
	 	}
	}

	function plda_loadmore_articles_ajax_handler(){
	 
		// get arguments for the query
		$args = json_decode( stripslashes( $_POST['query'] ), true );
		$args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
		$args['post_status'] = 'publish';
	 
		// it is always better to use WP_Query but not here
		query_posts( $args );
	 
		if( have_posts() ) :
	 
			// run the loop
			while( have_posts() ): the_post();
	 
				get_template_part( 'components/article', 'cards' );	 
	 
			endwhile;
		else:
			echo "EOF";
		endif;
        wp_die();
	}

	function plda_offre_vip_contact_action(){
	 
		// check the nonce
		if ( check_ajax_referer( 'offre_vip_form_' . $_POST['post_id'], 'nonce', false ) == false ) {
		    wp_send_json_error();
		}

		$to = 'newsletter@carredelune.com';

		$subject 	 = $_POST['subject'];
		$message 	 = 'nom : ' . $_POST['nom'];
		$message 	.= ' / prenom : ' . $_POST['prenom'];
		$message 	.= ' / societe : ' . $_POST['societe'];
		$message 	.= ' / email : ' . $_POST['email'];
		$message 	.= ' / telephone : ' . $_POST['telephone'];

        if ( $this->hasConfigKey( 'Plda/contacts/contact_offre_vip' ) ) {
            $to = $this->getConfigKey( 'Plda/contacts/contact_offre_vip' );
        }
        if ( $this->hasConfigKey( 'Plda/contacts/message_success' ) ) {
            $message_success = $this->getConfigKey( 'Plda/contacts/message_success' );
        }

		if( wp_mail($to, $subject, $message) ){
			wp_send_json_success( $message_success );
		} else {
			wp_send_json_error();
		}
        wp_die();
	}

}
