<?php

namespace App\Http\Controllers\Gallery;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Controller;
use App\Http\Managers\Gallery\GalleryManager;
use App\ViewModels\Gallery\GalleryViewModel;
use App\Constants\WebRoute;
use App\Constants\MediaThumbnailSize;
use App\DataModels\RelationalUsers;

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

        
        $thumbnail_size = $request->Cookie('thumbnail_size');
        if ($thumbnail_size == null) {
            $thumbnail_size = MediaThumbnailSize::MEDIUM;
        }

        $param['viewModel'] = new GalleryViewModel('', '', $page, $thumbnail_size, $items);

        return  response()
            ->view('gallery.index', $param)
            ->cookie('thumbnail_size', $thumbnail_size, 60*24*365);
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
        
        $thumbnail_size = $request->Cookie('thumbnail_size');
        if ($thumbnail_size == null) {
            $thumbnail_size = MediaThumbnailSize::MEDIUM;
        }

        $relational_user = RelationalUsers::select(['name'])
            ->where('user_id', $user_id)
            ->first();

        $param['viewModel'] = new GalleryViewModel($user_id, $relational_user['name'], $page, $thumbnail_size, $items);

        return  response()
            ->view('gallery.index', $param)
            ->cookie('thumbnail_size', $thumbnail_size, 60*24*365);
    }
}
