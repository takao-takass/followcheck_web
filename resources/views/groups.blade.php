@extends('layout')

@section('style')
@endsection

@section('content')
<div class="container">

    <!-- スペーサ -->
    <div style="margin-bottom:50px"></div>

    <!-- グループリスト -->
    <div class="row">
        <div class="col-lg-3 col-md-4 col-6" style="margin-bottom:1em">
            <div class="card shadow-sm" style="width:100%;height:100%;">
                <img class="card-img-top" src="{{asset('./img/usericon1.jpg')}}" style="height: 100px;object-fit: cover;*/">
                <div class="card-body">
                    <h5 class="card-title" style="font-weight: bold;">全てのユーザ</h6>
                    <h6 class='card-subtitle text-muted' style='margin-top:0.5em;'><a href="{{ action('TweetsController@gindex',['ALL']) }}" target='_blank' rel='noreferrer' class='card-link'>ツイート</a></h6>
                    <h6 class='card-subtitle text-muted' style='margin-top:0.5em;'><a href="{{ action('ShowController@gindex',['ALL']) }}" target='_blank' rel='noreferrer' class='card-link'>観賞モード</a></h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6" style="margin-bottom:1em">
            <div class="card shadow-sm" style="width:100%;height:100%;">
                <img class="card-img-top" src="{{asset('./img/usericon1.jpg')}}" style="height: 100px;object-fit: cover;*/">
                <div class="card-body">
                    <h5 class="card-title" style="font-weight: bold;">まもなく削除される</h6>
                    <h6 class='card-subtitle text-muted' style='margin-top:0.5em;'><a href="{{ action('TweetsController@gindex',['OLD']) }}" target='_blank' rel='noreferrer' class='card-link'>ツイート</a></h6>
                    <h6 class='card-subtitle text-muted' style='margin-top:0.5em;'><a href="{{ action('ShowController@gindex',['OLD']) }}" target='_blank' rel='noreferrer' class='card-link'>観賞モード</a></h6>
                </div>
            </div>
        </div>
        @foreach($groups as $group)
            <div class="col-lg-3 col-md-4 col-6" style="margin-bottom:1em" id="row_{{$group['group_id']}}">
                <div class="card shadow-sm" style="width:100%;height:100%;">
                    <img class="card-img-top" src="{{$group['thumbnail_url']}}" style="height: 100px;object-fit: cover;*/">
                    <div class="card-body">
                        <h5 class="card-title" style="font-weight: bold;">{{$group['name']}}</h6>
                        <div class="text-right">
                            <button class="btn btn-secondary rounded-pill del-button" value="{{$group['group_id']}}" style="height:35px;font-size: 10pt;">削除</button>
                        </div>
                        <h6 class='card-subtitle text-muted' style='margin-top:0.5em;'><a href='' target='_blank' rel='noreferrer' class='card-link'>ツイート</a></h6>
                        <h6 class='card-subtitle text-muted' style='margin-top:0.5em;'><a href='' target='_blank' rel='noreferrer' class='card-link'>観賞モード</a></h6>
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

</script>
@endsection
