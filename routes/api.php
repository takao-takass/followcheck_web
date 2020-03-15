<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

# ログイン
# - 認証API
Route::post('/followcheck/login/auth','LoginController@auth');

# サインアップ
# - 登録API
Route::post('/followcheck/signup/entry','SignupController@entry');

# フォロバ待ちリスト
# - 非表示API
Route::post('/followcheck/unfblist/hide','UnfblistController@hide');

# 相互フォローリスト
# - 非表示API
Route::post('/followcheck/fleolist/hide','FleolistController@hide');


# Twitterアカウント管理
# - アカウント追加API
Route::post('/followcheck/accounts/add','AccountsController@add');
# - アカウント削除API
Route::post('/followcheck/accounts/del','AccountsController@del');


# ツイートダウンロード管理
# - アカウント追加API
Route::post('/followcheck/dlaccounts/add','DownloadAccountsController@add');
# - アカウント削除API
Route::post('/followcheck/dlaccounts/del','DownloadAccountsController@del');