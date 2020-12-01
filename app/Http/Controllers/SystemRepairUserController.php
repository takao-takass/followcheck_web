<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Exceptions\ParamConflictException;
use App\Models\Token;
use App\Models\TweetTakeUser;
use App\ViewModels\TweetUsersViewModel;
use Carbon\Carbon;


set_include_path(config('app.vendor_path'));
require "vendor/autoload.php";

class SystemRepairUserController extends Controller
{

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (! $this->isValidToken()) {
            return redirect()->route('login.logout');
        }

        $results = DB::table('relational_users')
            ->where('name','=','　')
            ->where('icecream','=','0')
            ->select(['user_id'])
            ->take(500)
            ->get();

        $users = [];
        foreach ($results as $result) {
            array_push($users, $result->user_id);
        }
        $param['repairable_users'] = $users;

        return response()->view('system_repair_user', $param);
    }

    /**
     * ユーザを追加する
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if (! $this->isValidToken()) {
            return redirect()->route('login.logout');
        }

        $user_id = $request['user_id'];
        if (empty($user_id)) {
            $param['error'] = 'require';
            return redirect()->route('tweetuser.index', $param);
        }

        // Twitterアカウントの情報を取得
        $twitter_api = new TwitterOAuth(config('app.consumer_key'), config('app.consumer_secret'), config('app.access_token'), config('app.access_token_secret'));
        $response = $twitter_api->get("users/show", [
            "user_id" => $user_id
        ]);

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (! property_exists($response, 'id_str')) {
            $param['error'] = 'user_not_found';
            return redirect()->route('system.repair_user.index', $param);
        }

        DB::table('relational_users')
            ->where('user_id','=',$response->id_str)
            ->delete();

        // Twitterユーザマスタに登録する
        $remusers = DB::connection('mysql')->insert(
            " INSERT INTO relational_users (user_id, disp_name, name, description, theme_color, follow_count, follower_count, create_datetime, update_datetime, deleted)" .
            " VALUES (?, ?, ?, '', '', 0, 0, NOW(), '2000-01-01', 0)".
            " ON DUPLICATE KEY UPDATE ".
            " update_datetime = NOW() /*既に登録済みの場合は更新日時のみ更新*/ "
            ,[$response->id_str,$response->screen_name,$response->name]);

        return redirect()->route('system.repair_user.index');
    }
}
