<?php

namespace App\Http\Controllers\Gallery;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Managers\Gallery\GalleryManager;
use App\ViewModels\Gallery\GalleryViewModel;
use App\Constants\WebRoute;
use App\Constants\MediaThumbnailSize;
use App\Constants\ListSort;
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


        $list_sort = $request->Cookie('list_sort');
        if ($list_sort == null) {
            $list_sort = ListSort::DESC;
        }

        $manager = new GalleryManager();
        $items = $manager->fetch($service_user_id, $page, $list_sort);

        $sum_media_size = 0;
        $min_tweeted_datetime = '';
        if(count($items)){
            $sum_media_size = array_sum(array_column($items, 'media_size'));
            $min_tweeted_datetime = min(array_column($items, 'tweeted_datetime'));
        }
        
        $thumbnail_size = $request->Cookie('thumbnail_size');
        if ($thumbnail_size == null) {
            $thumbnail_size = MediaThumbnailSize::MEDIUM;
        }

        $param['viewModel'] = new GalleryViewModel(
            '',
            '',
            $page,
            $thumbnail_size,
            $list_sort,
            $items,
            $sum_media_size,
            $min_tweeted_datetime
        );

        return  response()
            ->view('gallery.index', $param)
            ->cookie('thumbnail_size', $thumbnail_size, 60*24*365)
            ->cookie('list_sort', $list_sort, 60*24*365);
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


        $list_sort = $request->Cookie('list_sort');
        if ($list_sort == null) {
            $list_sort = ListSort::DESC;
        }

        $manager = new GalleryManager();
        $items = $manager->fetch($service_user_id, $page, $list_sort, $user_id);
        
        $sum_media_size = 0;
        $min_tweeted_datetime = '';
        if(count($items)){
            $sum_media_size = array_sum(array_column($items, 'media_size'));
            $min_tweeted_datetime = min(array_column($items, 'tweeted_datetime'));
        }

        $thumbnail_size = $request->Cookie('thumbnail_size');
        if ($thumbnail_size == null) {
            $thumbnail_size = MediaThumbnailSize::MEDIUM;
        }

        $relational_user = RelationalUsers::select(['name'])
            ->where('user_id', $user_id)
            ->first();

        $param['viewModel'] = new GalleryViewModel(
            $user_id,
            $relational_user['name'],
            $page,
            $thumbnail_size,
            $list_sort,
            $items,
            $sum_media_size,
            $min_tweeted_datetime
        );

        return  response()
            ->view('gallery.index', $param)
            ->cookie('thumbnail_size', $thumbnail_size, 60*24*365)
            ->cookie('list_sort', $list_sort, 60*24*365);
    }
}
