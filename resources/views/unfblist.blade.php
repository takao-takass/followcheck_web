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
                    <strong>ログアウト</strong>
                </a>
            </div>
        </div>


        <div class="container">

            <!-- ページタイトル -->
            <div class="row" style="margin-top:2em;">
                <div class="col-md-12">
                    <h2 class="text-center">フォロバ待ちリスト</h2>
                </div>
            </div>

            <!-- ページ切り替えボタン類 -->
            <div class="row">
                <div class="col-md-6">
                    <a href="https://twitter.com/home" target="_blank" rel="noopener noreferrer"><img src="{{ asset('/img/twittericon.png') }}" class="twitterlinkicon"></a>
                    <a href="https://twitter.com/takaosan_takas" target="_blank" rel="noopener noreferrer"><img src="{{ asset('/img/usericon.jpg') }}" class="twitterlinkicon"></a>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        <nav aria-label="Page navigation example" style="margin-top:1em;">
                            <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                                </a>
                            </li>
                            <li class="page-item disabled"><a class="page-link" href="#">ページ切り替え</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                                </a>
                            </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- リムられリスト -->
            <table class="table unfblist-table">
                <tbody>

                    @foreach($users as $unfbuser)
                    <tr id="row_{{$unfbuser['user_id']}}">
                        <td>
                            <span>
                                <img src="{{$unfbuser['thumbnail_url']}}" class="usericon">
                            </span>
                        </td>
                        <td>
                            <div>
                                <span>{{$unfbuser['name']}}</span>
                                <span><a href="https://twitter.com/{{$unfbuser['disp_name']}}" target="_blank" rel="noopener noreferrer">{{'@'.$unfbuser['disp_name']}}</a></span>
                            </div>
                            <div>
                                <span>フォロー：{{$unfbuser['follow_count']}}</span>
                                <span>フォロワー：{{$unfbuser['follower_count']}}　（フォロバ率 {{$unfbuser['fbrate']}}%）</span>
                                <span>{{$unfbuser['dayold']}}日経過</span>
                            </div>
                        </td>
                        <td>
                            <span><button class="btn btn-secondary rounded-pill hide-button" value="{{$unfbuser['user_id']}}" onclick="" style="">非表示</button></span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
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
                    url:'./hide',
                    type:'POST',
                    data:{
                        user_id : '459277410',
                        unfollowbacked_user_id : this.value
                    }
                }).done( (data) => {
                    //$('#row_'+val).hide();
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