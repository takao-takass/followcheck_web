<?php
/**
 * Controller class for "全てのツイートを見る"
 * 
 * PHP Version >= 8.0
 * 
 * @category ShowAll
 * @package  App\Http\Controllers
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
namespace App\Http\Controllers;

use App\Models\ShowThumbnail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\ViewModels\ShowThumbnailViewModel;
use App\DataModels\Tweets;
use App\DataModels\TweetMedias;
use App\DataModels\ShownTweets;
use App\DataModels\UserConfig;
use App\Constants\WebRoute;

/**
 * Class ShowAllController
 * 
 * @category ShowAll
 * @package  App\Http\Controllers
 * @author   Takahiro Tada <takao@takassoftware.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     None
 */
class ShowAllController extends Controller
{
    const RECORDS_COUNT = 50;

    /**
     * Render Index.
     *
     * @param Request $request Request parameter.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }
        return  response()->view(
            'show_all',
            $this->_createViewParam(0, '', $request)
        );
    }

    /**
     * Render Index. Reversed list.
     *
     * @param Request $request Request parameter.
     * 
     * @return \Illuminate\Http\Response
     */
    public function indexReverse(Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }
        return  response()->view(
            'show_all_reverse',
            $this->_createViewParam(1, '', $request)
        );
    }

    /**
     * Render Index. By User list.
     *
     * @param string  $user_id 
     * @param Request $request Request parameter.
     * 
     * @return \Illuminate\Http\Response
     */    
    public function indexByUser(string $user_id, Request $request)
    {
        // 有効なトークンが無い場合はログイン画面に飛ばす
        if (!$this->isValidToken()) {
            return redirect()->route(WebRoute::LOGIN_LOGOUT);
        }
        return  response()->view(
            'show_user',
            $this->_createViewParam(0, $user_id, $request)
        );
    }

    /**
     * Create Tweet medias data.
     * 
     * @param int     $sort 
     * @param string  $user_id 
     * @param Request $request 
     * 
     * @return \App\ViewModels\ShowThumbnailViewModel[]
     */
    private function _createViewParam(int $sort, string $user_id, Request $request)
    {
        $page = $request->input('page');
        $viewModel = new ShowThumbnailViewModel();
        $viewModel->user_id = $user_id;
        $viewModel->Page = $page == null ? 0 : $page;

        $remove_retweet = UserConfig::
            select(
                [
                    'value',
                ]
            )
            ->Where('service_user_id', $this->session_user->service_user_id)
            ->Where('config_id', 1)
            ->first();

        $filter_checked = UserConfig::
            select(
                [
                    'value',
                ]
            )
            ->Where('service_user_id', $this->session_user->service_user_id)
            ->Where('config_id', 4)
            ->first();

        $query = Tweets::from('tweets as tweets')
            ->select(
                [
                    'user_id',
                    'tweet_id',
                    'tweeted_datetime',
                ]
            )
            ->Where('service_user_id', $this->session_user->service_user_id)
            ->Where('is_media', 1)
            ->Where('media_ready', 1)
            ->Where('deleted', 0);

        if ($filter_checked->value == 1) {
            $query = $query->whereNotExists(
                function ($sub_query) {
                    $sub_query
                        ->select(DB::raw(1))
                        ->from('delete_tweets')
                        ->where(
                            'service_user_id',
                            $this->session_user->service_user_id
                        )
                        ->whereRaw('delete_tweets.user_id = tweets.user_id')
                        ->whereRaw('delete_tweets.tweet_id = tweets.tweet_id');
                }
            );
        }

        if ($remove_retweet->value == 1) {
            $query = $query->Where('retweeted', '=', 0);
        }

        if (!empty($user_id)) {
            $query = $query->Where('user_id', $user_id);
        }

        $viewModel->Count = $query->count();
        $viewModel->MaxPage = floor($viewModel->Count / self::RECORDS_COUNT);

        if ($sort==0) {
            $query = $query->orderByDesc('tweeted_datetime');
        } else {
            $query = $query->orderBy('tweeted_datetime');
        }

        $tweets = $query
            ->skip($page * self::RECORDS_COUNT)
            ->take(self::RECORDS_COUNT)
            ->get()
            ->toArray();

        ShownTweets::
            where('sign', $this->getToken())
            ->delete();
        $media_type = $request['media_type'];
        $tweet_medias = [];
        foreach ($tweets as $tweet) {

            $query = TweetMedias::
                select(
                    [
                        'tweet_id',
                        'thumb_directory_path',
                        'thumb_file_name',
                        'directory_path',
                        'file_name',
                        'type',
                    ]
                )
                ->Where('service_user_id', $this->session_user->service_user_id)
                ->Where('user_id', $tweet['user_id'])
                ->where('tweet_id', $tweet['tweet_id']);
            if (!empty($media_type)) {
                $query = $query
                    ->where('type', $media_type);
            }
            $records = $query
                ->get()
                ->toArray();

            foreach ($records as $record) {
                array_push($tweet_medias, $record);
            }

            if (count($records) > 0) {
                ShownTweets::updateOrInsert(
                    [
                        'sign' => $this->getToken(),
                        'user_id' => $tweet['user_id'],
                        'tweet_id' => $tweet['tweet_id'],
                        'tweeted_datetime' => $tweet['tweeted_datetime'],
                    ]
                );
            }
        }

        $viewModel->show_thumbnails = [];
        foreach ($tweet_medias as $tweet_media) {

            if (empty($tweet_media['thumb_directory_path'])
                || empty($tweet_media['directory_path'])
            ) {
                continue;
            }
            $split_thumb_path = explode("/", $tweet_media['thumb_directory_path']);
            array_push(
                $viewModel->show_thumbnails,
                new ShowThumbnail(
                    $tweet_media['tweet_id'],
                    '/img/tweetmedia/' .
                    $split_thumb_path[5] .
                    '/' .
                    $tweet_media['thumb_file_name'],
                    $tweet_media['file_name'],
                    $tweet_media['type'],
                )
            );

        }

        $param['Thumbnails'] = $viewModel;

        return $param;
    }

}
