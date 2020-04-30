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
    
        <!-- 上に移動ボタン -->
        <a href="#"><div class="movetop"><i data-feather="arrow-up" class="iconwhite"></i></div></a>

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

        <!-- メインコンテンツ -->
        <div class="container">

            <div class="row" style="margin-top:2em;">

                <!-- ページタイトル -->
                <div class="col-md-12">
                    <h2 class="text-center">
                    </h2>
                </div>

                <!-- 絞り込み -->
                <div class="col-md-12">
                    <a class="btn btn-secondary"
                        data-toggle="collapse"
                        href="#filter"
                        role="button"
                        aria-expand="false"
                        aria-controls="example-2">ツイートの絞り込み</a>
                    <div class="collapse" id="filter">
                        <div class="card card-body">
                            <div class="form-check" style="margin:1em;">
                                <input class="form-check-input filter-item" type="checkbox" id="filter-reply" {{$filter['reply_check']}}>
                                <label class="form-check-label" for="filter-reply">リプライは表示しない</label>
                            </div>
                            <div class="form-check" style="margin:1em;">
                                <input class="form-check-input filter-item" type="checkbox" id="filter-media" {{$filter['media_check']}}>
                                <label class="form-check-label" for="filter-media">メディアのみ表示する</label>
                            </div>
                    </div>
                </div>

                <!-- 読み込み中表示 -->
                <div class="col-md-12" style="text-align:center;margin-top:1em;" id="spinner">
                    <div class="spinner-grow text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                    </div>
                </div>

                <!-- ページ切り替えボタン -->
                <div class="col-md-12 contents">
                    <nav style="margin-top:1em;">
                        <ul class="pagination">
                            <li class="page-item" id="prev">
                                <a class="page-link" id="prev-button">
                                    <i data-feather="arrow-left" class="iconwhite"></i>
                                </a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link">
                                    <span id="tweet-ct"></span>ツイート
                                </a>
                            </li>
                            <li class="page-item" id="next">
                                <a class="page-link" id="next-button">
                                    <i data-feather="arrow-right" class="iconwhite"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <input type="hidden" id="page" value="{{$filter['page']}}">
                    <input type="hidden" id="user" value="{{$filter['user_id']}}">
                </div>

                <!-- ツイート一覧 -->
                <div class="col-md-12 contents" id="twlist">
                </div>

                <!-- ページ下部のスペーサ -->
                <div style="margin-bottom:15em">
                </div>
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

        <!-- Business JavaScript -->
        <script type="text/javascript">

            // 画面表示
            $(document).ready(function(){
                showList();
            });

            // 前ページ
            $('#prev-button').on('click',function(){
                $('#page').val(Number($('#page').val())-1);
                showList();
            });

            // 次ページ
            $('#next-button').on('click',function(){
                $('#page').val(Number($('#page').val())+1);
                showList();
            });
            
            // 検索条件の変更
            $('.filter-item').on('change',function(){
                $('#page').val(0);
                showList();
            });

            // ツイート一覧を取得する
            function showList(){

                $('.contents').hide();
                $('#spinner').show();

                $.ajax({
                    url:'{{ action('TweetsController@list') }}',
                    type:'POST',
                    data:{
                        'user' : $('#user').val(),
                        'page' : $('#page').val(),
                        'filter-reply' : $('#filter-reply').prop('checked') ? 1:'',
                        'filter-media' : $('#filter-media').prop('checked') ? 1:'',
                    }
                }).done( (data) => {

                    $('#tweet-ct').text(data.record);
                    $('#twlist').empty();

                    // ページングの表示切替
                    (data.prev_page>=0) ? $('#prev').show() : $('#prev').hide();
                    (data.next_page<data.max_page) ? $('#next').show() : $('#next').hide();

                    // ツイートの表示
                    $.each(data.accounts, function(index,account){

                        // HTMLのテンプレート
                        html = 
                            "<div class='media shadow-sm' style='margin-bottom:0.5em;width:100%' >"+
                            "    <img class='usericon' style='margin:1em' src='[[thumbnail_url]]'>"+
                            "    <div class='media-body' style='margin:1em 1em 1em 0em'>"+
                            "        <h6 class='tweet-body' style='word-wrap:break-all;'>[[body]]</h6>"+
                            "        <div>[[tweeted_datetime]]　RT:[[retweet_count]]　FAV:[[favolite_count]]</div>"+
                            "        <div>[[thunbs]]</div>" +
                            "    </div>"+
                            "</div>";

                        // メディアのサムネイル表示を作る
                        thumbhtml = "";
                        if(account.media_type != null){
                            for (var i = 0; i<account.thumb_names.length; i++) {
                                thumbhtml += "<span><a href='"+account.media_path[i]+" 'target='_blank' rel='noopener noreferrer'><img class='mr-3' style='margin:1em;width:10em;' src='"+account.thumb_names[i]+"'></a></span>";
                            }
                        }

                        // 一覧にHTMLを表示する
                        $('#twlist').append(
                            html
                                .replace('[[thumbnail_url]]',account.thumbnail_url)
                                .replace('[[body]]',account.body)
                                .replace('[[tweeted_datetime]]',account.tweeted_datetime)
                                .replace('[[retweet_count]]',account.retweet_count)
                                .replace('[[favolite_count]]',account.favolite_count)
                                .replace('[[thunbs]]',thumbhtml)
                        );

                    });

                }).fail( (data) => {

                }).always(function(){
                    $('.contents').show();
                    $('#spinner').hide();
                });

            }

        </script>

    </body>
</html>