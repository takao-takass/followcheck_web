@extends('layout')

@section('style')
@endsection

@section('content')
<div class="container">

    <!-- ページタイトル -->
    <div class="row" style="margin-top:2em;">
        <div class="col-md-12">
            <h2 class="text-center">
                <strong>Twitterアカウント</strong>
            </h2>
        </div>
    </div>

    <!-- アカウント追加フォーム -->
    <form action="{{ route('twitter.account.add') }}" method="post">
    @csrf
        <div class="row text-right" style="margin-top:2em;margin-bottom:2em;">
            <div class="input-group mb-2 col-md-12">
                <div class="input-group-prepend">
                    <span class="input-group-text">＠</span>
                </div>
                <input type="text" class="form-control" name="disp_name" >
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary" id="add-button">　　追加　　</button>
                </div>
            </div>
            @if($error!=null)
                <label style="color: red;">{{$error}}</label>
            @endif
        </div>
    </form>

    <div class="row" style="margin-top:2em;">
            <div class="col">
                <form action="{{ route('twitter.account.index') }}" method="get">
                    @csrf
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end">
                                @if($data->Page > 0)
                                    <li class="page-item"><a class="page-link" href="#" onclick="page(0);">&lt;&lt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{$data->Page - 1}});">&lt;</a></li>
                                @else
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;&lt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&lt;</a></li>
                                @endif
                                @for ($i = ($data->Page-5 < 0 ? 0 : $data->Page-5); $i <= ($data->Page+5 > $data->MaxPage ? $data->MaxPage : $data->Page+5); $i++)
                                    @if($i == $data->Page)
                                        <li class="page-item active"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="#" onclick="page({{ $i }});">{{ $i+1 }}</a></li>
                                    @endif
                                @endfor
                                @if($data->Page == $data->MaxPage)
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">&gt;&gt;</a></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $data->Page + 1 }});">&gt;</a></li>
                                    <li class="page-item"><a class="page-link" href="#" onclick="page({{ $data->MaxPage }});">&gt;&gt;</a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="page" id="pageNumber" value="{{$data->Page}}">
                    <button type="submit" id="searchSubmit" style="display:none;">
                </form>
            </div>
        </div>

    <!-- アカウントリスト -->
    <div class="row">
        @foreach($data->Accounts as $account)
            <div class="col-12 mt-2">
                <div class="d-flex p-3" style="background-color:#F6F6F6;">
                    <div class="d-inline-flex" style="min-width: 75px; width: 75px;">
                        <a href="{{ route('user.index', ['user_id' => $account->User['user_id']]) }}">
                            <img class='img-radius img-fluid async-load' src="{{asset('./img/usericon1.jpg')}}" data-async-load="{{$account->User['thumbnail_url']}}">
                        </a>
                    </div>
                    <div class="d-inline-flex d-flex flex-column ml-4">
                        <div>
                            <label><strong>{{$account->User['name']}}</strong></label>
                            <label class="ml-4" style="color: gray;"><strong>@ {{$account->User['disp_name']}}</strong></label>
                            <label class="ml-3" style="color: red;">{{$account->User['protected']==0 ? '' : '【鍵垢】'}}</label>
                            <label class="ml-3" style="color: red;">{{$account->User['icecream']==0 ? '' : '【凍結】'}}</label>
                            <label class="ml-3" style="color: red;">{{$account->User['not_found']==0 ? '' : '【垢消し済】'}}</label>
                        </div>
                        <div>
                            <label>{{$account->User['description']}}</label>
                        </div>
                        <div>
                            @foreach($account->MediaUrls as $MediaUrl)
                                <img alt="" class='mr-3 thumb-radius thumb-back' style='width:100%;' src='{{$MediaUrl}}'>
                            @endforeach
                        </div>
                        <div style="color: gray;">
                            <label>{{$account->User['follow_count']}}フォロー</label>
                            <label class="ml-4">{{$account->User['follower_count']}}フォロワー</label>
                        </div>
                        <div style="color: navy;">
                            <label>{{$account->TakingTweet ? '〇' : '－'}}</label><label class="ml-1">ツイート取得</label>
                            <label class="ml-3">{{$account->TakedFollow ? '〇' : '－'}}</label><label class="ml-1">フォロイー取得</label>
                            <label class="ml-3">{{$account->TakedFavorite ? '〇' : '－'}}</label><label class="ml-1">いいね取得</label>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- スペーサ -->
    <div style="margin-bottom:300px"></div>

</div>
@endsection

@section('script')
<script type="text/javascript">

    $(function(){
        asyncLoad();
    });

</script>
@endsection
