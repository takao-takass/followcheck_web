<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class TweetsController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id, $page=0)
    {
        // アカウントの情報を取得
        $service_user_id = "0000000001";

        // ツイートの総数を取得
        $res = DB::connection('mysql')->select(
            " SELECT COUNT(*) AS ct" .
            " FROM tweets TW" .
            " WHERE TW.service_user_id = '".$service_user_id."'" .
            " AND TW.user_id = '".$user_id."'"
        );
        $recordCount = $res[0]->ct;
        $param['record'] = $recordCount;

        // ページ数から取得範囲の計算
        $pageRecord = 200;
        $numPage = intval($page);

        // ページングのリンクを設定するための条件
        $param['uesr_id'] = $user_id;
        $param['prev_page'] = $numPage-1;
        $param['next_page'] = $numPage+1;
        $param['max_page'] = ceil($recordCount / $pageRecord);

        // ツイートを取得する
        $accounts = DB::connection('mysql')->select(
            " SELECT RU.thumbnail_url,TW.tweeted_datetime,TW.body,TW.favolite_count,TW.retweet_count,TW.replied,media_type,TM.media_path,TM.thumb_names" .
            " FROM tweets TW" .
            " LEFT JOIN (" .
            " 	SELECT tweet_id,`type` AS media_type,GROUP_CONCAT(CONCAT(REPLACE(directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/'),file_name)) AS media_path,GROUP_CONCAT(thumb_file_name) AS thumb_names" .
            " 	FROM tweet_medias" .
            " 	GROUP BY tweet_id,`type`" .
            " ) TM" .
            " ON TW.tweet_id = TM.tweet_id" .
            " INNER JOIN relational_users RU" .
            " ON TW.user_id = RU.user_id" .
            " WHERE TW.service_user_id = '".$service_user_id."'" .
            " AND TW.user_id = '".$user_id."'" .
            " ORDER BY TW.tweeted_datetime DESC".
            " LIMIT ". $pageRecord .
            " OFFSET ". $pageRecord*$numPage 
        );
        $param['accounts'] = [];
        foreach($accounts as $account){
            $param['accounts'][] = [
                'tweeted_datetime' => $account->tweeted_datetime,
                'body' => $account->body,
                'favolite_count' => $account->favolite_count,
                'retweet_count' => $account->retweet_count,
                'replied' => $account->replied,
                'media_type' => $account->media_type,
                'media_path' => explode(',',$account->media_path),
                'thumb_names' => explode(',',$account->thumb_names),
                'thumbnail_url'=> $account->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$account->thumbnail_url,
            ];
        }

        return  response()
        ->view('tweets', $param);
    }
}