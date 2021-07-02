@extends('layout')

@section('title')
    <title>グループメンバー</title>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('content')
    <div class="container">

        <div>
            <a href="{{ route(\App\Constants\WebRoute::GROUP_FREE_ACCOUNT_INDEX, [id => $view_model->group_id]) }}">
                ユーザを追加する
            </a>
        </div>

        <div style="display: none;">
            <form action="{{ route(\App\Constants\WebRoute::GROUP_MEMBER_LEAVE, ['id' => $view_model->group_id]) }}" method="post" id="leaveForm">
                @csrf
                <input type="hidden" id="user_id" name="userId">
                <button type="submit" id="leaveSubmit"></button>
            </form>
        </div>

        <!-- ユーザ一覧 -->
        <div class="row">

            @foreach ($view_model->group_members as $member)

                <div class="col-12 mt-2">
                    <div class="d-flex p-3" style="background-color:#F6F6F6;">
                        <div class="d-inline-flex" style="height: 75px; min-height: 75px; min-width:75px; width: 75px;">
                            <img class='img-radius img-fluid async-load' src="{{asset('./img/usericon1.jpg')}}" data-async-load="{{$member->thumbnail_url}}">
                        </div>
                        <div class="d-inline-flex d-flex flex-column ml-4">
                            <div>
                                <label><strong>{{$member->name}}</strong></label>
                                <label class="ml-4" style="color: gray;"><strong>@ {{$member->disp_name}}</strong></label>
                            </div>
                            <div>
                                <label>{{$member->description}}</label>
                            </div>
                        </div>
                        <div>
                            <a href="#" onclick="leave({{ $member->user_id }})">グループから除外</a>
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

    <script>
        $(function(){
            asyncLoad();
        });

        function leave(user_id){
            $('#userId').val(user_id);
            $('#leaveForm').submit();
        }

    </script>

@endsection
