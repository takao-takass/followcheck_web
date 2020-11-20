<?php

namespace App\Http\Controllers;

use App\Models\ShowThumbnail;
use App\Models\TweetTakeUser;
use App\ViewModels\MediaViewModel;
use App\ViewModels\TweetUsersViewModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\TweetListFilter;
use App\Models\TweetShow;
use App\Models\Token;
use App\ViewModels\ShowThumbnailViewModel;
use Carbon\Carbon;

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

        $query = DB::table('keep_tweets')
            ->join('tweet_medias','tweet_medias.tweet_id','=','keep_tweets.tweet_id')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('type','photo');

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
