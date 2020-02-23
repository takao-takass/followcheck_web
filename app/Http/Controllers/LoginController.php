<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        #if($this->isValidToken()){
        #    return redirect('eims/list/0')
        #    ->cookie('sign',$this->updateToken()->signtext,24*60);
        #}

        return view('login');
    }

    /**
     * ログアウトする
     */
    #public function logout(){
    #    return redirect('eims/login')
    #    ->cookie('sign','',0);
    #}

    /**
     * 認証API
     */
    /*
    public function auth(Request $request)
    {
        // リクエストパラメータを取得
        $email = $request['email'];
        $password = $request['password'];

        // 入力チェック
        if($email == null || $password == null){
            throw new ParamInvalidException(
                'メールアドレスとパスワードを入力してください。',
                ['email','password']
            );
        }

        // ユーザマスタのチェック
        $userrecord = DB::table('user_master')
        ->where('email', $email)
        ->where('deleted', 0)
        ->select('id','authtext')
        ->first();
        if($userrecord == null){
            // メールアドレスが登録されていない
            throw new ParamInvalidException(
                'メールアドレスまたはパスワードが違います。',
                ['email','password']
            );
        }
        if(!password_verify($password,$userrecord->authtext)){
            // パスワードが異なる
            throw new ParamInvalidException(
                'メールアドレスまたはパスワードが違います。',
                ['email','password']
            );
        }

        // トークンを生成する
        $now = Carbon::now('Asia/Tokyo');
        $token = new Token;
        $token->user_id = $userrecord->id;
        $token->ipaddress = \Request::ip();
        $token->expire_datetime = $now->addDay(1);
        $token->signtext = password_hash($token->user_id . $token->expire_datetime, PASSWORD_ARGON2I);
        DB::table('token')->insert(
            [
                'signtext' => $token->signtext,
                'user_id' => $token->user_id,
                'ipaddress'=> $token->ipaddress,
                'expire_datetime' => $token->expire_datetime,
            ]
        );

        return response('',200)->cookie('sign', $token->signtext, 60*24);
    }
*/
}
