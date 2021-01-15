<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Token;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $sign;
    private $ip;
    protected $session_user;

    function __construct() {
        $this->sign = \Request::cookie('sign');
        $this->ip = \Request::ip();
    }

    public function getToken(){
        return $this->sign;
    }

    /**
     * トークンの有効性を評価する
     */
    public function isValidToken(){

        \Log::debug('トークンを認証');
        \Log::debug('token = '. $this->sign);
        \Log::debug('address = '. $this->ip);

        DB::table('token')
        ->where('expire_datetime','<', Carbon::now('Asia/Tokyo'))
        ->delete();

        $user = DB::connection('mysql')->select(
            " SELECT SU.service_user_id,SU.`name`,SU.mailaddress".
            " FROM token TK".
            " INNER JOIN service_users SU".
            " ON TK.service_user_id = SU.service_user_id".
            " AND SU.deleted = 0".
            " WHERE TK.sign = '".$this->sign."'".
            " LIMIT 1"
        );

        if(count($user)==0){
            return false;
        }

        $this->updateExpire();

        $this->session_user = new User;
        $this->session_user->service_user_id = $user[0]->service_user_id;
        $this->session_user->name = $user[0]->name;
        $this->session_user->mailaddress = $user[0]->mailaddress;

        return true;
    }

    /**
     * トークンの有効期間を更新する
     */
    public function updateExpire(){
        $expire_datetime = Carbon::now('Asia/Tokyo')->addWeek(1);
        DB::table('token')
            ->where('sign', $this->sign)
            ->update(['expire_datetime' => $expire_datetime]);
    }

    /**
     * トークンを更新する
     */
    public function updateToken(){

        // 新しいトークンを発行
        $token = $this->createToken($this->getTokenUser()->service_user_id);

        \Log::debug('トークンを発行');
        \Log::debug('id = '. $token->user_id);
        \Log::debug('token = '. $token->signtext);

        // 使ったトークンを物理削除
        DB::table('token')
        ->where('sign', $this->sign)
        ->delete();
        return $token;
    }

    /**
     * トークンを生成する
     */
    public function createToken($user_id){

        $token = new Token;
        $token->user_id = $user_id;
        $token->ipaddress = $this->ip;
        $token->expire_datetime = Carbon::now('Asia/Tokyo')->addDay(1);
        $token->signtext = password_hash($token->user_id . $token->expire_datetime, PASSWORD_BCRYPT);
        DB::table('token')->insert(
            [
                'sign' => $token->signtext,
                'service_user_id' => $token->user_id,
                'ipaddress'=> $token->ipaddress,
                'expire_datetime' => $token->expire_datetime,
            ]
        );

        return $token;
    }

    /**
     * トークンからユーザ情報を取得する
     */
    public function getTokenUser(){

        $tokenUser = DB::table('token')
        ->leftJoin('service_users', 'service_users.service_user_id', '=', 'token.service_user_id')
        ->where('sign', $this->sign)
        ->select('service_users.service_user_id','service_users.name','service_users.mailaddress')
        ->first();
        $user = new User;
        $user->service_user_id = $tokenUser->service_user_id;
        $user->name = $tokenUser->name;
        $user->email = $tokenUser->mailaddress;

        return $user;
    }

}
