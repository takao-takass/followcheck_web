<?php

namespace App\Models\Accounts;

class UserTweetsModel
{
    public string $user_id;
    public array $tweets;
    public array $tweets_medias;
    public array $media_urls;
    public array $thumb_urls;

    public function __construct(string $user_id, array $tweets)
    {
        $this->user_id = $user_id;
        $this->tweets = $tweets;
    }

    public function set_medias(array $tweet_medias){
        $this->tweet_medias = $tweet_medias;
        $this->media_urls = [];
        $this->thumb_urls = [];
        foreach($tweet_medias as $tweet_media){
            $split_path = explode("/", $tweet_media->directory_path);
            array_push(
                $this->media_urls,
                '/img/tweetmedia/' . $split_path[5] . '/' . $tweet_media->file_name
            );
            $split_path = explode("/", $tweet_media->thumb_directory_path);
            array_push(
                $this->thumb_urls,
                '/img/tweetmedia/' . $split_path[5] . '/' . $tweet_media->thumb_file_name
            );
            
        }
    }
}