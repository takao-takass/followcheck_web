<?php
/**
 * Controller class for "ツイートを見る"
 * 
 * PHP Version >= 8.0
 * 
 * @category TweetUsers
 * @package  App\Http\Controllers
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\DataModels\TweetTakeUsers;
use App\DataModels\RelationalUsers;
use App\Models\TweetTakeUser;
use App\ViewModels\TweetUsersViewModel;

/**
 * Class TweetUsers2Controller
 * 
 * @category TweetUsers
 * @package  App\Http\Controllers
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class TweetUsers2Controller extends Controller
{

    /**
     * Render Index.
     *
     * @param Request $request Request parameter.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$this->isValidToken()) {
            return redirect()->route('login.logout');
        }

        $param['ErrorMessage'] = "";
        if (property_exists($request, 'error')) {
            $param['ErrorMessage'] = match ($request['error']) {
                "user_not_found" => 'Twitterに登録されていないユーザです。',
                "require" => 'ユーザ名を入力してください。',
                "duplicated" => '既に登録されているユーザです。',
            };
        }
        $page = $request->input('page');

        $view_model = new TweetUsersViewModel();
        $view_model->page = $page == null ? 0 : $page;
        $view_model->count = DB::table('tweet_take_users')
            ->Where('service_user_id', '=', $this->session_user->service_user_id)
            ->Count();
        $view_model->max_page = floor($view_model->count / 20);

        $tweet_take_users = TweetTakeUsers::
            select(
                [
                    'user_id',
                    'status',
                ]
            )
            ->where('service_user_id', '=', $this->session_user->service_user_id)
            ->orderBy('update_datetime', 'desc')
            ->skip(20 * $view_model->page)
            ->take(20)
            ->get()
            ->toArray();
        $user_ids = array_column($tweet_take_users, 'user_id');

        $user_details = RelationalUsers::
            select(
                [
                    'user_id',
                    'disp_name',
                    'name',
                    'thumbnail_url',
                    'description',
                ]
            )
            ->whereIn('user_id', $user_ids)
            ->get()
            ->toArray();

        $view_model->tweet_take_users = [];
        foreach ($tweet_take_users as $tweet_take_user) {
            $user_detail = $user_details[
                array_search(
                    $tweet_take_user['user_id'],
                    array_column(
                        $user_details,
                        'user_id'
                    )
                )
            ];
            array_push(
                $view_model->tweet_take_users,
                new TweetTakeUser(
                    $tweet_take_user['user_id'],
                    $user_detail['disp_name'],
                    $user_detail['name'],
                    $user_detail['thumbnail_url'],
                    $tweet_take_user['status'],
                    $user_detail['description']
                )
            );
        }

        $param['Users'] = $view_model;

        return response()->view('tweetusers2', $param);
    }

    /**
     * Add account, And redirect to Index.
     *
     * @param Request $request Request parameter.
     * 
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {

        if (!$this->isValidToken()) {
            return redirect()->route('login.logout');
        }

        $user_id = $request['user_id'];
        if (empty($user_id)) {
            $param['error'] = 'require';
            return redirect()->route('tweetuser.index', $param);
        }

        // Twitterアカウントの情報を取得
        $twitter_api = new TwitterOAuth(
            config('app.consumer_key'),
            config('app.consumer_secret'),
            config('app.access_token'),
            config('app.access_token_secret')
        );
        $response = $twitter_api->get(
            "users/show",
            ["screen_name" => $user_id]
        );

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (!property_exists($response, 'id_str')) {
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
        $remusers = DB::connection('mysql')->insert(
            " INSERT INTO tweet_take_users (".
            "   service_user_id,".
            "   user_id,".
            "   status,".
            "   create_datetime,".
            "   update_datetime,".
            "   deleted".
            " ) VALUES (".
            "   ?,".
            "    ?,".
            "   '0',".
            "   NOW(),".
            "   NOW(),".
            "   0".
            " )",
            [
                $this->session_user->service_user_id,
                $response->id_str
            ]
        );

        // Twitterユーザマスタに登録する
        $remusers = DB::connection('mysql')->insert(
            " INSERT INTO relational_users (".
            "   user_id,".
            "   disp_name,".
            "   name,".
            "   description,".
            "   theme_color,".
            "   follow_count,".
            "   follower_count,".
            "   create_datetime,".
            "   update_datetime,".
            "   deleted".
            " ) VALUES (".
            "   ?,".
            "   ?,".
            "   ?,".
            "   '',".
            "   '',".
            "   0,".
            "   0,".
            "   NOW(),".
            "   '2000-01-01',".
            "   0".
            " ) ON DUPLICATE KEY UPDATE ".
            "   update_datetime = NOW()",
            [
                $response->id_str,
                $response->screen_name,
                $response->name
            ]
        );

        return redirect()->route('tweetuser.index');
    }
}
