<?php

namespace TillProchaska\SocialImport\Services;

use \Exception;
use \Kirby\Http\Remote;
use \TillProchaska\SocialImport\IService;

class YoutubeService implements IService {

    protected static $baseUrl = 'https://www.googleapis.com/youtube/v3/videos';
    protected $apiKey;

    public function __construct() {
        $this->apiKey = kirby()->option('tillprochaska.social-import.services.youtube.key');

        if(!$this->apiKey) {
            throw new Exception('In order to use the YouTube service, a Google API key has to be set in the application configuration.', 500);
        }
    }

    public static function getName(): string {
        return 'youtube';
    }

    public function getIdFromUrl(string $url): string {
        $pattern = '/(?:youtube\.com\/watch\?.*v=|youtu\.be\/)([a-zA-Z0-9-_]+)/';


        if(!preg_match($pattern, $url, $match)) {
            throw new Exception('Could not extract video ID from the given URL.');
        }

        return $match[1];
    }

    public function getPreview(string $url): array {
        $data = $this->getData($url);
        $date = new \DateTime($data['datePublished']);

        return [
            'title' => $data['title'],
            'author' => $data['channel'],
            'description' => $data['description'],
            'meta' => 'Video uploaded by “' . $data['channel'] . '” on ' . $date->format('Y-m-d'),
            'image' => $data['thumbnail'],
        ];
    }

    public function getData(string $url): array {
        $params = [
            'key'  => $this->apiKey,
            'id'   => $this->getIdFromUrl($url),
            'part' => 'snippet,status',
        ];

        try {
            $remote = Remote::get(self::$baseUrl, [ 'data' => $params ]);
            $data = json_decode($remote->content());
        } catch(Exception $e) {
            throw new Exception('There was an error requesting video data from YouTube.', 500);
        }

        // Check if the request was successful
        if($remote->code() !== 200) {
            throw new Exception('There was an error requesting video data from YouTube.' , 500);
        }

        // Check if a video has been found for the given id
        if(count($data->items) < 1) {
            throw new Exception('Could not find a video on YouTube with the given video ID.', 400);
        } 

        $item = $data->items[0];

        return [
            'id' => $item->id,
            'title' => $item->snippet->title,
            'datePublished' => $item->snippet->publishedAt,
            'channel' => $item->snippet->channelTitle,
            'tags' => $item->snippet->tags,
            'description' => $item->snippet->description,
            'thumbnail' => $item->snippet->thumbnails->maxres->url,

            'uploadStatus' => $item->status->uploadStatus,
            'privacyStatus' => $item->status->privacyStatus,
            'embeddable' => $item->status->embeddable,
        ];
    }

}
