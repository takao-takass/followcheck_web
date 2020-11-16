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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if($this->isValidToken()){
            return redirect(action('AccountsController@index'));
        }

        return view('login');
    }

    /**
     * ログアウトする
     */
    public function logout(){
        return redirect(action('LoginController@index'))
        ->cookie('sign','',0);
    }

    /**
     * 認証API
     */
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
        $userrecord = DB::table('service_users')
        ->where('mailaddress', $email)
        ->where('deleted', 0)
        ->select('service_user_id','password')
        ->first();
        if($userrecord == null){
            // メールアドレスが登録されていない
            throw new ParamInvalidException(
                'メールアドレスまたはパスワードが違います。',
                ['email','password']
            );
        }
        if(!password_verify($password,$userrecord->password)){
            // パスワードが異なる
            throw new ParamInvalidException(
                'メールアドレスまたはパスワードが違います。',
                ['email','password']
            );
        }

        // トークンを生成する
        $now = Carbon::now('Asia/Tokyo');
        $token = new Token;
        $token->user_id = $userrecord->service_user_id;
        $token->ipaddress = \Request::ip();
        $token->expire_datetime = $now->addDay(1);
        $token->signtext = password_hash($token->user_id . $token->expire_datetime, PASSWORD_BCRYPT);
        DB::table('token')->insert(
            [
                'sign' => $token->signtext,
                'service_user_id' => $token->user_id,
                'ipaddress'=> $token->ipaddress,
                'expire_datetime' => $token->expire_datetime,
                'create_datetime' => NOW(),
                'update_datetime' => NOW(),
            ]
        );

        return response('',200)->cookie('sign', $token->signtext, 60*24*30);
    }
}
