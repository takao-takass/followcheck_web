<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use App\Models\TweetFilter;
use Carbon\Carbon;

class TweetsController extends Controller
{
    // 検索方法の区分値
    const SEARCH_TYPE_BYUSER = 1;
    const SEARCH_TYPE_BUGROUP = 2;
    const SEARCH_TYPE_BYOLD = 3;
    
    /**
     * 画面表示(ユーザID)
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id, $page=0)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // 初期検索条件を設定する
        $param['filter'] = [
            'user_id' => $user_id,
            'group_id' => '',
            'page' => $page,
            'reply_check' => '',
            'retweet_check' => '',
            'media_check' => '',
            'keep_check' => '',
            'unkeep_check' => '',
            'unchecked_check' => 'checked'
        ];

        return  response()->view('tweets', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * 画面表示(グループID)
     *
     * @return \Illuminate\Http\Response
     */
    public function gindex($group_id, $page=0)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // 初期検索条件を設定する
        $param['filter'] = [
            'user_id' => '',
            'group_id' => $group_id,
            'page' => $page,
            'reply_check' => '',
            'retweet_check' => '',
            'media_check' => '',
            'keep_check' => '',
            'unkeep_check' => '',
            'unchecked_check' => 'checked'
        ];

        return  response()->view('tweets', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ツイート一覧API
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 取得条件を取り出す
        $filters = new TweetListFilter(
            $this->session_user->service_user_id,
            $request['user'],
            $request['group'],
            intval($request['page']),
            $request['filter-reply'],
            $request['filter-retweet'],
            $request['filter-media'],
            $request['filter-keep'],
            $request['filter-unchecked']
        );
        
        // 入力チェックを行う



        // 検索方法の決定
        // グループIDが設定されていない：BY USER
        // グループIDが"ALL"：BY USER （ユーザ指定なし）
        // グループIDが"OLD"：OLD
        // グループIDが指定されている：BY GROUP
        var $searchType;
        switch ($filters->group_id) {
            case "":
                $searchType = self::SEARCH_TYPE_BYUSER;
                break;
            case "ALL":
                $searchType = self::SEARCH_TYPE_BYUSER;
                $filters->user_id = "";
                $filters->group_id = "";
                break;
            case "OLD":
                $searchType = self::SEARCH_TYPE_BYOLD;
                break;
            default :
                $searchType = self::SEARCH_TYPE_BYGROUP;
                break;
        }

        // クエリの準備
        $tweetList = new TweetList($filters);

        // ツイートの総数を取得
        var $tweetCount = 0;
        switch ($searchType) {
            case self::SEARCH_TYPE_BYUSER:
                $tweetCount = $tweetList->CountByUser();
                break;
            case self::SEARCH_TYPE_BYOLD:
                //$queryCnt = ;
                break;
            case self::SEARCH_TYPE_BYGROUP:
                $tweetCount = $tweetList->CountByGroup();
                break;
        }

        // ページ切り替えのリンクを設定するための条件
        $param['uesr_id'] = $user_id;
        $param['group_id'] = $group_id;
        $param['prev_page'] = $numPage-1;
        $param['next_page'] = $numPage+1;
        $param['max_page'] = ceil($tweetCount / 100);
        $param['record'] = $tweetCount;

        // ツイートの一覧を取得
        switch ($searchType) {
            case self::SEARCH_TYPE_BYUSER:
                $param['accounts'] = $tweetList->ListByUser();
                break;
            case self::SEARCH_TYPE_BYOLD:
                //$queryCnt = ;
                break;
            case self::SEARCH_TYPE_BYGROUP:
                $param['accounts'] = $tweetList->ListByGroup();
                break;
        }

        return response($param,200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ツイートをキープする
     *
     * @return \Illuminate\Http\Response
     */
    public function keep(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (!isset($request['tweetid'])){
            throw new ParamInvalidException(
                'プロパティが設定されていません。',
                ['tweetid']
            );
        }

        // キープテーブルに存在するかチェックする
        // 無ければ登録する
        $tweetIdList = explode(",",$request['tweetid']);
        foreach($tweetIdList as $tweetId){

            $exists = DB::table('keep_tweets')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', $tweetid)
            ->count();
    
            if($exists==0){
                DB::table('keep_tweets')
                ->insert(
                    [
                        'service_user_id'=>$this->session_user->service_user_id,
                        'tweet_id'=>$tweetid
                    ]
                );
            }
        }

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * ツイートをキープから外す
     *
     * @return \Illuminate\Http\Response
     */
    public function unkeep(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (!isset($request['tweetid'])){
            throw new ParamInvalidException(
                'プロパティが設定されていません。',
                ['tweetid']
            );
        }
        
        // キープテーブルに登録されているか確認する
        // 登録されていれば削除する
        $tweetIdList = explode(",",$request['tweetid']);
        foreach($tweetIdList as $tweetId){

            $exists = DB::table('keep_tweets')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', $tweetId)
            ->count();
    
            if($exists>0){
                DB::table('keep_tweets')
                ->where('service_user_id', $this->session_user->service_user_id)
                ->where('tweet_id', $tweetId)
                ->delete();
            }

        }

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }


    /**
     * ツイートを既読する
     *
     * @return \Illuminate\Http\Response
     */
    public function checked(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        // 入力チェック
        // APIからユーザが取得できない場合はエラー
        if (!isset($request['tweetid'])){
            throw new ParamInvalidException(
                'プロパティが設定されていません。',
                ['tweetid']
            );
        }

        // 既読テーブルに存在するかチェックする
        // 無ければ登録する
        $tweetIdList = explode(",",$request['tweetid']);
        foreach($tweetIdList as $tweetId){

            $exists = DB::table('checked_tweets')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('tweet_id', $tweetId)
            ->count();
    
            if($exists==0){
                DB::table('checked_tweets')
                ->insert(
                    [
                        'service_user_id'=>$this->session_user->service_user_id,
                        'tweet_id'=>$tweetId
                    ]
                );
            }

        }

        return response('',200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }


}