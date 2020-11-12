<?php

namespace App\Models;

class ShowThumbnail
{
    public string $tweet_id;
    public string $thumbnail_url;
    public string $file_name;
    public string $type;

    public function __construct(string $tweet_id, string $thumbnail_url, string $file_name, string $type)
    {
        $this->tweet_id = $tweet_id;
        $this->thumbnail_url = $thumbnail_url;
        $this->file_name = $file_name;
        $this->type = $type;
    }
}
