<?php

namespace App\Http\Managers\Gallery;

use App\DataModels\RelationalUsers;
use App\DataModels\Tweets;
use App\DataModels\TweetMedias;
use App\DataModels\UserConfig;
use App\DataModels\TweetTakeUsers;
use App\Models\Gallery\GalleryItemModel;
use App\Models\Gallery\MediaDetailModel;
use App\Models\TweetTakeUser;
use App\Constants\ListSort;

/**
 * 観賞画面（新しいツイート順）
 */
class GalleryManager
{
    const RECORDS_COUNT = 75;

    /**
     * 観賞画面をフェッチする
     */
    public function fetch(string $service_user_id, int $page, string $list_sort, string $user_id = null)
        : array
    {

        $query = Tweets::select
        (
            [
                'user_id',
                'tweet_id',
                'tweeted_datetime',
                'body',
                'kept',
                'shown'
            ]
        )
            ->where('service_user_id', $service_user_id)
            ->where('is_media', 1)
            ->where('media_ready', 1)
            ->where('deleted', 0);

        if ($user_id != null) {

            $query = $query->where('user_id', $user_id);

            $show_kept = TweetTakeUsers::select(['show_kept'])
                ->where('service_user_id', $service_user_id)
                ->where('user_id', $user_id)
                ->first();
            if ($show_kept['show_kept'] == 0) {
                $query = $query->where('shown', 0);
            }

        } else {

            $query = $query->where('shown', 0);

        }

        if ($list_sort == ListSort::DESC) {
            $query = $query->orderByDesc('tweeted_datetime');
        } else {
            $query = $query->orderBy('tweeted_datetime');
        }

        $query = $query
            ->skip($page * self::RECORDS_COUNT)
            ->take(self::RECORDS_COUNT);

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
                'directory_path',
                'file_name',
                'file_size',
                'thumb_file_size',
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

            if ($tweet_media['thumb_directory_path'] == "") {
                continue;
            }

            $tweet_id = $tweet_media['tweet_id'];
            $tweets_index = array_search($tweet_id, $tweet_ids);

            $thumb_directory = explode("/", $tweet_media['thumb_directory_path'])[5];
            $thumb_file = $tweet_media['thumb_file_name'];

            $media_directory = explode("/", $tweet_media['directory_path'])[5];
            $media_file = $tweet_media['file_name'];

            $model = new GalleryItemModel(
                $tweet_media['user_id'],
                $tweet_id,
                "/img/tweetmedia/{$media_directory}/{$media_file}",
                "/img/tweetmedia/{$thumb_directory}/{$thumb_file}",
                $media_file,
                $tweet_media['file_size'] + $tweet_media['thumb_file_size'],
                $tweet_media['type'],
                $tweets[$tweets_index]['body'],
                $tweets[$tweets_index]['kept'] == '1' ? true : false,
                $tweets[$tweets_index]['shown'] == '1' ? true : false,
                $tweets[$tweets_index]['tweeted_datetime'],
            );

            array_push($models, $model);

        }

        return $models;
    }

    
    /**
     * メディアの詳細情報を取得する
     */
    public function mediaDetail(
        string $service_user_id,
        string $user_id,
        string $tweet_id,
        string $media_name
    ): MediaDetailModel {

        $tweet = Tweets::select(
            [
                'user_id',
                'tweet_id',
                'body',
                'favolite_count',
                'retweet_count'
            ]
        )
            ->where('service_user_id', $service_user_id)
            ->where('user_id', $user_id)
            ->where('tweet_id', $tweet_id)
            ->first();
        
        $tweet_media = TweetMedias::select(
            [
                'directory_path',
                'file_name',
                'type'
            ]
        )
            ->where('service_user_id', $service_user_id)
            ->where('user_id', $user_id)
            ->where('tweet_id', $tweet_id)
            ->where('file_name', $media_name)
            ->first();

        $relational_user = RelationalUsers::select(
            [
                'name',
                'disp_name',
                'thumbnail_url'
            ]
        )
            ->where('user_id', $user_id)
            ->first();


        $media_directory = explode("/", $tweet_media['directory_path'])[5];
        $media_file = $tweet_media['file_name'];

        return new MediaDetailModel(
            $tweet['user_id'],
            $tweet['tweet_id'],
            $relational_user['name'],
            $relational_user['disp_name'],
            $relational_user['thumbnail_url'],
            "/img/tweetmedia/{$media_directory}/{$media_file}",
            $tweet_media['type'],
            $tweet['body'],
            $tweet['favolite_count'],
            $tweet['retweet_count'],
            "https://twitter.com/{$relational_user['disp_name']}/status/{$tweet['tweet_id']}",
        );
    }


    /**
     * ツイートを既読にする
     */
    public function checked(string $service_user_id, string $raw_user_ids, string $raw_tweet_ids): bool
    {

        $user_ids = explode(",", $raw_user_ids);
        $tweet_ids = explode(",", $raw_tweet_ids);

        $count = Tweets::where('service_user_id', $service_user_id)
            ->whereIn('user_id', $user_ids)
            ->whereIn('tweet_id', $tweet_ids)
            ->update(['shown'=>1]);

        if ($count == 0) {
            return false;
        } else {
            return true;
        }

    }


    /**
     * ツイートをKEEPする
     */
    public function keep(string $service_user_id, string $raw_user_ids, string $raw_tweet_ids): bool
    {

        $user_ids = explode(",", $raw_user_ids);
        $tweet_ids = explode(",", $raw_tweet_ids);

        $count = Tweets::where('service_user_id', $service_user_id)
            ->whereIn('user_id', $user_ids)
            ->whereIn('tweet_id', $tweet_ids)
            ->update(['kept'=>1,'shown'=>1]);

        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function unkeep(string $service_user_id, string $raw_user_ids, string $raw_tweet_ids): bool
    {

        $user_ids = explode(",", $raw_user_ids);
        $tweet_ids = explode(",", $raw_tweet_ids);

        $count = Tweets::where('service_user_id', $service_user_id)
            ->whereIn('user_id', $user_ids)
            ->whereIn('tweet_id', $tweet_ids)
            ->update(['kept'=>0,'shown'=>0]);

        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function changeShowKept(string $service_user_id, string $user_id): bool
    {

        $tweet_take_user = TweetTakeUsers::select(['show_kept'])
            ->where('service_user_id', $service_user_id)
            ->where('user_id', $user_id)
            ->first();

        $current_show_kept = $tweet_take_user['show_kept'] == 1 ? true : false;

        $count = 0;
        if ($current_show_kept) {
            $count = TweetTakeUsers::where('service_user_id', $service_user_id)
                ->where('user_id', $user_id)
                ->update(['show_kept'=>0]);
        } else {
            $count = TweetTakeUsers::where('service_user_id', $service_user_id)
                ->where('user_id', $user_id)
                ->update(['show_kept'=>1]);
        }

        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

}
