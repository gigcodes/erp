@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Instagram Feeds</h1>
            <p>This is the list of your Instagram Posts.</p>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-image">
                            <img class="img-responsive" src="http://lorempixel.com/555/300/sports">

                        </div><!-- card image -->

                        <div class="card-content">
                            <span class="card-title">Material Cards</span>
                            <button type="button" class="btn btn-custom pull-right show-details" data-pid="1" aria-label="Left Align">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                        </div><!-- card content -->
                        <div class="card-action">

                        </div><!-- card actions -->
                        <div class="card-reveal reveal-1">
                            <span class="card-title">Card Title</span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <p>Here is some more information about this product that is only revealed once clicked on.</p>
                        </div><!-- card reveal -->
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-image">
                            <img class="img-responsive" src="http://lorempixel.com/555/300/sports">

                        </div><!-- card image -->

                        <div class="card-content">
                            <span class="card-title">Material Cards</span>
                            <button type="button" class="btn btn-custom pull-right show-details" data-pid="2" aria-label="Left Align">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                        </div><!-- card content -->
                        <div class="card-action">
                            <a href="#" target="new_blank">Link</a>
                            <a href="#" target="new_blank">Link</a>
                            <a href="#" target="new_blank">Link</a>
                            <a href="#" target="new_blank">Link</a>
                            <a href="#" target="new_blank">Link</a>
                        </div><!-- card actions -->
                        <div class="card-reveal reveal-2">
                            <span class="card-title">Card Title</span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <p>Here is some more information about this product that is only revealed once clicked on.</p>
                        </div><!-- card reveal -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card .card-image{
            overflow: hidden;
            -webkit-transform-style: preserve-3d;
            -moz-transform-style: preserve-3d;
            -ms-transform-style: preserve-3d;
            -o-transform-style: preserve-3d;
            transform-style: preserve-3d;
        }

        .card .card-image img{
            -webkit-transition: all 0.4s ease;
            -moz-transition: all 0.4s ease;
            -ms-transition: all 0.4s ease;
            -o-transition: all 0.4s ease;
            transition: all 0.4s ease;
        }

        .card .card-image:hover img{
            -webkit-transform: scale(1.2) rotate(-7deg);
            -moz-transform: scale(1.2) rotate(-7deg);
            -ms-transform: scale(1.2) rotate(-7deg);
            -o-transform: scale(1.2) rotate(-7deg);
            transform: scale(1.2) rotate(-7deg);
        }

        .card{
            font-family: 'Roboto', sans-serif;
            margin-top: 10px;
            position: relative;
            -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
            -moz-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
            box-shadow: 4 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        }

        .card .card-content {
            padding: 10px;
        }

        .card .card-content .card-title, .card-reveal .card-title{
            font-size: 24px;
            font-weight: 200;
        }

        .card .card-action{
            padding: 20px;
            border-top: 1px solid rgba(160, 160, 160, 0.2);
        }
        .card .card-action a{
            font-size: 15px;
            color: #ffab40;
            text-transform:uppercase;
            margin-right: 20px;
            -webkit-transition: color 0.3s ease;
            -moz-transition: color 0.3s ease;
            -o-transition: color 0.3s ease;
            -ms-transition: color 0.3s ease;
            transition: color 0.3s ease;
        }
        .card .card-action a:hover{
            color:#ffd8a6;
            text-decoration:none;
        }

        .card .card-reveal{
            padding: 20px;
            position: absolute;
            background-color: #FFF;
            width: 100%;
            overflow-y: auto;
            /*top: 0;*/
            left:0;
            bottom:0;
            height: 100%;
            z-index: 1;
            display: none;
        }

        .card .card-reveal p{
            color: rgba(0, 0, 0, 0.71);
            margin:20px ;
        }

        .btn-custom{
            background-color: transparent;
            font-size:18px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function(){

            $('.show-details').on('click',function(){
                var id = $(this).attr('data-pid');
                console.log(id);
                $('.reveal-'+id).slideToggle('slow');
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });
        });
    </script>
@endsection