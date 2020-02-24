<?php

namespace Plda\Core;

/**
 * Sidebar.
 */
class Sidebar
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register()
    {
        add_action( 'widgets_init', array( $this, 'widgets_init' ) );
    }

    /*
        Define the sidebar
    */
    public function widgets_init()
    {
        register_sidebar( array(
            'name' => esc_html__('Footer Partenaires', 'plda'),
            'id' => 'plda-footer-partenaires',
            'description' => esc_html__('Zone pied de page - image partenaires', 'awps'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ) );

        register_sidebar( array(
            'name' => esc_html__('Footer RS', 'plda'),
            'id' => 'plda-footer-rs',
            'description' => esc_html__('Zone pied de page - réseaux sociaux', 'awps'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ) );

        register_sidebar( array(
            'name' => esc_html__('Footer Menu', 'plda'),
            'id' => 'plda-footer-menu',
            'description' => esc_html__('Zone pied de page - menu bas de page', 'awps'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ) );
    }
}
