<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SlideshowApiController extends Controller
{
    public function image()
    {
        if(!$this->isValidToken()){
            return response(401);
        }

        $query = DB::table('tweets')
            ->join('tweet_medias','tweet_medias.tweet_id','=','tweets.tweet_id')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('type','photo')
            ->where('kept',1);

        $image_count = $query
            ->count();

        $media = $query
            ->skip( rand(0, $image_count-1) )
            ->take(1)
            ->first();

        $split_media_path = explode("/", $media->directory_path);
        $url = '/img/tweetmedia/' . $split_media_path[5] . '/' . $media->file_name;

        return response()->json([
            'url' => $url
        ]);
    }
}
