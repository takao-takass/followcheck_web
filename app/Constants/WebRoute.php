<?php
/**
 * Constants for Routes.
 *
 * PHP Version >= 8.0
 *
 * @category Constants
 * @package  App\Constants
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
namespace App\Constants;

/**
 * Class WebRoute
 *
 * @category Constants
 * @package  App\Constants
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class WebRoute
{
    const TWITTER_ACCOUNT_INDEX = 'twitter.account.index';
    const TWITTER_ACCOUNT_ADD = 'twitter.account.add';

    const LOGIN_LOGOUT = 'login.logout';

    const USER_DETAIL = 'user.index';

    const TWEETUSER_INDEX = 'tweetuser.index';

    const GROUP_INDEX = 'group.index';
    const GROUP_ADD = 'group.add';
    const GROUP_DELETE = 'group.delete';
    const GROUP_MEMBER_INDEX = 'group_member.index';
    const GROUP_MEMBER_JOIN = 'group_member.join';
    const GROUP_MEMBER_LEAVE = 'group_member.leave';
    const GROUP_FREE_ACCOUNT_INDEX = 'group_free_account.index';

    const GALLERY_ALL = 'gallery.all';
    const GALLERY_USER = 'gallery.user';

}
