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

        <label style="color: white">削除するツイートをクリックします。</label>

        <!-- ページ切り替えフォーム -->
        <div class="row" style="margin-top:2em;">
            <div class="col">
                <form action="{{ route('show_user.index', ['user_id' => $Thumbnails->user_id] ) }}" method="get">
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
        <form action="{{ route('show_user.remove', ['user_id' => $Thumbnails->user_id] )}}" method="post" >
            @csrf
            <input type="hidden" name="tweet_id" id="tweet_id">
            <button style="display:none" type="submit" id="submit"></button>
        </form>
        <div class="row contents">
            @foreach ( $Thumbnails->show_thumbnails as $show_thumbnail )
                <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                    <a href="#" onclick="remove({{$show_thumbnail->tweet_id}})">
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
        function remove(id){
            $('#tweet_id').val(id);
            $('#submit').click();
        }

    </script>
@endsection
