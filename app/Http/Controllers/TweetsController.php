<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class TweetsController extends Controller
{
    /**
     * 画面表示(ユーザID)
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id, $page=0)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // 初期検索条件を設定する
        $param['filter'] = [
            'user_id' => $user_id,
            'group_id' => '',
            'page' => $page,
            'reply_check' => '',
            'retweet_check' => '',
            'media_check' => '',
            'keep_check' => ''
        ];

        return  response()->view('tweets', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * 画面表示(グループID)
     *
     * @return \Illuminate\Http\Response
     */
    public function gindex($group_id, $page=0)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // 初期検索条件を設定する
        $param['filter'] = [
            'user_id' => '',
            'group_id' => $group_id,
            'page' => $page,
            'reply_check' => '',
            'retweet_check' => '',
            'media_check' => '',
            'keep_check' => ''
        ];

        return  response()->view('tweets', $param)
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
        $user_id = $request['user'];
        $group_id = $request['group'];
        $page = $request['page'];
        $onreply = $request['filter-reply'];
        $onretweet = $request['filter-retweet'];
        $onlymedia = $request['filter-media'];
        $onkeep = $request['filter-keep'];
        
        // 入力チェックを行う
        if($group_id=="ALL"){
            $group_id = "";
        }
        
        // ページ数から取得範囲の計算
        $pageRecord = 50;
        $numPage = intval($page);

        // ツイートの総数を取得
        $queryCnt = 
            " SELECT COUNT(*) AS ct" .
            " FROM tweets TW" .
            " WHERE TW.service_user_id = '".$this->session_user->service_user_id."'" .
            // ユーザIDで絞り込む
            (
                $user_id=='' ? "" :
                " AND TW.user_id = '".$user_id."'"
            ).
            // グループIDで絞り込む
            (
                $group_id=='' ? "" :
                " AND ( ".
                "          TW.user_id IN ( ".
                "               SELECT GU.user_id ".
                "                 FROM `groups` GP ".
                "                INNER JOIN group_users GU".
                "                   ON GP.group_id = GU.group_id".
                "                WHERE GP.service_user_id = '".$this->session_user->service_user_id."'" .
                "                  AND GP.group_id = '".$group_id."'".
                "           ) ".
                "        OR 'ALL' = '".$group_id."'".
                "     ) "
            ).
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
                " AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE TW.tweet_id = TM.tweet_id )"
            ).
            // メディア添付のみに絞る
            (
                $onkeep=='' ? "" :
                " AND EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
            );
        Log::info($queryCnt);
        $res = DB::connection('mysql')->select($queryCnt);
        $recordCount = $res[0]->ct;

        // ページ切り替えのリンクを設定するための条件
        $param['uesr_id'] = $user_id;
        $param['group_id'] = $group_id;
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
        "       ,TW.favolite_count".
        "       ,TW.retweet_count".
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
        "                  ,TW.favolite_count".
        "                  ,TW.retweet_count".
        "                  ,TW.replied".
        "                  ,CONCAT('https://twitter.com/',RU.disp_name,'/status/',TW.tweet_id) AS weblink".
        "                  ,TW.tweet_user_id AS user_id".
        "                  ,TW.service_user_id".
        "              FROM tweets TW".
        "             INNER JOIN relational_users RU ".
        "                ON TW.tweet_user_id = RU.user_id ".
        // グループIDで取得
        (
            $group_id=='' ? "" :
                    " INNER JOIN group_users GU".
                    "    ON TW.user_id = GU.user_id".
                    " INNER JOIN `groups` GP".
                    "    ON GP.group_id = GU.group_id".
                    "   AND GP.service_user_id = '".$this->session_user->service_user_id."'" .
                    "   AND GP.group_id = '".$group_id."'"
        ).
        "             WHERE TW.service_user_id = '".$this->session_user->service_user_id."' ".
        // ユーザIDで取得
        (
            $user_id=='' ? "" :
                    "   AND TW.user_id = '".$user_id."'"
        ).
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
                    "   AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE TW.tweet_id = TM.tweet_id )"
        ).
        // メディア添付のみに絞る
        (
            $onkeep=='' ? "" :
            " AND EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
        ).
        "             ORDER BY TW.tweeted_datetime DESC ".
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
        "          ,TW.favolite_count".
        "          ,TW.retweet_count".
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
                'favolite_count' => $tweet->favolite_count,
                'retweet_count' => $tweet->retweet_count,
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

    /**
     * ツイートをキープする
     *
     * @return \Illuminate\Http\Response
     */
    public function keep(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (!isset($request['tweetid'])){
            throw new ParamInvalidException(
                'プロパティが設定されていません。',
                ['tweetid']
            );
        }

        // キープテーブルに存在するかチェックする
        // 無ければ登録する
        $exists = DB::table('keep_tweets')
        ->where('service_user_id', $this->session_user->service_user_id)
        ->where('tweet_id', $request->tweetid)
        ->count();

        if($exists==0){
            DB::table('keep_tweets')
            ->insert(
                [
                    'service_user_id'=>$this->session_user->service_user_id,
                    'tweet_id'=>$request->tweetid
                ]
            );
        }

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ツイートをキープから外す
     *
     * @return \Illuminate\Http\Response
     */
    public function unkeep(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (!isset($request['tweetid'])){
            throw new ParamInvalidException(
                'プロパティが設定されていません。',
                ['tweetid']
            );
        }

        // キープテーブルに登録されているか確認する
        // 登録されていれば削除する
        $exists = DB::table('keep_tweets')
        ->where('service_user_id', $this->session_user->service_user_id)
        ->where('tweet_id', $request->tweetid)
        ->count();

        if($exists>0){
            DB::table('keep_tweets')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', $request->tweetid)
            ->delete();
        }

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }
}