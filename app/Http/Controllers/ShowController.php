<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class ShowController extends Controller
{
    /**
     * 画面表示(ユーザ指定)
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
            'page' => $page
        ];

        return  response()->view('show', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * 画面表示(グループ指定)
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
            'page' => $page
        ];

        return  response()->view('show', $param)
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
        $onreply = '1';
        $onretweet = '1';
        $onlymedia = '1';

        // 入力チェックを行う


        
        // ページ数から取得範囲の計算
        $pageRecord = 500;
        $numPage = intval($page);

        // ツイートの総数を取得
        $query = 
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
            );
        $res = DB::connection('mysql')->select($query);
        $recordCount = $res[0]->ct;

        // ページ切り替えのリンクを設定するための条件
        $param['uesr_id'] = $user_id;
        $param['group_id'] = $group_id;
        $param['prev_page'] = $numPage-1;
        $param['next_page'] = $numPage+1;
        $param['max_page'] = ceil($recordCount / $pageRecord);
        $param['record'] = $recordCount;

        // ツイートを取得する
        $query = 
            " SELECT RU.thumbnail_url,convert_tz(TW.tweeted_datetime, '+00:00','+09:00') AS tweeted_datetime," .
            " REGEXP_REPLACE(TW.body, '[\r\n|\r|\n]', '') AS body,TW.favolite_count,TW.retweet_count,TW.replied,media_type,TM.media_path,TM.thumb_names," .
            " CONCAT('https://twitter.com/',RU.disp_name,'/status/',TW.tweet_id) AS weblink " .
            " FROM tweets TW" .
            " LEFT JOIN (" .
            " 	SELECT tweet_id,`type` AS media_type" .
            "       ,GROUP_CONCAT(CONCAT(REPLACE(directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),file_name)) AS media_path" .
            "       ,GROUP_CONCAT(CONCAT(REPLACE(thumb_directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),thumb_file_name)) AS thumb_names" .
            " 	FROM tweet_medias" .
            " 	GROUP BY tweet_id,`type`" .
            " ) TM" .
            " ON TW.tweet_id = TM.tweet_id" .
            " INNER JOIN relational_users RU" .
            " ON TW.tweet_user_id = RU.user_id" .
            " WHERE TW.service_user_id = '".$this->session_user->service_user_id."'" .
            // ユーザIDで取得
            (
                $user_id=='' ? "" :
                " AND TW.user_id = '".$user_id."'"
            ).
            // グループIDで取得
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
            " ORDER BY TW.tweeted_datetime DESC".
            " LIMIT ". $pageRecord .
            " OFFSET ". $pageRecord*$numPage;

        $accounts = DB::connection('mysql')->select($query);
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
                'weblink'=>$account->weblink
            ];
        }

        return response($param,200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }
}