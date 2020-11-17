<?php

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

Route::get('/', function () {
    return view('welcome');
});

# ログイン
# - 画面表示
Route::redirect('/fc', '/followcheck/login');
Route::redirect('/followcheck', '/followcheck/login');
Route::get('/followcheck/login','LoginController@index');
# - ログアウト処理
Route::get('/followcheck/logout','LoginController@logout')->name('login.logout');

# サインアップ
# - 画面表示
Route::get('/followcheck/signup','SignupController@index');

# リムられリスト
# - 画面表示
Route::get('/followcheck/remlist', 'RemlistController@init');
Route::get('/followcheck/remlist/{user_id}/{page}','RemlistController@index');

# フォロバ待ちリスト
# - 画面表示
Route::get('/followcheck/unfblist', 'UnfblistController@init');
Route::get('/followcheck/unfblist/{user_id}/{page}','UnfblistController@index');

# 相互フォローリスト
# - 画面表示
Route::get('/followcheck/fleolist', 'FleolistController@init');
Route::get('/followcheck/fleolist/{user_id}/{page}','FleolistController@index');

# アカウント管理
# - 画面表示
Route::get('/followcheck/accounts','AccountsController@index');

# ツイートを見る
# - 画面表示
Route::get('/followcheck/tweetusers/{page?}','TweetUsersController@index');

# ツイート一覧
# - 画面表示(ユーザ指定)
Route::get('/followcheck/tweets/u/{user_id}/{page?}','TweetsController@index');
# - 画面表示(グループ指定)
Route::get('/followcheck/tweets/g/{group_id}/{page?}','TweetsController@gindex');

# 観賞モード
# - 画面表示(ユーザ指定)
Route::get('/followcheck/show/u/{user_id}/{page?}','ShowController@index');
# - 画面表示(グループ指定)
Route::get('/followcheck/show/g/{group_id}/{page?}','ShowController@gindex');

# 削除対象ツイート一覧
# - 画面表示
Route::get('/followcheck/oldtweets/{page?}','OldTweetsController@index');

# グループ
# - 画面表示
Route::get('/followcheck/groups','GroupsController@index');

# ユーザ
# - 画面表示
Route::get('/followcheck/user/{user_id}','UserController@index');


# キーワード検索
# - キーワード一覧
Route::get('/followcheck/keywords/{page?}','KeywordsController@index');



# コンフィグ
Route::get('/followcheck/user_config','UserConfigController@index')->name('config.index');
Route::post('/followcheck/user_config','UserConfigController@save')->name('config.save');

# New ツイートを見る
Route::get('/followcheck/tweetusers2','TweetUsers2Controller@index')->name('tweetuser.index');
Route::post('/followcheck/tweetusers2/add','TweetUsers2Controller@add');

# New 観賞モード
Route::get('/followcheck/show_user/{user_id}','ShowByUserController@index')->name('show_user.index');
Route::get('/followcheck/show_all','ShowAllController@index')->name('show_all.index');

# メディア表示
Route::get('/followcheck/media','MediaController@index')->name('media.index');
Route::post('/followcheck/media/delete','MediaController@delete')->name('media.delete');
Route::post('/followcheck/media/keep','MediaController@keep')->name('media.keep');
