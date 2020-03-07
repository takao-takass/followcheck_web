<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class RemlistController extends Controller
{
    /**
     * 初期処理（初期ユーザ確定）
     *
     * @return \Illuminate\Http\Response
     */
    public function init()
    {
        $service_user_id = "0000000001";

        $userIds = DB::connection('mysql')->select(
            ' SELECT UA.user_id' .
            ' FROM service_users SU' .
            ' INNER JOIN users_accounts UA' .
            ' ON SU.service_user_id = UA.service_user_id' .
            ' ORDER BY UA.user_id DESC'
        );

        // ユーザIDの取得
        $param = "";
        foreach($userIds as $userId){
            $param = $userId->user_id;
            break;
        }

        return redirect("followcheck/remlist/".$param."/0");#->action('RemlistController@index', ['user_id' => $param, 'page' => 0]);
    }

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id, $page)
    {
        // アカウントの情報を取得
        $service_user_id = "0000000001";
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
                'selected'=>$user_id==$account->user_id ? 1 : 0
            ];
        }

        // リムられリストを取得
        $remusers = DB::connection('mysql')->select(
        " SELECT RL.name, RL.disp_name, RL.thumbnail_url, RL.follow_count, RL.follower_count, RM.followed, DATEDIFF(NOW(), RM.create_datetime) AS dayold" .
        " FROM remove_users RM" .
        " LEFT JOIN relational_users RL" .
        " ON RM.remove_user_id = RL.user_id" .
        " WHERE RM.user_id = '". $user_id ."'" .
        " ORDER BY DATEDIFF(NOW(), RM.create_datetime)"
        );

        $param['users'] = [];
        foreach($remusers as $user){
            $param['users'][] = [
                'name' => $user->name,
                'disp_name' => $user->disp_name,
                'thumbnail_url'=> $user->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$user->thumbnail_url,
                'follow_count' => $user->follow_count,
                'follower_count' => $user->follower_count,
                'followed' => $user->followed,
                'dayold' => $user->dayold,
            ];
        }

        return response()
        ->view('remlist', $param);
    }
}