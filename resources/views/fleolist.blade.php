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
        <link rel="stylesheet" href="{{ asset('/css/fleolist.css') }}">
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
                        @foreach($accounts as $account)
                            @if($account['selected'] == 1)
                            <strong><img src="{{$account['thumbnail_url']}}" class="twitterlinkicon">{{$account['name']}} の相互フォローリスト</strong>
                            <input id="selected-user-id" type="hidden" value="{{$account['user_id']}}">
                            @endif
                        @endforeach
                    </h2>
                </div>
            </div>
        
            <!-- 他画面遷移ボタン -->
            <div class="row text-center" style="margin-bottom:2em;">
                <div class="col-md-12">
                    <button class="btn btn-primary rounded-pill" onclick="location.href='{{ action('RemlistController@init') }}'" style="width:15em;height:3em;margin-top:1em;">リムられリスト</button>
                    <button class="btn btn-primary rounded-pill" onclick="location.href='{{ action('UnfblistController@init') }}'" style="width:15em;height:3em;margin-top:1em;">フォロバ待ちリスト</button>
                </div>
            </div>

            <div class="row text-center">
                <!-- アカウントアイコン -->
                <div class="col-md-12">
                    <a href="https://twitter.com/home" target="_blank" rel="noopener noreferrer"><img src="{{ asset('/img/twittericon.png') }}" class="twitterlinkicon"></a>
                    @foreach($accounts as $account)
                    <a href="{{ action('FleolistController@index',[$account['user_id'],0]) }}"><img src="{{$account['thumbnail_url']}}" class="twitterlinkicon"></a>
                    @endforeach
                    <a href="{{ action('AccountsController@index') }}"><img src="{{ asset('/img/setting.png') }}" class="twitterlinkicon"></a>
                </div>

                <!-- ページ切り替えボタン類 -->
                <div class="col-md-12">
                    <div class="float-right">
                        <nav aria-label="Page navigation example" style="margin-top:1em;">
                            <ul class="pagination">
                                @if($prev_page >= 0)
                                <li class="page-item">
                                    <a class="page-link" href="{{ action('FleolistController@index',[$uesr_id,$prev_page]) }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                @endif
                                <li class="page-item disabled"><a class="page-link" href="#">{{$record}}件</a></li>
                                @if($next_page < $max_page)
                                <li class="page-item">
                                    <a class="page-link" href="{{ action('FleolistController@index',[$uesr_id,$next_page]) }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- リムられリスト -->
            <div class="row">
                <table class="table fleolist-table">
                    <tbody>

                        @foreach($users as $fleouser)
                        <tr id="row_{{$fleouser['user_id']}}">
                            <td>
                                <span>
                                    <img src="{{$fleouser['thumbnail_url']}}" class="usericon">
                                </span>
                            </td>
                            <td>
                                <div>
                                    <span><a href="https://twitter.com/{{$fleouser['disp_name']}}" target="_blank" rel="noopener noreferrer">{{$fleouser['name']}}</a></span>
                                    <span><!--<a href="https://twitter.com/{{$fleouser['disp_name']}}" target="_blank" rel="noopener noreferrer">{{'@'.$fleouser['disp_name']}}</a>--></span>
                                </div>
                                <div>
                                    <span>フォロー：{{$fleouser['follow_count']}}</span>
                                    <span>フォロワー：{{$fleouser['follower_count']}}</span>
                                    <span>{{$fleouser['dayold']}}日前</span>
                                </div>
                            </td>
                            <td>
                                <span><button class="btn btn-secondary rounded-pill hide-button" value="{{$fleouser['user_id']}}" onclick="" style="">×</button></span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

        <!-- Business JavaScript -->
        <script type="text/javascript">
            
            $('.hide-button').on('click',function(){
                var val = this.value;
                $.ajax({
                    url:'{{ action('FleolistController@hide') }}',
                    type:'POST',
                    data:{
                        user_id : $('#selected-user-id').val(),
                        follow_user_id : this.value
                    }
                }).done( (data) => {
                    $('#row_'+val).hide();
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