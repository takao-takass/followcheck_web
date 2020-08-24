<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class OldTweetsController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page=0)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // 初期検索条件を設定する
        $param['filter'] = [
            'page' => $page,
            'reply_check' => '',
            'retweet_check' => '',
            'media_check' => ''
        ];

        return  response()->view('oldtweets', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ツイート一覧API
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 取得条件を取り出す
        $page = $request['page'];
        $onreply = $request['filter-reply'];
        $onretweet = $request['filter-retweet'];
        $onlymedia = $request['filter-media'];

        // ページ数から取得範囲の計算
        $pageRecord = 200;
        $numPage = intval($page);

        // ツイートの総数を取得
        $queryCnt = 
            " SELECT COUNT(*) AS ct" .
            " FROM queue_delete_tweets DT " .
            " INNER JOIN tweets TW".
            " ON DT.service_user_id = TW.service_user_id".
            " AND DT.user_id = TW.user_id".
            " AND DT.tweet_id = TW.tweet_id".
            " WHERE DT.service_user_id = '".$this->session_user->service_user_id."'" .
            // リプライを除く
            (
                $onreply=='' ? "" :
                " AND TW.replied = '0'"
            ).
            // リツイートを除く
            (
                $onretweet=='' ? "" :
                " AND TW.retweeted = '0'"
            ).
            // メディア添付のみに絞る
            (
                $onlymedia=='' ? "" :
                " AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE DT.tweet_id = TM.tweet_id )"
            );
        Log::info($queryCnt);
        $res = DB::connection('mysql')->select($queryCnt);
        $recordCount = $res[0]->ct;

        // ページ切り替えのリンクを設定するための条件
        $param['prev_page'] = $numPage-1;
        $param['next_page'] = $numPage+1;
        $param['max_page'] = ceil($recordCount / $pageRecord);
        $param['record'] = $recordCount;

        // ツイートを取得する
        $queryList = 
        " SELECT TW.tweet_id".
        "       ,TW.thumbnail_url".
        "       ,TW.tweeted_datetime".
        "       ,TW.body".
        "       ,TW.replied".
        "       ,TW.weblink".
        "       ,TW.user_id".
        "       ,TM.`type`".
        "       ,CASE WHEN KT.tweet_id IS NULL THEN '0' ELSE '1' END AS kept".
        "       ,GROUP_CONCAT(CONCAT(REPLACE(TM.directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),TM.file_name)) AS media_path".
        "       ,GROUP_CONCAT(CONCAT(REPLACE(TM.thumb_directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),TM.thumb_file_name)) AS thumb_names".
        "   FROM (".
        "            SELECT TW.tweet_id".
        "                  ,RU.thumbnail_url".
        "                  ,convert_tz(TW.tweeted_datetime, '+00:00','+09:00') AS tweeted_datetime".
        "                  ,TW.body".
        "                  ,TW.replied".
        "                  ,CONCAT('https://twitter.com/',RU.disp_name,'/status/',TW.tweet_id) AS weblink".
        "                  ,TW.tweet_user_id AS user_id".
        "                  ,TW.service_user_id".
        "              FROM queue_delete_tweets DT".
        "             INNER JOIN tweets TW".
        "                ON DT.service_user_id = TW.service_user_id".
        "               AND DT.user_id = TW.user_id".
        "               AND DT.tweet_id = TW.tweet_id".
        "             INNER JOIN relational_users RU ".
        "                ON TW.tweet_user_id = RU.user_id ".
        "             WHERE DT.service_user_id = '".$this->session_user->service_user_id."' ".
        // リプライを除く
        (
            $onreply=='' ? "" :
                    "   AND TW.replied = '0'"
        ).
        // リツイートを除く
        (
            $onretweet=='' ? "" :
                    "   AND TW.retweeted = '0'"
        ).
        // メディア添付のみに絞る
        (
            $onlymedia=='' ? "" :
                    "   AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE DT.tweet_id = TM.tweet_id )"
        ).
        "             ORDER BY TW.tweeted_datetime ".
        "             LIMIT ". $pageRecord .
        "            OFFSET ". $pageRecord*$numPage .
        "        ) TW".
        "  LEFT JOIN tweet_medias TM".
        "    ON TW.tweet_id = TM.tweet_id".
        "  LEFT JOIN keep_tweets KT ".
        "    ON TW.service_user_id = KT.service_user_id".
        "   AND TW.tweet_id = KT.tweet_id".
        "  GROUP BY TW.tweet_id".
        "          ,TW.thumbnail_url".
        "          ,TW.tweeted_datetime".
        "          ,TW.body".
        "          ,TW.replied".
        "          ,TW.weblink".
        "          ,TW.user_id".
        "          ,TM.`type`".
        "          ,CASE WHEN KT.tweet_id IS NULL THEN '0' ELSE '1' END".
        "  ORDER BY TW.tweeted_datetime DESC ";

        Log::info($queryList);
        $tweets = DB::connection('mysql')->select($queryList);
        $param['accounts'] = [];
        foreach($tweets as $tweet){
            $param['accounts'][] = [
                'tweeted_datetime' => $tweet->tweeted_datetime,
                'body' => $tweet->body,
                'replied' => $tweet->replied,
                'media_type' => $tweet->type,
                'media_path' => explode(',',$tweet->media_path),
                'thumb_names' => explode(',',$tweet->thumb_names),
                'thumbnail_url'=> $tweet->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$tweet->thumbnail_url,
                'weblink'=>$tweet->weblink,
                'user_id'=>$tweet->user_id,
                'kept'=>$tweet->kept,
                'tweet_id'=>$tweet->tweet_id
            ];
        }

        return response($param,200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

}