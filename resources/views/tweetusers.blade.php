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
        <link rel="stylesheet" href="{{ asset('/css/remlist.css') }}">
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
                        <a class="nav-item nav-link" href="#">ログアウト</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">

            <!-- ページタイトル -->
            <div class="row" style="margin-top:2em;">
                <div class="col-md-12">
                    <h2 class="text-center">
                    </h2>
                </div>
            </div>

            <!-- ページ切り替えボタン -->
            <div class="row text-center">
                <div class="col-md-12">
                </div>
            </div>

            <!-- ユーザ一覧 -->
            <div class="row">
                @foreach($accounts as $account)
                    <div class="card shadow-sm" style="width:13rem;margin:0.5em">
                        <img class="card-img-top" src="{{$account['thumbnail_url']}}" style="height: 100px;object-fit: cover;*/">
                        <div class="card-body">
                            <h5 class="card-title">{{$account['name']}}</h5>
                            <h6 class="card-subtitle text-muted">＠{{$account['disp_name']}}</h6>
                            <p class="card-text">ツイート：{{$account['tweet_ct']}}<br/>
                            メディア：{{$account['media_ct']}}</p>
                            <a href="#" class="card-link">Tweets</a>
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

    </body>
</html>