@extends('layout')

@section('style')
        <link rel="stylesheet" href="{{ asset('/css/fleolist.css') }}">
@endsection

@section('content')

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
@endsection


@section('script')
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
@endsection
