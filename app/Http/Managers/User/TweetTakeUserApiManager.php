<?php

namespace App\Http\Managers\User;

use App\DataModels\TweetTakeUsers;

class TweetTakeUserApiManager
{

    public function setNotTweetLongtime(string $service_user_id, string $user_id, int $notTweetLongTime): bool
    {
        $count = TweetTakeUsers::where('service_user_id', $service_user_id)
            ->where('user_id', $user_id)
            ->update(['not_tweeted_longtime' => $notTweetLongTime]);

        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }
}
