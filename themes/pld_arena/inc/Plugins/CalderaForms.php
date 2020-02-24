<?php
/**
 * CALDERA FORMS
 *
 *
 * @package plda
 */

namespace Plda\Plugins;

class CalderaForms
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register()
    {
        add_filter( 'caldera_forms_render_get_field', array( $this, 'render_contact_form_event_list' ), 10, 2 );
    }

    public function render_contact_form_event_list( $field, $form )  {
        if ( 'evenement_vip' == $field[ 'slug' ] || 'evenement_billetterie' == $field[ 'slug' ]  ) {

            if ( 'evenement_vip' == $field[ 'slug' ] )
                $idsOfProducts = \Plda\Custom\Transients::get_transient_query_ids( 'ALL_CURRENT_VIP' );
            else
                $idsOfProducts = \Plda\Custom\Transients::get_transient_query_ids( 'ALL_CURRENT_EVENTS' );

            $args = array(
                'post_type'      => 'produit',
                'posts_per_page' => -1,
                'order'          => 'ASC',
                'orderby'        => 'title',
                'post__in'       => ((!isset($idsOfProducts) || empty($idsOfProducts)) ? array(0) : $idsOfProducts)
            );

            $events = get_posts( $args );
            //mb_internal_encoding("UTF-8");
            if ( ! empty( $events ) ) {
                foreach( $events as $event ) {
                    $field[ 'config' ][ 'option' ][ $event->ID ] = array(
                        'value' => $event->ID,
                        'label' => ucwords(mb_strtolower($event->post_title))
                    );
                }
            }
            if ('evenement_billetterie' == $field[ 'slug' ]){

                $field[ 'config' ][ 'option' ][ 'parking' ] = array(
                    'value' => 'parking',
                    'label' => 'Parking'
                );
                $field[ 'config' ][ 'option' ][ 'clubenfant' ] = array(
                    'value' => 'clubenfant',
                    'label' => 'Club Enfant'
                );
                $field[ 'config' ][ 'option' ][ 'autre' ] = array(
                    'value' => 'autre',
                    'label' => 'Autre'
                );
            }
            if ('evenement_vip' == $field[ 'slug' ]){
                $field[ 'config' ][ 'option' ][ 'autre' ] = array(
                    'value' => 'autre',
                    'label' => 'Autre'
                );
            }
        }
     
        return $field;
     
    }
}