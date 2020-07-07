<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Exceptions\ParamConflictException;
use App\Models\Token;
use Carbon\Carbon;

set_include_path(config('app.vendor_path'));
require "vendor/autoload.php";

class TweetUsersController extends Controller
{

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page = 0)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // 初期検索条件を設定する
        $param['filter'] = [
            'page' => $page,
        ];
        
        return  response()->view('tweetusers', $param)
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
        $userName = $request['user'];
        $page = $request['page'];

        // 入力チェックを行う
        
        // ページ数から取得範囲の計算
        $pageRecord = 50;
        $numPage = intval($page);

        // アカウントの総数を取得
        $query = 
            " SELECT COUNT(*) AS ct" .
            " FROM tweet_take_users TT" .
            " WHERE TT.service_user_id = '".$this->session_user->service_user_id."'";
        $res = DB::connection('mysql')->select($query);
        $recordCount = $res[0]->ct;

        // ページ切り替えのリンクを設定するための条件
        $param['prev_page'] = $numPage-1;
        $param['next_page'] = $numPage+1;
        $param['max_page'] = ceil($recordCount / $pageRecord);
        $param['record'] = $recordCount;

        // ツイートを取得する
        $query = 
            " SELECT RU.user_id".
            "       ,RU.disp_name".
            "       ,RU.name".
            "       ,RU.thumbnail_url".
            "       ,TT.`status` " .
            "       ,CASE TT.`status` " .
            "            WHEN '0' THEN '予約済' " .
            "            WHEN '1' THEN '処理中' " .
            "            WHEN '5' THEN '完了' " .
            "            WHEN '6' THEN '最新化中' " .
            "            WHEN '9' THEN '完了' " .
            "            WHEN 'D' THEN '削除予約済' " .
            "        END AS status_nm" .
            "   FROM tweet_take_users TT" .
            "  INNER JOIN relational_users RU" .
            "     ON TT.user_id = RU.user_id" .
            "  WHERE TT.service_user_id = '". $this->session_user->service_user_id ."'" .
            // ユーザ名による絞り込み
            (
                $userName == "" ? "" :
                "    AND RU.disp_name = '". $userName ."'" 
            ).
            "    AND TT.deleted = 0" .
            "  ORDER BY TT.create_datetime DESC".
            "  LIMIT ". $pageRecord .
            " OFFSET ". $pageRecord*$numPage;

        $accounts = DB::connection('mysql')->select($query);
        $param['accounts'] = [];
        foreach($accounts as $account){
            $param['accounts'][] = [
                'user_id' => $account->user_id,
                'disp_name' => $account->disp_name,
                'name' => $account->name,
                'status' => $account->status_nm,
                'tweet_show'=> in_array($account->status, array('5','6','9')) ? '1':'0',
                'delbtn_show' => $account->status=='D' ? '0':'1',
                'thumbnail_url'=> $account->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$account->thumbnail_url
            ];
        }

        return response($param,200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ユーザを追加する
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // Twitterアカウントの情報を取得
        $twitterApi = new TwitterOAuth(config('app.consumer_key'), config('app.consumer_secret'), config('app.access_token'), config('app.access_token_secret'));
        $response = $twitterApi->get("users/show", ["screen_name" => $request['accountname']]);

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (!property_exists($response, 'id_str')){
            throw new ParamInvalidException(
                '入力されたアカウントはTwitterに存在しません。',
                ['accountname']
            );
        }

        // 既に登録されているアカウントはエラー
        $exists = DB::table('tweet_take_users')
        ->where('user_id', $response->id_str)
        ->where('service_user_id', $this->session_user->service_user_id)
        ->count();
        if($exists>0){
            throw new ParamConflictException(
                '入力されたアカウントは既に登録されています。',
                ['accountname']
            );
        }

        // ダウンロードアカウントマスタに登録する
        $remusers = DB::connection('mysql')->insert(
        " INSERT INTO tweet_take_users (service_user_id, user_id, status, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, '0',NOW(), NOW(), 0)" 
        ,[$this->session_user->service_user_id,$response->id_str]);

        // Twitterユーザマスタに登録する
        $remusers = DB::connection('mysql')->insert(
        " REPLACE INTO relational_users (user_id, disp_name, name, description, theme_color, follow_count, follower_count, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, ?, '', '', 0, 0, NOW(), '2000-01-01', 0)" 
        ,[$response->id_str,$response->screen_name,$response->name]);

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ユーザを削除する
     *
     * @return \Illuminate\Http\Response
     */
    public function del(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // アカウントのステータスを削除予約にする
        $remusers = DB::connection('mysql')->update(
        " UPDATE tweet_take_users" .
        " SET status = 'D' " .
        "    ,update_datetime = NOW() " .
        " WHERE service_user_id = ?" .
        " AND user_id = ?" 
        ,[$this->session_user->service_user_id,$request['user_id']]);

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

}