<?php
/**
 * Data model for groups entity.
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
 * Class Groups
 * 
 * @category DataModel
 * @package  App\DataModels
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class Groups extends Model
{
    const TABLE_NAME = 'groups';

    // タイムスタンプのカスタマイズ
    const CREATED_AT = 'create_datetime';
    const UPDATED_AT = 'update_datetime';

    // モデルと関連しているテーブル
    protected $table = self::TABLE_NAME;
    // テーブルの主キー
    protected $primaryKey = ['id'];
    // IDが自動増分
    public $incrementing = true;
    // 主キーの型
    protected $keyType = 'int';
    // タイムスタンプの自動更新
    public $timestamps = false;

    /**
     * Relational for group_users entity.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupUsers()
    {
        return $this->hasMany('App\DataModels\GroupUsers', 'group_id');
    }

}