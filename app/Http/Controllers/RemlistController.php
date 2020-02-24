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
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $remusers = DB::connection('mysql')->select(
        ' SELECT RL.name, RL.disp_name, RL.follow_count, RL.follower_count, RM.followed, DATEDIFF(NOW(), RM.update_datetime) AS dayold' .
        ' FROM remove_users RM' .
        ' LEFT JOIN relational_users RL' .
        ' ON RM.remove_user_id = RL.user_id' .
        ' ORDER BY DATEDIFF(NOW(), RM.update_datetime)'
        );
        #$remusers = DB::table('remove_users')
        #->get();
        /*->leftJoin('relational_users RL', 'remove_users.remove_user_id', '=', 'RL.user_id')
        ->orderBy('DATEDIFF(NOW(), RM.update_datetime)', 'asc')
        ->select('RL.name', 'RL.disp_name', 'RL.follow_count', 'RL.follower_count', 'remove_users.followed', 'DATEDIFF(NOW(), remove_users.update_datetime) AS dayold')
        ->get();
            */
        // 一覧データの設定
        $param['users'] = [];
        foreach($remusers as $user){
            $param['users'][] = [
                'name' => $user->name,
                'disp_name' => $user->disp_name,
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