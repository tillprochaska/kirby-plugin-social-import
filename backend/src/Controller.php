<?php

namespace TillProchaska\SocialImport;

use \Exception;
use \Kirby\Http\Url;
use \Kirby\Http\Request;
use \TillProchaska\ApiHelpers\Api;

class Controller {

    public static function getPreview(Api $api): array {
        $url = self::getParam('url');
        $importable = self::getImportable($url);

        return [
            'url'  => $url,
            'id'   => $importable->getId(),
            'preview' => $importable->getPreview(),
        ];
    }

    public static function getForm(Api $api): array {
        $url = self::getParam('url');
        $importable = self::getImportable($url);

        return [
            'url'  => $url,
            'id'   => $importable->getId(),
            'form' => $importable->getForm(),
        ];
    }

    public static function createPage(Api $api): array {
        $url = self::getParam('url');
        $importable = self::getImportable($url);

        $request = new Request();
        $data = $request->body()->toArray();

        $importable->createPage($data);
        $importable->createFiles();

        return [
            'url'  => $url,
            'id'   => $importable->getId(),
            'pageId' => $importable->getPage()->id(),
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

    protected static function getImportable(string $url) {
        return Importable::factory([
            'url' => $url,
            'transformer' => self::getTransformer(),
        ]);
    }

    }

    protected static function getTransformer() {
        return kirby()->option('tillprochaska.social-import.transformer');
    }

}
