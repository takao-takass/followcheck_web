<?php

namespace App\Http\Controllers;

use App\DataModels\Tweets;
use App\DataModels\KeepTweets;
use Illuminate\Http\Response;

class KeepApiController extends Controller
{
    /*
     * まとめてKEEP
     */
    public function entry(array $tweet_ids): Response
    {
        $this->apiAuthentication();

        $tweets = Tweets::select(
                [
                    'service_user_id',
                    'tweet_id',
                ]
            )
            ->Where('service_user_id', $this->session_user->service_user_id)
            ->WhereIn('tweet_id', $tweet_ids)
            ->get()
            ->toArray();

        foreach ($tweets as $tweet){
            $keep_tweet = new KeepTweets();
            $keep_tweet['service_user_id'] = $tweet['service_user_id'];
            $keep_tweet['tweet_id'] = $tweet['tweet_id'];
            $keep_tweet->save();
        }

        return response('',200);
    }
}
