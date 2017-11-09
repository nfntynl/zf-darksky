<?php
/**
 * Created by PhpStorm.
 * User: erwinkloosterboer
 * Date: 09/11/2017
 * Time: 09:34
 */

namespace NF\DarkSky\Factory;


use NF\DarkSky\DarkSky;
use Interop\Container\ContainerInterface;

class DarkSkyFactory {

    /**
     * @param  ContainerInterface $container
     * @return DarkSky
     */
    public function __invoke(ContainerInterface $container) {
        $config = $container->get('config');
        if(!isset($config['nf-darksky']['darksky-api-key'])){
            throw new \RuntimeException('Darksky API key not found in config. Did you forget to copy the nf-darksky.local.php.dist file to your autoload directory?');
        }
        return new DarkSky($config['nf-darksky']['darksky-api-key']);
    }
}