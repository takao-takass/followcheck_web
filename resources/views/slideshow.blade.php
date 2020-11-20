@extends('layout_nohead')

@section('title')
    <title>スライドショー</title>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/css/slideshow.css') }}">
@endsection

@section('content')

    <div class="slideshow">
        <img id="image" src=""/>
    </div>

    <!--
    <div style="text-align: center">
        <img id="image" src="" style="max-width:100vw;max-height:100vh;" />
    </div>
    -->
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
            setInterval(changeImage,20*1000);
        });

        function changeImage(){
            $.get("{{ route('api.slideshow.image') }}",function(response){

                var image = new Image();
                image.src = response.url;

                $("#image").animate({opacity:'0'},500,function(){

                    $("#image").attr("src",response.url)
                    if(image.width < image.height){
                        $("#image").addClass('height-zoom');
                        $("#image").removeClass('width-zoom');
                    }else{
                        $("#image").addClass('width-zoom');
                        $("#image").removeClass('height-zoom');
                    }
                    $("#image").animate({opacity:'1'},500);
                    
                });

            });
        }
    </script>
@endsection
