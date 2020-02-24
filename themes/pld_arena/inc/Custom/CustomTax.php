<?php

namespace Plda\Custom;

/**
 * Custom Tax
 * Define mcustom Taxonomies
 * @link https://wordpress.stackexchange.com/questions/20043/inserting-taxonomy-terms-during-a-plugin-activation/181242
 */
class CustomTax
{

	public $taxonomies = array();
	public $terms      = array();

	/**
     * register default hooks and actions for WordPress
     * @return
     */
	public function register()
	{

		$this->storeCustomTaxonomies();
		$this->storeTerms();

		if ( ! empty( $this->taxonomies ) ) {
			add_action( 'init', array( $this, 'registerCustomTaxonomy' ));
		}
		if ( ! empty( $this->terms ) ) {
			add_action( 'init', array( $this, 'registerTerms' ));
		}
	}

	public function setSettings()
	{
		//$this->settings->setSettings( $args );
	}

	public function storeTerms()
	{
		$this->terms[] = array
		(
		    'term'		=> 'Page hospitalite',   // the term 
		    'taxonomy'	=> 'plda_type_page', 	 // the taxonomy
		    'args'		=> array(
		        'description' => 'Modèle de page Hospitalité',
		        'slug'        => 'typepage-hospitalite'
			),
		);
		$this->terms[] = array
		(
		    'term'		=> 'Page Entreprise',   // the term 
		    'taxonomy'	=> 'plda_type_page', 	 // the taxonomy
		    'args'		=> array(
		        'description' => 'Page de la rubrique Entreprises',
		        'slug'        => 'page-entreprise'
			),
		);
		$this->terms[] = array
		(
		    'term'		=> 'Vert générique',   // the term 
		    'taxonomy'	=> 'plda_thematique_couleur', 	 // the taxonomy
		    'args'		=> array(
		        'description' => 'Theme vert',
		        'slug'        => 'green-theme'
			),
		);
		$this->terms[] = array
		(
		    'term'		=> 'Bleu Racing92',   // the term 
		    'taxonomy'	=> 'plda_thematique_couleur', 	 // the taxonomy
		    'args'		=> array(
		        'description' => 'Theme racing92',
		        'slug'        => 'racing92-theme'
			),
		);
		$this->terms[] = array
		(
		    'term'		=> 'Or VIP',   // the term 
		    'taxonomy'	=> 'plda_thematique_couleur', 	 // the taxonomy
		    'args'		=> array(
		        'description' => 'Theme vip',
		        'slug'        => 'vip-theme'
			),
		);
		$this->terms[] = array
		(
		    'term'		=> 'Orange',   // the term 
		    'taxonomy'	=> 'plda_thematique_couleur', 	 // the taxonomy
		    'args'		=> array(
		        'description' => 'Theme orange',
		        'slug'        => 'orange-theme'
			),
		);
		$this->terms[] = array
		(
		    'term'		=> 'Rouge',   // the term 
		    'taxonomy'	=> 'plda_thematique_couleur', 	 // the taxonomy
		    'args'		=> array(
		        'description' => 'Theme rouge',
		        'slug'        => 'rouge-theme'
			),
		);
	}

	public function storeCustomTaxonomies()
	{
		$custom_tax = array(
			array(
	            'taxonomy' => 'plda_service',
	            'singular_name' => 'Service',
	            'public' => true,
	            'rewrite' => array('slug' => 'plda_service'),
	            'show_ui' => true,
	            'objects' => array('page'),
			),
			array(
	            'taxonomy' => 'plda_type_page',
	            'singular_name' => 'Type de page',
	            'public' => true,
	            'show_ui' => true,
	            'rewrite' => array('slug' => 'plda_type_page'),
	            'objects' => array('page'),
			),
			array(
	            'taxonomy' => 'plda_thematique_couleur',
	            'singular_name' => 'Thématique couleur',
	            'public' => true,
	            'rewrite' => array('slug' => 'plda_thematique_couleur'),
	            'show_ui' => true,
	            'objects' => array('post', 'produit'),
	            'hierarchical' => true,
			),
		);

		foreach ($custom_tax as $option) {
			$labels = array(
				'name'              => $option['singular_name'],
				'singular_name'     => $option['singular_name'],
				'search_items'      => 'Rechercher un ' . $option['singular_name'],
				'all_items'         => 'Tous les ' . $option['singular_name'] . 's',
				'parent_item'       => 'Parent ' . $option['singular_name'],
				'parent_item_colon' => 'Parent ' . $option['singular_name'] . ':',
				'edit_item'         => 'Modifier ' . $option['singular_name'],
				'update_item'       => 'Mise à jour ' . $option['singular_name'],
				'add_new_item'      => 'Ajouter un ' . $option['singular_name'],
				'new_item_name'     => 'Nouveau ' . $option['singular_name'] . ' Name',
				'menu_name'         => $option['singular_name'] . 's',
			);

			$this->taxonomies[] = array(
				'hierarchical'      => isset($option['hierarchical']) ? true : false,
				'labels'            => $labels,
				'show_ui'           => isset($option['show_ui']) ? true : false,
				'show_in_nav_menus' => isset($option['show_in_nav_menus']) ? true : isset($option['show_ui']) ? true : false,
				'show_admin_column' => true,
				'query_var'         => true,
				'show_in_rest'		=> true,
				'rewrite'           => array( 'slug' => $option['taxonomy'] ),
				'objects'           => isset($option['objects']) ? $option['objects'] : null
			);

		}
	}

	public function registerCustomTaxonomy()
	{
		foreach ($this->taxonomies as $taxonomy) {
			//$objects = isset($taxonomy['objects']) ? array_keys($taxonomy['objects']) : null;
			register_taxonomy( $taxonomy['rewrite']['slug'], $taxonomy['objects'], $taxonomy );
		}
	}

	public function registerTerms()
	{
		foreach ($this->terms as $term) {
			wp_insert_term(
			    $term['term'],   	// the term 
			    $term['taxonomy'], 	// the taxonomy
			    $term['args']		// args
			);
		}
	}

}
