<?php

namespace TillProchaska\SocialImport;

interface IService {

    public static function getName(): string;
    public function getIdFromUrl(string $url): string;
    public function getPreview(string $url): array;
    public function getData(string $url): array;

}