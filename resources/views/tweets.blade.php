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
                        <a class="nav-item nav-link" href="#">ログアウト</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">


            <div class="row" style="margin-top:2em;">

                <!-- ページタイトル -->
                <div class="col-md-12">
                    <h2 class="text-center">
                    </h2>
                </div>

                <!-- ページ切り替えボタン -->
                <div class="col-md-12">
                    <div class="float-right">
                        <nav aria-label="Page navigation example" style="margin-top:1em;">
                            <ul class="pagination">
                                @if($prev_page >= 0)
                                <li class="page-item">
                                    <a class="page-link" href="{{ action('TweetsController@index',[$uesr_id,$prev_page]) }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                @endif
                                <li class="page-item disabled"><a class="page-link" href="#">{{$record}}ツイート</a></li>
                                @if($next_page < $max_page)
                                <li class="page-item">
                                    <a class="page-link" href="{{ action('TweetsController@index',[$uesr_id,$next_page]) }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>

            <!-- ツイート一覧 -->
            <div class="row">

                @foreach($accounts as $account)
                    <div class="media shadow-sm col-md-12" style="margin:0.5em" >
                        <img class="mr-3 usericon" style="margin:1em" src="{{$account['thumbnail_url']}}">
                        <div class="media-body">
                            <h5 class="mt-0">{{$account['body']}}</h5>
                            <div>{{$account['tweeted_datetime']}}　RT:{{$account['retweet_count']}}　FAV:{{$account['favolite_count']}}</div>
                            @if (!is_null($account['media_type']))
                                <div>
                                    
                                    @for ($i = 0; $i < count($account['thumb_names']); $i++)
                                        <span><a href="{{$account['media_path'][$i]}}"><img class="mr-3" style="margin:1em;width:10em;" src="{{asset('/img/thumbs/').$account['thumb_names'][$i]}}"></a></span>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach


        </div>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

    </body>
</html>