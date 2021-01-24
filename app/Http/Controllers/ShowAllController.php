<?php

namespace App\Http\Controllers;

use App\Models\ShowThumbnail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\ViewModels\ShowThumbnailViewModel;

class ShowAllController extends Controller
{

    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }
        return  response()->view('show_all',$this->createViewParam(0, $request));
    }

    public function index_reverse(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if(!$this->isValidToken()){
            return redirect(action('LoginController@logout'));
        }
        return  response()->view('show_all_reverse',$this->createViewParam(1, $request));
    }

    private function createViewParam(int $sort, Request $request)
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
            $query = $query->whereNotExists(function ($sub_query) {
                $sub_query
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

        if($sort==0){
            $query = $query->orderByDesc('tweeted_datetime');
        }else{
            $query = $query->orderBy('tweeted_datetime');
        }

        $tweets = $query
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
