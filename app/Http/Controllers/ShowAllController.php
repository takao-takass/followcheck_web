<?php

namespace App\Http\Controllers;

use App\Models\ShowThumbnail;
use App\Models\TweetTakeUser;
use App\ViewModels\TweetUsersViewModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exceptions\ParamInvalidException;
use App\Models\TweetListFilter;
use App\Models\TweetShow;
use App\Models\Token;
use App\ViewModels\ShowThumbnailViewModel;
use Carbon\Carbon;

class ShowAllController extends Controller
{

    /**
     * 画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $page = $request->input('page');
        $viewModel = new ShowThumbnailViewModel();
        $viewModel->Page = $page == null ? 0 : $page;

        $viewModel->Count = DB::table('tweets')
            ->Where('service_user_id', '=', $this->session_user->service_user_id)
            ->Where('is_media', '=', 1)
            ->Where('media_ready', '=', 1)
            ->Count();
        $viewModel->MaxPage = ceil($viewModel->Count/300);

        $tweets = DB::table('tweets')
            ->Where('service_user_id', '=', $this->session_user->service_user_id)
            ->Where('is_media', '=', 1)
            ->Where('media_ready', '=', 1)
            ->orderByDesc('update_datetime')
            ->skip($page * 300)
            ->take(300)
            ->get();

        $tweet_ids = [];
        foreach ($tweets as $tweet) {
            array_push($tweet_ids, $tweet->tweet_id);
        }

        $tweet_medias =
            DB::table('tweet_medias')
            ->whereIn('tweet_id', $tweet_ids)
            ->get();

        $viewModel->show_thumbnails = [];
        foreach ($tweet_medias as $tweet_media) {

            if(empty($tweet_media->thumb_directory_path) || empty($tweet_media->directory_path)){
                continue;
            }
            $split_thumb_path = explode("/", $tweet_media->thumb_directory_path);
            array_push($viewModel->show_thumbnails,
                new ShowThumbnail(
                    $tweet_media->tweet_id,
                    '/img/tweetmedia/' . $split_thumb_path[5] . '/' . $tweet_media->thumb_file_name,
                    $tweet_media->file_name,
                    $tweet_media->type
                )
            );

        }

        $param['Thumbnails'] = $viewModel;

        return  response()->view('show_all', $param)
        ->cookie('sign',$this->updateToken()->signtext,24*60);
    }

}
