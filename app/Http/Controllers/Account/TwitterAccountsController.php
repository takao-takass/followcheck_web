<?php

namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\Controller;
use App\Exceptions\ParamInvalidException;
use App\DataModels\RelationalUsers;
use App\DataModels\TweetTakeUsers;
use App\DataModels\Tweets;
use App\DataModels\TweetMedias;
use App\Constants\WebRoute;
use App\Constants\Invalid;
use App\Models\Accounts\UserTweetsModel;
use App\ViewModels\TwitterAccountsViewModel;
use App\ViewModels\TwitterAccount;


class TwitterAccountsController extends Controller
{
    const RECORDS_COUNT = 20;

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authentication();

        switch ($request->input('error')) {
            case Invalid::DUPULICATED:
                $param['error'] = 'すでに登録されています。';
                break;
            case Invalid::REQUIRED:
                $param['error'] = 'ユーザIDを入力してください。';
                break;
            case Invalid::NOT_FOUND:
                $param['error'] = 'Twitterに存在しないIDです。';
                break;
            default:
                $param['error'] = null;
                break;
        }

        // ユーザリストを取得
        $viewModel = new TwitterAccountsViewModel();
        $viewModel->Page = $request->input('page') == null ? 0 : $request->input('page');
        $viewModel->Count = RelationalUsers::count();
        $viewModel->MaxPage = floor($viewModel->Count / self::RECORDS_COUNT);
        $relational_users = RelationalUsers::orderBy('create_datetime','desc')
            ->skip(self::RECORDS_COUNT * $viewModel->Page)
            ->take(self::RECORDS_COUNT)
            ->get()
            ->toArray();
        
        // アカウントの登録で重複した場合は当該ユーザを一覧の先頭に追加する
        if($request->input('error') == Invalid::DUPULICATED && $request->input('disp_name')!=null){
            $add_relational_user = RelationalUsers::orderBy('create_datetime','desc')
                ->where('disp_name',$request->input('disp_name'))
                ->get()
                ->toArray();
            array_unshift($relational_users,$add_relational_user[0]);
        }

        $relational_user_ids = array_column( $relational_users, 'user_id');

        // ツイート取得対象を取得
        $tweet_take_users = TweetTakeUsers::select(['user_id'])
            ->where('service_user_id', $this->session_user->service_user_id)
            ->wherein('user_id',$relational_user_ids)
            ->get()
            ->toArray();

        $twitter_accounts = [];
        foreach($relational_users as $relational_user ){

            $twitter_account = new TwitterAccount();
            $twitter_account->User = $relational_user;
            $twitter_account->TakingTweet = False != array_search(
                $relational_user['user_id'],
                array_column( $tweet_take_users, 'user_id')
            );
            $twitter_account->TakedFollow = False;
            $twitter_account->TakedFavorite = False;
            array_push($twitter_accounts,$twitter_account);
        }
        $viewModel->Accounts = $twitter_accounts;
        $param['data'] = $viewModel;
        
        return view('account.twitter_accounts', $param);
    }

    /**
     * ユーザを追加する
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $this->apiAuthentication();

        $disp_name = $request['disp_name'];

        // 入力OK？
        if(empty($disp_name)){
            $param['error'] = Invalid::REQUIRED;
            return redirect()->route(WebRoute::TWITTER_ACCOUNT_INDEX, $param);
        }
        
        // 登録済？
        $record = RelationalUsers::where('disp_name',$disp_name)
            ->count();
        if($record == 1){
            $param['error'] = Invalid::DUPULICATED;
            $param['disp_name'] = $disp_name;
            return redirect()->route(WebRoute::TWITTER_ACCOUNT_INDEX, $param);
        }

        // Twitterに問い合わせ
        $twitterApi = new TwitterOAuth(
            config('app.consumer_key'), 
            config('app.consumer_secret'), 
            config('app.access_token'), 
            config('app.access_token_secret')
        );
        $response = $twitterApi
            ->get("users/show", ["screen_name" => $disp_name]);

        // レスポンスの確認
        if (!property_exists($response, 'id_str')){
            $param['error'] = Invalid::NOT_FOUND;
            return redirect()->route(WebRoute::TWITTER_ACCOUNT_INDEX, $param);
        }
        
        DB::table(RelationalUsers::TABLE_NAME)
            ->insert(
                [
                    'user_id' => $response->id_str,
                    'disp_name' => $response->screen_name,
                    'name' => $response->name,
                    'description' => $response->description,
                    'protected' => $response->protected,
                    'theme_color' => '',
                    'follow_count' => $response->friends_count,
                    'follower_count' => $response->followers_count,
                    'update_datetime' => '2000-01-01',
                ]
            );

        return redirect(route(WebRoute::TWITTER_ACCOUNT_INDEX));
    }
}
