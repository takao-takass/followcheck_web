<?php

namespace App\Http\Controllers\Gallery;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Managers\Gallery\GalleryManager;
use App\ViewModels\Gallery\GalleryViewModel;
use App\Constants\WebRoute;

class GalleryController extends Controller
{
    public function all(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }
        
        $raw_page = $request->input('page');
        $page = $raw_page == null ? 0 : $raw_page;
        $service_user_id = $this->session_user->service_user_id;

        $manager = new GalleryManager();
        $items = $manager->fetch($service_user_id, $page);

        $param['viewModel'] = new GalleryViewModel($page, $items);

        return  response()->view('gallery.index', $param);
    }

    public function user(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }

        $user_id = $request->input('user_id');
        if ($user_id == null) {
            return response(400);
        }

        $raw_page = $request->input('page');
        $page = $raw_page == null ? 0 : $raw_page;
        $service_user_id = $this->session_user->service_user_id;

        $manager = new GalleryManager();
        $items = $manager->fetch($service_user_id, $page, $user_id);

        $param['viewModel'] = new GalleryViewModel($page, $items);

        return  response()->view('gallery.index', $param);
    }

}
