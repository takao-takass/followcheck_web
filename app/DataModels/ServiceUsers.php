<?php

namespace App\DataModels;

use Illuminate\Database\Eloquent\Model;

class ServiceUsers extends Model
{
    const TABLE_NAME = 'service_users';

    // タイムスタンプのカスタマイズ
    const CREATED_AT = 'create_datetime';
    const UPDATED_AT = 'update_datetime';

    // モデルと関連しているテーブル
    protected $table = self::TABLE_NAME;
    // テーブルの主キー
    protected $primaryKey = ['service_user_id'];
    // IDが自動増分
    public $incrementing = false;
    // 主キーの型
    protected $keyType = 'string';
    // タイムスタンプの自動更新
    public $timestamps = false;

}
