<?php

namespace TillProchaska\SocialImport;
$plugin = 'tillprochaska/social-import';

Importable::$services = [
    [
        'name'    => 'youtube',
        'domains' => ['youtube.com', 'youtu.be'],
        'service' => 'TillProchaska\SocialImport\Services\YoutubeService',
    ],
];

$baseUrl = '/plugin/' . $plugin;
$api = new \TillProchaska\ApiHelpers\Api($baseUrl);

$api->get(
    'previews',
    '\TillProchaska\SocialImport\Controller::getPreview'
);

$api->get(
    'forms',
    '\TillProchaska\SocialImport\Controller::getForm'
);

$api->post(
    'pages',
    '\TillProchaska\SocialImport\Controller::createPage'
);

\Kirby::plugin($plugin, [
    
    'options' => [
        'prefix' => 'socialImport',
        'parent' => function($service, $url) {
            return site();
        },
        'template' => function($service, $url) {
            return 'default';
        },
        'transformer' => function($service, $url, $data) {
            return $data;
        },
        'services.youtube.key' => null,
    ],

    'routes' => $api->routes(),

    'sections' => [
        'SocialUrlImport' => [],
    ],

]);