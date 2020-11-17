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

class MediaController extends Controller
{

    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $tweet_id = $request->input('tweet_id');
        $file_name = $request->input('file_name');

        $viewModel = new MediaViewModel();

        // メディアのパス
        $tweet_media = DB::table('tweet_medias')
            ->Where('tweet_id', '=', $tweet_id)
            ->Where('file_name', '=', $file_name)
            ->first();
        $split_media_path = explode("/", $tweet_media->directory_path);
        $viewModel->path = '/img/tweetmedia/' . $split_media_path[5] . '/' . $tweet_media->file_name;

        // ツイート本文とツイートしたユーザ
        $tweet = DB::table('tweets')
            ->Where('service_user_id', '=', $this->session_user->service_user_id)
            ->Where('tweet_id', '=', $tweet_id)
            ->first();

        $user = DB::table('relational_users')
            ->Where('user_id', '=', $tweet->tweet_user_id)
            ->first();

        $keep_count = DB::table('keep_tweets')
            ->Where('service_user_id', '=', $this->session_user->service_user_id)
            ->Where('tweet_id', '=', $tweet_id)
            ->count();

        $viewModel->tweet_body = $tweet->body;
        $viewModel->user_thumbnail_path = $user->thumbnail_url;
        $viewModel->twitter_url = 'https://twitter.com/' . $user->disp_name . '/status/' . $tweet_id;
        $viewModel->tweet_id = $tweet_id;
        $viewModel->keep_count = $keep_count;

        $param['Media'] = $viewModel;

        return  response()->view('media', $param);
    }

    public function delete(Request $request)
    {
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $tweet_id = $request['tweet_id'];
        DB::table('deletable_tweets')
            ->insert(
                [
                    'service_user_id'=>$this->session_user->service_user_id,
                    'tweet_id'=>$tweet_id
                ]
            );

        DB::table('tweets')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id',$tweet_id)
            ->update(['deleted' => 1]);

        $tweet = DB::table('tweets')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', '=',  $tweet_id)
            ->first();

        return redirect( route('show_user.index', ['user_id' => $tweet->user_id]) );
    }


    public function keep(Request $request)
    {
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $tweet_id = $request['tweet_id'];
        DB::table('keep_tweets')
            ->insert(
                [
                    'service_user_id'=>$this->session_user->service_user_id,
                    'tweet_id'=>$tweet_id
                ]
            );

        $tweet = DB::table('tweets')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', '=',  $tweet_id)
            ->first();

        return redirect( route('show_user.index', ['user_id' => $tweet->user_id]) );
    }
}
