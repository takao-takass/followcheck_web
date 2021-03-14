<?php
namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Exceptions\ParamInvalidException;
use App\DataModels\RelationalUser;
use App\Constants\WebRoute;
use App\Constants\Invalid;
use App\Http\Controllers\Controller;

class TwitterAccountsController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authentication();

        $view['users'] = RelationalUser::orderBy('create_datetime','desc')
            ->take(50)
            ->get();

        return view('account.twitter_accounts', $view);
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
        $record = RelationalUser::where('disp_name',$disp_name)
            ->count();
        if($record == 1){
            $param['error'] = Invalid::DUPULICATED;
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

        // アカウントの登録
        /* save()しても反映されないため原因調査が必要
        $relational_user = new RelationalUser;
        $relational_user->user_id = $response->id_str;
        $relational_user->disp_name = $response->screen_name;
        $relational_user->name = $response->name;
        $relational_user->description = $response->description;
        $relational_user->protected = $response->protected
        $relational_user->theme_color = '';
        $relational_user->follow_count = $response->friends_count;
        $relational_user->follower_count = $response->followers_count;
        $relational_user->update_datetime = '2000-01-01';
        $relational_user->save();
        */
        
        DB::table(RelationalUser::TABLE_NAME)
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
