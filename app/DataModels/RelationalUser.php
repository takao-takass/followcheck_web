<?php

namespace App\DataModels;

use Illuminate\Database\Eloquent\Model;

class RelationalUser extends Model
{
    const TABLE_NAME = 'relational_users';

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
    public $user_id;
    public $disp_name;
    public $name;
    public $thumbnail_url;
    public $description;
    public $theme_color;
    public $follow_count;
    public $follower_count;
    public $icecream;
    public $icecream_datetime;
    public $not_found;
    public $protected;
    public $verify_datetime;
    public $create_datetime;
    public $update_datetime;
    public $deleted;


}