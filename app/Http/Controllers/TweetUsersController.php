<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class TweetUsersController extends Controller
{

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page = 0)
    {
        // アカウントの情報を取得
        $service_user_id = "0000000001";
 
        // 表示するアカウントを取得する
        $accounts = DB::connection('mysql')->select(
            " SELECT RU.user_id,RU.disp_name,RU.name,RU.thumbnail_url,TWC.tweet_ct,TMC.media_ct" .
            " FROM tweet_take_users TT" .
            " INNER JOIN relational_users RU" .
            " ON TT.user_id = RU.user_id" .
            " LEFT JOIN (" .
            " 	SELECT TW.service_user_id,TW.user_id,COUNT(tweet_id) AS tweet_ct" .
            " 	FROM tweets TW" .
            " 	GROUP BY TW.service_user_id,TW.user_id" .
            " ) TWC" .
            " ON TT.user_id = TWC.user_id" .
            " AND TT.service_user_id = TWC.service_user_id" .
            " LEFT JOIN (" .
            " 	SELECT TW.service_user_id,TW.user_id,COUNT(TM.url) AS media_ct" .
            " 	FROM tweets TW" .
            " 	INNER JOIN tweet_medias TM" .
            " 	ON TW.tweet_id = TM.tweet_id" .
            " 	GROUP BY TW.service_user_id,TW.user_id" .
            " ) TMC" .
            " ON TT.user_id = TMC.user_id" .
            " AND TT.service_user_id = TMC.service_user_id" .
            " WHERE TT.service_user_id = '". $service_user_id ."'" .
            " AND TT.`status` >= '5'" .
            " AND TT.deleted = 0" .
            " ORDER BY TT.create_datetime DESC"
        );
        $param['accounts'] = [];
        foreach($accounts as $account){
            $param['accounts'][] = [
                'user_id' => $account->user_id,
                'disp_name' => $account->disp_name,
                'name' => $account->name,
                'tweet_ct' => $account->tweet_ct,
                'media_ct' => $account->media_ct,
                'thumbnail_url'=> $account->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$account->thumbnail_url
            ];
        }

        return  response()
        ->view('tweetusers', $param);
    }
}