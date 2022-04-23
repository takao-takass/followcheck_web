<?php

namespace App\Http\Controllers\Api\Gallery;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Controller;
use App\Http\Managers\Gallery\GalleryManager;
use App\Constants\MediaThumbnailSize;

class GalleryApiController extends Controller
{

    public function mediaDetail(Request $request)
    {

        if (!$this->isValidToken()) {
            return response(401);
        }


        $tweet_id = $request->input('tweet_id');
        if ($tweet_id == null) {
            return response(400);
        }

        $user_id = $request->input('user_id');
        if ($user_id == null) {
            return response(400);
        }

        $media_name = $request->input('media_name');
        if ($media_name == null) {
            return response(400);
        }

        $service_user_id = $this->session_user->service_user_id;

        $manager = new GalleryManager();
        $result = $manager->mediaDetail(
            $service_user_id,
            $user_id,
            $tweet_id,
            $media_name
        );

        return response()->json(
            [
                'user_id' => $result->user_id,
                'tweet_id' => $result->tweet_id,
                'user_name' => $result->user_name,
                'disp_name' => $result->disp_name,
                'user_icon_url' => $result->user_icon_url,
                'media_url' => $result->media_url,
                'media_type' => $result->media_type,
                'tweet_text' => $result->tweet_text,
                'favolite_count' => $result->favolite_count,
                'retweet_count' => $result->retweet_count,
                'twitter_url' => $result->twitter_url,
            ]
        );


    }



    public function checked(Request $request)
    {

        if (!$this->isValidToken()) {
            return response(401);
        }

        $tweet_ids = $request->input('tweet_ids');
        if ($tweet_ids == null) {
            return response(400);
        }

        $user_ids = $request->input('user_ids');
        if ($user_ids == null) {
            return response(400);
        }
        $service_user_id = $this->session_user->service_user_id;

        $manager = new GalleryManager();
        $result = $manager->checked($service_user_id, $user_ids, $tweet_ids);

        if ($result == false) {
            return response(400);
        }

        return response(200);
    }


    public function keep(Request $request)
    {

        if (!$this->isValidToken()) {
            return response(401);
        }

        $tweet_id = $request->input('tweet_id');
        if ($tweet_id == null) {
            return response(400);
        }

        $user_id = $request->input('user_id');
        if ($user_id == null) {
            return response(400);
        }

        $service_user_id = $this->session_user->service_user_id;

        $manager = new GalleryManager();
        $result = $manager->keep($service_user_id, $user_id, $tweet_id);

        if ($result == false) {
            return response(400);
        }

        return response(200);
    }

    public function changeThumbnailSize(Request $request)
    {
        if (!$this->isValidToken()) {
            return response(401);
        }
        
        $thumnbail_size = $request->input('thumnbail_size');
        $set_size = '';
        switch ($thumnbail_size) {
            case MediaThumbnailSize::SMALL:
                $set_size = MediaThumbnailSize::MEDIUM;
                // Cookie::queue(Cookie::make('thumbnail_size', MediaThumbnailSize::MEDIUM, 525600)); // cookie of 1 year.
                break;
            case MediaThumbnailSize::LARGE:
                $set_size = MediaThumbnailSize::SMALL;
                // Cookie::queue(Cookie::make('thumbnail_size', MediaThumbnailSize::SMALL, 525600)); // cookie of 1 year.
                break;
            case MediaThumbnailSize::MEDIUM:
            default:
                $set_size = MediaThumbnailSize::LARGE;
                // Cookie::queue(Cookie::make('thumbnail_size', MediaThumbnailSize::LARGE, 525600)); // cookie of 1 year.
                break;
        }

        return response($set_size, 200)->cookie('thumbnail_size', $set_size, 60*24*365);
    }
}
