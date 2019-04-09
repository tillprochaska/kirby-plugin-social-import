<?php

namespace TillProchaska\SocialImport;
$plugin = 'tillprochaska/social-import';

Importable::$services = [
    'youtube' => 'TillProchaska\SocialImport\Services\YoutubeService',
];

$baseUrl = '/plugin/' . $plugin;
$api = new \TillProchaska\ApiHelpers\Api($baseUrl);

$api->get(
    '/importables/previews',
    '\TillProchaska\SocialImport\Controller::getImportablePreview'
);

$api->get(
    '/importables/forms',
    '\TillProchaska\SocialImport\Controller::getImportableForm'
);

$api->post(
    '/importables/pages',
    '\TillProchaska\SocialImport\Controller::createImportablePage'
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
        'transformer' => function($service, $data) {
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