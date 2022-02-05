@extends('layout')

@section('title')
    <title>ギャラリー</title>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/darktheme.css') }}">
@endsection

@section('content')

    <!-- メインコンテンツ -->
    <div class="row mt-3"></div>

    <div class="container-fluid">

        <!-- ツイート一覧 -->
        <div class="row contents">

            @foreach ( $viewModel->items as $item )

            <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                <a onclick="keep('{{$item->user_id}}', '{{$item->tweet_id}}')">
                    <img
                        alt=""
                        class='mr-3 thumb-radius thumb-back async-load tweet-{{$item->tweet_id}}'
                        data-toggle="tooltip" data-placement="top" title="{{$item->tweet_text}}"
                        style='width:100%;'
                        src="{{asset('./img/media_default.jpg')}}"
                        data-async-load='{{$item->thumbnail_url}}'>
                </a>
                <button 
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    style='width: 100%;'
                    onclick='modal("{{$item->tweet_id}}", "{{$item->user_id}}", "{{$item->media_name}}")'>

                    @if($item->type == 'video')
                        Video DETAIL
                    @else
                        Photo DETAIL
                    @endif
                    
                </button>
            </div>

            @endforeach

            <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                <a href="#" onclick="page({{ $viewModel->page + 1 }});">
                    <img alt="" class='mr-3 thumb-radius thumb-back' style='width:100%;' src='{{ asset('/img/media_next.jpg') }}'>
                </a>
            </div>

        </div>

        <div class="row">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="checked()">このページを既読にする</button>
        </div>
        
        <!-- ページ下部のスペーサ -->
        <div style="margin-bottom:15em">
        </div>

    </div>



    <!-- Modal -->
    <div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="userName" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userName"></h5>
                    <label id="tweetText"></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <a id="originalLink" href="#" target="_blank" rel="noopener noreferrer"><img id="originalMedia" alt="メディアにアクセス" src=""/></a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="twitterLink"  target="_blank" rel="noopener noreferrer" type="button" class="btn btn-primary">Twitterで表示</a>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <!-- Business JavaScript -->
    <script>
    
        let tweetIds = "{{ implode(',', array_column($viewModel->items, 'tweet_id')) }}";
        let userIds = "{{ implode(',', array_column($viewModel->items, 'user_id')) }}";
    
        $(function(){
            $('[data-toggle="tooltip"]').tooltip();
            asyncLoad();
        });

        function keep(userId, tweetId){

            let param = {
                user_id: userId,
                tweet_id: tweetId
            }

            $.post("{{ route('api.gallery_all.keep') }}", param, function(){
                $('.tweet-'+tweetId).addClass('img-opacity');
            });
        }

        function checked(){

            let param = {
                user_ids: userIds,
                tweet_ids: tweetIds,
            }

            $.post("{{ route('api.gallery_all.checked') }}", param, function(){
                window.scroll({top: 0});
                location.reload();
            });

        }

        function modal(tweetId, userId, mediaName){

            let param = {
                tweet_id: tweetId,
                user_id: userId,
                media_name: mediaName,
            }

            $.get("{{ route('api.gallery_all.mediadetail') }}", param, function(response){
                $("#userName").text(response.name);
                $("#tweetText").text(response.tweet_text);
                $("#originalLink").attr("href", response.media_url);
                $("#originalMedia").attr("src", response.media_url);
                $("#twitterLink").attr("href", response.twitter_url);
                $('#mediaModal').modal();
            });



        }
    </script>
@endsection
