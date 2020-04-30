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

    function __construct() {
        $this->sign = \Request::cookie('sign');
        $this->ip = \Request::ip();
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

        $res = DB::table('token')
        ->where('sign', $this->sign)
        ->where('ipaddress',$this->ip)
        ->count();

        if($res>0){
            return true;
        }
        return false;
    }

    /**
     * トークンを更新する
     */
    public function updateToken(){
        
        // 新しいトークンを発行
        $token = $this->createToken($this->getTokenUser()->id);

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
        $user->id = $tokenUser->service_user_id;
        $user->name = $tokenUser->name;
        $user->email = $tokenUser->mailaddress;

        return $user;
    }

}
