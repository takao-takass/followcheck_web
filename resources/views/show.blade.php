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

                <div class="form-check" style="margin:1em;">
                    <input class="form-check-input filter-item" type="checkbox" id="filter-retweet" {{$filter['retweet_check']}}>
                    <label class="form-check-label" for="filter-retweet" style="color:white;">リツイートは表示しない</label>
                </div>
                <div class="form-check" style="margin:1em;">
                    <input class="form-check-input" type="checkbox" id="filter-keepmode">
                    <label class="form-check-label" for="filter-keepmode" style="color:white;">KEEPモード</label>
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
                    <input type="hidden" id="group" value="{{$filter['group_id']}}">
                </div>
            </div>
            
            <!-- ツイート一覧 -->
            <div class="row contents" id="twlist">
            </div>

            <!-- ページ下部のスペーサ -->
            <div style="margin-bottom:15em">
            </div>

        </div>

        <!-- モーダルコンテンツ -->
        <div class="modal fade" id="showmodal" tabindex="-1" role="dialog" aria-labelledby="showmodal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title">
                            <span>
                                <img src="" id="accounticon" class="twitterlinkicon">
                            </span>
                            <span id="tweetbody">
                            </span>
                            <span>
                                <a id="weblink" href="" target='_blank' rel='noreferrer'>　Twitterで見る</a>
                            </span>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <a id='originimagelink' href='' target='_blank' rel='noreferrer'>
                                <img id='originimage' class='mr-3 thumb-radius' style='width:100%;' src='' alt='新しいタブで表示する'>
                            </a>
                        </div>
                    </div>
                </div>
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

            // サムネイルクリック
            $(document).on('click','.thumb',function(){

                if($('#filter-keepmode').prop('checked')){

                    if($(this).children('span').hasClass('keepoff')){
                        // KEEPされていない場合はKEEPする
                        setKeep($(this).data('tweetid'));
                    }else{
                        // KEEPされている場合はKEEPから外す
                        unsetKeep($(this).data('tweetid'));
                    }

                }else{
                    showimage(
                        $(this).data('mediapath'),
                        $(this).data('thumbnailurl'),
                        $(this).data('body'),
                        $(this).data('weblink')
                    );
                }

            });

            // 画像モーダルを表示
            showimage = function(source,icon,body,weblink){
                $('#originimagelink').attr('href',source);
                $('#originimage').attr('src',source);
                $('#accounticon').attr('src',icon);
                $('#weblink').attr('href',weblink);
                $('#tweetbody').text(body);
                $('#showmodal').modal();
            }

            // ツイート一覧を取得する
            function showList(){

                $('.contents').hide();
                $('#spinner').show();

                $.ajax({
                    url:'{{ action('ShowController@list') }}',
                    type:'POST',
                    data:{
                        'user' : $('#user').val(),
                        'group' : $('#group').val(),
                        'page' : $('#page').val(),
                        'filter-retweet' : $('#filter-retweet').prop('checked') ? 1:'',
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
                        if(account.media_type != null){
                            for (var i = 0; i<account.thumb_names.length; i++) {

                                var html = 
                                    "<div class='thumb col-lg-2 col-md-3 col-4' role='button' style='margin-bottom:1em;' data-tweetid='"+account.tweet_id+"' data-mediapath='"+account.media_path[i]+"' data-thumbnailurl='"+account.thumbnail_url[i]+"' data-body='"+account.body+"' data-weblink='"+account.weblink+"'>"+
                                    "   <span class='thumb-keep-label [[keepclass]]'>K</span>" +
                                    "   <img class='mr-3 thumb-radius thumb-back' style='width:100%;' src='"+account.thumb_names[i]+"'>"+
                                    "</div>";

                                if(account.kept=='0'){
                                    html = html.replace('[[keepclass]]','keepoff');
                                }else{
                                    html = html.replace('[[keepclass]]','');
                                }

                                // 一覧にHTMLを表示する
                                $('#twlist').append(html);
                            }
                        }
                    });

                }).fail( (data) => {

                }).always(function(){
                    $('.contents').show();
                    $('#spinner').hide();
                });

            }

            // ツイートをキープする
            function setKeep(tweetid){

                $.ajax({
                    url:'{{ action('TweetsController@keep') }}',
                    type:'POST',
                    data:{
                        'tweetid' : tweetid
                    }
                }).done( (data) => {
                    $("div[data-tweetid='"+tweetid+"']").children('span').removeClass('keepoff');
                });

            }

            // ツイートをキープから外す
            function unsetKeep(tweetid){

                $.ajax({
                    url:'{{ action('TweetsController@unkeep') }}',
                    type:'POST',
                    data:{
                        'tweetid' : tweetid
                    }
                }).done( (data) => {
                    $("div[data-tweetid='"+tweetid+"']").children('span').addClass('keepoff');
                });

            }

        </script>
@endsection