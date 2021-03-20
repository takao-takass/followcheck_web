<?php
namespace App\Http\Controllers;

//set_include_path(config('app.vendor_path'));
//require "vendor/autoload.php";

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        // 登録されているグループを取得
        $param['serviceUserId'] = $this->session_user->service_user_id;
        $groups = DB::connection('mysql')->select(
            " SELECT NULL FROM DUAL WHERE 1 = 2"
        );

        $param['groups'] = [];
        foreach($groups as $group){
            $param['groups'][] = [
                'group_id' => $group->user_id,
                'name' => $group->name,
                'thumbnail_url'=> $group->thumbnail_url=='' ? asset('./img/usericon1.jpg'):$group->thumbnail_url,
            ];
        }

        return response()
        ->view('groups', $param);
    }

    /**
     * グループを追加する
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {

    }

    /**
     * グループを削除する
     *
     * @return \Illuminate\Http\Response
     */
    public function del(Request $request)
    {

    }
}
