<?php
namespace App\Http\Controllers;

set_include_path(config('app.vendor_path'));
require "vendor/autoload.php";

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;
use Abraham\TwitterOAuth\TwitterOAuth;

class DownloadAccountsController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // アカウントの情報を取得
        $service_user_id = "0000000001";
        $param['serviceUserId'] = $service_user_id;
        $accounts = DB::connection('mysql')->select(
            " SELECT RU.user_id " .
            " 		,RU.name " .
            "       ,RU.disp_name" .
            " 		,RU.thumbnail_url " .
            " 		,TT.`status` " .
            " 		,CASE TT.`status` " .
            " 			WHEN '0' THEN '予約済' " .
            " 			WHEN '1' THEN '処理中' " .
            " 			WHEN '9' THEN '完了' " .
            " 			WHEN 'D' THEN '削除予約済' " .
            " 		END AS status_nm" .
            " FROM tweet_take_users TT " .
            " INNER JOIN relational_users RU " .
            " ON TT.user_id = RU.user_id " .
            " WHERE TT.service_user_id = ?" .
            " ORDER BY TT.create_datetime desc"
            ,[$service_user_id]
        );

        $param['accounts'] = [];
        foreach($accounts as $account){
            $param['accounts'][] = [
                'user_id' => $account->user_id,
                'name' => $account->name,
                'disp_name' => $account->disp_name,
                'status' => $account->status_nm,
                'delbtn_show' => $account->status=='D' ? '0':'1',
                'thumbnail_url'=> $account->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$account->thumbnail_url,
            ];
        }

        return response()
        ->view('dlaccounts', $param);
    }

    /**
     * ユーザを追加する
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {

        // 入力チェック


        
        // Twitterアカウントの情報を取得
        $twitterApi = new TwitterOAuth(config('app.consumer_key'), config('app.consumer_secret'), config('app.access_token'), config('app.access_token_secret'));
        $response = $twitterApi->get("users/show", ["screen_name" => $request['account_name']]);

        // デバッグ出力
        print_r($response);
        if (!property_exists($response, 'id_str')){
            return response('',400);
        }

        // ダウンロードアカウントマスタに登録する
        $remusers = DB::connection('mysql')->insert(
        " INSERT INTO tweet_take_users (service_user_id, user_id, status, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, '0',NOW(), NOW(), 0)" 
        ,[$request['service_user_id'],$response->id_str]);

        // Twitterユーザマスタに登録する
        $remusers = DB::connection('mysql')->insert(
        " REPLACE INTO relational_users (user_id, disp_name, name, thumbnail_url, description, theme_color, follow_count, follower_count, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, ?, '', '', '', 0, 0, NOW(), '2000-01-01', 0)" 
        ,[$response->id_str,$response->screen_name,$response->name]);

        return response('',200);
    }

    /**
     * ユーザを削除する
     *
     * @return \Illuminate\Http\Response
     */
    public function del(Request $request)
    {

        // 入力チェック


        
        // アカウントのステータスを削除予約にする
        $remusers = DB::connection('mysql')->update(
        " UPDATE tweet_take_users" .
        " SET status = 'D' " .
        "    ,update_datetime = NOW() " .
        " WHERE service_user_id = ?" .
        " AND user_id = ?" 
        ,[$request['service_user_id'],$request['user_id']]);

        return response('',200);
    }
}