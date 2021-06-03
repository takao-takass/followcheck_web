<?php

namespace App\Http\Controllers;

use app\Exceptions\ParamInvalidException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\DataModels\Tweets;
use App\DataModels\TweetMedias;
use App\DataModels\KeepTweets;
use App\ViewModels\KeepByUserViewModel;

class KeepApiController extends Controller
{
    // TODO まとめてKEEPするAPIを実装してください
    public function entry(array $tweet_ids)
    {
        $this->apiAuthentication();

        return response('',200);
    }
}
