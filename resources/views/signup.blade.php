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
    </head>

    <body>
    
        <!-- ヘッダ  -->
        <div class="navbar navbar-dark shadow-sm" style="background-color: #436be3;">
            <div class="container d-flex justify-content-between">
                <a href="#" class="navbar-brand d-flex">
                    <img class="titlelogo" src="{{ asset('/img/title2.png') }}">
                </a>
            </div>
        </div>

        <!-- 登録フォーム -->
        <div class="container">
            <div class="row" style="margin-top:2em;">
                <div class="col-md-12">
                    <h2 class="text-center">サインアップ</h2>
                </div>
            </div>
            <div class="row" style="margin-top:2em;">
                <div class="col-md-3">
                    <label>招待コード</label>
                </div>
                <div class="col-md-9">
                    <input type="email" class="form-control rounded-pill" id="invite" aria-describedby="" placeholder="">
                </div>
            </div>
            <div class="row" style="margin-top:2em;">
                <div class="col-md-3">
                    <label>Twitter ID(@付き)</label>
                </div>
                <div class="col-md-9">
                    <input type="email" class="form-control rounded-pill" id="twitterid" aria-describedby="" placeholder="">
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
                    <button class="btn btn-secondary rounded-pill" onclick="" style="width:100%;margin-top:2em;">戻る</button>
                </div>
            </div>
        </div>

        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

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
                        passwordcheck : $('#passwordcheck').val()
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

    </body>
</html>