<?php

namespace App\Http\Controllers;

use App\DataModels\Tweets;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SlideshowController extends Controller
{

    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        return  response()->view('slideshow');
    }

    public function image()
    {
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $image_count = Tweets::join('tweet_medias','tweet_medias.tweet_id','=','Tweets.tweet_id')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('type','photo')
            ->where('kept',1)
            ->count();

        $media = Tweets::join('tweet_medias','tweet_medias.tweet_id','=','Tweets.tweet_id')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('type','photo')
            ->where('kept',1)
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
