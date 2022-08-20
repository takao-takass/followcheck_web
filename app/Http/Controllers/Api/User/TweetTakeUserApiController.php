<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Managers\User\TweetTakeUserApiManager;

class TweetTakeUserApiController extends Controller
{

    public function setNotTweetLongtime(Request $request)
    {

        if (!$this->isValidToken()) {
            return response('', 401);
        }

        $user_id = $request->input('user_id');
        if ($user_id == null) {
            return response('', 400);
        }

        $manager = new TweetTakeUserApiManager();
        $succeed = $manager->setNotTweetLongtime($this->session_user->service_user_id, $user_id, 1);

        if(!$succeed){
            return response('', 400);
        }

        return response()->json(
            [
                'succeed' => true,
            ]
        );


    }
}
