<?php
return [
    'service_manager' => [
        'factories' => [
            \NF\DarkSky\DarkSky::class => \NF\DarkSky\Factory\DarkSkyFactory::class
        ]
    ],
    'nf-darksky' => [
        'darksky-api-key' => null, //Replace with your api key
    ]
];