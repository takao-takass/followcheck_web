@extends('layout')

@section('style')
        <link rel="stylesheet" href="{{ asset('/css/remlist.css') }}">
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
    <div class="col-md-3 text-left">
        <label>Twitterアカウントを追加：</label>
    </div>
    <div class="col-md-6 text-center">
        <span><input type="email" class="form-control rounded-pill" id="accountname" aria-describedby="" placeholder="アットマーク（＠）は不要"></span>
    </div>
    <div class="col-md-2 text-center">
        <button class="btn btn-primary rounded-pill" id="add-button" style="width:80%;" onclick="">追加</button>
    </div>
</div>

<!-- アカウントリスト -->
<div class="row">
    <table class="table unfblist-table">
        <tbody>

            @foreach($accounts as $account)
            <tr id="row_{{$account['user_id']}}">
                <td>
                    <span>
                        <img src="{{$account['thumbnail_url']}}" class="usericon">
                    </span>
                </td>
                <td>
                    <div>
                        <span>{{$account['name']}}</span>
                        <span></span>
                    </div>
                    <div>
                        <span><span class="badge badge-secondary">{{$account['status']}}</span></span>
                        <span>{{$account['disp_name']}}</span>
                        <span></span>
                    </div>
                </td>
                <td>
                    <span>
                        @if($account['delbtn_show']=='1')
                        <button class="btn btn-secondary rounded-pill del-button" value="{{$account['user_id']}}" onclick="" style="">削除</button>
                        @endif
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
        
<!-- 他画面遷移ボタン -->
<div class="row text-center" style="margin-top:1em;margin-bottom:1em;">
    <div class="col-md-12">
        <button class="btn btn-primary rounded-pill" onclick="location.href='{{ action('AccountsController@index') }}'" style="width:15em;height:3em;margin-top:1em;">アカウント管理</button>
    </div>
</div>
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

