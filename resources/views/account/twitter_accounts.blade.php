@extends('layout')

@section('style')
@endsection

@section('content')
<div class="container">

    <!-- ページタイトル -->
    <div class="row" style="margin-top:2em;">
        <div class="col-md-12">
            <h2 class="text-center">
                <strong>Twitterアカウント</strong>
            </h2>
        </div>
    </div>

    <!-- アカウント追加フォーム -->
    <form action="{{ route('twitter.account.add') }}" method="post">
    @csrf
        <div class="row text-right" style="margin-top:2em;margin-bottom:2em;">
            <div class="input-group mb-2 col-md-12">
                <div class="input-group-prepend">
                    <span class="input-group-text">＠</span>
                </div>
                <input type="text" class="form-control" name="disp_name" >
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary" id="add-button">　　追加　　</button>
                </div>
            </div>
        </div>
    </form>

    <!-- アカウントリスト -->
    <div class="row">
        @foreach($users as $user)
            <div class="col-12" style="margin-bottom:1em">
                <div class="card shadow-sm" style="width:100%;height:100%;">
                    <a href="{{ action('UserController@index',[''])}}/{{$user['user_id']}}"><img class="card-img-top" src="{{$user['thumbnail_url']}}" style="height: 100px;object-fit: cover;*/"></a>
                    <div class="card-body">
                        <h5 class="card-title" style="font-weight: bold;">{{$user['name']}}</h6>
                        <div class="text-right">
                            <button class="btn btn-secondary rounded-pill del-button" value="{{$user['user_id']}}" style="height:35px;font-size: 10pt;">削除</button>
                        </div>
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
<script type="text/javascript">


</script>
@endsection
