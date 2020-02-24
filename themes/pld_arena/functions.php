<?php
/**
 *
 * This theme uses PSR-4 and OOP logic instead of procedural coding
 *
 * @package plda
 */

if ( file_exists( ABSPATH . '/../../vendor/autoload.php' ) ) :
	require_once ABSPATH . '/../../vendor/autoload.php';
endif;

if ( class_exists( 'Plda\\Init' ) ) :
	Plda\Init::register_services();
endif;
