@extends('layout')

@section('style')
        <link rel="stylesheet" href="{{ asset('/css/remlist.css') }}">
@endsection

@section('content')

        <div class="container">

            <!-- ページタイトル -->
            <div class="row" style="margin-top:2em;">
                <div class="col-md-12">
                    <h2 class="text-center">
                        @foreach($accounts as $account)
                            @if($account['selected'] == 1)
                            <strong><img src="{{$account['thumbnail_url']}}" class="twitterlinkicon">{{$account['name']}} のリムられリスト</strong>
                            <input id="selected-user-id" type="hidden" value="{{$account['user_id']}}">
                            @endif
                        @endforeach
                    </h2>
                </div>
            </div>

            <!-- 他画面遷移ボタン -->
            <div class="row text-center" style="margin-bottom:2em;">
                <div class="col-md-12">
                    <button class="btn btn-primary rounded-pill" onclick="location.href='{{ action('UnfblistController@init') }}'" style="width:15em;height:3em;margin-top:1em;">フォロバ待ちリスト</button>
                    <button class="btn btn-primary rounded-pill" onclick="location.href='{{ action('FleolistController@init') }}'" style="width:15em;height:3em;margin-top:1em;">相互フォローリスト</button>
                </div>
            </div>

            <div class="row text-center">
                <!-- アカウントアイコン -->
                <div class="col-md-12">
                    <a href="https://twitter.com/home" target="_blank" rel="noopener noreferrer"><img src="{{ asset('/img/twittericon.png') }}" class="twitterlinkicon"></a>
                    @foreach($accounts as $account)
                    <a href="{{ action('RemlistController@index',[$account['user_id'],0]) }}"><img src="{{$account['thumbnail_url']}}" class="twitterlinkicon"></a>
                    @endforeach
                    <a href="{{ action('AccountsController@index') }}"><img src="{{ asset('/img/setting.png') }}" class="twitterlinkicon"></a>
                </div>

                <!-- ページ切り替えボタン -->
                <div class="col-md-12">
                    <div class="float-right">
                        <nav aria-label="Page navigation example" style="margin-top:1em;">
                            <ul class="pagination">
                                @if($prev_page >= 0)
                                <li class="page-item">
                                    <a class="page-link" href="{{ action('RemlistController@index',[$uesr_id,$prev_page]) }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                @endif
                                <li class="page-item disabled"><a class="page-link" href="#">{{$record}}件</a></li>
                                @if($next_page < $max_page)
                                <li class="page-item">
                                    <a class="page-link" href="{{ action('RemlistController@index',[$uesr_id,$next_page]) }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- リムられリスト -->
            <div class="row">
                @foreach($users as $remuser)
                    <div class="media col-md-6 shadow-sm" style="margin-bottom:1em;padding:1em;">
                        <a href="{{ action('UserController@index',[''])}}/{{$remuser['user_id']}}"><img src="{{$remuser['thumbnail_url']}}" class="usericon mr-3"></a>
                        <div class="media-body">
                            <h5 class="mt-0 name"><a href="https://twitter.com/{{$remuser['disp_name']}}" target="_blank" rel="noopener noreferrer">{{$remuser['name']}}</a></h5>
                            <p>{{$remuser['description']}}</p>
                            <div>フォロー：{{$remuser['follow_count']}}　フォロワー：{{$remuser['follower_count']}}</div>
                            <div>
                                <span>{{$remuser['dayold']}}日前　</span>
                                @if($remuser['followed'] == '1')
                                <span>【フォロー中】</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- スペーサ -->
            <div style="margin-bottom:150px;"></div>
        </div>
@endsection


@section('script')

@endsection

