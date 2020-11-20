@extends('layout_nohead')

@section('title')
    <title>スライドショー</title>
@endsection

@section('content')

    <div style="text-align: center">
        <img id="image" src="" style="max-width:100vw;max-height:100vh;" />
    </div>

@endsection

@section('script')
    <!-- Business JavaScript -->
    <script>
        // 画面表示
        $(document).ready(function(){
            $('body').css('background-color','#232323');
            changeImage();
        });

        $(function(){
            setInterval(changeImage,10*1000);
        });

        function changeImage(){
            $.get("{{ route('api.slideshow.image') }}",function(response){
                $("#image").stop().animate({opacity:'0'},500);
                $("#image").attr("src",response.url)
                $("#image").stop().animate({opacity:'1'},500);
            });
        }



    </script>
@endsection
