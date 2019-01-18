@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Instagram Feeds</h1>
            <p>This is the list of your Instagram Posts.</p>
        </div>
        <div class="col-md-12">
            <div class="row">
                @if(isset($posts) && !empty($posts))
                    @foreach($posts as $key=>$post)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-image">
                                    <img style="width: 100%;" src="{!! $post['full_picture'] ?? 'http://lorempixel.com/555/300/black' !!}">
                                </div><!-- card image -->

                                <div class="card-content">
                                    <span class="card-title">
                                        <span>
                                            <i class="fa fa-heart text-danger"></i> {{ $post['likes']['summary']['total_count'] }}
                                        </span>
                                        <span class="ml-4 cp">
                                            <i class="fa fa-comment text-info show-details s-d-{{$key}}" data-pid="{{ $key }}" data-media-id="{{ $post['id'] }}"></i> <span class="count-for-{{$key}}">{{ $post['comments']['summary']['total_count'] }}</span>
                                        </span>
                                    </span>
                                    <button type="button" class="btn btn-custom pull-right show-details s-d-{{$key}}" data-pid="{{ $key }}" data-media-id="{{ $post['id'] }}" aria-label="Left Align">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                </div><!-- card content -->
                                <div class="card-action">
                                    <span class="text-muted" title="{{ $post['created_time'] ?? 'N/A' }}">
                                      <strong>
                                         {{ isset($post['created_time']) ? \Carbon\Carbon::createFromTimestamp(strtotime($post['created_time']))->diffForHumans() : 'N/A' }}
                                      </strong>
                                    </span>
                                    <p>
                                        {!! $post['message'] ? preg_replace('/(?:^|\s)#(\w+)/', ' <a class="text-info" href="https://www.instagram.com/explore/tags/$1">#$1</a>', $post['message']) : '' !!}
                                    </p>
                                </div><!-- card actions -->
                                <div class="card-reveal reveal-{{ $key }}">
                                    <span class="card-title">Comments (<span class="count-for-{{$key}}">{{ $post['comments']['summary']['total_count'] }}</span>)</span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <div class="comments-content">
                                        <p><strong>There are no comments loaded at this moment.</strong></p>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control reply" data-pid="{{ $key }}" data-post-id="{{ $post['id'] }}" placeholder="Leave a comment...">
                                    </div>
                                </div><!-- card reveal -->
                            </div>
                        </div>
                    @endforeach

                    <div class="container text-left mt-4">
                        <div class="row">
                            @if(isset($posts) && !empty($posts))
                                <div class="col-md-6 ml-auto mr-auto">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            @if(isset($previous))
                                                <li class="page-item">

                                                    <div class="col-md-4 ">
                                                        <!-- Next -->
                                                        <form method="post" action="{{route('social.get-post.page')}}">
                                                            @csrf
                                                            <input type="hidden" name="previous" value="{{$previous}}">
                                                            <input type="submit" value="Previous" class="btn btn-info">
                                                        </form>
                                                    </div>

                                                </li>
                                            @endif
                                            @if(isset($next))
                                                <li class="page-item">
                                                    <div class="col-md-4 ml-3">
                                                        <form method="post" action="{{route('social.get-post.page')}}">
                                                            @csrf
                                                            <input type="hidden" name="next" value="{{$next}}">
                                                            <input type="submit" value="Next" class="btn btn-info">
                                                        </form>
                                                    </div>

                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            @endif
                            @endif
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

            $('.show-details').on('click',function() {
                var id = $(this).attr('data-pid');
                var post_id = $(this).attr('data-media-id');

                $.ajax({
                    url: "{{ action('InstagramController@getComments') }}",
                    data: {
                        post_id: post_id
                    },
                    success: function(response) {
                        $('.reveal-'+id+' .comments-content').html('');
                        response.forEach(function (comment) {
                            var commentHTML = '<p class="comment text-justify" data-cid="'+comment.id+'">';
                                commentHTML += '<span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>';
                            commentHTML+= '<span class="text-info">@AUTHOR</span>';
                            commentHTML += '<span style="display: block">'+comment.text+'</span></p>';
                            $('.reveal-'+id+' .comments-content').append(commentHTML);
                        })
                    },
                    error: function() {
                        $('.reveal-'+id+' .comments-content').html('<p style="text-align: center;font-weight: bolder">We could not load comments at the moment. Please try again later.</p>');
                    },
                    beforeSend: function () {
                        $('.reveal-'+id).slideToggle('slow');
                        $('.reveal-'+id+' .comments-content').html('<p style="text-align: center"><img style="width:50px" src="/images/loading2.gif">Loading Comments...</p>');
                    }
                });


            });

            $('.reply').keypress(function (event) {
                if (event.keyCode == 13) {
                    var reply = $(this).val();
                    $(this).val('');
                    var id = $(this).attr('data-pid');
                    var self = this;
                    var postId = $(this).attr('data-post-id');
                    $.ajax({
                        url: "{{ action('InstagramController@postComment') }}",
                        type: 'post',
                        dataType: 'json',
                        data: {
                            message: reply,
                            post_id: postId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                var commentHTML = '<p class="comment text-justify" data-cid="'+response.id+'">';
                                commentHTML += '<span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>';
                                commentHTML+= '<span class="text-info">@author</span>';
                                commentHTML += '<span style="display: block">'+response.message+'</span></p>';
                                $('.reveal-'+id+' .comments-content').prepend(commentHTML);
                                $('.count-for-'+id).html(parseInt($('.count-for-'+id).html())+1);
                                $(".s-d-"+id).attr('data-comment-ids', $(".s-d-"+id).attr('data-comment-ids')+','+response.id);

                            }
                        },
                        error: function() {
                            alert("There was an unknown error saving this reply.");
                        },
                        complete: function () {
                            $(self).removeAttr('disabled');
                        },
                        beforeSend: function () {
                            $(self).attr('disabled', 'disabled');
                        }
                    });
                }
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });
        });


    </script>
@endsection