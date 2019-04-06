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

    /**
     * Getter method for the URL.
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * Shortcut to access the service’s name.
     */
    public function getServiceName(): string {
        return $this->service->getName();
    }

    /**
     * Shortcut to get the item’s unique id using
     * the detected service.
     */
    public function getId(): string {
        return $this->service->getIdFromUrl($this->getUrl());
    }

    /**
     * Shortcut to get the item’s preview data using
     * the detected service.
     */
    public function getPreview(): array {
        return $this->service->getPreview($this->getUrl());
    }

    /**
     * Shortcut to get the item’s raw data using the
     * detected service.
     */
    public function getData(): array {
        return $this->service->getData($this->getUrl());
    }

    /**
     * Returns a list of fields and field data required
     * to render a review form for the fetched data.
     */
    public function getForm(callable $template, callable $transformer): array {
        $serviceName = $this->getServiceName();
        $url = $this->getUrl();

        $data = $this->service->getData($url);

        // use the transformer and template hooks to allow the
        // user to specify which template should be used and
        // how the data returned should be mapped to the template’s
        // blueprint fields
        $data = $transformer($serviceName, $url, $data);
        $template = $template($serviceName, $url);

        $page = self::constructPage($template, $data);
        $blueprint = self::constructBlueprint($page);

        // keep only fields for which the service returned data
        $fields = array_filter(
            $blueprint->fields(),
            function($key) use($data) { return isset($data[$key]); },
            ARRAY_FILTER_USE_KEY
        );

        // remove the `width` property as the form fields will
        // be shown in a single-column layout inside a modal
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

    /**
     * Creates a new page for the importable using
     * the given data.
     */
    public function createPage(callable $template, callable $parent, array $data): array {
        $serviceName = $this->getServiceName();
        $url = $this->getUrl();

        // use the template and parent hooks to determine
        // which template to use for the new page and where
        // it should be saved
        $template = $template($serviceName, $url);
        $parent = $parent($serviceName, $url);

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

    /**
     * Constructs a Kirby `Page` object.
     */
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


    /**
     * Constructs a Kirby `PageBlueprint` object.
     */
    protected static function constructBlueprint(Page $page): PageBlueprint {
        return PageBlueprint::factory(
           'pages/' . $page->intendedTemplate(),
           'pages/default',
           $page
       );
    }

    /**
     * Creates a new instance of a service that can be used
     * to fetch data for the given URL.
     */
    protected function initializeService() {
        $service = $this->detectService();
        $this->service = new $service();
    }

    /**
     * Tries to detect a service that can be used to fetch
     * data for the given URL.
     */
    protected function detectService() {
        foreach(self::$services as $service) {
            // make sure to use only supported services
            $interface = __NAMESPACE__ . '\IService';
            if(!in_array($interface, class_implements($service))) {
                continue;
            }

            if($service::testUrl($this->getUrl())) {
                return $service;
            }
        }

        throw new Exception('Could not retrieve provider information for this URL.', 500);        
    }

}
