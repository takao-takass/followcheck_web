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
Route::redirect('/followcheck/remlist', '/followcheck/remlist/0');
Route::get('/followcheck/remlist/{page}','RemlistController@index');

# フォロバ待ちリスト
# - 画面表示
Route::redirect('/followcheck/unfblist', '/followcheck/unfblist/0');
Route::get('/followcheck/unfblist/{page}','UnfblistController@index');
