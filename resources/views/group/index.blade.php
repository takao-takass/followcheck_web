@extends('layout')

@section('title')
    <title>グループ</title>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('content')
    <div class="container">

        <div class="row" style="margin-top:2em;">
            <div class="col-md-12">
                <form action="{{ route('group.add') }}" method="post" >
                    @csrf
                    <div class="input-group mb-2" style="margin-bottom:0.5em;">
                        <div class="input-group-prepend">
                            <span class="input-group-text">名前</span>
                        </div>
                        <input type="text" name="groupName" class="form-control">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-secondary" id="add-button">追加</button>
                        </div>
                    </div>
                </form>
            </div>
            @if($error != null)
                <label style="color: red;">{{$error}}</label>
            @endif
        </div>

        <div style="display: none;">
            <form action="{{ route('group.delete') }}" method="post" id="deleteForm">
                @csrf
                <input type="hidden" id="groupId" name="groupId">
                <button type="submit" id="deleteSubmit"></button>
            </form>
        </div>

        <div class="row">
            @foreach ($view_model->groups as $group)

                <div class="col-12 mt-2">
                    <div class="d-flex p-3" style="background-color:#F6F6F6;">
                        <label>{{ $group['name'] }}</label>
                        <label class="ml-5"><a href="#" onclick="groupDelete({{ $group['id'] }})">削除</a></label>
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

        function groupDelete(id){
            $('#groupId').val(id);
            $('#deleteForm').submit();
        }

    </script>

@endsection
