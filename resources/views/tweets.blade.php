@extends('layout')

@section('style')
        <link rel="stylesheet" href="{{ asset('/css/tweets.css') }}">
@endsection

@section('content')
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
                                <input class="form-check-input filter-item" type="checkbox" id="filter-retweet" {{$filter['retweet_check']}}>
                                <label class="form-check-label" for="filter-retweet">リツイートは表示しない</label>
                            </div>
                            <div class="form-check" style="margin:1em;">
                                <input class="form-check-input filter-item" type="checkbox" id="filter-reply" {{$filter['reply_check']}}>
                                <label class="form-check-label" for="filter-reply">リプライは表示しない</label>
                            </div>
                            <div class="form-check" style="margin:1em;">
                                <input class="form-check-input filter-item" type="checkbox" id="filter-media" {{$filter['media_check']}}>
                                <label class="form-check-label" for="filter-media">メディアのみ表示する</label>
                            </div>
                            <div class="form-check" style="margin:1em;">
                                <input class="form-check-input filter-item" type="checkbox" id="filter-keep" {{$filter['keep_check']}}>
                                <label class="form-check-label" for="filter-keep">KEEPのみ表示する</label>
                            </div>
                            <div class="form-check" style="margin:1em;">
                                <input class="form-check-input filter-item" type="checkbox" id="filter-unkeep" {{$filter['unkeep_check']}}>
                                <label class="form-check-label" for="filter-unkeep">KEEP以外のみ表示する</label>
                            </div>
                            <div class="form-check" style="margin:1em;">
                                <input class="form-check-input filter-item" type="checkbox" id="filter-unchecked" {{$filter['unchecked_check']}}>
                                <label class="form-check-label" for="filter-unchecked">既読は非表示</label>
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
                    <input type="hidden" id="group" value="{{$filter['group_id']}}">
                </div>

                <!-- ツイート一覧 -->
                <div class="col-md-12 contents" id="twlist">
                </div>

                <div class="col-md-12 contents" style="margin-bottom:1em">
                    <button class="btn btn-primary rounded-pill" id="checked-button" style="width:100%;margin-top:2em;">表示中のツイートを既読にする</button>
                </div>

                <div class="col-md-12 contents" style="margin-bottom:1em">
                    <button class="btn btn-primary rounded-pill" id="allkeep-button" style="width:100%;margin-top:2em;">表示中のツイートをKEEPする</button>
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

            // キープ
            $(document).on('click','.keep',function(obj){
                if($(this).attr('value')=='0'){
                    // キープする
                    setKeep($(this));
                }else{
                    // キープを解除
                    unsetKeep($(this));
                }
            });
            
            // 検索条件の変更
            $('.filter-item').on('change',function(){
                $('#page').val(0);
                showList();
            });

            // 既読
            $('#checked-button').on('click',function(){
                var idList = []
                $(".tweet-id").each(function(i, idobj) {
                    idList.push($(idobj).val());
                });
                checked(idList.join(','));
            });

            // 全てKEEP
            $('#allkeep-button').on('click',function(){
                var idList = []
                $(".tweet-id").each(function(i, idobj) {
                    idList.push($(idobj).val());
                });
                setAllKeep(idList.join(','));
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
                        'group' : $('#group').val(),
                        'page' : $('#page').val(),
                        'filter-reply' : $('#filter-reply').prop('checked') ? 1:'',
                        'filter-retweet' : $('#filter-retweet').prop('checked') ? 1:'',
                        'filter-media' : $('#filter-media').prop('checked') ? 1:'',
                        'filter-keep' : $('#filter-keep').prop('checked') ? 1:'',
                        'filter-unkeep' : $('#filter-unkeep').prop('checked') ? 1:'',
                        'filter-unchecked' : $('#filter-unchecked').prop('checked') ? 1:'',
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
                            "    <a href='{{ action('UserController@index',['']) }}/[[user_id]]' target='_blank' rel='noopener noreferrer'><img class='usericon' style='margin:1em' src='[[thumbnail_url]]'></a>"+
                            "    <div class='media-body' style='margin:1em 1em 1em 0em'>"+
                            "        <h6 class='tweet-body' style='word-wrap:break-all;'>[[body]]</h6>"+
                            "        <div class='row contents'>[[thunbs]]</div>" +
                            "        <div>[[tweeted_datetime]]　<a href='[[weblink]]' target='_blank' rel='noopener noreferrer'>Twitterで見る</a></div>"+
                            "        <div class='keepbutton'><span class='keep [[keep_style]]' id='[[tweet_id]]' value='[[kept]]'>　　KEEP　　</span></div>"+
                            "        <input type='hidden' class='tweet-id' value='[[tweet_id_hidden]]'>"+
                            "    </div>"+
                            "</div>";

                        // メディアのサムネイル表示を作る
                        thumbhtml = "";
                        if(account.media_type != null){
                            for (var i = 0; i<account.thumb_names.length; i++) {
                                thumbhtml +=
                                    "<div class='thumb col-lg-3' style='margin-bottom:1em;'>"+
                                    "    <a href='"+account.media_path[i]+" 'target='_blank' rel='noopener noreferrer'>"+
                                    "       <img class='mr-3 thumb-radius' style='width:100%;' src='"+account.thumb_names[i]+"'>"+
                                    "    </a>"+
                                    "</div>";
                            }
                        }

                        // 一覧にHTMLを表示する
                        $('#twlist').append(
                            html
                                .replace('[[thumbnail_url]]',account.thumbnail_url)
                                .replace('[[user_id]]',account.user_id)
                                .replace('[[body]]',account.body)
                                .replace('[[tweeted_datetime]]',account.tweeted_datetime)
                                .replace('[[retweet_count]]',account.retweet_count)
                                .replace('[[favolite_count]]',account.favolite_count)
                                .replace('[[weblink]]',account.weblink)
                                .replace('[[thunbs]]',thumbhtml)
                                .replace('[[keep_style]]', (account.kept == '0' ? 'keepoff' : 'keepon') )
                                .replace('[[tweet_id]]',account.tweet_id)
                                .replace('[[tweet_id_hidden]]',account.tweet_id)
                                .replace('[[kept]]',account.kept)
                        );

                    });

                }).fail( (data) => {

                }).always(function(){
                    $('.contents').show();
                    $('#spinner').hide();
                });

            }

            // ツイートをキープする
            function setKeep($evobj){

                $.ajax({
                    url:'{{ action('TweetsController@keep') }}',
                    type:'POST',
                    data:{
                        'tweetid' : $evobj.attr('id'),
                    }
                }).done( (data) => {
                    $evobj.attr('value','1');
                    $evobj.addClass('keepon');
                    $evobj.removeClass('keepoff');
                });

            }

            // ツイートをキープから外す
            function unsetKeep($evobj){

                $.ajax({
                    url:'{{ action('TweetsController@unkeep') }}',
                    type:'POST',
                    data:{
                        'tweetid' : $evobj.attr('id'),
                    }
                }).done( (data) => {
                    $evobj.attr('value','0');
                    $evobj.addClass('keepoff');
                    $evobj.removeClass('keepon');
                });

            }

            // ツイートを全てキープする
            function setAllKeep(tweetId){

                $.ajax({
                    url:'{{ action('TweetsController@keep') }}',
                    type:'POST',
                    data:{
                        'tweetid' : tweetId,
                    }
                }).done( (data) => {
                    showList();
                });

            }

            // ツイートを既読する
            function checked(joinedId){

                $.ajax({
                    url:'{{ action('TweetsController@checked') }}',
                    type:'POST',
                    data:{
                        'tweetid' : joinedId,
                    }
                }).done( (data) => {
                    alert('既読にしました');
                    showList();
                });

            }

        </script>
@endsection