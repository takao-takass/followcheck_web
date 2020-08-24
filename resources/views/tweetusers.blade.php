@extends('layout')

@section('content')
    <div class="container">

        <div class="row" style="margin-top:2em;">
    
            <!-- ページタイトル -->
            <div class="col-md-12">
                <h2 class="text-center">
                </h2>
            </div>

            <!-- アカウント追加フォーム -->
            <div class="input-group mb-2 col-md-12" style="margin-bottom:0.5em;">
                <div class="input-group-prepend">
                    <span class="input-group-text">＠</span>
                </div>
                <input type="email" class="form-control" id="accountname" >
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" id="add-button">　　追加　　</button>
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
                                <span id="tweet-ct"></span>ユーザ
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
            </div>

        </div>

        <!-- ユーザ一覧 -->
        <div class="row contents" id="userlist">
        <div>

        <!-- スペーサ -->
        <div style="margin-bottom:300px"></div>

    </div>
@endsection

@section('script')

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

    // アカウント追加ボタン
    $('#add-button').on('click',function(){
        addUser();
    });

    // アカウント削除ボタン
    $(document).on('click', '.del-button', function(){
        deleteUser(this.value);
    });

    // ツイート一覧を取得する
    function showList(){
        $('.contents').hide();
        $('#spinner').show();
        $.ajax({
            url:'{{ action('TweetUsersController@list') }}',
            type:'POST',
            data:{
                'page' : $('#page').val(),
            }
        }).done( (data) => {
            $('#tweet-ct').text(data.record);
            $('#userlist').empty();
            // ページングの表示切替
            (data.prev_page>=0) ? $('#prev').show() : $('#prev').hide();
            (data.next_page<data.max_page) ? $('#next').show() : $('#next').hide();
            // ユーザ一覧の表示
            $.each(data.accounts, function(index,account){
                $('#userlist').append(createUserCard(account));
            });
        }).fail( (data) => {
        }).always(function(){
            $('.contents').show();
            $('#spinner').hide();
        });
    }

    // アカウントを追加する
    function addUser(){
        $.ajax({
            url:'{{ action('TweetUsersController@add') }}',
            type:'POST',
            data:{
                service_user_id : $('#service-user-id').val(),
                accountname : $('#accountname').val()
            }
        }).done( (data) => {
            location.reload();
        }).fail( (data) => {
            // サーバメッセージの表示
            resobj = JSON.parse(data.responseText);

            // コンフリクト以外の場合、エラーメッセージを表示
            if(data.status!=409){
                alert(resobj.message);
                $('.input_error').removeClass('input_error');
                $.each(resobj.params, function(index, value) {
                    $('#'+value).addClass('input_error');
                });
            }

            // コンフリクトの場合
            // 当該ユーザの情報を取得して一覧の先頭に表示する
            if(data.status==409){
                $.ajax({
                    url:'{{ action('TweetUsersController@list') }}',
                    type:'POST',
                    data:{
                        'user' : $('#accountname').val(),
                        'page' : 0,
                    }
                }).done( (data) => {
                    $.each(data.accounts, function(index,account){
                        $('#userlist').prepend(createUserCard(account));
                    });
                });
            }
        });
    }

    // ユーザリストのカードHTMLを作成する
    function createUserCard(account){
        // HTMLのテンプレート
        html = 
            "<div class='col-lg-3 col-md-4 col-6' style='margin-bottom:1em'>"+
            "    <div class='card shadow-sm' style='width:100%;height:100%;'> "+
            "        <a href='{{ action('UserController@index',['']) }}/"+account.user_id+"'><img class='card-img-top' src='[[thumbnail_url]]' style='height: 100px;object-fit: cover;*/'></a> "+
            "        <div class='card-body'> "+
            "        <h5 class='card-title' style='font-weight: bold;'>[[name]]</h6> "+
            "            [[delbtn]]"+
            "            [[tweetlink]]"+
            "        </div> "+
            "    </div> "+
            "</div>"

        // 削除ボタンのHTMLを作成する
        delbtnHtml = "";
        if(account.delbtn_show=="1"){
            delbtnHtml = 
                " <div class='text-right'> "+
                "   <button class='btn btn-secondary rounded-pill del-button' value='"+account.user_id+"' style='height:35px;font-size: 10pt;'>削除</button> "+
                " </div> ";
        }

        // ツイート表示用リンクのHTMLを作成する
        linkHtml = "";
        if(account.tweet_show=="1"){
            linkHtml = 
                " <h6 class='card-subtitle text-muted' style='margin-top:0.5em;'><a href='{{ action('TweetsController@index',['']) }}/"+account.user_id+"' target='_blank' rel='noreferrer' class='card-link'>ツイート</a></h6> "+
                " <h6 class='card-subtitle text-muted' style='margin-top:0.5em;'><a href='{{ action('ShowController@index',['']) }}/"+account.user_id+"' target='_blank' rel='noreferrer' class='card-link'>観賞モード</a></h6> ";
        }else{
            linkHtml = 
                " <span class='badge badge-secondary' style='margin-bottom:1em;'>"+account.status+"</span> ";
        }

        return html
                .replace('[[thumbnail_url]]',account.thumbnail_url)
                .replace('[[name]]',account.name)
                .replace('[[disp_name]]',account.disp_name)
                .replace('[[delbtn]]',delbtnHtml)
                .replace('[[tweetlink]]',linkHtml);
    }

    // アカウントを削除する
    function deleteUser(userId){
        $.ajax({
            url:'{{ action('TweetUsersController@del') }}',
            type:'POST',
            data:{
                service_user_id : $('#service-user-id').val(),
                user_id : userId
            }
        }).done( (data) => {
            location.reload();
        }).fail( (data) => {
            resobj = JSON.parse(data.responseText);
                alert(resobj.message);
                $('.input_error').removeClass('input_error');
                $.each(resobj.params, function(index, value) {
                    $('#'+value).addClass('input_error');
                });
        });
    }

</script>

@endsection
