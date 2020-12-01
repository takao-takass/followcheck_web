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
                        <img class='usericon' style='margin:1em' src="{{$account['thumbnail_url']}}">
                        <h6 class="card-title" style="font-weight: bold;">{{$account['name']}}</h6>
                        <h6 class="card-title">＠{{$account['disp_name']}}</h6>
                        <h6 class="card-title">{{$account['description']}}</h6>
                        <h6 class="card-title">フォロー：{{$account['follow_count']}}</h6>
                        <h6 class="card-title">フォロワー：{{$account['follower_count']}}</h6>

                        <h6 class="card-title mt-2">ユーザID：{{$account['user_id']}}</h6>
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
