<?php

namespace TillProchaska\SocialImport;
$plugin = 'tillprochaska/social-import';

Importable::$services = [
    'youtube' => 'TillProchaska\SocialImport\Services\YoutubeService',
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

$languages = ['en'];
$translations = [];

foreach($languages as $language) {
    $file = __DIR__ . '/translations/' . $language . '.json';
    $data = json_decode(file_get_contents($file), true);
    $translations[$language] = $data;
}

\Kirby::plugin($plugin, [
    
    'options' => [
        'storage' => '/plugins/tillprochaska/social-import/',
        'prefix' => 'socialImport',
        'parent' => function($service, $url) { return site(); },
        'template' => function($service, $url) { return 'default'; },
        'transformer' => function($service, $url, $data) {
            return [
                'parent' => site(),
                'template' => 'default',
                'content' => $data,
                'files' => [],
            ];
        },
        'services.youtube.key' => null,
    ],

    'translations' => $translations,

    'routes' => $api->routes(),

    'sections' => [
        'socialImportSingle' => [],
    ],

]);