@extends('layout')

@section('style')
@endsection

@section('content')
<div class="container">

    <!-- ページタイトル -->
    <div class="row" style="margin-top:2em;">
        <div class="col-md-12">
            <h2 class="text-center">
            <strong>コンフィグ</strong>
            </h2>
        </div>
    </div>

    <form action="{{ route('config.save') }}" method="post">
        @csrf

        <div class="row">
            <h4>ツイート表示</h4>
        </div>
        <hr/>

        <div class="row">
            <div class="col">
                <div class="custom-control custom-switch">
                    @if($user_config[0]->value == 1)
                        <input type="checkbox" class="custom-control-input" id="retweet" checked name="retweet">
                    @else
                        <input type="checkbox" class="custom-control-input" id="retweet" name="retweet">
                    @endif
                    <label class="custom-control-label" for="retweet">リツイートを表示しない</label>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col">
                <div class="custom-control custom-switch">
                    @if($user_config[1]->value == 1)
                        <input type="checkbox" class="custom-control-input" id="reply" checked name="reply">
                    @else
                        <input type="checkbox" class="custom-control-input" id="reply" name="reply">
                    @endif
                    <label class="custom-control-label" for="reply">リプライを表示しない</label>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col">
                <div class="custom-control custom-switch">
                    @if($user_config[2]->value == 1)
                        <input type="checkbox" class="custom-control-input" id="check" checked name="check">
                    @else
                        <input type="checkbox" class="custom-control-input" id="check" name="check">
                    @endif
                    <label class="custom-control-label" for="check">既読ツイートを記録する</label>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col">
                <div class="custom-control custom-switch">
                    @if($user_config[3]->value == 1)
                        <input type="checkbox" class="custom-control-input" id="filter_checked" checked name="filter_checked">
                    @else
                        <input type="checkbox" class="custom-control-input" id="filter_checked" name="filter_checked">
                    @endif
                    <label class="custom-control-label" for="filter_checked">既読ツイートは表示しない</label>
                </div>
            </div>
        </div>

        <button class="btn btn-primary mt-4" type="submit">保存</button>

    </form>


</div>
@endsection
