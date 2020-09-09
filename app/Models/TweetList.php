<?php
/**
 * ツイートリストに関するモデル
 */
 
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ツイートリストの条件モデル
 */
class TweetListFilter
{
    // 特定条件
	public $service_user_id;
    public $user_id;
    public $group_id;

	// 絞り込み条件
    public　$onreply;
    public $onretweet;
    public $onlymedia;
    public $onkeep;
    public $onunkeep;
    public $onunchecked;

	// ページング条件
    public $page;

	// コンストラクタ
    function __construct(
		$service_user_id, $user_id, $group_id,
		$onreply, $onretweet, $onlymedia, $onkeep, $onunkeep, $onunchecked)
    {
        $this->service_user_id = $service_user_id;
        $this->user_id = $user_id;
        $this->group_id = $group_id;
        $this->onreply = $onreply;
        $this->onretweet = $onretweet;
        $this->onlymedia = $onlymedia;
        $this->onkeep = $onkeep;
        $this->onunkeep = $onunkeep;
        $this->onunchecked = $onunchecked;
    }
}

/**
 * ツイートリストの検索を行う
 */
class TweetList
{
    public $filters;

    public function __construct($filters){
        $this->filters = $filters;
    }
	
	/**
	 * ユーザ指定による件数取得クエリ
	 */
	public function CountByUser(){
		
		var $query = 
            " SELECT COUNT(*) AS ct" .
            " FROM tweets TW" .
            " WHERE TW.service_user_id = ?" .
            (
				// ユーザIDで絞り込む
                $this->filters->user_id=='' ? "" :
                " AND TW.user_id = ?"
            ).
            (
				// リプライを除く
                $this->filters->onreply=='' ? "" :
                " AND TW.replied = '0'"
            ).
            (
				// リツイートを除く
                $this->filters->onretweet=='' ? "" :
                " AND TW.retweeted = '0'"
            ).
            (
				// メディア添付のみに絞る
                $this->filters->onlymedia=='' ? "" :
                " AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE TW.tweet_id = TM.tweet_id )"
            ).
            (
				// キープしているものに絞る
                $this->filters->onkeep=='' ? "" :
                " AND EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
            ).
            (
				// キープしていないものに絞る
                $this->filters->onunkeep=='' ? "" :
                " AND NOT EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
            ).
            (
				// 既読でないものに絞る
                $this->filters->onunchecked=='' ? "" :
                " AND NOT EXISTS( SELECT 1 FROM checked_tweets CT WHERE TW.service_user_id = CT.service_user_id AND TW.tweet_id = CT.tweet_id )"
            );
			
		// 絞込みパラメータを設定
		var $filterParam = [];
		array_push($filterParam, $this->filters->service_user_id);
		array_push($filterParam, $this->filters->user_id);

		// SQLを発行
		Log::info($query);
		Log::info($filterParam);			
		$queryCnt = DB::connection('mysql')->select($query,$filterParam);
		
		return $queryCnt[0]->ct;

	}

	/* ユーザ指定によるリスト取得 */
	public function ListByUser(){

		var $query = 
	        " SELECT TW.tweet_id".
			"       ,TW.thumbnail_url".
			"       ,TW.tweeted_datetime".
			"       ,TW.body".
			"       ,0 AS favolite_count".
			"       ,0 AS retweet_count".
			"       ,TW.replied".
			"       ,TW.weblink".
			"       ,TW.user_id".
			"       ,TM.`type`".
			"       ,CASE WHEN KT.tweet_id IS NULL THEN '0' ELSE '1' END AS kept".
			"       ,GROUP_CONCAT(CONCAT(REPLACE(TM.directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),TM.file_name)) AS media_path".
			"       ,GROUP_CONCAT(CONCAT(REPLACE(TM.thumb_directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),TM.thumb_file_name)) AS thumb_names".
			"   FROM (".
			"            SELECT TW.tweet_id".
			"                  ,RU.thumbnail_url".
			"                  ,convert_tz(TW.tweeted_datetime, '+00:00','+09:00') AS tweeted_datetime".
			"                  ,TW.body".
			"                  ,TW.replied".
			"                  ,CONCAT('https://twitter.com/',RU.disp_name,'/status/',TW.tweet_id) AS weblink".
			"                  ,TW.tweet_user_id AS user_id".
			"                  ,TW.service_user_id".
			"              FROM tweets TW".
			"             INNER JOIN relational_users RU ".
			"                ON TW.tweet_user_id = RU.user_id ".
			"             WHERE TW.service_user_id = ? ".
			(
				// ユーザIDで取得
				$this->filters->user_id=='' ? "" :
						"   AND TW.user_id = ?"
			).
			(
				// リプライを除く
				$this->filters->onreply=='' ? "" :
						"   AND TW.replied = '0'"
			).
			(
				// リツイートを除く
				$this->filters->onretweet=='' ? "" :
						"   AND TW.retweeted = '0'"
			).
			(
				// メディア添付のみに絞る
				$this->filters->onlymedia=='' ? "" :
						"   AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE TW.tweet_id = TM.tweet_id )"
			).
			(
				// キープしているものに絞る
				$this->filters->onkeep=='' ? "" :
				" AND EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
			).
			(
				// キープしていないものに絞る
				$this->filters->onunkeep=='' ? "" :
				" AND NOT EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
			).
			(
				// 既読でないものに絞る
				$this->filters->onunchecked=='' ? "" :
				" AND NOT EXISTS( SELECT 1 FROM checked_tweets CT WHERE TW.service_user_id = CT.service_user_id AND TW.tweet_id = CT.tweet_id )"
			).
			"             ORDER BY TW.tweeted_datetime DESC ".
			"             LIMIT ? " .
			"            OFFSET ? " .
			"        ) TW".
			"  LEFT JOIN tweet_medias TM".
			"    ON TW.tweet_id = TM.tweet_id".
			"  LEFT JOIN keep_tweets KT ".
			"    ON TW.service_user_id = KT.service_user_id".
			"   AND TW.tweet_id = KT.tweet_id".
			"  GROUP BY TW.tweet_id".
			"          ,TW.thumbnail_url".
			"          ,TW.tweeted_datetime".
			"          ,TW.body".
			"          ,0".
			"          ,0".
			"          ,TW.replied".
			"          ,TW.weblink".
			"          ,TW.user_id".
			"          ,TM.`type`".
			"          ,CASE WHEN KT.tweet_id IS NULL THEN '0' ELSE '1' END".
			"  ORDER BY TW.tweeted_datetime DESC ";
			
		// 絞込みパラメータを設定
		var $filterParam = [];
		array_push($filterParam, $this->filters->service_user_id);
		array_push($filterParam, $this->filters->user_id);
		array_push($filterParam, 100);
		array_push($filterParam, 100 * $this->filters->page);

		// SQLを発行
		Log::info($query);
		Log::info($filterParam);
        $results = DB::connection('mysql')->select($queryList);

		// 結果を整形
        $tweets = [];
        foreach($results as $result){
            $tweets = [
                'tweeted_datetime' => $result->tweeted_datetime,
                'body' => $result->body,
                'favolite_count' => $result->favolite_count,
                'retweet_count' => $result->retweet_count,
                'replied' => $result->replied,
                'media_type' => $result->type,
                'media_path' => explode(',',$result->media_path),
                'thumb_names' => explode(',',$result->thumb_names),
                'thumbnail_url'=> $result->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$result->thumbnail_url,
                'weblink'=>$result->weblink,
                'user_id'=>$result->user_id,
                'kept'=>$result->kept,
                'tweet_id'=>$result->tweet_id
            ];
        }

		return $tweets;
	}

	/* グループ指定による件数取得 */
	public function CountByGroup(){
				
		var $query = 
            " SELECT COUNT(*) AS ct" .
            " FROM tweets TW" .
            " WHERE TW.service_user_id = ?" .
            (
				// ユーザIDで絞り込む
                $this->filters->user_id=='' ? "" :
                " AND TW.user_id = ?"
            ).
            // グループIDで絞り込む
            (
                $this->filters->group_id=='' ? "" :
                " AND ( ".
                "          TW.user_id IN ( ".
                "               SELECT GU.user_id ".
                "                 FROM `groups` GP ".
                "                INNER JOIN group_users GU".
                "                   ON GP.group_id = GU.group_id".
                "                WHERE GP.service_user_id = ?" .
                "                  AND GP.group_id = ?".
                "           ) ".
                "     ) "
            ).
            (
				// リプライを除く
                $this->filters->onreply=='' ? "" :
                " AND TW.replied = '0'"
            ).
            (
				// リツイートを除く
                $this->filters->onretweet=='' ? "" :
                " AND TW.retweeted = '0'"
            ).
            (
				// メディア添付のみに絞る
                $this->filters->onlymedia=='' ? "" :
                " AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE TW.tweet_id = TM.tweet_id )"
            ).
            (
				// キープしているものに絞る
                $this->filters->onkeep=='' ? "" :
                " AND EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
            ).
            (
				// キープしていないものに絞る
                $this->filters->onunkeep=='' ? "" :
                " AND NOT EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
            ).
            (
				// 既読でないものに絞る
                $this->filters->onunchecked=='' ? "" :
                " AND NOT EXISTS( SELECT 1 FROM checked_tweets CT WHERE TW.service_user_id = CT.service_user_id AND TW.tweet_id = CT.tweet_id )"
            );
			
		// 絞込みパラメータを設定
		var $filterParam = [];
		array_push($filterParam, $this->filters->service_user_id);
		array_push($filterParam, $this->filters->user_id);
		array_push($filterParam, $this->filters->service_user_id);
		array_push($filterParam, $this->filters->group_id);

		// SQLを発行
		Log::info($query);
		Log::info($filterParam);			
		$queryCnt = DB::connection('mysql')->select($query,$filterParam);
		
		return $queryCnt[0]->ct;

	}

	/* グループ指定によるリスト取得 */
	public function ListByGroup(){

		var $query = 
	        " SELECT TW.tweet_id".
			"       ,TW.thumbnail_url".
			"       ,TW.tweeted_datetime".
			"       ,TW.body".
			"       ,0 AS favolite_count".
			"       ,0 AS retweet_count".
			"       ,TW.replied".
			"       ,TW.weblink".
			"       ,TW.user_id".
			"       ,TM.`type`".
			"       ,CASE WHEN KT.tweet_id IS NULL THEN '0' ELSE '1' END AS kept".
			"       ,GROUP_CONCAT(CONCAT(REPLACE(TM.directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),TM.file_name)) AS media_path".
			"       ,GROUP_CONCAT(CONCAT(REPLACE(TM.thumb_directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),TM.thumb_file_name)) AS thumb_names".
			"   FROM (".
			"            SELECT TW.tweet_id".
			"                  ,RU.thumbnail_url".
			"                  ,convert_tz(TW.tweeted_datetime, '+00:00','+09:00') AS tweeted_datetime".
			"                  ,TW.body".
			"                  ,TW.replied".
			"                  ,CONCAT('https://twitter.com/',RU.disp_name,'/status/',TW.tweet_id) AS weblink".
			"                  ,TW.tweet_user_id AS user_id".
			"                  ,TW.service_user_id".
			"              FROM tweets TW".
			"             INNER JOIN relational_users RU ".
			"                ON TW.tweet_user_id = RU.user_id ".
			// グループIDで取得
			(
				$this->filters->group_id=='' ? "" :
						" INNER JOIN group_users GU".
						"    ON TW.user_id = GU.user_id".
						" INNER JOIN `groups` GP".
						"    ON GP.group_id = GU.group_id".
						"   AND GP.service_user_id = ?" .
						"   AND GP.group_id = ?"
			).
			"             WHERE TW.service_user_id = ? ".
			(
				// ユーザIDで取得
				$this->filters->user_id=='' ? "" :
						"   AND TW.user_id = ?"
			).
			(
				// リプライを除く
				$this->filters->onreply=='' ? "" :
						"   AND TW.replied = '0'"
			).
			(
				// リツイートを除く
				$this->filters->onretweet=='' ? "" :
						"   AND TW.retweeted = '0'"
			).
			(
				// メディア添付のみに絞る
				$this->filters->onlymedia=='' ? "" :
						"   AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE TW.tweet_id = TM.tweet_id )"
			).
			(
				// キープしているものに絞る
				$this->filters->onkeep=='' ? "" :
				" AND EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
			).
			(
				// キープしていないものに絞る
				$this->filters->onunkeep=='' ? "" :
				" AND NOT EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
			).
			(
				// 既読でないものに絞る
				$this->filters->onunchecked=='' ? "" :
				" AND NOT EXISTS( SELECT 1 FROM checked_tweets CT WHERE TW.service_user_id = CT.service_user_id AND TW.tweet_id = CT.tweet_id )"
			).
			"             ORDER BY TW.tweeted_datetime DESC ".
			"             LIMIT ? " .
			"            OFFSET ? " .
			"        ) TW".
			"  LEFT JOIN tweet_medias TM".
			"    ON TW.tweet_id = TM.tweet_id".
			"  LEFT JOIN keep_tweets KT ".
			"    ON TW.service_user_id = KT.service_user_id".
			"   AND TW.tweet_id = KT.tweet_id".
			"  GROUP BY TW.tweet_id".
			"          ,TW.thumbnail_url".
			"          ,TW.tweeted_datetime".
			"          ,TW.body".
			"          ,0".
			"          ,0".
			"          ,TW.replied".
			"          ,TW.weblink".
			"          ,TW.user_id".
			"          ,TM.`type`".
			"          ,CASE WHEN KT.tweet_id IS NULL THEN '0' ELSE '1' END".
			"  ORDER BY TW.tweeted_datetime DESC ";
			
		// 絞込みパラメータを設定
		var $filterParam = [];
		array_push($filterParam, $this->filters->service_user_id);
		array_push($filterParam, $this->filters->group_id);
		array_push($filterParam, $this->filters->service_user_id);
		array_push($filterParam, $this->filters->user_id);
		array_push($filterParam, 100);
		array_push($filterParam, 100 * $this->filters->page);

		// SQLを発行
		Log::info($query);
		Log::info($filterParam);
        $results = DB::connection('mysql')->select($queryList);

		// 結果を整形
        $tweets = [];
        foreach($results as $result){
            $tweets = [
                'tweeted_datetime' => $result->tweeted_datetime,
                'body' => $result->body,
                'favolite_count' => $result->favolite_count,
                'retweet_count' => $result->retweet_count,
                'replied' => $result->replied,
                'media_type' => $result->type,
                'media_path' => explode(',',$result->media_path),
                'thumb_names' => explode(',',$result->thumb_names),
                'thumbnail_url'=> $result->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$result->thumbnail_url,
                'weblink'=>$result->weblink,
                'user_id'=>$result->user_id,
                'kept'=>$result->kept,
                'tweet_id'=>$result->tweet_id
            ];
        }

		return $tweets;
	}

	/* 削除リストによる件数取得 */
	public function CountByOldQuery(){

		var $query = 
            " SELECT COUNT(*) AS ct" .
            " FROM queue_delete_tweets DT " .
            " INNER JOIN tweets TW".
            " ON DT.service_user_id = TW.service_user_id".
            " AND DT.user_id = TW.user_id".
            " AND DT.tweet_id = TW.tweet_id".
            " WHERE DT.service_user_id = ?" .
            (
				// リプライを除く
                $this->filters->onreply=='' ? "" :
                " AND TW.replied = '0'"
            ).
            (
				// リツイートを除く
                $this->filters->onretweet=='' ? "" :
                " AND TW.retweeted = '0'"
            ).
            (
				// メディア添付のみに絞る
                $this->filters->onlymedia=='' ? "" :
                " AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE TW.tweet_id = TM.tweet_id )"
            ).
            (
				// キープしているものに絞る
                $this->filters->onkeep=='' ? "" :
                " AND EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
            ).
            (
				// キープしていないものに絞る
                $this->filters->onunkeep=='' ? "" :
                " AND NOT EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
            ).
            (
				// 既読でないものに絞る
                $this->filters->onunchecked=='' ? "" :
                " AND NOT EXISTS( SELECT 1 FROM checked_tweets CT WHERE TW.service_user_id = CT.service_user_id AND TW.tweet_id = CT.tweet_id )"
            );
			
		// 絞込みパラメータを設定
		var $filterParam = [];
		array_push($filterParam, $this->filters->service_user_id);

		// SQLを発行
		Log::info($query);
		Log::info($filterParam);			
		$queryCnt = DB::connection('mysql')->select($query,$filterParam);
		
		return $queryCnt[0]->ct;

	}

	/* 削除リストによるリスト取得 */
	public function ListByOldQuery(){

		var $query = 
	        " SELECT TW.tweet_id".
			"       ,TW.thumbnail_url".
			"       ,TW.tweeted_datetime".
			"       ,TW.body".
			"       ,TW.replied".
			"       ,TW.weblink".
			"       ,TW.user_id".
			"       ,TM.`type`".
			"       ,CASE WHEN KT.tweet_id IS NULL THEN '0' ELSE '1' END AS kept".
			"       ,GROUP_CONCAT(CONCAT(REPLACE(TM.directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),TM.file_name)) AS media_path".
			"       ,GROUP_CONCAT(CONCAT(REPLACE(TM.thumb_directory_path,'/opt/followcheck/fcmedia/tweetmedia/','/img/tweetmedia/'),TM.thumb_file_name)) AS thumb_names".
			"   FROM (".
			"            SELECT TW.tweet_id".
			"                  ,RU.thumbnail_url".
			"                  ,convert_tz(TW.tweeted_datetime, '+00:00','+09:00') AS tweeted_datetime".
			"                  ,TW.body".
			"                  ,TW.replied".
			"                  ,CONCAT('https://twitter.com/',RU.disp_name,'/status/',TW.tweet_id) AS weblink".
			"                  ,TW.tweet_user_id AS user_id".
			"                  ,TW.service_user_id".
			"              FROM queue_delete_tweets DT".
			"             INNER JOIN tweets TW".
			"                ON DT.service_user_id = TW.service_user_id".
			"               AND DT.user_id = TW.user_id".
			"               AND DT.tweet_id = TW.tweet_id".
			"             INNER JOIN relational_users RU ".
			"                ON TW.tweet_user_id = RU.user_id ".
			"             WHERE DT.service_user_id = ? ".
			(
				// リプライを除く
				$this->filters->onreply=='' ? "" :
						"   AND TW.replied = '0'"
			).
			(
				// リツイートを除く
				$this->filters->onretweet=='' ? "" :
						"   AND TW.retweeted = '0'"
			).
			(
				// メディア添付のみに絞る
				$this->filters->onlymedia=='' ? "" :
						"   AND EXISTS( SELECT 1 FROM tweet_medias TM WHERE TW.tweet_id = TM.tweet_id )"
			).
			(
				// キープしているものに絞る
				$this->filters->onkeep=='' ? "" :
				" AND EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
			).
			(
				// キープしていないものに絞る
				$this->filters->onunkeep=='' ? "" :
				" AND NOT EXISTS( SELECT 1 FROM keep_tweets KT WHERE TW.service_user_id = KT.service_user_id AND TW.tweet_id = KT.tweet_id )"
			).
			(
				// 既読でないものに絞る
				$this->filters->onunchecked=='' ? "" :
				" AND NOT EXISTS( SELECT 1 FROM checked_tweets CT WHERE TW.service_user_id = CT.service_user_id AND TW.tweet_id = CT.tweet_id )"
			).
			"             ORDER BY TW.tweeted_datetime DESC ".
			"             LIMIT ? " .
			"            OFFSET ? " .
			"        ) TW".
			"  LEFT JOIN tweet_medias TM".
			"    ON TW.tweet_id = TM.tweet_id".
			"  LEFT JOIN keep_tweets KT ".
			"    ON TW.service_user_id = KT.service_user_id".
			"   AND TW.tweet_id = KT.tweet_id".
			"  GROUP BY TW.tweet_id".
			"          ,TW.thumbnail_url".
			"          ,TW.tweeted_datetime".
			"          ,TW.body".
			"          ,TW.replied".
			"          ,TW.weblink".
			"          ,TW.user_id".
			"          ,TM.`type`".
			"          ,CASE WHEN KT.tweet_id IS NULL THEN '0' ELSE '1' END".
			"  ORDER BY TW.tweeted_datetime DESC ";
			
		// 絞込みパラメータを設定
		var $filterParam = [];
		array_push($filterParam, $this->filters->service_user_id);
		array_push($filterParam, 100);
		array_push($filterParam, 100 * $this->filters->page);

		// SQLを発行
		Log::info($query);
		Log::info($filterParam);
        $results = DB::connection('mysql')->select($queryList);

		// 結果を整形
        $tweets = [];
        foreach($results as $result){
            $tweets = [
                'tweeted_datetime' => $result->tweeted_datetime,
                'body' => $result->body,
                'replied' => $result->replied,
                'media_type' => $result->type,
                'media_path' => explode(',',$result->media_path),
                'thumb_names' => explode(',',$result->thumb_names),
                'thumbnail_url'=> $result->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$result->thumbnail_url,
                'weblink'=>$result->weblink,
                'user_id'=>$result->user_id,
                'kept'=>$result->kept,
                'tweet_id'=>$result->tweet_id
            ];
        }

		return $tweets;
	}

}
