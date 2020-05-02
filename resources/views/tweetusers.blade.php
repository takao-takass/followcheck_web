@extends('layout')

@section('content')
        <div class="container">

            <!-- ページタイトル -->
            <div class="row" style="margin-top:2em;">
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
                            <h6 class="card-title">{{$account['name']}}</h6>
                            <h6 class="card-subtitle text-muted" style='word-wrap:break-all;'>＠{{$account['disp_name']}}</h6>
                            {{--<p class="card-text">ツイート：{{$account['tweet_ct']}}<br/>--}}
                            {{--メディア：{{$account['media_ct']}}</p>--}}
                            <h6 class="card-subtitle text-muted" style="margin-top:0.5em;"><a href="{{ action('TweetsController@index',[$account['user_id']]) }}" class="card-link">Tweets</a></h6>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
@endsection

@section('script')
@endsection
