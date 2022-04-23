@extends('layout')

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('title')
    <title>{{$user->name}} - ユーザー情報 / followcheck</title>
@endsection

@section('content')
<div class="container">

    <!-- アカウントリスト -->
    <div class="row">
        <div class="col-12">
            <div style="margin-bottom:1em">
                <div class="card shadow-sm" style="width:100%;height:100%;">
                    <div class="card-body">
                        <img class='usericon' style='margin:1em' src="{{$user->thumbnail_url ?: asset('./img/usericon1.jpg')}}">
                        <h6 class="card-title" style="font-weight: bold;">{{$user->name}}</h6>
                        <h6 class="card-title">＠{{$user->disp_name}}</h6>
                        <h6 class="card-title mt-2">ユーザID：{{$user->user_id}}</h6>
                        <h6 class="card-title">{{$user->description}}</h6>
                        <h6 class="card-title">フォロー：{{$user->follow_count}}　フォロワー：{{$user->follower_count}}</h6>
                        <h6 class="card-title">- - - - - - - - - - -</h6>
                        <h6 class="card-title">ツイート数：{{$tweet_count}}</h6>
                        <h6 class="card-title">メディア数：{{$media_ready_count}} READY / {{$media_checked_count}} CHECKED / {{$media_count}} TOTAL</h6>
                        <h6 class="card-title">- - - - - - - - - - -</h6>
                        <h6 class="card-title mt-2">{{$tweet_taking ? '〇' : '－'}} ツイート取得対象</h6>
                        <h6 class="card-title">- - - - - - - - - - -</h6>
                        <a class='mt-2' href="https://twitter.com/{{$user->disp_name}}"><input type="button" class="btn btn-primary form-control" value="Twitter" /></a>
                        <a class='mt-2' href="{{ route('gallery.user', ['user_id' => $user->user_id]) }}"><input type="button" class="btn btn-primary form-control" value="ギャラリー" /></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ツイート一覧 -->
    <div class="row contents">
        @foreach ( $thumb_urls as $thumb_url )
            <div class='col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-1'>
                <img alt="" class='mr-3 thumb-radius thumb-back async-load' style='width:100%;' src="{{asset('./img/media_default.jpg')}}" data-async-load='{{$thumb_url}}'>
            </div>
        @endforeach
    </div>

    <!-- スペーサ -->
    <div style="margin-bottom:300px"></div>

</div>
@endsection

@section('script')
<script type="text/javascript">
    $(function(){
        asyncLoad();
    });
</script>
@endsection
