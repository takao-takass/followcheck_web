<?php

namespace App\Http\Controllers\Gallery;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Managers\Gallery\GalleryAllManager;
use App\ViewModels\Gallery\GalleryAllViewModel;
use App\Constants\WebRoute;

class GalleryAllController extends Controller
{
    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }
        
        $raw_page = $request->input('page');
        $page = $raw_page == null ? 0 : $raw_page;
        $service_user_id = $this->session_user->service_user_id;

        $manager = new GalleryAllManager();
        $items = $manager->fetch($service_user_id, $page);

        $param['viewModel'] = new GalleryAllViewModel($page, $items);

        return  response()->view('gallery.all', $param);
    }


}
