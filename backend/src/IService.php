<?php

namespace TillProchaska\SocialImport;

interface IService {

    /**
     * Retruns a unique identifier for the service
     */
    public static function getName(): string;

    /**
     * Extracts a unique id for the service for a given
     * URL. Throws an error if the URL can’t be used with
     * the service.
     */
    public function getIdFromUrl(string $url): string;

    /**
     * Returns data to be used in the Kirby Panel to preview
     * the item to be imported. Should least contain a
     * `title`, a short `description` and optionally
     * `meta` information and a `thumbnail` URL.
     */
    public function getPreview(string $url): array;

    /**
     * Returns raw data for a given URL to be used when
     * importing the item, or to preview it in the Panel.
     */
    public function getData(string $url): array;

}