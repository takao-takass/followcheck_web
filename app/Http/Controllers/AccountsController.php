<?php
namespace App\Http\Controllers;

set_include_path(config('app.vendor_path'));
require "vendor/autoload.php";

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
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
        $param['serviceUserId'] = $this->session_user->service_user_id;
        $accounts = DB::connection('mysql')->select(
            " SELECT RU.user_id,RU.name,RU.thumbnail_url" .
            " FROM service_users SU" .
            " INNER JOIN users_accounts UA" .
            " ON SU.service_user_id = UA.service_user_id" .
            " INNER JOIN relational_users RU" .
            " ON UA.user_id = RU.user_id" .
            " AND SU.service_user_id = '" . $this->session_user->service_user_id ."'" .
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
        ->view('accounts', $param);
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
        $exists = DB::table('users_accounts')
        ->where('user_id', $response->id_str)
        ->where('service_user_id', $this->session_user->service_user_id)
        ->count();
        if($exists>0){
            throw new ParamInvalidException(
                '入力されたアカウントは既に登録されています。',
                ['accountname']
            );
        }

        // アカウントマスタに登録する
        DB::connection('mysql')->insert(
        " INSERT INTO users_accounts (service_user_id, user_id, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, NOW(), NOW(), 0)"
        ,[$this->session_user->service_user_id,$response->id_str]);

        // Twitterユーザマスタに登録する
        DB::connection('mysql')->insert(
        " INSERT INTO relational_users (user_id, disp_name, name, description, theme_color, follow_count, follower_count, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, ?, '', '', 0, 0, NOW(), '2000-01-01', 0)".
        " ON DUPLICATE KEY UPDATE ".
        " update_datetime = NOW() /*既に登録済みの場合は更新日時のみ更新*/ "
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

        // アカウントマスタから削除する
        DB::connection('mysql')->delete(
        " DELETE FROM users_accounts" .
        " WHERE service_user_id = ?" .
        " AND user_id = ?"
        ,[$this->session_user->service_user_id,$request['user_id']]);

        return response('',200);
    }
}
