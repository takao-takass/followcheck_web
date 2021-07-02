@extends('layout')

@section('title')
    <title>フリーのアカウント</title>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('content')
    <div class="container">

        <div style="display: none;">
            <form action="{{ route(\App\Constants\WebRoute::GROUP_MEMBER_JOIN, ['id' => $view_model->group_id]) }}" method="post" id="joinForm">
                @csrf
                <input type="hidden" id="user_id" name="userId">
                <button type="submit" id="joinSubmit"></button>
            </form>
        </div>

        <!-- ユーザ一覧 -->
        <div class="row">


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
