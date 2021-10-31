<?php

namespace App\Http\Controllers;

use App\DataModels\Tweets;
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
        $count = Tweets::where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', $tweet_id)
            ->update(['kept'=>1,'shown'=>1]);

        return response()->json([
            'success' => True
        ]);
    }
}
