<?php

namespace App\Http\Controllers;

use App\DataModels\ServiceUsers;
use App\DataModels\Tokens;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;

class LoginController extends Controller
{

    public function index()
    {
        if($this->isValidToken()){
            return redirect(action('AccountsController@index'));
        }
        return view('login');
    }

    public function logout(){
        return redirect(action('LoginController@index'))
        ->cookie('sign','',0);
    }

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
        $service_user = ServiceUsers::where('mailaddress', $email)
            ->where('deleted', 0)
            ->select('service_user_id','password')
            ->first();

        if($service_user == null){
            // メールアドレスが登録されていない
            throw new ParamInvalidException(
                'メールアドレスまたはパスワードが違います。',
                ['email','password']
            );
        }

        if(!password_verify($password, $service_user->password)) {
            throw new ParamInvalidException(
                'メールアドレスまたはパスワードが違います。',
                ['email','password']
            );
        }

        // トークンを生成する
        $now = Carbon::now('Asia/Tokyo');
        $token = new Token;
        $token->user_id = $service_user->service_user_id;
        $token->ipaddress = \Request::ip();
        $token->expire_datetime = $now->addDay(1);
        $token->signtext = password_hash($token->user_id . $token->expire_datetime, PASSWORD_BCRYPT);

        Tokens::insert(
            [
                'sign' => $token->signtext,
                'service_user_id' => $token->user_id,
                'ipaddress'=> $token->ipaddress,
                'expire_datetime' => $token->expire_datetime
            ]
        );

        return response('',200)
            ->cookie('sign', $token->signtext, 60*24*30);
    }
}
