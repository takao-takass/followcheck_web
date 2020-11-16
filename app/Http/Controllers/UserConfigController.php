<?php
namespace App\Http\Controllers;

set_include_path(config('app.vendor_path'));
require "vendor/autoload.php";

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\Token;
use Carbon\Carbon;
use Abraham\TwitterOAuth\TwitterOAuth;

class UserConfigController extends Controller
{
    public function index()
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $param['user_config'] = DB::table('user_config')
            ->select(['config_id','value'])
            ->where('service_user_id', $this->session_user->service_user_id)
            ->orderBy('config_id')
            ->get();

        return response()
        ->view('user_config', $param);
    }

    public function save(Request $request)
    {
        // 有効なトークンでない場合は認証エラー
        if(!$this->isValidToken()){
            response('Unauthorized ',401);
        }

        $retweet = $request->input('retweet');
        $reply = $request->input('reply');

        DB::table('user_config')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('config_id',1)
            ->update(['value'=> $retweet=='on'?1:0]);

        DB::table('user_config')
            ->where('service_user_id', $this->session_user->service_user_id)
            ->where('config_id',2)
            ->update(['value'=>$reply=='on'?1:0]);

        return redirect()->route('config.index');
    }

}
