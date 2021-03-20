<?php

namespace App\DataModels;

use Illuminate\Database\Eloquent\Model;

class Tweets extends Model
{
    const TABLE_NAME = 'tweets';

    // タイムスタンプのカスタマイズ
    const CREATED_AT = 'create_datetime';
    const UPDATED_AT = 'update_datetime';

    // モデルと関連しているテーブル
    protected $table = self::TABLE_NAME;
    // テーブルの主キー
    protected $primaryKey = ['service_user_id','user_id'];
    // IDが自動増分
    public $incrementing = false;
    // 主キーの型
    protected $keyType = 'string';
    // タイムスタンプの自動更新
    public $timestamps = false;

    // カラム
    public $service_user_id;
    public $user_id;
    public $tweet_id;
    public $tweet_user_id;
    public $body;
    public $arranged_body;
    public $tweeted_datetime;
    public $favolite_count;
    public $retweet_count;
    public $replied;
    public $retweeted;
    public $is_media;
    public $media_ready;
    public $create_datetime;
    public $update_datetime;
    public $deleted;

}