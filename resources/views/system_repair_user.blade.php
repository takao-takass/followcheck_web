@extends('layout')

@section('style')
@endsection

@section('content')
<div class="container">

    <!-- ページタイトル -->
    <div class="row" style="margin-top:2em;">
        <div class="col-md-12">
            <h2 class="text-center">
            <strong>ユーザの修復</strong>
            </h2>
        </div>
    </div>

    <form action="{{ route('system.repair_user.repair') }}" method="post">
        @csrf

        <div class="row">
            <div class="col">
                <input type="text" class="form-control" name="user_id">
            </div>
        </div>

        <button class="btn btn-primary mt-4" type="submit">保存</button>

    </form>

    <div class="row">
        @foreach ( $repairable_users as $repairable_user )
            <div class="col">
                <label>{{$repairable_user}}</label>
            </div>
        @endforeach
    </div>

</div>
@endsection
