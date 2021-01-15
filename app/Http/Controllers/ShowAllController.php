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

    public function index($user_id, Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }
        return  response()->view('show_all',$this->createViewParam($user_id,$request));
    }

    private function createViewParam(Request $request)
    {
        $page = $request->input('page');
        $viewModel = new ShowThumbnailViewModel();
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
            ->Where('is_media', 1)
            ->Where('media_ready', 1)
            ->Where('deleted', 0);

        if ($filter_checked->value == 1) {
            /* ↓ delete_tweets に切り替えた際は削除する */
            $query = $query->whereNotExists(function ($subquery) {
                $subquery
                    ->select(DB::raw(1))
                    ->from('checked_tweets')
                    ->where('service_user_id', $this->session_user->service_user_id)
                    ->whereRaw('checked_tweets.tweet_id = tweets.tweet_id');
            });
            /* ↑ delete_tweets に切り替えた際は削除する */
            $query = $query->whereNotExists(function ($subquery) {
                $subquery
                    ->select(DB::raw(1))
                    ->from('delete_tweets')
                    ->where('service_user_id', $this->session_user->service_user_id)
                    ->whereRaw('delete_tweets.user_id = tweets.user_id')
                    ->whereRaw('delete_tweets.tweet_id = tweets.tweet_id');
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

        DB::table('shown_tweets')
            ->where('sign', $this->getToken())
            ->delete();
        $tweet_medias = [];
        foreach ($tweets as $tweet) {

            $records = DB::table('tweet_medias')
                ->Where('service_user_id', $this->session_user->service_user_id)
                ->Where('user_id', $tweet->user_id)
                ->where('tweet_id', $tweet->tweet_id)
                ->orderByDesc('tweeted_datetime')
                ->get();
            foreach ($records as $record){
                array_push($tweet_medias,$record);
            }

            DB::table('shown_tweets')->updateOrInsert(
                [
                    'sign'=>$this->getToken(),
                    'user_id'=>$tweet->user_id,
                    'tweet_id'=>$tweet->tweet_id,
                    'tweeted_datetime'=>$tweet->tweeted_datetime,
                ]
            );
        }

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
