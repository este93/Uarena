<?php
/**
 * check if a current page has children, if so display them, if not display all pages on the same level as current page 
 *
 */

namespace Plda\Setup\Shortcodes;

use BrightNucleus\Config\ConfigInterface as Config;
use BrightNucleus\Config\ConfigTrait;


if ( ! class_exists( 'ChildPagesMenu' ) ) {

	class ChildPagesMenu {

		use ConfigTrait;

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct( Config $config ) 
		{
			
			$this->processConfig( $config );

			// Registers the shortcode in WordPress
			add_shortcode( 'plda_child_page_menu', array( $this, 'outputMenu' ) );
			add_filter('nav_menu_css_class' , array($this, 'plda_selected_nav_class') , 10 , 2);
			add_filter('wp_list_pages', array( $this, 'plda_list_pages_filter' ) );
			add_filter( 'nav_menu_link_attributes', array($this, 'add_data_attribute_to_menu_anchors'), 10, 4 );

		}

		/**
		 * Shortcode output : version menu spécifique
		 *
		 * @since 1.0.0
		 */
		public static function outputMenu( $atts, $content = null ) {

			global $post;

			extract(shortcode_atts(array( 'menu' => null, 'class' => null ), $atts));

			if (!$menu)
				return $this->outputListPages( $atts, $content );
			else{
				return '<div class="modal-button-wrapper subnav-widget hidden-up-sm"><a href="#" class="back-rubbon"><i class="fas fa-angle-left"></i> RETOUR</a><button class="btn btn-outline-white" uk-toggle="target: #modal-nav; animation: uk-animation-slide-top" type="button">' . $menu . '<span><i class="plda fa-chevron-bas"></i></span></button></div>
				' . wp_nav_menu( 
					array( 
						'menu' => $menu, 
						'container_class' 	=> 'wrapper-filters display-block-up-sm nav-child', 
						'menu_class' 		=> 'sub-nav nav-filter', 
						'items_wrap'      	=> '<!-- Modal nav -->
												<div id="modal-nav" class="uk-flex-top js-close-on-click" uk-modal>
												    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-top">
												        <h2 class="uk-modal-title">' . $menu . '</h2>
												        <ul class="">%3$s</ul>
												    </div>
												</div>
												<ul id="%1$s" class="%2$s">%3$s<li class="nav_indicator"></li></ul>',
						'fallback_cb'	    => '__return_false',
						'echo' 				=> false 
					) 
				);
			}

		}

		/**
		 * Add 'selected' class to current post for navigation style
		 */

		public function plda_list_pages_filter($output) {
		  $output = str_replace('current_page_item', 'current_page_item selected', $output);
		  return $output;
		}


		/**
		 * Add data-postid attribute to a tag  for ajax navigation
		 */
		function add_data_attribute_to_menu_anchors( $atts, $item, $args, $depth ) {
		    $atts['data-postid'] 	= $item->object_id;
		    $atts['data-posttype'] 	= $item->object;
		 
		    return $atts;
		}


		/**
		 * Add 'selected' class to current post for navigation style
		 */

		public function plda_selected_nav_class ($classes, $item) {
		  if (in_array('current-menu-item', $classes) ){
		    $classes[] = 'selected';
		  }
		  return $classes;
		}

		/**
		 * Shortcode output : version menu List pages
		 *
		 */

		public static function outputListPages( $atts, $content = null ) {

			global $post;
			$output = '';

			if ( $post->post_parent ) {
			    $children = wp_list_pages( array(
			        'title_li' => '',
			        'child_of' => $post->post_parent,
			        'echo'     => 0
			    ) );
			} else {
			    $children = wp_list_pages( array(
			        'title_li' => '',
			        'child_of' => $post->ID,
			        'echo'     => 0
			    ) );
			}
			 
			if ( $children ) :
				$output  = '<ul class="sub-nav nav-filter">';
			    $output .= $children;
				$output .= '<li class="nav_indicator"></li></ul>';
				$output  = '<div class="wrapper-filters nav-child">' . $output . '</div>';
			endif;

			return $output;

		}

		/**
		 * Shortcode output : version child page
		 *
		 */

		public static function outputChildPages( $atts, $content = null ) {

			global $post;

			// Extract shortcode attributes 
			extract(shortcode_atts(array(), $atts));

			$output = '<ul class="sub-nav nav-filter">';

			$base_args = array(
				'hierarchical' => 0
			);
			if (plda_has_children( $post->ID )):
				$args = array(
					'child_of' => $post->ID,
					'parent' => $post->ID
				);
			else:
				if (plda_is_top_level( $post->ID )):
					$args = array(
						'parent' => $post->post_parent
					);
				else:
					$args = array(
						'parent' => 0
					);
				endif;
			endif;

			$args = array_merge($base_args, $args);

			$pages = get_pages($args);

			if (!$pages || empty($pages))
				return '';

			foreach ($pages as $page):
				$class = ($page->ID == $post->ID)? "selected":"";
				$output .= '<li class="' . $class . '"><a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a></li>';
			endforeach;

			$output .= '<li class="nav_indicator"></li></ul>';

			// Return output
			return '<div class="wrapper-filters nav-child">' . $output . '</div>';

		}

	}

}