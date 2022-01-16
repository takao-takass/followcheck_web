@extends('layout')

@section('title')
    <title>ツイートを見る</title>
@endsection

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
            @if($error != null)
                <label style="color: red;">{{$error}}</label>
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
                                @if($Users->page > 0)
                                    <li class="page-item"><a class="page-link" href="#" onclick="page(0);">&lt;&lt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{$Users->page - 1}});">&lt;</a></li>
                                @else
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;&lt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;</a></li>
                                @endif
                                @for ($i = ($Users->page-5 < 0 ? 0 : $Users->page-5); $i <= ($Users->page+5 > $Users->max_page ? $Users->max_page : $Users->page+5); $i++)
                                    @if($i == $Users->page)
                                        <li class="page-item active"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @endif
                                @endfor
                                @if($Users->page == $Users->max_page)
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;&gt;</a></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $Users->page + 1 }});">&gt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $Users->max_page }});">&gt;&gt;</a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="page" id="pageNumber" value="{{$Users->page}}">
                    <button type="submit" id="searchSubmit" style="display:none;">
                </form>
            </div>
        </div>

        <!-- ユーザ一覧 -->
        <div class="row">


            @foreach ($Users->tweet_take_users as $tweet_take_user)
                @if (in_array($tweet_take_user->status, array('5','6','9')))
                    <div class="col-12 mt-2">
                        <div class="d-flex p-3" style="background-color:#F6F6F6;">
                            <div class="d-inline-flex" style="height: 75px; min-height: 75px; min-width:75px; width: 75px;">
                                <a href="{{ route('show_user.index', ['user_id' => $tweet_take_user->user_id]) }}">
                                    <img class='img-radius img-fluid async-load' src="{{asset('./img/usericon1.jpg')}}" data-async-load="{{$tweet_take_user->thumbnail_url}}">
                                </a>
                            </div>
                            <div class="d-inline-flex d-flex flex-column ml-4">
                                <div>
                                    <a href="{{ route('show_user.index', ['user_id' => $tweet_take_user->user_id]) }}">
                                        <label><strong>{{$tweet_take_user->name}}</strong></label>
                                    </a>
                                    <label class="ml-4" style="color: gray;"><strong>@ {{$tweet_take_user->disp_name}}</strong></label>
                                </div>
                                <div>
                                    <label>{{$tweet_take_user->description}}</label>
                                </div>
                                <div>
                                    <label>{{$tweet_take_user->tweet_ready_count}} ツイート閲覧可能</label>
                                    <a class="ml-2" href="{{ route('user.index', ['user_id' => $tweet_take_user->user_id] ) }}">プロフィール</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-12 mt-2">
                        <div class="d-flex p-3" style="background-color:#F6F6F6;">
                            <div class="d-inline-flex" style="height: 75px; min-height: 75px; min-width:75px; width: 75px;">
                                <img class='img-radius img-opacity img-fluid async-load' src="{{asset('./img/usericon1.jpg')}}" data-async-load="{{$tweet_take_user->thumbnail_url}}">
                            </div>
                            <div class="d-inline-flex d-flex flex-column ml-4">
                                <div>
                                    <label><strong>{{$tweet_take_user->name}}</strong></label>
                                    <label class="ml-4" style="color: gray;"><strong>@ {{$tweet_take_user->disp_name}}</strong></label>
                                </div>
                                <div>
                                    <label>{{$tweet_take_user->description}}</label>
                                </div>
                                <div>
                                    <label style='color: red;'>ダウンロード中...</label>
                                    <a class="ml-2" href="{{ route('user.index', ['user_id' => $tweet_take_user->user_id] ) }}">プロフィール</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif



            @endforeach
            @if($Users->page <> $Users->max_page)
                <div class="col-4 col-md-3 col-lg-2">
                    <a href="#" onclick="page({{ $Users->page + 1 }});">
                        <img class='img-radius img-fluid' style=" max-width: 100%; height: auto;" src='{{ asset('/img/user_next.jpg') }}'>
                    </a>
                </div>
            @endif

        </div>

        <!-- スペーサ -->
        <div style="margin-bottom:300px"></div>

    </div>
@endsection

@section('script')

    <script>
        $(function(){
            asyncLoad();
        });
    </script>

@endsection
