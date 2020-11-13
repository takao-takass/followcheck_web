@extends('layout')

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('content')
    <div class="container">
        <!-- アカウント追加フォーム -->
        <div class="row" style="margin-top:2em;">
            <div class="col-md-12">
                <form action="{{url()->current()}}/add" method="post" >
                    @csrf
                    <div class="input-group mb-2" style="margin-bottom:0.5em;">
                        <div class="input-group-prepend">
                            <span class="input-group-text">＠</span>
                        </div>
                        <input type="text" name="user_id" class="form-control">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-secondary" id="add-button">追加</button>
                        </div>
                    </div>
                </form>
            </div>
            @if ($ErrorMessage<>"")
                <div class="col-md-12">
                    <span style="color:red;">{{ $ErrorMessage }}</span>
                </div>
            @endif
        </div>

        <!-- ページ切り替えフォーム -->
        <div class="row" style="margin-top:2em;">
            <div class="col">
                <form action="{{ route('tweetuser.index') }}" method="get">
                    @csrf
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end">
                                @if($Users->Page > 0)
                                    <li class="page-item"><a class="page-link" href="#" onclick="page(0);">&lt;&lt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{$Users->Page - 1}});">&lt;</a></li>
                                @else
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;&lt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;</a></li>
                                @endif
                                @for ($i = ($Users->Page-5 < 0 ? 0 : $Users->Page-5); $i <= ($Users->Page+5 > $Users->MaxPage ? $Users->MaxPage : $Users->Page+5); $i++)
                                    @if($i == $Users->Page)
                                        <li class="page-item active"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @endif
                                @endfor
                                @if($Users->Page == $Users->MaxPage)
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;&gt;</a></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $Users->Page + 1 }});">&gt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $Users->MaxPage }});">&gt;&gt;</a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="page" id="pageNumber" value="{{$Users->Page}}">
                    <button type="submit" id="searchSubmit" style="display:none;">
                </form>
            </div>
        </div>

        <!-- ユーザ一覧 -->
        <div class="row">
            @foreach ($Users->TweetTakeUsers as $tweetTakeUser)
                @if (in_array($tweetTakeUser->Status, array('5','6','9')))
                    <div class="col-4 col-md-3 col-lg-2">
                        <a href="{{ route('show_user.index', ['user_id' => $tweetTakeUser->UserId]) }}" target='_blank' rel='noreferrer'>
                            <img class='img-radius img-fluid' style=" max-width: 100%; height: auto;" src='{{$tweetTakeUser->ThumbnailUrl}}'>
                        </a>
                    </div>
                @else
                    <div class="col-4 col-md-3 col-lg-2">
                        <img class='img-radius img-opacity img-fluid' style=" max-width: 100%; height: auto;" src='{{$tweetTakeUser->ThumbnailUrl}}'>
                    </div>
                @endif
            @endforeach
            <div class="col-4 col-md-3 col-lg-2">
                <a href="#" onclick="page({{ $Users->Page + 1 }});">
                    <img class='img-radius img-fluid' style=" max-width: 100%; height: auto;" src='{{ asset('/img/user_next.jpg') }}'>
                </a>
            </div>
        </div>

        <!-- スペーサ -->
        <div style="margin-bottom:300px"></div>

    </div>
@endsection

@section('script')

    <script>
    </script>

@endsection
