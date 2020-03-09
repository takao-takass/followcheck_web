<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class AccountsController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            ];
        }

        return response()
        ->view('accounts', $param);
    }
}