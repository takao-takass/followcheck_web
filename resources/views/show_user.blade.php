@extends('layout')

@section('style')
        <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('content')

        <!-- メインコンテンツ -->
        <div class="container" style="">

            <div class="row" style="margin-top:2em;">

                <!-- ページ切り替えボタン -->
                <form action="{{ route('show_user.index', ['user_id' => $Thumbnails->user_id] ) }}" method="get">
                    @csrf

                    <nav style="margin-top:1em;">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" onclick="page({{ ($Thumbnails->Page)-1 }});">
                                    <i data-feather="arrow-left" class="iconwhite"></i>
                                </a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link">
                                    <span id="tweet-ct"></span>ユーザ
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" onclick="page({{ ($Thumbnails->Page)+1 }});">
                                    <i data-feather="arrow-right" class="iconwhite"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <input type="hidden" name="page" id="pageNumber" value="{{$Thumbnails->Page}}">
                    <button type="submit" id="searchSubmit" style="display:none;">

                </form>
            </div>

            <!-- ツイート一覧 -->
            <div class="row contents">
                @foreach ( $Thumbnails->show_thumbnails as $show_thumbnail )

                <div class='thumb col-lg-2 col-md-3 col-4 mb-1'>
                    <a href="{{$show_thumbnail->media_url}}"></a>
                    <img class='mr-3 thumb-radius thumb-back' style='width:100%;' src='{{$show_thumbnail->thumbnail_url}}'>
                </div>
                @endforeach
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
