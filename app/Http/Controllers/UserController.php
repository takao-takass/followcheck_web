<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\DataModels\Tweets;
use App\DataModels\TweetMedias;
use App\DataModels\TweetTakeUsers;
use App\Functions\TweetMediaFunctions;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // ユーザの基本情報
        $param['user'] = DB::table('relational_users')
            ->where('user_id',$request['user_id'])
            ->first();
        $param['tweet_count'] = DB::table('tweets')
            ->where('user_id',$request['user_id'])
            ->where('service_user_id',$this->session_user->service_user_id)
            ->count();

        // メディアの数
        $param['media_count'] = DB::table('tweets')
            ->join('tweet_medias','tweets.tweet_id','=','tweet_medias.tweet_id')
            ->where('tweets.user_id',$request['user_id'])
            ->where('tweets.service_user_id',$this->session_user->service_user_id)
            ->count();
        $param['media_ready_count'] = DB::table('tweets')
            ->join('tweet_medias','tweets.tweet_id','=','tweet_medias.tweet_id')
            ->where('tweets.user_id',$request['user_id'])
            ->where('tweets.service_user_id',$this->session_user->service_user_id)
            ->where('tweets.media_ready',1)
            ->count();
        $param['media_checked_count'] = DB::table('tweets')
            ->join('delete_tweets','tweets.tweet_id','=','delete_tweets.tweet_id')
            ->join('tweet_medias','delete_tweets.tweet_id','=','tweet_medias.tweet_id')
            ->where('tweets.user_id',$request['user_id'])
            ->where('tweets.service_user_id',$this->session_user->service_user_id)
            ->where('delete_tweets.service_user_id',$this->session_user->service_user_id)
            ->count();
        
        // ユーザの投稿メディア
        $tweets = Tweets::select(['tweet_id'])
            ->orderBy('tweeted_datetime','desc')
            ->where('service_user_id',$this->session_user->service_user_id)
            ->where('user_id',$request['user_id'])
            ->where('is_media',1)
            ->where('media_ready',1)
            ->take(50)
            ->get()
            ->toArray();
        $tweet_ids = array_column( $tweets, 'tweet_id');
        $tweet_medias = TweetMedias::select(['thumb_directory_path','thumb_file_name'])
            ->orderBy('create_datetime')
            ->where('service_user_id',$this->session_user->service_user_id)
            ->where('user_id',$request['user_id'])
            ->WhereIn('tweet_id',$tweet_ids)
            ->get()
            ->toArray();
        $param['thumb_urls'] = TweetMediaFunctions::makeThumbUrls($tweet_medias);

        // ツイートの取得対象か否か
        $param['tweet_taking'] = 1 == TweetTakeUsers::where('service_user_id',$this->session_user->service_user_id)
            ->where('user_id',$request['user_id'])
            ->count();

        // フォロイー取得したか否か
        // TODO: Not implemented.
        $param['follow_taked'] = 1 == 0;

        // イイネしたツイートを取得したか否か
        // TODO: Not implemented.
        $param['favorite_taked'] = 1 == 0;



        return response()->view('user', $param);
    }
}
