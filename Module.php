<?php
/**
 * Created by PhpStorm.
 * User: erwinkloosterboer
 * Date: 09/11/2017
 * Time: 09:26
 */

namespace NF\DarkSky;


class Module {
    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'ZF\Apigility\Autoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/src/DarkSky',
                )
            )
        );
    }

}