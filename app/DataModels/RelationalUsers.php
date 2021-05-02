<?php
/**
 * Data model for relational_users entity.
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
 * Class RelationalUsers
 * 
 * @category DataModel
 * @package  App\DataModels
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class RelationalUsers extends Model
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