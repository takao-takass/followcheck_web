@extends('layout')

@section('style')
@endsection

@section('content')
<div class="container">

    <!-- ページタイトル -->
    <div class="row" style="margin-top:2em;">
        <div class="col-md-12">
            <h2 class="text-center">
            <strong>ツイートダウンロード管理</strong>
            <input type="hidden" id="service-user-id" value="{{$serviceUserId}}">
            </h2>
        </div>
    </div>

    <!-- アカウント追加フォーム -->
    <div class="row text-right" style="margin-top:2em;margin-bottom:2em;">
        <div class="input-group mb-2 col-md-12">
            <div class="input-group-prepend">
                <span class="input-group-text">＠</span>
            </div>
            <input type="email" class="form-control" id="accountname" >
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" id="add-button">　　追加　　</button>
            </div>
        </div>
    </div>

    <!-- アカウントリスト -->
    <div class="row">
        @foreach($accounts as $account)
            <div class="col-lg-3 col-md-4 col-6" style="margin-bottom:1em" id="row_{{$account['user_id']}}">
                <div class="card shadow-sm" style="width:100%;height:100%;">
                    <img class="card-img-top" src="{{$account['thumbnail_url']}}" style="height: 100px;object-fit: cover;*/">
                    <div class="card-body">
                        <span class="badge badge-secondary" style="margin-bottom:1em;">{{$account['status']}}</span>
                        <h5 class="card-title" style="font-weight: bold;">{{$account['name']}}</h6>
                        <h6 class="card-subtitle text-muted" style='word-wrap:break-all;'>＠{{$account['disp_name']}}</h6>
                        @if($account['delbtn_show']=='1')
                            <div class="text-right">
                                <button class="btn btn-secondary rounded-pill del-button" value="{{$account['user_id']}}" style="height:35px;font-size: 10pt;">削除</button>
                            </div>
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
            url:'{{ action('DownloadAccountsController@add') }}',
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
            url:'{{ action('DownloadAccountsController@del') }}',
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

