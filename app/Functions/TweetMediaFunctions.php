<?php
namespace App\Functions;

class TweetMediaFunctions
{
    
    public static function makeMediaUrls(array $tweet_medias){
        $media_urls = [];
        foreach($tweet_medias as $tweet_media){
            $split_path = explode("/", $tweet_media->media_directory_path);
            array_push(
                $media_urls,
                '/img/tweetmedia/' . $split_path[5] . '/' . $tweet_media->media_file_name
            );
        }
        return $media_urls;
    }

    public static function makeThumbUrls(array $tweet_medias){
        $thumb_urls = [];
        foreach($tweet_medias as $tweet_media){
            $split_path = explode("/", $tweet_media->thumb_directory_path);
            array_push(
                $thumb_urls,
                '/img/tweetmedia/' . $split_path[5] . '/' . $tweet_media->thumb_file_name
            );
        }
        return $thumb_urls;
    }

}