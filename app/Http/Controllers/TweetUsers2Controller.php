<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Exceptions\ParamConflictException;
use App\Models\Token;
use App\Models\TweetTakeUser;
use Carbon\Carbon;

set_include_path(config('app.vendor_path'));
require "vendor/autoload.php";

class TweetUsers2Controller extends Controller
{
    const USERS_COUNT_BY_PAGE = 50;

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page = 0)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $ret = DB::table('tweet_take_users')
            ->Where('service_user_id','=',$this->session_user->service_user_id)
            ->Count();
        $param['Count'] = $ret;
        

        $tweetTakeUsers = DB::table('tweet_take_users')
            ->select('user_id','status')
            ->where('service_user_id','=',$this->session_user->service_user_id)
            ->orderBy('update_datetime','desc')
            ->skip(50 * $page)
            ->take(50)
            ->get();

        $user_ids = [];
        foreach ($tweetTakeUsers as $tweetTakeUser){
            array_push($user_ids, $tweetTakeUser->user_id);
        }
        
        $userDetails = DB::table('relational_users')
            ->select('user_id','disp_name','name','thumbnail_url')
            ->whereIn('user_id',$user_ids)
            ->get();

        $param['Users'] = $tweetTakeUsers;
        $param['UserDetails'] = $userDetails;

        return  response()->view('tweetusers2', $param);
/*

        $query = 
            " SELECT RU.user_id".
            "       ,RU.disp_name".
            "       ,RU.name".
            "       ,RU.thumbnail_url".
            "       ,TT.`status` " .
            "       ,CASE TT.`status` " .
            "            WHEN '0' THEN '取得中..' " .
            "            WHEN '1' THEN '取得中..' " .
            "            WHEN '5' THEN '完了' " .
            "            WHEN '6' THEN '完了' " .
            "            WHEN '9' THEN '完了' " .
            "            WHEN 'D' THEN '削除中..' " .
            "        END AS status_nm" .
            "   FROM tweet_take_users TT" .
            "  INNER JOIN relational_users RU" .
            "     ON TT.user_id = RU.user_id" .
            "   LEFT JOIN (".
            "            SELECT service_user_id".
            "                  ,user_id".
            "                  ,MAX(tweeted_datetime) AS tweeted_datetime".
            "              FROM tweets".
            "             GROUP BY service_user_id".
            "                      ,user_id".
            "        ) SA".
            "     ON TT.service_user_id = SA.service_user_id".
            "    AND TT.user_id = SA.user_id".
            "  WHERE TT.service_user_id = '". $this->session_user->service_user_id ."'" .
            // ユーザ名による絞り込み
            (
                $userName == "" ? "" :
                "    AND RU.disp_name = '". $userName ."'" 
            ).
            "    AND TT.deleted = 0" .
            "  ORDER BY SA.tweeted_datetime DESC".
            "  LIMIT ". $pageRecord .
            " OFFSET ". $pageRecord*$numPage;

        $accounts = DB::connection('mysql')->select($query);
        $param['accounts'] = [];
        foreach($accounts as $account){
            $param['accounts'][] = [
                'user_id' => $account->user_id,
                'disp_name' => $account->disp_name,
                'name' => $account->name,
                'status' => $account->status_nm,
                'tweet_show'=> in_array($account->status, array('5','6','9')) ? '1':'0',
                'delbtn_show' => $account->status=='D' ? '0':'1',
                'thumbnail_url'=> $account->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$account->thumbnail_url
            ];
        }

        
        return  response()->view('tweetusers', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);*/
    }

}