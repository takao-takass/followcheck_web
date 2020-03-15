<!doctype html>
<html lang="ja">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        <!-- App CSS -->
        <link rel="stylesheet" href="{{ asset('/css/common.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/unfblist.css') }}">
    </head>

    <body>
    
        <!-- ヘッダ  -->
        <div class="navbar navbar-dark shadow-sm" style="background-color: #436be3;">
            <div class="container d-flex justify-content-between">
                <a href="#" class="navbar-brand d-flex">
                    <img class="titlelogo" src="{{ asset('/img/title2.png') }}">
                </a>
                <a href="" class="navbar-brand" style="text-align:right;"> 
                    <!--<strong>ログアウト</strong> -->
                </a>
            </div>
        </div>

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
                    <span><input type="email" class="form-control rounded-pill" id="account-name" aria-describedby="" placeholder="アットマーク（＠）は不要"></span>
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
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

        <!-- Business JavaScript -->
        <script type="text/javascript">

            // アカウント追加ボタン
            $('#add-button').on('click',function(){
                var val = this.value;
                $.ajax({
                    url:'{{ action('DownloadAccountsController@add') }}',
                    type:'POST',
                    data:{
                        service_user_id : $('#service-user-id').val(),
                        account_name : $('#account-name').val()
                    }
                }).done( (data) => {
                    location.reload();
                }).fail( (data) => {
                    /*
                    resobj = JSON.parse(data.responseText);
                        alert(resobj.message);
                        $('.input_error').removeClass('input_error');
                        $.each(resobj.params, function(index, value) {
                            $('#'+value).addClass('input_error');
                        });
                        */
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
                    /*
                    resobj = JSON.parse(data.responseText);
                        alert(resobj.message);
                        $('.input_error').removeClass('input_error');
                        $.each(resobj.params, function(index, value) {
                            $('#'+value).addClass('input_error');
                        });
                        */
                });
            });
        </script>

    </body>
</html>