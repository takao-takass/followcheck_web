<!doctype html>
<html lang="ja">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        <!-- App CSS -->
        <link rel="stylesheet" href="{{ asset('/css/common.css') }}">
    </head>

    <body>
    
        <!-- ヘッダ  -->
        <nav class="navbar navbar-dark shadow-sm" style="background-color: #436be3;">
            <div class="container d-flex justify-content-between">
                <a href="#" class="navbar-brand d-flex">
                    <img class="titlelogo" src="{{ asset('/img/title2.png') }}">
                </a>
                <button class="navbar-toggler" type="button"
                    data-toggle="collapse"
                    data-target="#navmenu"
                    aria-controls="navmenu"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navmenu">
                    <div class="navbar-nav">
                        <a class="nav-item nav-link" href="{{ action('RemlistController@init') }}">リムられリスト</a>
                        <a class="nav-item nav-link" href="{{ action('DownloadAccountsController@index') }}">ダウンロード管理</a>
                        <a class="nav-item nav-link" href="{{ action('TweetUsersController@index') }}">ツイートを見る</a>
                        <a class="nav-item nav-link" href="{{ action('LoginController@logout') }}">ログアウト</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 上に移動ボタン -->
        <a href="#"><div class="movetop"><i data-feather="arrow-up" class="iconwhite"></i></div></a>

        <div class="container">

            <!-- ページタイトル -->
            <div class="row" style="margin-top:2em;">
                <div class="col-md-12">
                    <h2 class="text-center">
                    </h2>
                </div>

            <!-- ユーザ一覧 -->
                @foreach($accounts as $account)
                <div class="col-lg-3 col-md-4 col-6" style="margin-bottom:1em">
                    <div class="card shadow-sm" style="width:100%;height:100%;">
                        <img class="card-img-top" src="{{$account['thumbnail_url']}}" style="height: 100px;object-fit: cover;*/">
                        <div class="card-body">
                            <h6 class="card-title">{{$account['name']}}</h6>
                            <h6 class="card-subtitle text-muted" style='word-wrap:break-all;'>＠{{$account['disp_name']}}</h6>
                            {{--<p class="card-text">ツイート：{{$account['tweet_ct']}}<br/>--}}
                            {{--メディア：{{$account['media_ct']}}</p>--}}
                            <h6 class="card-subtitle text-muted" style="margin-top:0.5em;"><a href="{{ action('TweetsController@index',[$account['user_id']]) }}" class="card-link">Tweets</a></h6>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

        <!-- feather Iconfont JavaScript -->
        <!-- icons: https://feathericons.com/ -->
        <script src="https://unpkg.com/feather-icons"></script>
        <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
        <script>feather.replace()</script>

    </body>
</html>