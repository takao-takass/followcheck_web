<?php

namespace App\Models\Gallery;

class GalleryItemModel
{
    public function __construct(
        public string $user_id,
        public string $tweet_id,
        public string $thumbnail_url,
        public string $media_name,
        public string $type,
        public string $tweet_text
    ) {
    }
}
