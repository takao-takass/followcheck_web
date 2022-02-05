<?php

namespace App\Http\Managers\Gallery;

use App\DataModels\Tweets;
use App\DataModels\TweetMedias;
use App\DataModels\UserConfig;
use App\Models\Gallery\GalleryAllItemModel;

/**
 * 観賞画面（新しいツイート順）
 */
class GalleryAllManager
{
    const RECORDS_COUNT = 200;

    /**
     * 観賞画面（新しいツイート順）をフェッチする
     */
    public function fetch(string $service_user_id, int $page): array
    {
        $filter_checked = UserConfig::where('service_user_id', $service_user_id)
            ->where('config_id', 4)
            ->first()
            ->getAttributes();

        if ($filter_checked['value'] == 1) {
            $query = Tweets::select(['user_id','tweet_id','body'])
                ->where('service_user_id', $service_user_id)
                ->where('is_media', 1)
                ->where('media_ready', 1)
                ->where('deleted', 0)
                ->where('shown', 0)
                ->orderBy('tweeted_datetime')
                ->skip($page * self::RECORDS_COUNT)
                ->take(self::RECORDS_COUNT);
        } else {
            $query = Tweets::select(['user_id','tweet_id','body'])
                ->where('service_user_id', $service_user_id)
                ->where('is_media', 1)
                ->where('media_ready', 1)
                ->where('deleted', 0)
                ->orderBy('tweeted_datetime')
                ->skip($page * self::RECORDS_COUNT)
                ->take(self::RECORDS_COUNT);
        }

        $tweets = $query->get()->toArray();
        $user_ids = array_column($tweets, 'user_id');
        $tweet_ids = array_column($tweets, 'tweet_id');

        $tweet_medias = TweetMedias::select(
            [
                'user_id',
                'tweet_id',
                'type',
                'thumb_directory_path',
                'thumb_file_name',
            ]
        )
            ->where('service_user_id', $service_user_id)
            ->whereIn('user_id', $user_ids)
            ->whereIn('tweet_id', $tweet_ids)
            ->orderBy('tweet_id')
            ->get()
            ->toArray();

        $models = [];
        foreach ($tweet_medias as $tweet_media) {

            $tweet_id = $tweet_media['tweet_id'];
            $tweets_index = array_search($tweet_id, $tweet_ids);

            //$media_directory = explode("/", $tweet_media['directory_path'])[5];
            //$media_file = $tweet_media['file_name'];

            $thumb_directory = explode("/", $tweet_media['thumb_directory_path'])[5];
            $thumb_file = $tweet_media['thumb_file_name'];

            $model = new GalleryAllItemModel(
                $tweet_id,
                $tweet_media['user_id'],
                "/img/tweetmedia/{$thumb_directory}/{$thumb_file}",
                $tweet_media['type'],
                $tweets[$tweets_index]['body']
            );

            array_push($models, $model);

        }

        return $models;
    }

}
