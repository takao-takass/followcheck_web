<?php

namespace App\Http\Controllers;

use App\ViewModels\MediaViewModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        $viewModel->user_id = $user->user_id;
        $viewModel->twitter_url = 'https://twitter.com/' . $user->disp_name . '/status/' . $tweet_id;
        $viewModel->tweet_id = $tweet_id;
        $viewModel->keep_count = $keep_count;
        $param['Media'] = $viewModel;
        $param['ShowType'] = $request->input('show_type');
        $sort = $request->input('sort');

        // 既読ツイートの登録
        $check_enabled = DB::table('user_config')
            ->select(['value'])
            ->Where('service_user_id',  $this->session_user->service_user_id)
            ->Where('config_id', 3)
            ->first();

        if($check_enabled->value == 1){

            $query = DB::table('shown_tweets')
                ->select(['user_id','tweet_id'])
                ->Where('sign', $this->getToken());
            if($sort==1){
                $query = $query->Where('tweeted_datetime', '<=', $tweet->tweeted_datetime);
            }else{
                $query = $query->Where('tweeted_datetime', '>=', $tweet->tweeted_datetime);
            }
            $checked_tweets_ids = $query->get();

            foreach ($checked_tweets_ids as $checked_tweets_id){
                DB::table('delete_tweets')->updateOrInsert(
                    [
                        'service_user_id'=>$this->session_user->service_user_id,
                        'user_id'=>$checked_tweets_id->user_id,
                        'tweet_id'=>$checked_tweets_id->tweet_id
                    ]
                );
            }
        }

        return  response()->view('media', $param);
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

        if($request['show_type'] == 'user'){
            return redirect( route('show_user.index', ['user_id' => $tweet->user_id]) );
        }else{
            return redirect( route('show_all.index') );
        }
    }
}
