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
    <div class="container-fluid">

        <!-- ページ切り替えフォーム -->
        <div class="row" style="margin-top:2em;">
            <div class="col">
                <form action="{{ route('gallery.all') }}" method="get">
                    @csrf
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end">
                                @if($viewModel->page > 0)
                                    <li class="page-item"><a class="page-link" href="#" onclick="page(0);">&lt;&lt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{$viewModel->page - 1}});">&lt;</a></li>
                                @else
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;&lt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;</a></li>
                                @endif
                                
                                <li class="page-item"><a class="page-link" href="#" onclick="page({{ $viewModel->page + 1 }});">&gt;</a></li>
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="page" id="pageNumber" value="{{$viewModel->page}}">
                    <button type="submit" id="pageSubmit" style="display:none;"></button>
                </form>
            </div>
        </div>

        <!-- ツイート一覧 -->
        <div class="row contents">

            @foreach ( $viewModel->items as $item )

            <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                <a onclick="keep('{{$item->tweet_id}}')">
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
                    onclick='modal("{{$item->tweet_id}}", "{{$item->user_id}}")'>

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
            <a id="originarlLink" href="#" target="_blank" rel="noopener noreferrer"><img id="originalMedia" alt="メディアにアクセス" src=""/></a>
        </div>
        <div class="modal-footer">
            <ba type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <a href="#" type="button" class="btn btn-primary">Twitterで表示</button>
        </div>
        </div>
    </div>
    </div>


@endsection

@section('script')
    <!-- Business JavaScript -->
    <script>
    
        $(function(){
            $('[data-toggle="tooltip"]').tooltip();
            asyncLoad();
        });

        function keep(tweet_id){
            $.post("{{ route('api.show_all.keep') }}",{ tweet_id: tweet_id },function(){
                $('.tweet-'+tweet_id).addClass('img-opacity');
            });
        }

        function checked(){

        }

        function modal(tweetId, userId){
            $("#userName").text("えうぅんｍｎ");
            $("#tweetText").text("");
            $("#originalLink").attr("href", "");
            $("#originalMedia").attr("src", "");
            $('#mediaModal').modal();
        }
    </script>
@endsection
