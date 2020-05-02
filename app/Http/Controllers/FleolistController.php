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
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $userIds = DB::connection('mysql')->select(
            ' SELECT UA.user_id' .
            ' FROM service_users SU' .
            ' INNER JOIN users_accounts UA' .
            ' ON SU.service_user_id = UA.service_user_id' .
            ' WHERE SU.service_user_id = ?'.
            ' ORDER BY UA.create_datetime'
            ,[$this->session_user->service_user_id]
        );

        // ユーザIDの取得
        $param = "0";
        foreach($userIds as $userId){
            $param = $userId->user_id;
            break;
        }

        return redirect("followcheck/fleolist/".$param."/0")
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id, $page)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }


        // アカウントの情報を取得
        $accounts = DB::connection('mysql')->select(
            " SELECT RU.user_id,RU.name,RU.thumbnail_url" .
            " FROM service_users SU" .
            " INNER JOIN users_accounts UA" .
            " ON SU.service_user_id = UA.service_user_id" .
            " INNER JOIN relational_users RU" .
            " ON UA.user_id = RU.user_id" .
            " AND SU.service_user_id = '". $this->session_user->service_user_id ."'" .
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
            " WHERE RM.user_id = '". $user_id ."'" .
            " AND RM.undisplayed = 0"
        );
        $recordCount = $res[0]->ct;
        $param['record'] = $recordCount;
        
        // ページ数から取得範囲の計算
        $pageRecord = 50;
        $numPage = intval($page);

        // 相互フォローリストの取得
        $remusers = DB::connection('mysql')->select(
            " SELECT RL.user_id, RL.name, RL.disp_name, RL.thumbnail_url, RL.follow_count, RL.follower_count, DATEDIFF(NOW(), RM.create_datetime) AS dayold" .
            " FROM follow_eachother RM" .
            " LEFT JOIN relational_users RL" .
            " ON RM.follow_user_id = RL.user_id" .
            " WHERE RM.undisplayed = 0" .
            " AND RM.user_id = '" . $user_id . "'" .
            " ORDER BY RM.create_datetime DESC, RL.disp_name".
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
        $param['max_page'] = ceil($recordCount / $pageRecord);

        return response()
        ->view('fleolist', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ユーザを非表示にする
     *
     * @return \Illuminate\Http\Response
     */
    public function hide(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 入力チェック


        // 非表示に更新する
        $remusers = DB::connection('mysql')->update(
            " UPDATE follow_eachother RM" .
            " SET RM.undisplayed = 1" .
            "    ,RM.update_datetime = NOW()" .
            " WHERE RM.user_id = ?" .
            " AND RM.follow_user_id = ? "
            ,[$request['user_id'],$request['follow_user_id']]);
    
        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

}