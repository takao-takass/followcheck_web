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
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $remusers = DB::connection('mysql')->select(
        " SELECT RL.user_id, RL.name, RL.disp_name, RL.thumbnail_url, RL.follow_count, RL.follower_count, DATEDIFF(NOW(), RM.create_datetime) AS dayold" .
        " FROM follow_eachother RM" .
        " LEFT JOIN relational_users RL" .
        " ON RM.follow_user_id = RL.user_id" .
        " WHERE RM.undisplayed = '0'" .
        " ORDER BY DATEDIFF(NOW(), RM.create_datetime) DESC"
        );

        // 一覧データの設定
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