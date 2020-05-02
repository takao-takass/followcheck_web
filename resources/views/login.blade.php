@extends('layout_nomenu')

@section('content')

        <!-- ログインフォーム -->
        <div class="container">
            <div class="row" style="margin-top:2em;">
                <div class="col-md-12">
                    <h2 class="text-center">ログイン</h2>
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
            <div class="row" style="margin-top:3em;">
                <div class="col-md-6">
                    <button class="btn btn-primary rounded-pill" id="login-button" style="width:100%;margin-top:2em;">ログイン</button>
                </div>
                <div class="col-md-6">
                    <a href="{{action('SignupController@index')}}"><button class="btn btn-secondary rounded-pill" onclick="" style="width:100%;margin-top:2em;">サインアップ</button></a>
                </div>
            </div>
        </div>
@endsection


@section('script')
       <script type="text/javascript">

            // ログインボタン
            $('#login-button').on('click',function(){
                login();
            });

            // ログイン
            function login(){
                $.ajax({
                    url:"{{action('LoginController@auth')}}",
                    type:'POST',
                    data:{
                        email : $('#email').val(),
                        password : $('#password').val()
                    }
                })
                .done( (data) => {
                    window.location.reload();
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
