<?php

namespace TillProchaska\SocialImport;

use \Exception;
use \Kirby\Toolkit\Str;
use \Kirby\Cms\Page;
use \Kirby\Cms\PageBlueprint;
use \Kirby\Cms\File;
use \Kirby\Cms\Files;
use \Kirby\Http\Remote;
use \Kirby\Toolkit\F;

class Importable {

    public static $services;

    protected $service;
    protected $data;
    protected $page;

    protected $url;
    protected $template;
    protected $parent;
    protected $transformer;

    public function __construct(string $url, callable $template, callable $parent, callable $transformer) {
        $this->url = $url;
        $this->template = $template;
        $this->parent = $parent;
        $this->transformer = $transformer;

        $this->initializeService();
    }

    /**
     * Returns the URL.
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * Returns the page.
     */
    public function getPage(): Page {
        return $this->page;
    }

    /**
     * Returns the name of the detected service.
     */
    public function getServiceName(): string {
        return $this->service->getName();
    }

    /**
     * Shortcut to get the item’s unique id using the detected
     * service.
     */
    public function getId(): string {
        return $this->service->getIdFromUrl($this->getUrl());
    }

    /**
     * Returns the item’s preview data using the detected
     * service.
     */
    public function getPreview(): array {
        return $this->service->getPreview($this->getUrl());
    }

    /**
     * Returns the item’s transformed data using the detected service.
     */
    public function getData(): array {
        $transformer = $this->transformer;
        $url = $this->getUrl();

        // memoize data to prevent additional network requests
        if(!$this->data) {
            $raw = $this->service->getData($url);
            $this->data = $transformer(
                $this->getServiceName(),
                $this->getUrl(),
                $raw
            );
        }

        return $this->data;
    }

    /**
     * Returns the item’s content data using the detected service.
     */
    public function getContent(): array {
        $data = $this->getData();
        return $data['content'];
    }

    /**
     * Returns the item’s files using the detected service.
     */
    public function getFiles(): array {
        $data = $this->getData();
        return $data['files'];
    }

    /**
     * Returns the target parent page using the parent hook.
     */
    public function getParent(): Page {
        $parent = $this->parent;
        return $parent(
            $this->getServiceName(),
            $this->getUrl()
        );
    }

    /**
     * Returns the target page tempalte using the template hook.
     */
    public function getTemplate(): string {
        $template = $this->template;
        return $template(
            $this->getServiceName(),
            $this->getUrl()
        );
    }

    /**
     * Returns a list of fields and field data required
     * to render a review form for the fetched data.
     */
    public function getForm(): array {
        $content = $this->getContent();

        $page = self::constructPage($this->getTemplate(), $content);
        $blueprint = self::constructBlueprint($page);

        // keep only fields for which the service returned data
        $fields = array_filter(
            $blueprint->fields(),
            function($key) use($content) {
                return isset($content[$key]);
            },
            ARRAY_FILTER_USE_KEY
        );

        // remove the `width` property as the form fields will
        // be shown in a single-column layout inside a modal
        $fields = array_map(function($field) {
            unset($field['width']);
            return $field;
        }, $fields);

        return [
            'data' => $content,
            'status' => $blueprint->status(),
            'fields' => $fields,
        ];
    }

    /**
     * Creates a new page for the importable using
     * the given data.
     */
    public function createPage(array $content): Page {
        $parent = $this->getParent();
        $template = $this->getTemplate();

        if(!isset($content['slug'])) {
            throw new Exception ('Invalid slug.', 400);
        }

        if(!is_a($parent, 'Page')) {
            throw new Exception('Invalid parent page.', 400);
        }

        if(!isset($content['title'])) {
            throw new Exception('Missing field: title.', 400);
        }

        $slug = $content['slug'];
        unset($content['slug']);

        $this->page = $parent->createChild([
            'slug' => $slug,
            'template' => $template,
            'content' => $content,
        ]);

        return $this->page;
    }


    /**
     * Downloads the item’s associated files and permanently
     * stores them in the page’s content directory.
     */
    public function createFiles(): Files {
        if(!$this->page) {
            throw new Exception('Before creating files a page had to be created using the `createPage` method.');
        }

        foreach($this->getFiles() as $url) {
            $temporary = $this::downloadFile($url);

            File::create([
                'source' => $temporary,
                'filename' => basename($url),
                'parent' => $this->page,
            ]);
        }

        return $this->page->files();
    }

    /**
     * Helper method that downloads a file from a given URL to
     * the system’s temporary directory.
     */
    protected function downloadFile(string $url): string {
        // creates a temporary file with the contents
        // of the file at the given URL
        $temporary = tempnam(sys_get_temp_dir(), 'kirby.tillprochaska.social-import.');
        $remote = Remote::get($url);
        $contents = $remote->content();
        F::write($temporary, $contents);

        return $temporary;
    }

    /**
     * Constructs a Kirby `Page` object.
     */
    protected static function constructPage(string $template, array $data): Page {
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
    protected function detectService(): string {
        foreach(self::$services as $service) {
            $interface = __NAMESPACE__ . '\IService';
            if(!in_array($interface, class_implements($service))) {
                throw new Exception('Service need to implement the `IService` interface.');
            }

            if($service::testUrl($this->getUrl())) {
                return $service;
            }
        }

        throw new Exception('Could not retrieve provider information for this URL.', 500);        
    }

}
