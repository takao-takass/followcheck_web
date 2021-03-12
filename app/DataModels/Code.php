<?php

namespace App\DataModels;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    
    // モデルと関連しているテーブル
    protected $table = 'code';
    // テーブルの主キー
    protected $primaryKey = ['type','value'];
    // IDが自動増分
    public $incrementing = false;
    // 主キーの型
    protected $keyType = 'string';
    // タイムスタンプの自動更新
    public $timestamps = false;

    // カラム
    public $type;
    public $value;
    public $used_count;
    public $disabled;
    public $create_datetime;
    public $update_datetime;
    public $deleted;

}
