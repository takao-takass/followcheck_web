<?php
namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Exceptions\ParamInvalidException;
use App\DataModels\RelationalUser;
use App\Http\Controllers\Controller;

class TwitterAccountsApiController extends Controller
{
    /**
     * ユーザを追加する
     *
     * @return \Illuminate\Http\Response
     */
    public function add($disp_name)
    {
        $this->apiAuthentication();
        
        // 登録済？
        $record = RelationalUser::where('disp_name',$disp_name)
            ->count();
        if($record == 1){
            throw new ParamInvalidException(
                '入力されたアカウントは既に登録されています。',
                ['disp_name']
            );
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
            throw new ParamInvalidException(
                '入力されたアカウントはTwitterに存在しません。',
                ['disp_name']
            );
        }

        // アカウントの登録
        $relational_user = new RerationalUser;
        $relational_user->user_id = $response->id_str;
        $relational_user->disp_name = $response->screen_name;
        $relational_user->name = $response->name;
        $relational_user->description = $response->description;
        $relational_user->protected = $response->protected;
        $relational_user->theme_color = '';
        $relational_user->follow_count = $response->followers_count;
        $relational_user->follower_count = $response->friends_count;
        $relational_user->update_datetime = '2000-01-01';
        $relational_user->save();

        
        return response('',200);
    }
}
