<?php
/**
 * Congif Project
 *
 * @package plda
 */

namespace Plda\Api;

use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Exception\RuntimeException;

/*
    $oConfigPlda = configPlda::getInstance();
    $oConfigPlda->get_key();
*/

class ConfigPlda {
    
    use ConfigTrait;

    /** @var  @var ConfigInterface */
    protected $config;

    /**
     * @var Singleton
     * @access private
     * @static
     */
     private static $_instance = null;


    private function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Static constructor / factory
     */

    public static function getInstance()
    {

        $config = ConfigFactory::create( ABSPATH . '../project_settings.php' );

        if(is_null(self::$_instance))
        {
            self::$_instance = new self($config);
        }

        return self::$_instance;

    }

    public function get_key( $key )
    {
        if (is_string($key) && $this->hasConfigKey($key))
        {
            return $this->getConfigKey( $key );
        }

        return '';
    }
}