@extends('layout')

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
                        <input type="text" name="accountname" class="form-control">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-secondary" id="add-button">　　追　加　　</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ページ切り替えフォーム -->
        <div class="row" style="margin-top:2em;">

            <div class="col-md-12 contents">

                <form action="{{ route('tweetuser.index') }}" method="get">
                @csrf

                    <nav style="margin-top:1em;">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" onclick="page({{ ($Users->Page)-1 }});">
                                    <i data-feather="arrow-left" class="iconwhite"></i>
                                </a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link">
                                    <span id="tweet-ct"></span>ユーザ
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" onclick="page({{ ($Users->Page)+1 }});">
                                    <i data-feather="arrow-right" class="iconwhite"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <input type="hidden" name="page" id="pageNumber" value="{{$Users->Page}}">
                    <button type="submit" id="searchSubmit" style="display:none;">


                </form>


            </div>

        </div>



        <!-- ユーザ一覧 -->
        <div class="row contents" id="userlist">

            @foreach ($Users->TweetTakeUsers as $tweetTakeUser)
            <div class='col-lg-3 col-md-4 col-6' style='margin-bottom:1em'>
                <div class='card shadow-sm' style='width:100%;height:100%;'> 
                    <a href=''><img class='card-img-top' src='{{$tweetTakeUser->ThumbnailUrl}}' style='height: 100px;object-fit: cover;'></a>
                    <div class='card-body'> 
                        <h5 class='card-title' style='font-weight: bold;'>{{$tweetTakeUser->Name}}</h6> 
                        @if ($tweetTakeUser->Status != 'D')
                        <div class='text-right'>
                            <button class='btn btn-secondary rounded-pill del-button' style="height:35px;font-size:10pt;">削除</button>
                        </div>
                        @endif

                        @if (in_array($tweetTakeUser->Status, array('5','6','9')))
                        <div>
                            <h6 class='card-subtitle text-muted' style="margin-top:0.5em;"><a href="{{ action('TweetsController@index',[$tweetTakeUser->UserId]) }}" target='_blank' rel='noreferrer' class='card-link'>ツイート</a></h6>
                            <h6 class='card-subtitle text-muted' style="margin-top:0.5em;"><a href="{{ action('ShowController@index',[$tweetTakeUser->UserId]) }}" target='_blank' rel='noreferrer' class='card-link'>観賞モード</a></h6>
                        </div>
                        @else
                        <div>
                            <h6 class='card-subtitle text-muted' style="margin-top:0.5em;">削除中...</h6>
                        </div>
                        @endif
                    </div> 
                </div> 
            </div>
            @endforeach

        <div>

        <!-- スペーサ -->
        <div style="margin-bottom:300px"></div>

    </div>
@endsection

@section('script')

<script type="text/javascript">

</script>

@endsection
