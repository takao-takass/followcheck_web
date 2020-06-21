@extends('layout')

@section('style')
        <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('content')
        <!-- メインコンテンツ -->
        <div class="container" style="">

            <div class="row" style="margin-top:2em;">

                <!-- ページタイトル -->
                <div class="col-md-12">
                    <h2 class="text-center">
                    </h2>
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
            </div>
            
            <!-- ツイート一覧 -->
            <div class="row contents" id="twlist">
            </div>

            <!-- ページ下部のスペーサ -->
            <div style="margin-bottom:15em">
            </div>

        </div>
@endsection

@section('script')
        <!-- Business JavaScript -->
        <script type="text/javascript">

            // 画面表示
            $(document).ready(function(){
                $('body').css('background-color','#232323');
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
                    url:'{{ action('ShowController@list') }}',
                    type:'POST',
                    data:{
                        'user' : $('#user').val(),
                        'page' : $('#page').val(),
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
                        thumbhtml = "";
                        if(account.media_type != null){
                            for (var i = 0; i<account.thumb_names.length; i++) {
                                html = 
                                    "<div class='col-lg-2 col-md-3 col-4' style='margin-bottom:1em;'>"+
                                    "        <span><a href='"+account.media_path[i]+" 'target='_blank' rel='noopener noreferrer'><img class='mr-3 thumb-radius' style='width:100%;' src='"+account.thumb_names[i]+"'></a></span>" +
                                    "</div>";

                                // 一覧にHTMLを表示する
                                $('#twlist').append(
                                    html
                                        .replace('[[thunbs]]',thumbhtml)
                                );
                            }
                        }



                    });

                }).fail( (data) => {

                }).always(function(){
                    $('.contents').show();
                    $('#spinner').hide();
                });

            }

        </script>
@endsection