@extends('layout')

@section('title')
    <title>KEEP</title>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/darktheme.css') }}">
@endsection

@section('content')

    <!-- メインコンテンツ -->
    <div class="container-fluid" style="">

        @php
            // TODO 全て選択ボタンと、鑑賞に戻るを実装してください。
        @endphp

        <div class="row" style="margin-top:2em;">
            <div class="col">
                <form action="{{ route(\App\Constants\WebRoute::KEEP_INDEX, ['user_id' => $view_model->user_id] ) }}" method="get">
                    @csrf
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end">
                                @if($Thumbnails->Page > 0)
                                    <li class="page-item"><a class="page-link" href="#" onclick="page(0);">&lt;&lt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{$view_model->page - 1}});">&lt;</a></li>
                                @else
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;&lt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;</a></li>
                                @endif
                                @for ($i = ($view_model->page-5 < 0 ? 0 : $view_model->page-5); $i <= ($view_model->page+5 > $view_model->max_page ? $view_model->max_page : $view_model->page+5); $i++)
                                    @if($i == $view_model->page)
                                        <li class="page-item active"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @endif
                                @endfor
                                @if($view_model->page == $view_model->max_page)
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;&gt;</a></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $view_model->page + 1 }});">&gt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $view_model->max_page }});">&gt;&gt;</a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="page" id="pageNumber" value="{{$view_model->page}}">
                    <button type="submit" id="searchSubmit" style="display:none;"></button>
                </form>
            </div>
        </div>


        <div class="row contents">
            @foreach ( $view_model->list as $media )
                <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                    <img
                        alt=""
                        class='mr-3 thumb-radius thumb-back async-load tweet-{{$media->tweet_id}}'
                        onclick="media_click({{$media->tweet_id}})"
                        style='width:100%;'
                        src="{{asset('./img/media_default.jpg')}}"
                        data-async-load='{{$media->thumbnail_url}}'>
                </div>
            @endforeach
            @if($view_model->page <> $view_model->max_page)
                <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                    <a href="#" onclick="page({{ $view_model->page + 1 }});">
                        <img alt="" class='mr-3 thumb-radius thumb-back' style='width:100%;' src='{{ asset('/img/media_next.jpg') }}'>
                    </a>
                </div>
            @endif
        </div>

        <div class="row contents">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="keep()">選択をKEEP</button>
        </div>

        <div style="margin-bottom:15em">
        </div>

    </div>


@endsection

@section('script')
    <script>
        $(function(){
            asyncLoad();
        });

        var selected_tweet_id = [];
        function media_click(tweet_id){
            let index = selected_tweet_id.indexOf(tweet_id);
            if(index === -1){
                selected_tweet_id.push(tweet_id);
                $('.tweet-'+tweet_id).addClass('img-opacity');
            }else{
                selected_tweet_id = selected_tweet_id.filter(t => t !== tweet_id);
                $('.tweet-'+tweet_id).removeClass('img-opacity');
            }
        }

        function keep(){
            $.post("{{ route(\App\Constants\ApiRoute::KEEP_ENTRY) }}",{ tweet_ids: selected_tweet_id }, function(){
                Location.reload();
            });
        }
    </script>
@endsection
