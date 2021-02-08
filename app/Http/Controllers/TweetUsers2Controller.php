<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\TweetTakeUser;
use App\ViewModels\TweetUsersViewModel;


set_include_path(config('app.vendor_path'));
require "vendor/autoload.php";

class TweetUsers2Controller extends Controller
{

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (! $this->isValidToken()) {
            return redirect()->route('login.logout');
        }

        $param['ErrorMessage'] = "";
        if (property_exists($request, 'error')) {
            switch ($request['error']) {
                case "user_not_found":
                    $param['ErrorMessage'] = 'Twitterに登録されていないユーザです。';
                    break;
                case "require":
                    $param['ErrorMessage'] = 'ユーザ名を入力してください。';
                    break;
                case "duplicated":
                    $param['ErrorMessage'] = '既に登録されているユーザです。';
                    break;
            }
        }
        $page = $request->input('page');

        $viewModel = new TweetUsersViewModel();
        $viewModel->Page = $page == null ? 0 : $page;
        $viewModel->Count = DB::table('tweet_take_users')
            ->Where('service_user_id', '=', $this->session_user->service_user_id)
            ->Count();
        $viewModel->MaxPage = floor($viewModel->Count / 20);

        $tweetTakeUsers = DB::table('tweet_take_users')->select('user_id', 'status')
            ->where('service_user_id', '=', $this->session_user->service_user_id)
            ->orderBy('update_datetime', 'desc')
            ->skip(20 * $viewModel->Page)
            ->take(20)
            ->get();

        $user_ids = [];
        foreach ($tweetTakeUsers as $tweetTakeUser) {
            array_push($user_ids, $tweetTakeUser->user_id);
        }

        $userDetails = json_decode(json_encode(DB::table('relational_users')->select('user_id', 'disp_name', 'name', 'thumbnail_url')
            ->whereIn('user_id', $user_ids)
            ->get()), true);

        $viewModel->TweetTakeUsers = [];
        foreach ($tweetTakeUsers as $tweetTakeUser) {
            $userDetail = $userDetails[array_search($tweetTakeUser->user_id, array_column($userDetails, 'user_id'))];
            array_push($viewModel->TweetTakeUsers, new TweetTakeUser($tweetTakeUser->user_id, $userDetail['disp_name'], $userDetail['name'], $userDetail['thumbnail_url'], $tweetTakeUser->status));
        }

        $param['Users'] = $viewModel;

        return response()->view('tweetusers2', $param);
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
            "screen_name" => $user_id
        ]);

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (! property_exists($response, 'id_str')) {
            $param['error'] = 'user_not_found';
            return redirect()->route('tweetuser.index', $param);
        }

        // 既に登録されているアカウントはエラー
        $exists = DB::table('tweet_take_users')->where('user_id', $response->id_str)
            ->where('service_user_id', $this->session_user->service_user_id)
            ->count();
        if ($exists > 0) {
            $param['error'] = 'duplicated';
            return redirect()->route('tweetuser.index', $param);
        }

        // ダウンロードアカウントマスタに登録する
        $remusers = DB::connection('mysql')->insert(" INSERT INTO tweet_take_users (service_user_id, user_id, status, create_datetime, update_datetime, deleted)" . " VALUES (?, ?, '0',NOW(), NOW(), 0)", [
            $this->session_user->service_user_id,
            $response->id_str
        ]);

        // Twitterユーザマスタに登録する
        $remusers = DB::connection('mysql')->insert(
            " INSERT INTO relational_users (user_id, disp_name, name, description, theme_color, follow_count, follower_count, create_datetime, update_datetime, deleted)" .
            " VALUES (?, ?, ?, '', '', 0, 0, NOW(), '2000-01-01', 0)".
            " ON DUPLICATE KEY UPDATE ".
            " update_datetime = NOW() /*既に登録済みの場合は更新日時のみ更新*/ "
            ,[$response->id_str,$response->screen_name,$response->name]);

        return redirect()->route('tweetuser.index');
    }
}
