<?php

namespace App\Http\Controllers\Group;

use App\DataModels\GroupUsers;
use App\DataModels\RelationalUsers;
use App\DataModels\TweetTakeUsers;
use App\ViewModels\TweetUsersViewModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataModels\Groups;
use App\ViewModels\Group\GroupsViewModel;
use App\Constants\WebRoute;
use App\Constants\Invalid;
use Illuminate\Support\Facades\DB;

class GroupMemberController extends Controller
{
    // メンバーの一覧
    public function index(int $id)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }

        $view_model = new GroupMembersViewModel();
        $view_model->group_id = id;
        $user_ids = GroupUsers::
            select(
                [
                    'user_id'
                ]
            )
            ->where('service_user_id', $this->getTokenUser()->service_user_id)
            ->orderBy('create_datetime', 'asc')
            ->take(100)
            ->get()
            ->toArray();
        $view_model->group_members = RelationalUsers::
            select(
                [
                    'user_id',
                    'disp_name',
                    'name',
                    'thumbnail_url',
                    'description',
                ]
            )
            ->wherein('user_id', array_column( $user_ids, 'user_id'))
            ->get()
            ->toArray();
        $param['view_model'] = $view_model;

        return  response()->view('group.members', $param);
    }

    // グループに追加
    public function join(int $id, Request $request)
    {
        // TODO 実装
    }

    // グループから除外
    public function leave(int $id, Request $request)
    {
        // TODO 実装
    }

    // フリーのアカウント一覧
    public function free(int $id)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }

        $view_model = new FreeMembersViewModel();
        $view_model->group_id = id;

        // グループに属していないユーザを取得
        $group_ids = Groups::
            select(
                [
                    'id'
                ]
            )
            ->where('service_user_id', $this->getTokenUser()->service_user_id)
            ->get()
            ->toArray();
        $user_ids = GroupUsers::
            select(
                [
                    'user_id',
                ]
            )
            ->wherein('group_id', array_column( $group_ids, 'id'))
            ->get()
            ->toArray();
        $user_ids = array_column( $user_ids, 'user_id');
        $tweet_take_user_ids = TweetTakeUsers::
            select(
                [
                    'user_id'
                ]
            )
            ->where('service_user_id', $this->getTokenUser()->service_user_id)
            ->get()
            ->toArray();
        $tweet_take_user_ids = array_column( $tweet_take_user_ids, 'user_id');
        $fetch_user_ids = [];
        foreach ($tweet_take_user_ids as $tweet_take_user_id){
            if(!in_array($tweet_take_user_id, $user_ids)){
                array_push($fetch_user_ids, $tweet_take_user_id);
            }
            if(count($fetch_user_ids) > 200){
                break;
            }
        }

        $view_model->free_members = RelationalUsers::
            select(
                [
                    'user_id',
                    'disp_name',
                    'name',
                    'thumbnail_url',
                    'description',
                ]
            )
            ->wherein('user_id', $fetch_user_ids)
            ->get()
            ->toArray();
        $param['view_model'] = $view_model;

        return  response()->view('group.free_members', $param);
    }
}
