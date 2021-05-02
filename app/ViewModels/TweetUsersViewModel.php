<?php
/**
 * View Model class.
 * 
 * PHP Version >= 8.0
 * 
 * @category TweetUsers
 * @package  App\Models
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
namespace App\ViewModels;

/**
 * Class TweetUsersViewModel
 * 
 * @category TweetUsers
 * @package  App\Http\Controllers
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class TweetUsersViewModel
{
    public int $page;

    public int $count;

    public int $max_page;

    public array $tweet_take_users = [];

}

