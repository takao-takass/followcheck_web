<?php

namespace App\Http\Controllers;

use App\DataModels\Tweets;

class TestController extends Controller
{
    public function index()
    {

        /*
        $codes = Tweets::first()
            ->tweetMedia()
            ->get()
            ->toArray();
        */
        

        //$codes = Tweets::take(10)->get();
        //$codes = $codes = Tweets::first();
        $codes = Tweets::take(10)->get();

        foreach($codes as $code){
            var_dump($code->tweetMedias()->get());
        }

        var_dump($codes);

        return response()
        ->view('test', $codes);
    }
}

// object(Illuminate\Database\Eloquent\Collection)