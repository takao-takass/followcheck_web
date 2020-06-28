@extends('layout')

@section('content')
    <div class="container">

        <div class="row" style="margin-top:2em;">
    
            <!-- ページタイトル -->
            <div class="col-md-12">
                <h2 class="text-center">
                </h2>
            </div>

            <!-- アカウント追加フォーム -->
            <div class="input-group mb-2 col-md-12">
                <div class="input-group-prepend">
                    <span class="input-group-text">＠</span>
                </div>
                <input type="email" class="form-control" id="accountname" >
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" id="add-button">　　追加　　</button>
                </div>
            </div>

            <!-- ユーザ一覧 -->
            @foreach($accounts as $account)
                <div class="col-lg-3 col-md-4 col-6" style="margin-bottom:1em">
                    <div class="card shadow-sm" style="width:100%;height:100%;">
                        <img class="card-img-top" src="{{$account['thumbnail_url']}}" style="height: 100px;object-fit: cover;*/">
                        <div class="card-body">
                        <h5 class="card-title" style="font-weight: bold;">{{$account['name']}}</h6>
                            <h6 class="card-subtitle text-muted" style='word-wrap:break-all;'>＠{{$account['disp_name']}}</h6>
                            @if($account['delbtn_show']=='1')
                                <div class="text-right">
                                <button class="btn btn-secondary rounded-pill del-button" value="{{$account['user_id']}}" style="height:35px;font-size: 10pt;">削除</button>
                                </div>
                            @endif
                            @if($account['tweet_show']=='1')
                                <h6 class="card-subtitle text-muted" style="margin-top:0.5em;"><a href="{{ action('TweetsController@index',[$account['user_id']]) }}" class="card-link">ツイートを見る</a></h6>
                                <h6 class="card-subtitle text-muted" style="margin-top:0.5em;"><a href="{{ action('ShowController@index',[$account['user_id']]) }}" class="card-link">観賞モード</a></h6>
                            @else
                                <span class="badge badge-secondary" style="margin-bottom:1em;">{{$account['status']}}</span>
                            @endif
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

    // アカウント追加ボタン
    $('#add-button').on('click',function(){
        var val = this.value;
        $.ajax({
            url:'{{ action('TweetUsersController@add') }}',
            type:'POST',
            data:{
                service_user_id : $('#service-user-id').val(),
                accountname : $('#accountname').val()
            }
        }).done( (data) => {
            location.reload();
        }).fail( (data) => {
            resobj = JSON.parse(data.responseText);
                alert(resobj.message);
                $('.input_error').removeClass('input_error');
                $.each(resobj.params, function(index, value) {
                    $('#'+value).addClass('input_error');
                });
        });
    });

    // アカウント削除ボタン
    $('.del-button').on('click',function(){
        var val = this.value;
        $.ajax({
            url:'{{ action('TweetUsersController@del') }}',
            type:'POST',
            data:{
                service_user_id : $('#service-user-id').val(),
                user_id : val
            }
        }).done( (data) => {
            location.reload();
        }).fail( (data) => {
            resobj = JSON.parse(data.responseText);
                alert(resobj.message);
                $('.input_error').removeClass('input_error');
                $.each(resobj.params, function(index, value) {
                    $('#'+value).addClass('input_error');
                });
        });
    });
</script>

@endsection
