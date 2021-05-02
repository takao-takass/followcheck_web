<?php
/**
 * Model class.
 * 
 * PHP Version >= 8.0
 * 
 * @category TweetUsers
 * @package  App\Models
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
namespace App\Models;

/**
 * Class TweetTakeUser
 * 
 * @category TweetUsers
 * @package  App\Http\Controllers
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class TweetTakeUser
{
    public string $user_id;
    public string $disp_name;
    public string $user_name;
    public string $thumbnail_url;
    public string $status;
    public string $description;
    public int $tweet_ready_count;

    /**
     * Constructor.
     * 
     * @param $user_id           string
     * @param $disp_name         ?string
     * @param $name              ?string
     * @param $thumbnail_url     ?string
     * @param $status            string
     * @param $description       ?string
     * @param $tweet_ready_count int
     */
    public function __construct(
        string $user_id,
        ?string $disp_name,
        ?string $name,
        ?string $thumbnail_url,
        string $status,
        ?string $description,
        int $tweet_ready_count
    ) {
        $this->user_id = $user_id;
        $this->disp_name = $disp_name ?? '';
        $this->name = $name ?? '';
        $this->thumbnail_url = $thumbnail_url ?? '';
        $this->status = $status;
        $this->description = $description ?? '';
        $this->tweet_ready_count = $tweet_ready_count;
    }
}
