<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class FleolistController extends Controller
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
            ' ORDER BY UA.create_datetime'
        );

        // ユーザIDの取得
        $param = "";
        foreach($userIds as $userId){
            $param = $userId->user_id;
            break;
        }

        return redirect("followcheck/fleolist/".$param."/0");
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

        // 相互フォローリストの総数を取得
        $res = DB::connection('mysql')->select(
            " SELECT COUNT(*) AS ct" .
            " FROM follow_eachother RM" .
            " WHERE RM.user_id = '". $user_id ."'"
        );
        $param['record'] = $res[0]->ct;
        
        // ページ数から取得範囲の計算
        $pageRecord = 50;
        $numPage = intval($page);

        // 相互フォローリストの取得
        $remusers = DB::connection('mysql')->select(
            " SELECT RL.user_id, RL.name, RL.disp_name, RL.thumbnail_url, RL.follow_count, RL.follower_count, DATEDIFF(NOW(), RM.create_datetime) AS dayold" .
            " FROM follow_eachother RM" .
            " LEFT JOIN relational_users RL" .
            " ON RM.follow_user_id = RL.user_id" .
            " WHERE RM.undisplayed = '0'" .
            " AND RM.user_id = '" . $user_id . "'" .
            " ORDER BY DATEDIFF(NOW(), RM.create_datetime) DESC".
            " LIMIT ". $pageRecord .
            " OFFSET ". $pageRecord*$numPage 
        );

        $param['users'] = [];
        foreach($remusers as $user){
            $param['users'][] = [
                'user_id' => $user->user_id,
                'name' => $user->name,
                'disp_name' => $user->disp_name,
                'thumbnail_url'=> $user->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$user->thumbnail_url,
                'follow_count' => $user->follow_count,
                'follower_count' => $user->follower_count,
                'dayold' => $user->dayold,
            ];
        }

        // ページングのリンクを設定するための条件
        $param['uesr_id'] = $user_id;
        $param['prev_page'] = $numPage-1;
        $param['next_page'] = $numPage+1;

        return response()
        ->view('fleolist', $param);
    }

    /**
     * ユーザを非表示にする
     *
     * @return \Illuminate\Http\Response
     */
    public function hide(Request $request)
    {

    }

}