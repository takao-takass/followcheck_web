@extends('layout')

@section('style')
        <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('content')

        <!-- メインコンテンツ -->
        <div class="container" style="">

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
                                        <li class="page-item"><a class="page-link" href="#" onclick="page({{$Thumbnails->Page - 1}});">&lt; 前</a></li>
                                    @else
                                        <li class="page-item disabled"><a class="page-link" href="#">&lt;&lt;</a></li>
                                        <li class="page-item disabled"><a class="page-link" href="#">&lt; 前</a></li>
                                    @endif
                                    @for ($i = ($Thumbnails->Page-2 < 0 ? 0 : $Thumbnails->Page-2); $i <= ($Thumbnails->Page+2 > $Thumbnails->MaxPage ? $Thumbnails->MaxPage : $Thumbnails->Page+2); $i++)
                                        @if($i == $Thumbnails->Page)
                                            <li class="page-item active"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                        @endif
                                    @endfor
                                    @if($Thumbnails->Page == $Thumbnails->MaxPage)
                                        <li class="page-item disabled"><a class="page-link" href="#">次 &gt;</a></li>
                                        <li class="page-item disabled"><a class="page-link" href="#">&gt;&gt;</a></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="#" onclick="page({{ $Thumbnails->Page + 1 }});">次 &gt;</a></li>
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

                <div class='col-lg-2 col-md-3 col-4 mb-1'>
                    <a href="{{$show_thumbnail->media_url}}">
                        <img class='mr-3 thumb-radius thumb-back' style='width:100%;' src='{{$show_thumbnail->thumbnail_url}}'>
                    </a>
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
