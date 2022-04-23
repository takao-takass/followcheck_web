@php
    use App\Constants\WebRoute;
    use App\Constants\ApiRoute;
    use App\Constants\MediaThumbnailSize;
@endphp

@extends('layout')

@section('title')
    <title>ギャラリー / followcheck</title>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/darktheme.css') }}">
@endsection

@section('content')

    <!-- メインコンテンツ -->
    <div class="row mt-3"></div>

    <div class="container-fluid">

    {{ Cookie::get('thumbnail_size') }}
    {{ $viewModel->thumbnail_size }}

        <!-- ツイート一覧 -->
        <div class="row contents">

            @php
            switch ($viewModel->thumbnail_size) {
                case MediaThumbnailSize::SMALL :
                    $layout_cols = 'col-xl-1 col-lg-2 col-md-3 col-sm-4 col-6';
                    break;
                case MediaThumbnailSize::LARGE:
                    $layout_cols = 'col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12';
                    break;
                case MediaThumbnailSize::MEDIUM:
                default:
                    $layout_cols = 'col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12';
                    break;
            }
            @endphp
        
            <div class='{{$layout_cols}} mb-1'>
                <button type="button" class="btn btn-secondary" style="width:100%; height:100%;" onclick="changeThumbnailSize('{{$viewModel->thumbnail_size}}')">サイズ変更</button>
            </div>

            @foreach ( $viewModel->items as $item )

            <div class='{{$layout_cols}} mb-1'>
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
                        Video
                    @else
                        Photo
                    @endif
                    
                </button>
            </div>

            @endforeach

            <div class='{{$layout_cols}} mb-1'>
                <button type="button" class="btn btn-primary" style="width:100%; height:100%;" onclick="checked()">ページを既読</button>
            </div>

        </div>
        
        <!-- ページ下部のスペーサ -->
        <div style="margin-bottom:15em">
        </div>

    </div>



    <!-- Modal -->
    <div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="userName" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userName"></h5>
                    <label id="tweetText"></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="imageDetail" class="modal-body">
                    <a id="originalLink" href="#" target="_blank" rel="noopener noreferrer"><img id="originalMedia" style="width: 100%;" alt="新しいタブで表示" src=""/></a>
                </div>
                <div id="videoDetail" class="modal-body">
                    <a id="videoLink" href="#" target="_blank" rel="noopener noreferrer">新しいタブで再生</a>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="modalUserId">
                    <input type="hidden" id="modalTweetId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" onclick="keepByModal()">KEEP</button>
                    <a href="#" id="userpageLink"  target="_blank" rel="noopener noreferrer" type="button" class="btn btn-primary">ユーザ</a>
                    <a href="#" id="twitterLink"  target="_blank" rel="noopener noreferrer" type="button" class="btn btn-primary">Twitter</a>
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

            $.post("{{ route('api.gallery.keep') }}", param, function(){
                $('.tweet-'+tweetId).addClass('img-opacity');
            });
        }

        function checked(){

            let param = {
                user_ids: userIds,
                tweet_ids: tweetIds,
            }

            $.post("{{ route('api.gallery.checked') }}", param, function(){
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

            @php
                $url_user_detail = route(WebRoute::USER_DETAIL, ['user_id' => '999userId999'] );
            @endphp

            $.get("{{ route('api.gallery.mediadetail') }}", param, function(response){

                if(response.media_type === "video") {
                    $('#imageDetail').hide();
                    $('#videoDetail').show();
                    $('#videoDetail').append(
                        "<video id='videoSource' style='max-width:100%' src='"+response.media_url+"' autoplay muted controls type='video/mp4'>"+
                        "<p>ご使用のブラウザでは動画再生に対応していません</p>"+
                        "</video>"
                    );
                    $("#videoLink").attr("href", response.media_url);
                    $("#originalMedia").attr("src", "");
                } else {
                    $('#imageDetail').show();
                    $('#videoDetail').hide();
                    $("#videoSource").attr("src", "");
                    $("#videoLink").attr("href", "");
                    $("#originalMedia").attr("src", response.media_url);
                }

                $("#userName").text(response.name);
                $("#tweetText").text(response.tweet_text);
                $("#originalLink").attr("href", response.media_url);
                $("#userpageLink").attr("href", '{{$url_user_detail}}'.replace('999userId999', userId));
                $("#twitterLink").attr("href", response.twitter_url);
                $("#modalUserId").val(userId);
                $("#modalTweetId").val(tweetId);
                $('#mediaModal').modal();
            });

        }

        $('#mediaModal').on('hidden.bs.modal', function (e) {
            $('#videoSource').remove();
        });

        function keepByModal(){
            let userId = $("#modalUserId").val();
            let tweetId = $("#modalTweetId").val();
            keep(userId, tweetId);
        }

        function openUserPage(){

        }

        function changeThumbnailSize(currentThumbnailSize) {

            @php
                $url_change_thumbnail_api = route(ApiRoute::GALLERY_CHANGE_THUMBNAILSIZE);
            @endphp
            let param = { "thumnbail_size" : currentThumbnailSize };
            $.post("{{ $url_change_thumbnail_api }}", param, function(response) {
                location.reload();
            });

        }
    </script>
@endsection
