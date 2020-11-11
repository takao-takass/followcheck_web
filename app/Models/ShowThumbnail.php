<?php

namespace App\Models;

class ShowThumbnail
{
    public string $tweet_id;
    public string $thumbnail_url;
    public string $media_url;
    public string $type;

    public function __construct(string $tweet_id, string $thumbnail_url, string $media_url, string $type)
    {
        $this->tweet_id = $tweet_id;
        $this->thumbnail_url = $thumbnail_url;
        $this->media_url = $media_url;
        $this->type = $type;
    }
}
