<?php

namespace Plda\Setup;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Setup
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register()
    {
        add_action( 'after_setup_theme', array( $this, 'setup' ) );
        add_filter( 'wp_calculate_image_sizes', array($this, 'plda_custom_responsive_image_sizes'), 10 , 2);
        add_action( 'after_setup_theme', array( $this, 'content_width' ), 0 );
        add_action( 'after_setup_theme', array($this, 'cbfdBoot') );
        add_action( 'wp_head', array($this,'define_ajaxurl') );

        add_action( 'init', array($this, 'plda_custom_post_status') );
        //add_action( 'admin_footer-post.php', array($this, 'plda_post_display_custom_post_status') );
        add_filter('wp_insert_post_data', array($this,'custom_post_status_hidden_update'), 10, 2);

        add_action( 'admin_footer-edit.php', array($this, 'plda_hidden_status_into_inline_edit') );
        add_filter( 'display_post_states', array( $this, 'plda_in_title_display_status_label') );
        add_action( 'pre_get_posts', array($this, 'plda_include_hidden_in_search_query') );

        add_action( 'type_offre_edit_form_fields', array($this, 'add_term_description_wysiwyg'), 10, 2 );
        remove_filter( 'pre_term_description', 'wp_filter_kses' );
        remove_filter( 'term_description', 'wp_kses_data' );
    }
    

    public function setup()
    {
        /*
         *  multilingual theme
         */
        load_theme_textdomain( 'plda', get_template_directory() . '/languages' );

        /*
         * Default Theme Support options better have
         */
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'customize-selective-refresh-widgets' );
        
        /**
        * Add woocommerce support and woocommerce override
        */
        // add_theme_support( 'woocommerce' );

        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );

        add_theme_support( 'custom-background', apply_filters( 'awps_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        ) ) );

        /*
         * Activate Post formats if you need
         */
        add_theme_support( 'post-formats', array( 
            'aside',
            'gallery',
            'link',
            'image',
            'quote',
            'status',
            'video',
            'audio',
            'chat',
        ) );

        /*
         * Gutenberg option for Large block
         */

        add_theme_support( 'align-wide' );
        add_theme_support( 'align-full' );
        add_theme_support( 'responsive-embeds' );

        /*
         * Custom Image format
         */

        add_image_size( 'vignette_affiche', 350, 494, true );
        add_image_size( 'vignette_affiche_@x2', 700, 988, true );
        add_image_size( 'vignette_affiche_@x3', 1050, 1482, true );

        add_image_size( 'vignette_news', 356, 200, true );
        add_image_size( 'vignette_news_@x2', 445, 250, true );
        add_image_size( 'vignette_news_@x3', 1068, 600, true );
    }

    /* 
     * ADD CUSTOM RESPONSIVE IMAGE SIZES
    */

    public function plda_custom_responsive_image_sizes($sizes, $size) {
      $width = $size[0];
      // blog posts
      if ( is_singular( 'post' ) ) {}
        // blog
      if ( is_home() ) {
        // half width images - medium size
        if ( $width === 1068 ) {
          return '(max-width: 768px) 100vw, (max-width: 900px) 50vw, 420px)';
        }
        // default to return if condition is not met
        return '(max-width: 1100px) 100vw, 1100px';
      }
      // default to return if condition is not met
      return '(max-width: ' . $width . 'px) 100vw, ' . $width . 'px';
    }

    /*
        Define a max content width to allow WordPress to properly resize your images
    */
    public function content_width()
    {
        $GLOBALS[ 'content_width' ] = apply_filters( 'content_width', 1440 );
    }

    /**
     * Add 'Caché' post status.
     */
    public function plda_custom_post_status(){

        register_post_status( 'hidden', array(
            'label'                     => 'Caché',
            'public'                    => true,
            'publicly_queryable'        => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Caché <span class="count">(%s)</span>', 'Cachés <span class="count">(%s)</span>' ),
        ) );
    }

    /* modifciation du statut dans la page edit.php
    * bug gutenberg qui empeche la gestion dans edit page : 
    * @link https://github.com/imath/wp-statuses
    * piste pour gestion en hook https://wordpress.org/support/topic/post-status-transition-with-quick-edit-bulk-action/
    * https://github.com/Automattic/Edit-Flow/blob/master/blocks/src/custom-status/block.js
    */
    public function custom_post_status_hidden_update($data, $postarr) {
      
      if($data['post_status'] == 'publish' && get_post_status($postarr['ID']) == 'hidden') {
        $data['post_status'] = 'hidden';
      }
      
      return $data;
    }

    public function plda_post_display_custom_post_status(){

        global $post;
        $complete = '';
        $label = '';

        if($post->post_type == 'produit') {

            if ( $post->post_status == 'hidden' ) {
                $complete = ' selected=\"selected\"';
                $label    = 'Caché';
            }

            $script = '


               jQuery(document).ready(function($){
                   $("select#post_status").append("<option value=\"hidden\" '.$complete.'>Caché</option>");

                   if( "{$post->post_status}" == "hidden" ){
                        $("span#post-status-display").html("$label");
                        $("input#save-post").val("Sauver caché");
                   }
                   var jSelect = $("select#post_status");

                   $("a.save-post-status").on("click", function(){

                        if( jSelect.val() == "hidden" ){

                            $("input#save-post").val("Sauver Caché");
                        }
                   });
              });


            ';

            echo '<script type="text/javascript">' . $script . '</script>';
        }

    }

     // affichage du statut dans la liste des evenements
    public function plda_hidden_status_into_inline_edit() { 
      echo "<script>
            jQuery(document).ready( function() {
              jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"hidden\">Caché</option>' );
            });
            </script>";
    }


     // affichage du statut avec le titre de l'evenement
    public function plda_in_title_display_status_label( $statuses ) {
      global $post;
      if( get_query_var( 'post_status' ) != 'hidden' ){ //pas sur les pages de recherche adhoc
        if( $post && $post->post_status == 'hidden' ){ 
          return array('Caché');
        }
      }
      return $statuses;
    }

    /**
     * Exclude hidden events from query search
     */
    public function plda_include_hidden_in_search_query( $query ) {
      if( $query->is_main_query() && ! is_admin() && $query->is_search() ) {
        $query->set('post_status', array('publish', 'hidden') );
      }
      if( $query->is_main_query() && is_home() ) {
        $query->set('post_status', array('publish') );
      }

    }     


    /**
     * Display advanced TinyMCE editor in type_offre taxonomy page
     */
    public function add_term_description_wysiwyg($term, $taxonomy){
        if ( $taxonomy == 'type_offre' ){
        ?>
        <tr valign="top">
            <th scope="row">Description</th>
            <td>
                <?php wp_editor(html_entity_decode($term->description), 'description', array('media_buttons' => false)); ?>
                <script>
                    jQuery(window).ready(function(){
                        jQuery('label[for=description]').parent().parent().remove();
                    });
                </script>
            </td>
        </tr>
        <?php
        }
    } 
    
    public static function cbfdBoot()
    {
        \Carbon_Fields\Carbon_Fields::boot();
    }
    
    public static function define_ajaxurl()
    {
       echo '<script type="text/javascript">
               var ajaxurl = "' . admin_url('admin-ajax.php') . '";
             </script>';
    }
}
