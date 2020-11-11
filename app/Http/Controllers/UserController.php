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

class UserController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // アカウントの情報を取得
        $account = DB::table('relational_users')
        ->where('user_id',$request['user_id'])
        ->first();

        $param['account'] = [
                'user_id' => $account->user_id,
                'disp_name' => $account->disp_name,
                'name' => $account->name,
                'thumbnail_url'=> $account->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$account->thumbnail_url,
                'description' => $account->description,
                'follow_count' => $account->follow_count,
                'follower_count' => $account->follower_count,
                'icecream' => $account->icecream,
            ];


        return response()
        ->view('user', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ユーザ画面が所属しているグループを取得する
     */
    public function groupList(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }
        
        // 取得条件を取り出す
        $user_id = $request['user'];

        // 入力チェックを行う
        if ($user_id == ''){
            throw new ParamInvalidException(
                'パラメータが指定されていません。',
                ['user']
            );
        }

        // グループの一覧を取得するSQL
        $query = 
            " SELECT GP.group_id" .
            "       ,GP.group_name" .
            "       ,CASE WHEN GU.group_id IS NULL THEN '0' ELSE '1' END AS member" .
            "   FROM `groups` GP" .
            "   LEFT JOIN group_users GU" .
            "     ON GP.group_id = GU.group_id" .
            "    AND GU.user_id = ?" .
            "  WHERE GP.service_user_id = ?".
            "  ORDER BY GP.create_datetime DESC";

        Log::info($query);
        $groups = DB::connection('mysql')->select($query,[$user_id,$this->session_user->service_user_id]);
        $param['groups'] = [];
        foreach($groups as $group){
            $param['groups'][] = [
                'group_id' => $group->group_id,
                'group_name' => $group->group_name,
                'member' => $group->member
            ];
        }

        return response($param,200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }


    /**
     * ユーザの所属グループを更新する
     */
    public function updateUserGroup(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }
        
        // 取得条件を取り出す
        $user_id = $request['user_id'];
        $group_id_list = explode(',',$request['group_id']);

        // 入力チェックを行う

        // ユーザに紐づくグループを全て削除する
        $query = "";
        Log::info($query);


        // ユーザに紐づくグループを登録する
        $query = "";
        Log::info($query);


        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }



    /**
     * 新しいグループを登録する
     */
    public function addGroup(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }
        
        // 取得条件を取り出す
        $group_name = $request['groupname'];

        // 入力チェックを行う
        if ($group_name == ''){
            throw new ParamInvalidException(
                'パラメータが指定されていません。',
                ['groupname']
            );
        }

        // グループIDを発番する
        $query = "";
        Log::info($query);


        // グループIDを取得する
        $query = "";
        Log::info($query);


        // グループを登録する
        $query = "";
        Log::info($query);


        return response('',200)
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
        $remusers = DB::connection('mysql')->insert(
        " INSERT INTO users_accounts (service_user_id, user_id, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, NOW(), NOW(), 0)" 
        ,[$this->session_user->service_user_id,$response->id_str]);

        // Twitterユーザマスタに登録する
        $remusers = DB::connection('mysql')->insert(
        " INSERT INTO relational_users (user_id, disp_name, name, description, theme_color, follow_count, follower_count, create_datetime, update_datetime, deleted)" .
        " VALUES (?, ?, ?, '', '', 0, 0, NOW(), '2000-01-01', 0)".
        " ON DUPLICATE KEY UPDATE ".
        " update_datetime = NOW() /*既に登録済みの場合は更新日時のみ更新*/ "
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
        
        // アカウントマスタから削除する
        $remusers = DB::connection('mysql')->delete(
        " DELETE FROM users_accounts" .
        " WHERE service_user_id = ?" .
        " AND user_id = ?" 
        ,[$this->session_user->service_user_id,$request['user_id']]);

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }
}