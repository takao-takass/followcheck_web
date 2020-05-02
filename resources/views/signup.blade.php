@extends('layout_nomenu')

@section('content')
        <!-- 登録フォーム -->
        <div class="container">
            <div class="row" style="margin-top:2em;">
                <div class="col-md-12">
                    <h2 class="text-center">サインアップ</h2>
                </div>
            </div>
            <div class="row" style="margin-top:2em;">
                <div class="col-md-3">
                    <label>招待コード：</label>
                </div>
                <div class="col-md-9">
                    <input type="email" class="form-control rounded-pill" id="invitecode" aria-describedby="" placeholder="">
                </div>
            </div>
            <div class="row" style="margin-top:2em;">
                <div class="col-md-3">
                    <label>メールアドレス：</label>
                </div>
                <div class="col-md-9">
                    <input type="email" class="form-control rounded-pill" id="email" aria-describedby="" placeholder="">
                </div>
            </div>
            <div class="row" style="margin-top:2em;">
                <div class="col-md-3">
                    <label>パスワード：</label>
                </div>
                <div class="col-md-9">
                    <input type="password" class="form-control rounded-pill" id="password" placeholder="">
                </div>
            </div>
            <div class="row" style="margin-top:2em;">
                <div class="col-md-3">
                    <label>パスワード(再入力)：</label>
                </div>
                <div class="col-md-9">
                    <input type="password" class="form-control rounded-pill" id="passwordcheck" placeholder="">
                </div>
            </div>
            <div class="row" style="margin-top:3em;">
                <div class="col-md-6">
                    <button class="btn btn-primary rounded-pill" id="entry-button" style="width:100%;margin-top:2em;">登録</button>
                </div>
                <div class="col-md-6">
                <a href="{{action('LoginController@index')}}"><button class="btn btn-secondary rounded-pill" onclick="" style="width:100%;margin-top:2em;">戻る</button></a>
                </div>
            </div>
        </div>
@endsection


@section('script')

        <script type="text/javascript">

            // 次ページ
            $('#entry-button').on('click',function(){
                entry();
            });

            // ユーザ登録
            function entry(){
                $.ajax({
                    url:"{{ action('SignupController@entry') }}",
                    type:'POST',
                    data:{
                        email : $('#email').val(),
                        password : $('#password').val(),
                        passwordcheck : $('#passwordcheck').val(),
                        invitecode : $('#invitecode').val()
                    }
                })
                .done( (data) => {
                    window.location.href = "{{ action('LoginController@index') }}";
                })
                .fail( (data) => {
                        resobj = JSON.parse(data.responseText);
                        alert(resobj.message);
                        $('.input_error').removeClass('input_error');
                        $.each(resobj.params, function(index, value) {
                            $('#'+value).addClass('input_error');
                        });
                });
            }

        </script>

@endsection


