<?php
/**
 * Data model for tweet_medias entity.
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
 * Class TweetMedias
 * 
 * @category DataModel
 * @package  App\DataModels
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class TweetMedias extends Model
{
    const TABLE_NAME = 'tweet_medias';

    // タイムスタンプのカスタマイズ
    const CREATED_AT = 'create_datetime';
    const UPDATED_AT = 'update_datetime';

    // モデルと関連しているテーブル
    protected $table = self::TABLE_NAME;
    // テーブルの主キー
    protected $primaryKey = ['service_user_id','user_id','tweet_id'];
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
    public $url;
    public $type;
    public $sizes;
    public $bitrate;
    public $file_name;
    public $directory_path;
    public $thumb_file_name;
    public $thumb_directory_path;
    public $download_error;
    public $create_datetime;
    public $update_datetime;
    public $deleted;

}
