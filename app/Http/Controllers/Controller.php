<?php

/**
 * Base class for Controllers
 * 
 * PHP Version >= 8.0
 * 
 * @category TweetUsers
 * @package  App\Http\Controllers
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Token;
use Carbon\Carbon;

/**
 * Class Controller
 * 
 * @category Base
 * @package  App\Http\Controllers
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $_sign;
    private $_ip;
    protected $session_user;

    /**
     * Constructer.
     */
    function __construct()
    {
        $this->_sign = \Request::cookie('sign');
        $this->_ip = \Request::ip();
    }

    /**
     * Return access token.
     * 
     * @return string
     */
    public function getToken()
    {
        return $this->_sign;
    }

    /**
     * Authentication token.
     * 
     * @return \Illuminate\Http\Response
     */
    public function authentication()
    {
        if (!$this->isValidToken()) {
            return redirect(action('LoginController@logout'));
        }
    }

    /**
     * Authentication token for API.
     * 
     * @return \Illuminate\Http\Response
     */
    public function apiAuthentication()
    {
        if (!$this->isValidToken()) {
            return redirect(action('LoginController@logout'));
        }
    }

    /**
     * Verify token is valid.
     * 
     * @return bool
     */
    public function isValidToken()
    {

        DB::table('token')
            ->where('expire_datetime', '<', Carbon::now('Asia/Tokyo'))
            ->delete();

        $user = DB::connection('mysql')->select(
            " SELECT SU.service_user_id,SU.`name`,SU.mailaddress".
            " FROM token TK".
            " INNER JOIN service_users SU".
            " ON TK.service_user_id = SU.service_user_id".
            " AND SU.deleted = 0".
            " WHERE TK.sign = '".$this->_sign."'".
            " LIMIT 1"
        );

        if (count($user)==0) {
            return false;
        }

        $this->updateExpire();
        $this->session_user = new User;
        $this->session_user->service_user_id = $user[0]->service_user_id;
        $this->session_user->name = $user[0]->name;
        $this->session_user->mailaddress = $user[0]->mailaddress;

        return true;
    }

    /**
     * Update expire time for token.
     * 
     * @return int
     */
    public function updateExpire()
    {
        $expire_datetime = Carbon::now('Asia/Tokyo')->addWeek(1);
        DB::table('token')
            ->where('sign', $this->_sign)
            ->update(['expire_datetime' => $expire_datetime]);
    }

    /**
     * Update token.
     * 
     * @return string
     */
    public function updateToken()
    {

        // 新しいトークンを発行
        $token = $this->createToken($this->getTokenUser()->service_user_id);

        // 使ったトークンを物理削除
        DB::table('token')
            ->where('sign', $this->_sign)
            ->delete();

        return $token;
    }

    /**
     * Create token.
     * 
     * @param $user_id string
     * 
     * @return string
     */
    public function createToken(string $user_id)
    {

        $token = new Token;
        $token->user_id = $user_id;
        $token->ipaddress = $this->_ip;
        $token->expire_datetime = Carbon::now('Asia/Tokyo')->addDay(1);
        $token->signtext = password_hash(
            $token->user_id . $token->expire_datetime,
            PASSWORD_BCRYPT
        );
        DB::table('token')->insert(
            [
                'sign' => $token->signtext,
                'service_user_id' => $token->user_id,
                'ipaddress'=> $token->ipaddress,
                'expire_datetime' => $token->expire_datetime,
            ]
        );

        return $token;
    }

    /**
     * トークンからユーザ情報を取得する
     * 
     * @return User
     */
    public function getTokenUser()
    {

        $tokenUser = DB::table('token')
            ->leftJoin(
                'service_users',
                'service_users.service_user_id',
                '=',
                'token.service_user_id'
            )
            ->where('sign', $this->_sign)
            ->select(
                'service_users.service_user_id',
                'service_users.name',
                'service_users.mailaddress'
            )
            ->first();

        $user = new User;
        $user->service_user_id = $tokenUser->service_user_id;
        $user->name = $tokenUser->name;
        $user->email = $tokenUser->mailaddress;

        return $user;
    }

}
