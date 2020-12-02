@extends('layout')

@section('style')
@endsection

@section('content')
<div class="container">

    <!-- アカウントリスト -->
    <div class="row">
            <div style="margin-bottom:1em">
                <div class="card shadow-sm" style="width:100%;height:100%;">
                    <div class="card-body">
                        <img class='usericon' style='margin:1em' src="{{$user->thumbnail_url ?: asset('./img/usericon1.jpg')}}">
                        <h6 class="card-title" style="font-weight: bold;">{{$user->name}}</h6>
                        <h6 class="card-title">＠{{$user->disp_name}}</h6>
                        <h6 class="card-title">{{$user->description}}</h6>
                        <h6 class="card-title">フォロー：{{$user->follow_count}}</h6>
                        <h6 class="card-title">フォロワー：{{$user->follower_count}}</h6>
                        <h6 class="card-title">- - - - - - - - - - -</h6>
                        <h6 class="card-title">ツイート数：{{$tweet_count}}</h6>
                        <h6 class="card-title">メディア数：{{$media_ready_count}} READY / {{$media_count}} TOTAL</h6>
                        <h6 class="card-title">- - - - - - - - - - -</h6>
                        <h6 class="card-title mt-2">ユーザID：{{$user->user_id}}</h6>
                    </div>
                </div>
            </div>
    </div>

    <!-- スペーサ -->
    <div style="margin-bottom:300px"></div>

</div>
@endsection

@section('script')
<script type="text/javascript">

</script>
@endsection
