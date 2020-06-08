@extends('layout')

@section('content')
    <div class="container">

        <div class="row" style="margin-top:2em;">
    
            <!-- ページタイトル -->
            <div class="col-md-12">
                <h2 class="text-center">
                </h2>
            </div>

            <!-- ユーザ一覧 -->
            @foreach($accounts as $account)
                <div class="col-lg-3 col-md-4 col-6" style="margin-bottom:1em">
                    <div class="card shadow-sm" style="width:100%;height:100%;">
                        <img class="card-img-top" src="{{$account['thumbnail_url']}}" style="height: 100px;object-fit: cover;*/">
                        <div class="card-body">
                        <h5 class="card-title" style="font-weight: bold;">{{$account['name']}}</h6>
                            <h6 class="card-subtitle text-muted" style='word-wrap:break-all;'>＠{{$account['disp_name']}}</h6>
                            {{--<p class="card-text">ツイート：{{$account['tweet_ct']}}<br/>--}}
                            {{--メディア：{{$account['media_ct']}}</p>--}}
                            <h6 class="card-subtitle text-muted" style="margin-top:0.5em;"><a href="{{ action('TweetsController@index',[$account['user_id']]) }}" class="card-link">ツイートを見る</a></h6>
                            <h6 class="card-subtitle text-muted" style="margin-top:0.5em;"><a href="{{ action('ShowController@index',[$account['user_id']]) }}" class="card-link">観賞モード</a></h6>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        <!-- スペーサ -->
        <div style="margin-bottom:300px"></div>

    </div>
@endsection

@section('script')
@endsection
