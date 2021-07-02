<?php
/**
 * Controller class for "グループ"
 * 
 * PHP Version >= 8.0
 * 
 * @category Group
 * @package  App\Http\Controllers\Group
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataModels\Groups;
use App\ViewModels\Group\GroupsViewModel;
use App\Constants\WebRoute;
use App\Constants\Invalid;

/**
 * Class GroupController
 * 
 * @category Group
 * @package  App\Http\Controllers\Group
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class GroupController extends Controller
{
    /**
     * Render Index.
     * 
     * @param Request $request 
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }

        $param['error'] = null;

        $view_model = new GroupsViewModel();
        $view_model->groups = Groups::
            select(
                [
                    'id',
                    'name',
                ]
            )
            ->where('service_user_id', $this->getTokenUser()->service_user_id)
            ->orderBy('create_datetime', 'asc')
            ->get()
            ->toArray();

        $param['view_model'] = $view_model;

        return  response()->view('group.index', $param);
    }

    /**
     * Add group.
     * 
     * @param Request $request 
     * 
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }

        $name = $request['groupName'];
        if (empty($name)) {
            $param['error'] = Invalid::REQUIRED;
            return redirect()->route(WebRoute::GROUP_INDEX, $param);
        }

        $group = new Groups;
        $group['service_user_id'] = $this->getTokenUser()->service_user_id;
        $group['name'] = $name;
        $group->save();

        return response()->redirectToRoute(WebRoute::GROUP_INDEX);
    }

    /**
     * Delete group.
     * 
     * @param Request $request 
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }

        $id = $request['groupId'];

        Groups::
            where('id', $id)
            ->where('service_user_id', $this->getTokenUser()->service_user_id)
            ->delete();

        return response()->redirectToRoute(WebRoute::GROUP_INDEX);
    }

}
