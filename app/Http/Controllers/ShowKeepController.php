<?php

namespace App\Http\Controllers;

use App\DataModels\Tweets;
use App\DataModels\TweetMedias;
use App\Models\ShowThumbnail;
use Illuminate\Http\Request;
use App\ViewModels\ShowThumbnailViewModel;

class ShowKeepController extends Controller
{

    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        $page = $request->input('page');
        $viewModel = new ShowThumbnailViewModel();
        $viewModel->Page = $page == null ? 0 : $page;

        $query = Tweets::where('service_user_id', $this->session_user->service_user_id)
            ->where('kept',1);

        $viewModel->Count = $query->count();
        $viewModel->MaxPage = floor($viewModel->Count/300);

        $tweets = $query
            ->orderByDesc('update_datetime')
            ->skip($page * 300)
            ->take(300)
            ->get();

        $tweet_ids = [];
        foreach ($tweets as $tweet) {
            $tweet_ids[] = $tweet->tweet_id;
        }

        $tweet_medias = TweetMedias::whereIn('tweet_id', $tweet_ids)
            ->get();

        $viewModel->show_thumbnails = [];
        foreach ($tweet_medias as $tweet_media) {

            if(empty($tweet_media->thumb_directory_path) || empty($tweet_media->directory_path)){
                continue;
            }

            $split_thumb_path = explode("/", $tweet_media->thumb_directory_path);

            $viewModel->show_thumbnails[] = new ShowThumbnail(
                $tweet_media->tweet_id,
                '/img/tweetmedia/' . $split_thumb_path[5] . '/' . $tweet_media->thumb_file_name,
                $tweet_media->file_name,
                $tweet_media->type
            );

        }

        $param['Thumbnails'] = $viewModel;

        return  response()->view('show_keep', $param);
    }

}
