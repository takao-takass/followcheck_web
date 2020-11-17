@extends('layout_nohead')

@section('title')
    <title>メディア</title>
@endsection

@section('content')

    <a href="{{ $Media->path }}"><img src="{{ $Media->path }}" style="max-width:100vw;max-height:100vh;" /></a>

    <!-- メインコンテンツ -->
    <div class="container">

        <div class="row mt-3">
            <div class="col">
                <img class="usericon" src="{{ $Media->user_thumbnail_path }}" />
                <label style="color:white;">{{ $Media->tweet_body }}</label>
            </div>
        </div>
        <div class="row mt-3 mb-3">
            <div class="col">
                <a href="{{ $Media->twitter_url }}"><input type="button" class="btn btn-secondary form-control" value="Twitterで見る" /></a>
            </div>
        </div>
        <form action="{{ route('media.keep', ['tweet_id' => $Media->tweet_id] ) }}" method="post">
            @csrf
            <div class="row mt-5 mb-3">
                <div class="col">
                    @if($Media->keep_count > 0)
                        <button type="button" disabled class="btn btn-outline-warning form-control">キープ済み</button>
                        <button type="submit" class="btn btn-warning form-control" style="display: none;">キープする</button>
                    @else
                        <button type="submit" class="btn btn-warning form-control">キープする</button>
                    @endif
                </div>
            </div>
        </form>
        <form action="{{ route('media.delete', ['tweet_id' => $Media->tweet_id] ) }}" method="post">
            @csrf
            <div class="row mt-5 mb-3">
                <div class="col">
                   <button type="submit" class="btn btn-danger form-control">削除する</button>
                </div>
            </div>
        </form>

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
