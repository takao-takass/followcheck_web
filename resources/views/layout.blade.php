<!doctype html>
<html lang="ja">
    <?php
        use App\Constants\WebRoute;
        use App\Constants\Invalid;
    ?>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        @yield('title')

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        <!-- App CSS -->
        <link rel="stylesheet" href="{{ asset('/css/common.css') }}">

        @yield('style')

    </head>

    <body>

        <!-- 上に移動ボタン -->
        <!--<a href="#"><div class="movetop"><i data-feather="arrow-up" class="iconwhite"></i></div></a>-->

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
                        <a class="nav-item nav-link" href="{{ route('tweetuser.index') }}">ツイートを見る</a>
                        <a class="nav-item nav-link" href="{{ route('gallery.all') }}">ギャラリー</a>
                        <a class="nav-item nav-link" href="{{ route('show_keep.index') }}">キープ観賞とスライドショー</a>
                        <!--<a class="nav-item nav-link" href="{{ route('group.index') }}">グループ</a>-->
                        <a class="nav-item nav-link" href="{{ route('config.index') }}">システムとコンフィグ</a>
                        <a class="nav-item nav-link" href="{{ action('LoginController@logout') }}">ログアウト</a>
                    </div>
                </div>
            </div>
        </nav>

        @yield('content')

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

        <!-- Common Logic -->

        <script src="{{ asset('/js/app.js') }}"></script>

        <!-- Business JavaScript -->
        @yield('script')

    </body>
</html>
