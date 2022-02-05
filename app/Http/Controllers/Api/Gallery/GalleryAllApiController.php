<?php

namespace App\Http\Controllers\Api\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Managers\Gallery\GalleryAllManager;
use Illuminate\Http\Request;

class GalleryAllApiController extends Controller
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

        $manager = new GalleryAllManager();
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

        $manager = new GalleryAllManager();
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

        $manager = new GalleryAllManager();
        $result = $manager->keep($service_user_id, $user_id, $tweet_id);

        if ($result == false) {
            return response(400);
        }

        return response(200);
    }
}
