<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\TweetListFilter;
use App\Models\TweetShow;
use App\Models\Token;
use Carbon\Carbon;

class ShowController extends Controller
{

    // 検索方法の区分値
    const SEARCH_TYPE_BYUSER = 1;
    const SEARCH_TYPE_BUGROUP = 2;
    const SEARCH_TYPE_BYOLD = 3;
        
    /**
     * 画面表示(ユーザ指定)
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
            'retweet_check' => '',
            'user_id' => $user_id,
            'group_id' => '',
            'page' => $page
        ];

        return  response()->view('show', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

    /**
     * 画面表示(グループ指定)
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
            'retweet_check' => '',
            'user_id' => '',
            'group_id' => $group_id,
            'page' => $page
        ];

        return  response()->view('show', $param)
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
            '',
            $request['filter-retweet'],
            '1',
            '',
            '',
            ''
        );

        // 検索方法の決定
        // グループIDが設定されていない：BY USER
        // グループIDが"ALL"：BY USER （ユーザ指定なし）
        // グループIDが"OLD"：OLD
        // グループIDが指定されている：BY GROUP
        $searchType = self::SEARCH_TYPE_BYUSER;;
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
        $tweetShow = new TweetShow($filters);

        // ツイートの総数を取得
        $tweetCount = 0;
        switch ($searchType) {
            case self::SEARCH_TYPE_BYUSER:
                $tweetCount = $tweetShow->CountByUser();
                break;
            case self::SEARCH_TYPE_BYOLD:
                $tweetCount = $tweetShow->CountByOld();
                break;
            case self::SEARCH_TYPE_BYGROUP:
                $tweetCount = $tweetShow->CountByGroup();
                break;
        }

        // ページ切り替えのリンクを設定するための条件
        $param['uesr_id'] = $filters->user_id;
        $param['group_id'] = $filters->group_id;
        $param['prev_page'] = $filters->page-1;
        $param['next_page'] = $filters->page+1;
        $param['max_page'] = ceil($tweetCount / 500);
        $param['record'] = $tweetCount;

        // ツイートの一覧を取得
        switch ($searchType) {
            case self::SEARCH_TYPE_BYUSER:
                $param['accounts'] = $tweetShow->ListByUser();
                break;
            case self::SEARCH_TYPE_BYOLD:
                $param['accounts'] = $tweetShow->ListByOld();
                break;
            case self::SEARCH_TYPE_BYGROUP:
                $param['accounts'] = $tweetShow->ListByGroup();
                break;
        }

        return response($param,200)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }
}