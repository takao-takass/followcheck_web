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
Route::get('/followcheck/logout','LoginController@logout');

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

# ツイートダウンロードアカウント管理
# - 画面表示
Route::get('/followcheck/dlaccounts','DownloadAccountsController@index');

# ツイートを見る
# - 画面表示
Route::get('/followcheck/tweetusers/{page?}','TweetUsersController@index');

# ツイート一覧
# - 画面表示
Route::get('/followcheck/tweets/{user_id}/{page?}','TweetsController@index');
