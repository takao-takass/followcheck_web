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

class TweetUsers2Controller extends Controller
{
    const USERS_COUNT_BY_PAGE = 50;

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect()->route('login.logout');
        }

        $page = $request->input('page');

        $viewModel = new TweetUsersViewModel();
        $viewModel->Page = $page == null ? 0 : $page;
        $viewModel->Count = DB::table('tweet_take_users')
            ->Where('service_user_id','=',$this->session_user->service_user_id)
            ->Count();
        
        $tweetTakeUsers = DB::table('tweet_take_users')
            ->select('user_id','status')
            ->where('service_user_id','=',$this->session_user->service_user_id)
            ->orderBy('update_datetime','desc')
            ->skip(50 * $viewModel->Page)
            ->take(50)
            ->get();

        $user_ids = [];
        foreach ($tweetTakeUsers as $tweetTakeUser){
            array_push($user_ids, $tweetTakeUser->user_id);
        }
        
        $userDetails = json_decode(json_encode(
            DB::table('relational_users')
                ->select('user_id','disp_name','name','thumbnail_url')
                ->whereIn('user_id',$user_ids)
                ->get()
        ), true);

        $viewModel->TweetTakeUsers = [];
        foreach ($tweetTakeUsers as $tweetTakeUser){
            $userDetail = $userDetails[array_search($tweetTakeUser->user_id, array_column($userDetails, 'user_id'))];
            array_push($viewModel->TweetTakeUsers, new TweetTakeUser(
                $tweetTakeUser->user_id,
                $userDetail['disp_name'],
                $userDetail['name'],
                $userDetail['thumbnail_url'],
                $tweetTakeUser->status,
            ));
        }
        
        $param['Users'] = $viewModel;

        return  response()->view('tweetusers2', $param)
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
            return redirect()->route('login.logout');
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
 //       $remusers = DB::connection('mysql')->insert(
 //       " INSERT INTO relational_users (user_id, disp_name, name, description, theme_color, follow_count, follower_count, create_datetime, update_datetime, deleted)" .
 //       " VALUES (?, ?, ?, '', '', 0, 0, NOW(), '2000-01-01', 0)".
 //       " ON DUPLICATE KEY UPDATE ".
 //       " update_datetime = NOW() /*既に登録済みの場合は更新日時のみ更新*/ "
 //       ,[$response->id_str,$response->screen_name,$response->name]);

        return redirect()->route('tweetuser.index')
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }




}