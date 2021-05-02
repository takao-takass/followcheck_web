<?php
/**
 * Data model for code entity.
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
 * Class Code
 * 
 * @category DataModel
 * @package  App\DataModels
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
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
