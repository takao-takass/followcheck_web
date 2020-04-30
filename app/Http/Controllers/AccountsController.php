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

class AccountsController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // アカウントの情報を取得
        $service_user_id = "0000000001";
        $param['serviceUserId'] = $service_user_id;
        $accounts = DB::connection('mysql')->select(
            " SELECT RU.user_id,RU.name,RU.thumbnail_url" .
            " FROM service_users SU" .
            " INNER JOIN users_accounts UA" .
            " ON SU.service_user_id = UA.service_user_id" .
            " INNER JOIN relational_users RU" .
            " ON UA.user_id = RU.user_id" .
            " AND SU.service_user_id = '". $service_user_id ."'" .
            " ORDER BY UA.create_datetime ASC"
        );

        $param['accounts'] = [];
        foreach($accounts as $account){
            $param['accounts'][] = [
                'user_id' => $account->user_id,
                'name' => $account->name,
                'thumbnail_url'=> $account->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$account->thumbnail_url,
            ];
        }


        return response()
        ->view('accounts', $param)
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

        // 入力チェック


        
        // Twitterアカウントの情報を取得
        $twitterApi = new TwitterOAuth(config('app.consumer_key'), config('app.consumer_secret'), config('app.access_token'), config('app.access_token_secret'));
        $response = $twitterApi->get("users/show", ["screen_name" => $request['account_name']]);

        // デバッグ出力
        print_r($response);
        if (!property_exists($response, 'id_str')){
            return response('',400);
        }

        // アカウントマスタに登録する
        $remusers = DB::connection('mysql')->insert(
        " INSERT INTO users_accounts (service_user_id, user_id, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, NOW(), NOW(), 0)" 
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
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 入力チェック


        
        // アカウントマスタから削除する
        $remusers = DB::connection('mysql')->delete(
        " DELETE FROM users_accounts" .
        " WHERE service_user_id = ?" .
        " AND user_id = ?" 
        ,[$request['service_user_id'],$request['user_id']]);

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }
}