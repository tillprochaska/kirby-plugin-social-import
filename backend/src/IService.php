<?php

namespace TillProchaska\SocialImport;

interface IService {

    /**
     * Retruns the service’s unique name.
     */
    public static function getName(): string;

    /**
     * Tests wether the service can be used to fetch data
     * for a given importable URL. Returns `true` if the
     * service supports the URL. Otherwise returns `false`.
     */
    public static function testImportableUrl(string $url): bool;

    /**
     * Tests wether the service can be used to fetch
     * a given feed URL. Returns `true` if the service supports
     * the URL. Otherwise returns `false`.
     */
    public static function testFeedUrl(string $Url): bool;

    /**
     * Extracts a unique id for the service for a given importable
     * URL. Throws an error if the URL can’t be used with the
     * service.
     */
    public static function getImportableIdFromUrl(string $url): string;

    /**
     * Extracts a unique id for the service for a given feed URL.
     * Throws an error if the URL can’t be used with the service.
     */
    public static function getFeedIdFromUrl(string $url): string;

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