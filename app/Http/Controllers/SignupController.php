<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\UserEntry;
use App\Models\Token;
use Carbon\Carbon;

class SignupController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('signup');
    }

    /**
     * ユーザー登録
     */
    public function entry(Request $request)
    {
        // 入力情報を取得
        $user = new UserEntry;
        $user->email = $request['email'];
        $user->password = $request['password'];
        $user->passwordcheck = $request['passwordcheck'];

        // 入力情報のチェック
        $this->checkParam($user);

        // ユーザーIDの採番
        $dbUserMaster = DB::table('service_users');
        $user->id = sprintf('%010d', $dbUserMaster->count());
        $user->name = substr($user->email, 0, 50);

        // ユーザマスタに登録
        $dbUserMaster->insert(
                [
                    'service_user_id' => $user->id,
                    'name' => $user->name,
                    'mailaddress' => $user->email,
                    'password'=> password_hash($user->password, PASSWORD_BCRYPT, ['cost' => 12]),
                    'create_datetime'=>NOW(),
                    'update_datetime'=>NOW()
                ]
        );

        // トークンを生成する
        $now = Carbon::now('Asia/Tokyo');
        $token = new Token;
        $token->user_id = $user->id;
        $token->ipaddress = \Request::ip();
        $token->expire_datetime = $now->addDay(1);
        $token->signtext = password_hash($token->user_id . $token->expire_datetime, PASSWORD_DEFAULT);
        DB::table('token')->insert(
            [
                'sign' => $token->signtext,
                'service_user_id' => $token->user_id,
                'ipaddress'=> $token->ipaddress,
                'expire_datetime' => $token->expire_datetime,
                'create_datetime'=>NOW(),
                'update_datetime'=>NOW()
            ]
        );

        return response('',200)->cookie('sign', $token->signtext, 60*24);
    }

    /**
     * ユーザー登録前のチェック処理
     */
    private function checkParam(UserEntry $user){

        // 更新パラメータにNULLが含まれていればエラー
        $nullKeys = [];
        $itemprops = get_object_vars($user);
        foreach($itemprops as $key => $value){
            if($value == null && in_array($key,UserEntry::$requireProps)){
                $nullKeys[] = $key;
            };
        }
        if(count($nullKeys)>0){
            throw new ParamInvalidException(
                '入力項目は全て入力してください。',
                $nullKeys
            );
        }

        // パスワードが確認用と一致しなければエラー
        if($user->password != $user->passwordcheck){
            throw new ParamInvalidException(
                'パスワードが一致しません。',
                ['password','passwordcheck']
            );
        }

        // メールアドレスが既に登録されている場合はエラー
        $emailCount = DB::table('service_users')
        ->where('mailaddress', $user->email)
        ->count();
        if($emailCount>0){
            throw new ParamInvalidException(
                'メールアドレスは既に登録されています。',
                ['email']
            );
        }

    }
}