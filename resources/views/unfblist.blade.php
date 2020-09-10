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
                    @foreach($accounts as $account)
                        @if($account['selected'] == 1)
                        <strong><img src="{{$account['thumbnail_url']}}" class="twitterlinkicon">{{$account['name']}} のフォロバ待ちリスト</strong>
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
                <button class="btn btn-primary rounded-pill" onclick="location.href='{{ action('FleolistController@init') }}'" style="width:15em;height:3em;margin-top:1em;">相互フォローリスト</button>
            </div>
        </div>

        <div class="row text-center">
            <!-- アカウントアイコン -->
            <div class="col-md-12">
                <a href="https://twitter.com/home" target="_blank" rel="noopener noreferrer"><img src="{{ asset('/img/twittericon.png') }}" class="twitterlinkicon"></a>
                @foreach($accounts as $account)
                <a href="{{ action('UnfblistController@index',[$account['user_id'],0]) }}"><img src="{{$account['thumbnail_url']}}" class="twitterlinkicon"></a>
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
                                <a class="page-link" href="{{ action('UnfblistController@index',[$uesr_id,$prev_page]) }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                                </a>
                            </li>
                            @endif
                            <li class="page-item disabled"><a class="page-link" href="#">{{$record}}件</a></li>
                            @if($next_page < $max_page)
                            <li class="page-item">
                                <a class="page-link" href="{{ action('UnfblistController@index',[$uesr_id,$next_page]) }}" aria-label="Next">
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

        <!-- フォロバ待ちリスト -->
        <div class="row">
            @foreach($users as $unfbuser)
                <div class="media col-md-6 shadow-sm" style="margin-bottom:1em;padding:1em;">
                    <a href="{{ action('UserController@index',[''])}}/{{$unfbuser['user_id']}}"><img src="{{$unfbuser['thumbnail_url']}}" class="usericon mr-3"></a>
                    <div class="media-body">
                        <h5 class="mt-0 name"><a href="https://twitter.com/{{$unfbuser['disp_name']}}" target="_blank" rel="noopener noreferrer">{{$unfbuser['name']}}</a></h5>
                        <label>{{$remuser['description']}}<label>
                        <label>フォロー：{{$unfbuser['follow_count']}}　フォロワー：{{$unfbuser['follower_count']}}</label>
                        <label>{{$unfbuser['dayold']}}日前　</label>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection

@section('script')
    <script type="text/javascript">      
        $('.hide-button').on('click',function(){
            var val = this.value;
            $.ajax({
                url:'{{ action('UnfblistController@hide') }}',
                type:'POST',
                data:{
                    user_id : $('#selected-user-id').val(),
                    unfollowbacked_user_id : this.value
                }
            }).done( (data) => {
                $('#row_'+val).hide();
            }).fail( (data) => {
            });
        });
    </script>
@endsection
