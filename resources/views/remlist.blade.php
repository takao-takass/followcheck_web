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
        <link rel="stylesheet" href="{{ asset('/css/remlist.css') }}">
    </head>

    <body>
    
        <!-- ヘッダ  -->
        <div class="navbar navbar-dark shadow-sm" style="background-color: #4a84cc;">
            <div class="container d-flex justify-content-between">
                <a href="#" class="navbar-brand d-flex">
                    <h2><strong>followcheck</strong></h2>
                </a>
                <a href="" class="navbar-brand" style="text-align:right;"> 
                    <strong>ログアウト</strong>
                </a>
            </div>
        </div>

        <!-- ページ切り替えボタン類 -->
        <div class="container">
            <div class="row" style="margin-top:2em;">
                <div class="col-md-12">
                    <h2 class="text-center">リムられリスト</h2>
                </div>
            </div>
            <div class="row justify-content-end">
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

            <!-- リムられリスト -->
            <table class="table remlist-table">
                <tbody>
                    {{-- 
                    @foreach($remuserList as $remuser)
                    <tr>
                        <td>
                            <span>
                                <img src="{{ asset('/img/usericon.jpg') }}" class="usericon">
                            </span>
                        </td>
                        <td>
                            <div>
                                <span>{{$item['userDispName']}}</span>
                                <span><a href="https://twitter.com/{{$item['userName']}}" target="_blank" rel="noopener noreferrer">@{{$item['userName']}}</a></span>
                            </div>
                            <div>
                                <span>フォロー：{{$item['follow']}}</span>
                                <span>フォロワー：{{$item['follower']}}</span>
                                <span>{{$item['dayold']}}日前</span>
                            </div>
                        </td>
                        <td>
                            @if($item['followed'] == '1')
                            <span>✔</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    --}}
                    <tr>
                        <td>
                            <span>
                                <img src="{{ asset('/img/usericon.jpg') }}" class="usericon">
                            </span>
                        </td>
                        <td>
                            <div>
                                <span>たかお🔞</span>
                                <span><a href="https://twitter.com/takaosan_takas" target="_blank" rel="noopener noreferrer">@takaosan_takas</a></span>
                            </div>
                            <div>
                                <span>フォロー：1120</span>
                                <span>フォロワー：1027</span>
                                <span>1日前</span>
                            </div>
                        </td>
                        <td>
                            <span>✔</span>
                        </td>
                        </a>
                    </tr>
                    <tr>
                        <td>
                            <span>
                                <img src="{{ asset('/img/usericon.jpg') }}" class="usericon">
                            </span>
                        </td>
                        <td>
                            <div>
                                <span>たかお１号</span>
                                <span><a href="https://twitter.com/takaosan_takas1" target="_blank" rel="noopener noreferrer">@takaosan_takas1</a></span>
                            </div>
                            <div>
                                <span>フォロー：1234567890</span>
                                <span>フォロワー：1234567890</span>
                                <span>4日前</span>
                            </div>
                        </td>
                        <td>
                            <span>✔</span>
                        </td>
                    </tr>
                </tbody>
              </table>
        </div>

        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

    </body>
</html>