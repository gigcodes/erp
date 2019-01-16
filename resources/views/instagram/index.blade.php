@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Instagram Feeds</h1>
            <p>This is the list of your Instagram Posts.</p>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-image">
                            <img class="img-responsive" src="http://lorempixel.com/555/300/sports">

                        </div><!-- card image -->

                        <div class="card-content">
                            <span class="card-title">
                                <span>
                                    <i class="fa fa-heart text-danger"></i> 22
                                </span>
                                <span class="ml-4">
                                    <i class="fa fa-comment text-info"></i> 2
                                </span>
                            </span>
                            <button type="button" class="btn btn-custom pull-right show-details" data-pid="1" aria-label="Left Align">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                        </div><!-- card content -->
                        <div class="card-action">
                            <p>
                                We will be showing a list of Instagram posts from our instagram account <span class="text-info">@sololuxury</span> and see the list of comments, and reply to those comments!
                            </p>
                            <p>
                                The comments will be displayed after we click <i class="fa fa-ellipsis-v"></i> button above.
                            </p>
                        </div><!-- card actions -->
                        <div class="card-reveal reveal-1">
                            <span class="card-title">Comments & Replies</span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@rishabh.aryal</span> nice one! what is this thing made of?
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@username</span> Good!
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@prabesh.bhetwal</span> I will surely purchase this!
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@rishabh.aryal</span> nice one! what is this thing made of?
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@username</span> Good!
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@prabesh.bhetwal</span> I will surely purchase this!
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@rishabh.aryal</span> nice one! what is this thing made of?
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@username</span> Good!
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@prabesh.bhetwal</span> I will surely purchase this!
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@rishabh.aryal</span> nice one! what is this thing made of?
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@username</span> Good!
                            </p>
                            <p>
                                <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                <span class="text-info">@prabesh.bhetwal</span> I will surely purchase this!
                            </p>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Leave a reply...">
                            </div>
                        </div><!-- card reveal -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
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