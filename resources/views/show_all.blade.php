@extends('layout')

@section('title')
    <title>観賞</title>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/darktheme.css') }}">
@endsection

@section('content')

    <!-- メインコンテンツ -->
    <div class="container-fluid" style="">

        <!-- ページ切り替えフォーム -->
        <div class="row" style="margin-top:2em;">
            <div class="col">
                <form action="{{ route('show_all.index') }}" method="get">
                    @csrf
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end">
                                @if($Thumbnails->Page > 0)
                                    <li class="page-item"><a class="page-link" href="#" onclick="page(0);">&lt;&lt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{$Thumbnails->Page - 1}});">&lt;</a></li>
                                @else
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;&lt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;</a></li>
                                @endif
                                @for ($i = ($Thumbnails->Page-5 < 0 ? 0 : $Thumbnails->Page-5); $i <= ($Thumbnails->Page+5 > $Thumbnails->MaxPage ? $Thumbnails->MaxPage : $Thumbnails->Page+5); $i++)
                                    @if($i == $Thumbnails->Page)
                                        <li class="page-item active"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @endif
                                @endfor
                                @if($Thumbnails->Page == $Thumbnails->MaxPage)
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;&gt;</a></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $Thumbnails->Page + 1 }});">&gt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $Thumbnails->MaxPage }});">&gt;&gt;</a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="page" id="pageNumber" value="{{$Thumbnails->Page}}">
                    <button type="submit" id="searchSubmit" style="display:none;">
                </form>
            </div>
        </div>

        <!-- ツイート一覧 -->
        <div class="row contents">
            @foreach ( $Thumbnails->show_thumbnails as $show_thumbnail )
                <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                    <a href="{{ route('media.index',['tweet_id'=>$show_thumbnail->tweet_id, 'file_name'=>$show_thumbnail->file_name, 'last_tweet_id'=>$Thumbnails->show_thumbnails[0]->tweet_id]) }}">
                        <img class='mr-3 thumb-radius thumb-back' style='width:100%;' src='{{$show_thumbnail->thumbnail_url}}'>
                    </a>
                </div>
            @endforeach
            @if($Thumbnails->Page <> $Thumbnails->MaxPage)
                <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                    <a href="#" onclick="page({{ $Thumbnails->Page + 1 }});">
                        <img class='mr-3 thumb-radius thumb-back' style='width:100%;' src='{{ asset('/img/media_next.jpg') }}'>
                    </a>
                </div>
            @endif
        </div>

        <!-- ページ下部のスペーサ -->
        <div style="margin-bottom:15em">
        </div>

    </div>


@endsection

@section('script')
    <!-- Business JavaScript -->
    <script>
    </script>
@endsection
