<?php

use App\Constants\WebRoute;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get(
    '/',
    function () {
        return view('welcome');
    }
);

// ログイン
Route::redirect('/fc', '/followcheck/login');
Route::redirect('/followcheck', '/followcheck/login');
Route::get('/followcheck/login', 'LoginController@index');
Route::get('/followcheck/logout', 'LoginController@logout')
    ->name('login.logout');

// サインアップ
Route::get('/followcheck/signup', 'SignupController@index');

// リムられリスト
Route::get('/followcheck/remlist', 'RemlistController@init');
Route::get('/followcheck/remlist/{user_id}/{page}', 'RemlistController@index');

// フォロバ待ちリスト
Route::get('/followcheck/unfblist', 'UnfblistController@init');
Route::get('/followcheck/unfblist/{user_id}/{page}', 'UnfblistController@index');

// 相互フォローリスト
Route::get('/followcheck/fleolist', 'FleolistController@init');
Route::get('/followcheck/fleolist/{user_id}/{page}', 'FleolistController@index');

// アカウント管理
Route::get('/followcheck/accounts', 'AccountsController@index');

// ユーザ
Route::get('/followcheck/user/{user_id}', 'UserController@index')
    ->name('user.index');

// キーワード検索
Route::get('/followcheck/keywords/{page?}', 'KeywordsController@index');






// ギャラリー
Route::get('/followcheck/gallery/all', 'Gallery\GalleryAllController@index')
    ->name(WebRoute::GALLERY_ALL);




// New ツイートを見る
Route::get('/followcheck/tweetusers2', 'TweetUsers2Controller@index')
    ->name('tweetuser.index');
Route::post('/followcheck/tweetusers2/add', 'TweetUsers2Controller@add');

// New 観賞モード
//Route::get('/followcheck/show_user/{user_id}', 'ShowByUserController@index')
//    ->name('show_user.index');
Route::get('/followcheck/show_user/{user_id}', 'ShowAllController@indexByUser')
    ->name('show_user.index');
Route::get('/followcheck/show_all', 'ShowAllController@index')
    ->name('show_all.index');
Route::get('/followcheck/show_all_reverse', 'ShowAllController@indexReverse')
    ->name('show_all_reverse.index');
Route::get('/followcheck/show_keep', 'ShowKeepController@index')
    ->name('show_keep.index');

// メディア表示
Route::get('/followcheck/media', 'MediaController@index')
    ->name('media.index');
Route::post('/followcheck/media/delete', 'MediaController@delete')
    ->name('media.delete');
Route::post('/followcheck/media/keep', 'MediaController@keep')
    ->name('media.keep');

// スライドショー
Route::get('/followcheck/slideshow', 'SlideshowController@index')
    ->name('slideshow.index');

// システム
Route::get('/followcheck/system/repair_user', 'SystemRepairUserController@index')
    ->name('system.repair_user.index');
Route::post('followcheck/system/repair_user/repair', 'SystemRepairUserController@add')
    ->name('system.repair_user.repair');

// コンフィグ
Route::get('/followcheck/user_config', 'UserConfigController@index')
    ->name('config.index');
Route::post('/followcheck/user_config', 'UserConfigController@save')
    ->name('config.save');

// アカウント一覧
Route::get('/followcheck/twitter/accounts', 'Account\TwitterAccountsController@index')
    ->name(WebRoute::TWITTER_ACCOUNT_INDEX);
Route::post('/followcheck/twitter/accounts', 'Account\TwitterAccountsController@add')
    ->name(WebRoute::TWITTER_ACCOUNT_ADD);

// グループ
Route::get('/followcheck/groups', 'Group\GroupController@index')
    ->name(WebRoute::GROUP_INDEX);
Route::post('/followcheck/groups/add', 'Group\GroupController@add')
    ->name(WebRoute::GROUP_ADD);
Route::post('/followcheck/groups/delete', 'Group\GroupController@delete')
    ->name(WebRoute::GROUP_DELETE);
Route::get('/followcheck/groups/{id}', 'Group\GroupMemberController@index')
    ->name(WebRoute::GROUP_MEMBER_INDEX);
Route::post('/followcheck/groups/{id}/join', 'Group\GroupMemberController@join')
    ->name(WebRoute::GROUP_MEMBER_JOIN);
Route::post('/followcheck/groups/{id}/leave', 'Group\GroupMemberController@leave')
    ->name(WebRoute::GROUP_MEMBER_LEAVE);
Route::get('/followcheck/groups/{id}/free_accounts', 'Group\GroupMemberController@free')
    ->name(WebRoute::GROUP_FREE_ACCOUNT_INDEX);

// テスト用
Route::get('/followcheck/test', 'TestController@index')
    ->name('test.index');
