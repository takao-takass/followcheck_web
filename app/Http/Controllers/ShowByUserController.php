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

class ShowByUserController extends Controller
{

    public function index($user_id, Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }
        return  response()->view('show_user',$this->createViewParam($user_id,$request));
    }

    public function indexRemove($user_id, Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }
        return  response()->view('show_user_remove',$this->createViewParam($user_id,$request));
    }

    public function removeTweet($user_id, Request $request)
    {
        var_dump($request->tweet_id);
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }

        DB::table('checked_tweets')->updateOrInsert(
            [
                'service_user_id'=>$this->session_user->service_user_id,
                'tweet_id'=>$request->tweet_id
            ]
        );

        return  response()->view('show_user_remove',$this->createViewParam($user_id,$request));
    }

    private function createViewParam($user_id, Request $request)
    {
        $page = $request->input('page');
        $viewModel = new ShowThumbnailViewModel();
        $viewModel->user_id = $user_id;
        $viewModel->Page = $page == null ? 0 : $page;

        $remove_retweet = DB::table('user_config')
            ->select(['value'])
            ->Where('service_user_id', $this->session_user->service_user_id)
            ->Where('config_id', 1)
            ->first();

        $filter_checked = DB::table('user_config')
            ->select(['value'])
            ->Where('service_user_id', $this->session_user->service_user_id)
            ->Where('config_id', 4)
            ->first();

        $query = DB::table('tweets')
            ->Where('service_user_id', $this->session_user->service_user_id)
            ->Where('user_id', $user_id)
            ->Where('is_media', 1)
            ->Where('media_ready', 1)
            ->Where('deleted', 0);

        if ($filter_checked->value == 1) {
            $query = $query->whereNotExists(function ($subquery) {
                $subquery
                    ->select(DB::raw(1))
                    ->from('checked_tweets')
                    ->where('service_user_id', $this->session_user->service_user_id)
                    ->whereRaw('checked_tweets.tweet_id = tweets.tweet_id');
            });
        }

        if ($remove_retweet->value == 1) {
            $query = $query->Where('retweeted', '=', 0);
        }

        $viewModel->Count = $query->Count();
        $viewModel->MaxPage = floor($viewModel->Count / 200);

        $tweets = $query
            ->orderByDesc('tweeted_datetime')
            ->skip($page * 200)
            ->take(200)
            ->get();

        $tweet_ids = [];
        foreach ($tweets as $tweet) {
            array_push($tweet_ids, $tweet->tweet_id);
        }

        $tweet_medias =
            DB::table('tweet_medias')
                ->join('tweets', 'tweets.tweet_id', '=', 'tweet_medias.tweet_id')
                ->Where('tweets.service_user_id', $this->session_user->service_user_id)
                ->Where('tweets.user_id', $user_id)
                ->whereIn('tweet_medias.tweet_id', $tweet_ids)
                ->orderByDesc('tweeted_datetime')
                ->get();

        $viewModel->show_thumbnails = [];
        foreach ($tweet_medias as $tweet_media) {

            if (empty($tweet_media->thumb_directory_path) || empty($tweet_media->directory_path)) {
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

        return $param;
    }

}
