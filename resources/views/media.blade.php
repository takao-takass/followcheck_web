@extends('layout_nohead')

@section('style')

@endsection

@section('content')

    <!-- メインコンテンツ -->
    <div class="container">

        <div class="row">
            <div class="col">
                <a href="{{ $Media->path }}"><img src="{{ $Media->path }}"></a>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-3">
                <img src="{{ $Media->user_thumbnail_path }}">
            </div>
            <div class="col-9">
                <label style="color:white;">{{ $Media->tweet_body }}</label>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <a href="{{ $Media->twitter_url }}"><input type="button" class="btn btn-secondary form-control" value="Twitterで見る" /></a>
            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Business JavaScript -->
    <script>
        // 画面表示
        $(document).ready(function(){
            $('body').css('background-color','#232323');
        });
    </script>
@endsection
