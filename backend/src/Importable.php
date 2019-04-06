<?php

namespace TillProchaska\SocialImport;

use \Exception;
use \Kirby\Toolkit\Str;
use \Kirby\Cms\Page;
use \Kirby\Cms\PageBlueprint;

class Importable {

    public static $services;

    protected $service;
    protected $url;
    protected $data;

    public function __construct(string $url) {
        $this->url = $url;
        $this->initializeService();
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getServiceName(): string {
        return $this->service->getName();
    }

    public function getId(): string {
        return $this->service->getIdFromUrl($this->getUrl());
    }

    public function getPreview(): array {
        return $this->service->getPreview($this->getUrl());
    }

    public function getData(): array {
        return $this->service->getData($this->getUrl());
    }

    public function getForm(callable $template, callable $transformer): array {
        $data = $this->service->getData($this->getUrl());
        $data = $transformer($this->getServiceName(), $this->getUrl(), $data);
        $template = $template($this->getServiceName(), $this->getUrl());

        $page = self::constructPage($template, $data);
        $blueprint = self::constructBlueprint($page);

        $fields = array_filter(
            $blueprint->fields(),
            function($key) use($data) {
                return isset($data[$key]);
            },
            ARRAY_FILTER_USE_KEY
        );

        $fields = array_map(function($field) {
            unset($field['width']);
            return $field;
        }, $fields);

        return [
            'data' => $data,
            'status' => $blueprint->status(),
            'fields' => $fields,
        ];
    }

    public function createPage(callable $template, callable $parent, array $data): array {
        $template = $template($this->getServiceName(), $this->getUrl());
        $parent = $parent($this->getServiceName(), $this->getUrl());

        if(!isset($data['slug'])) {
            throw new Exception ('Invalid slug.', 400);
        }

        if(!is_a($parent, 'Page')) {
            throw new Exception('Invalid parent page.', 400);
        }

        if(!isset($data['title'])) {
            throw new Exception('Missing field: title.', 400);
        }

        $slug = $data['slug'];
        unset($data['slug']);

        $page = $parent->createChild([
            'slug' => $slug,
            'template' => $template,
            'content' => $data,
        ]);

        return [
            'title' => $page->title(),
            'id' => $page->id(),
        ];
    }

    protected static function constructPage($template, $data): Page {
        if(!isset($data['title'])) {
            throw new Exception('Missing field: title');
        }

        return Page::factory([
            'slug' => Str::slug($data['title']),
            'template' => $template,
            'content' => $data,
        ]);
    }

    protected static function constructBlueprint(Page $page): PageBlueprint {
        return PageBlueprint::factory(
           'pages/' . $page->intendedTemplate(),
           'pages/default',
           $page
       );
    }

    protected function initializeService() {
        $service = $this->detectService();
        $this->service = new $service();
    }

    protected function detectService() {
        foreach(self::$services as $service) {
            if($service::testUrl($this->getUrl())) {
                return $service;
            }
        }

        throw new Exception('Could not retrieve provider information for this URL.', 500);        
    }

}
