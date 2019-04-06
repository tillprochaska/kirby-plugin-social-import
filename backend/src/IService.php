<?php

namespace TillProchaska\SocialImport;

interface IService {

    /**
     * Retruns the service’s unique name.
     */
    public static function getName(): string;

    /**
     * Tests wether the service can be used with a given
     * URL. Returns `true` if the service can be used to
     * fetch data for the URL. Otherwise returns `false`.
     */
    public static function testUrl(string $url): bool;

    /**
     * Extracts a unique id for the service for a given
     * URL. Throws an error if the URL can’t be used with
     * the service. Used to detect the service for a given
     * URL as well as to fetch data.
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