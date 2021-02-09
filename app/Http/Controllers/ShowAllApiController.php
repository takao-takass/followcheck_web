<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ShowAllApiController extends Controller
{
    public function keep(Request $request)
    {
        if(!$this->isValidToken()){
            return response(401);
        }

        $tweet_id = $request['tweet_id'];
        $count = DB::table('keep_tweets')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', '=',  $tweet_id)
            ->Count();

        if($count == 0){
            DB::table('keep_tweets')
                ->insert(
                    [
                        'service_user_id'=>$this->session_user->service_user_id,
                        'tweet_id'=>$tweet_id
                    ]
                );
        }

        return response();
    }
}
