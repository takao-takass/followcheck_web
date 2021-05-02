<?php
/**
 * Data model for user_config entity.
 * 
 * PHP Version >= 8.0
 * 
 * @category DataModel
 * @package  App\DataModels
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
namespace App\DataModels;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserConfig
 * 
 * @category DataModel
 * @package  App\DataModels
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class UserConfig extends Model
{
    const TABLE_NAME = 'user_config';

    // タイムスタンプのカスタマイズ
    const CREATED_AT = 'create_datetime';
    const UPDATED_AT = 'update_datetime';

    // モデルと関連しているテーブル
    protected $table = self::TABLE_NAME;
    // テーブルの主キー
    protected $primaryKey = ['Id'];
    // IDが自動増分
    public $incrementing = true;
    // 主キーの型
    protected $keyType = 'int';
    // タイムスタンプの自動更新
    public $timestamps = false;

    // カラム
    public $Id;
    public $tweet_user_id;
    public $config_id;
    public $value;
    public $create_datetime;
    public $update_datetime;

}