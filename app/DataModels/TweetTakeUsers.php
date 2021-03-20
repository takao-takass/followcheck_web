<?php

namespace App\DataModels;

use Illuminate\Database\Eloquent\Model;

class TweetTakeUsers extends Model
{
    const TABLE_NAME = 'tweet_take_users';

    // タイムスタンプのカスタマイズ
    const CREATED_AT = 'create_datetime';
    const UPDATED_AT = 'update_datetime';

    // モデルと関連しているテーブル
    protected $table = self::TABLE_NAME;
    // テーブルの主キー
    protected $primaryKey = 'user_id';
    // IDが自動増分
    public $incrementing = false;
    // 主キーの型
    protected $keyType = 'string';
    // タイムスタンプの自動更新
    public $timestamps = false;

    // カラム
    public $service_user_id;
    public $user_id;
    public $status;
    public $taked_datetime;
    public $continue_tweet_id;
    public $include_retweet;
    public $not_tweeted_longtime;
    public $create_datetime;
    public $update_datetime;
    public $deleted;


}
