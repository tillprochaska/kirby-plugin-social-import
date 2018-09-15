<?php

namespace TillProchaska\SocialImport;

use \Exception;
use \Kirby\Http\Url;
use \Kirby\Http\Request;
use \TillProchaska\ApiHelpers\Api;

class Controller {

    public static function getPreview(Api $api): array {
        $url = self::getParam('url');
        $importable = new Importable($url);

        return [
            'url'  => $importable->getUrl(),
            'id'   => $importable->getId(),
            'preview' => $importable->getPreview(),
        ];
    }

    public static function getForm(Api $api): array {
        $template = kirby()->option('tillprochaska.social-import.template');
        $transformer = kirby()->option('tillprochaska.social-import.transformer');

        $url = self::getParam('url');
        $importable = new Importable($url);

        return [
            'url'  => $importable->getUrl(),
            'id'   => $importable->getId(),
            'form' => $importable->getForm($template, $transformer),
        ];
    }

    public static function createPage(Api $api): array {
        $url = self::getParam('url');
        $importable = new Importable($url);
        $request = new Request();

        $template = kirby()->option('tillprochaska.social-import.template');
        $parent = kirby()->option('tillprochaska.social-import.parent');
        $data = $request->body()->toArray();

        return [
            'url'  => $importable->getUrl(),
            'id'   => $importable->getId(),
            'pageData' => $importable->createPage($template, $parent, $data),
        ];
    }

    protected static function getParam(string $key, bool $required = true) {
        if($value = Url::toObject()->query()->get($key)) {
            return $value;
        }

        if($required) {
            throw new Exception('Missing parameter: ' . $key, 400);
        }

        return null;
    }

}
