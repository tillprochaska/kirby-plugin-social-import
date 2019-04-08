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
            'url'  => $importable->getUrl(),
            'id'   => $importable->getId(),
            'preview' => $importable->getPreview(),
        ];
    }

    public static function getForm(Api $api): array {
        $url = self::getParam('url');
        $importable = self::getImportable($url);

        return [
            'url'  => $importable->getUrl(),
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
            'url'  => $importable->getUrl(),
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
        return new Importable(
            $url,
            self::getTemplate(),
            self::getParent(),
            self::getTransformer()
        );
    }

    protected static function getTemplate() {
        return kirby()->option('tillprochaska.social-import.template');
    }

    protected static function getParent() {
        return kirby()->option('tillprochaska.social-import.parent');
    }

    protected static function getTransformer() {
        return kirby()->option('tillprochaska.social-import.transformer');
    }

}
