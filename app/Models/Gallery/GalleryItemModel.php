<?php

namespace App\Models\Gallery;

class GalleryItemModel
{
    public function __construct(
        public string $user_id,
        public string $tweet_id,
        public string $media_url,
        public string $thumbnail_url,
        public string $media_name,
        public int $media_size,
        public string $type,
        public string $tweet_text,
        public bool $kept,
        public bool $shown,
        public string $tweeted_datetime
    ) {
    }
}
