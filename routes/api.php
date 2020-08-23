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


# ツイートを見る
# - リスト表示API
Route::post('/followcheck/tweetusers/list','TweetUsersController@list');
# - アカウント追加API
Route::post('/followcheck/tweetusers/add','TweetUsersController@add');
# - アカウント削除API
Route::post('/followcheck/tweetusers/del','TweetUsersController@del');


# ツイート一覧
# - ツイート取得
Route::post('/followcheck/tweets/list','TweetsController@list');
# - KEEP登録
Route::post('/followcheck/tweets/keep','TweetsController@keep');
# - KEEP解除
Route::post('/followcheck/tweets/unkeep','TweetsController@unkeep');

# 観賞モード
# - ツイート取得
Route::post('/followcheck/show/list','ShowController@list');

# 削除対象ツイート一覧
# - ツイート取得
Route::post('/followcheck/oldtweets/list','OldTweetsController@list');
