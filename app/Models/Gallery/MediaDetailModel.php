<?php

namespace App\Models\Gallery;

class MediaDetailModel
{
    public function __construct(
        public string $user_id,
        public string $tweet_id,
        public string $user_name,
        public string $disp_name,
        public string $user_icon_url,
        public string $media_url,
        public string $tweet_text,
        public int $favolite_count,
        public int $retweet_count,
        public string $twitter_url,
    ) {
    }
}
