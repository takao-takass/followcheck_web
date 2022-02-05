<?php

namespace App\Models\Gallery;

class GalleryAllItemModel
{
    public function __construct(
        public string $user_id,
        public string $tweet_id,
        public string $thumbnail_url,
        public string $type,
        public string $tweet_text
    ) {
    }
}
