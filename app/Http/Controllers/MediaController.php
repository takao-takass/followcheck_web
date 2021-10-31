<?php

namespace App\Http\Controllers;

use App\DataModels\Tweets;
use App\DataModels\TweetMedias;
use App\DataModels\RelationalUsers;
use App\DataModels\UserConfig;
use App\DataModels\ShownTweets;
use App\ViewModels\MediaViewModel;
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

        // メディアのパス
        $tweet_media = TweetMedias::where('tweet_id', '=', $tweet_id)
            ->Where('file_name', '=', $file_name)
            ->first();
        $split_media_path = explode("/", $tweet_media->directory_path);

        // ツイート本文とツイートしたユーザ
        $tweet = Tweets::where('service_user_id', '=', $this->session_user->service_user_id)
            ->Where('tweet_id', '=', $tweet_id)
            ->first();

        $user = RelationalUsers::where('user_id', '=', $tweet->tweet_user_id)
            ->first();

        $viewModel = new MediaViewModel();
        $viewModel->path = '/img/tweetmedia/' . $split_media_path[5] . '/' . $tweet_media->file_name;
        $viewModel->tweet_body = $tweet->body;
        $viewModel->user_thumbnail_path = $user->thumbnail_url;
        $viewModel->user_id = $user->user_id;
        $viewModel->twitter_url = 'https://twitter.com/' . $user->disp_name . '/status/' . $tweet_id;
        $viewModel->tweet_id = $tweet_id;
        $viewModel->keep_count = $tweet->kept == 0 ? 0 : 1;
        $param['Media'] = $viewModel;
        $param['ShowType'] = $request->input('show_type');
        $sort = $request->input('sort');

        // 既読ツイートの登録
        $check_enabled = UserConfig::select(['value'])
            ->Where('service_user_id',  $this->session_user->service_user_id)
            ->Where('config_id', 3)
            ->first()
            ->getAttributes();

        if($check_enabled['value'] == 1){

            $query = ShownTweets::select(['user_id','tweet_id'])
                ->Where('sign', $this->getToken());
            if($sort==1){
                $query = $query->Where('tweeted_datetime', '<=', $tweet->tweeted_datetime);
            }else{
                $query = $query->Where('tweeted_datetime', '>=', $tweet->tweeted_datetime);
            }

            foreach ($query->get() as $checked_tweets_id){
                Tweets::where('service_user_id',$this->session_user->service_user_id)
                    ->where('user_id',$checked_tweets_id->user_id)
                    ->where('tweet_id',$checked_tweets_id->tweet_id)
                    ->update(['shown'=>1]);
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
        Tweets::where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', $tweet_id)
            ->update(['kept'=>1,'shown'=>1]);

        $tweet = Tweets::where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', $tweet_id)
            ->first();

        if($request['show_type'] == 'user') {
            return redirect(route('show_user.index', ['user_id' => $tweet->user_id]));
        }elseif($request['show_type'] == 'all_reverse'){
            return redirect( route('show_all_reverse.index') );
        }else{
            return redirect( route('show_all.index') );
        }
    }
}
