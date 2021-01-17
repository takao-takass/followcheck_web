<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $param['user'] = DB::table('relational_users')
            ->where('user_id',$request['user_id'])
            ->first();

        $param['tweet_count'] = DB::table('tweets')
            ->where('user_id',$request['user_id'])
            ->where('service_user_id',$this->session_user->service_user_id)
            ->count();

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

        return response()
            ->view('user', $param)
            ->cookie('sign',$this->updateToken()->signtext,24*60);
    }
}
