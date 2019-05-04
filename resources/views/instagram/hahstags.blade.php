@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Instagram HashTags</h1>
            <p>This is the list of the hashtags with their posts</p>
        </div>
        <div class="col-md-12">
            <div class="row">
                @foreach($posts as $key=>$post)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-image">
                                <img style="width: 100%;" src="{!! $post->image_url !!}">
                            </div><!-- card image -->

                            <div class="card-content">
                                <span class="card-title">
                                    #{{ $post->hashtag }}
                                </span>
                                <button type="button" class="btn btn-custom pull-right show-details s-d-{{$key}}" data-pid="{{ $key }}" data-media-id="{{ $post->id }}" aria-label="Left Align">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                            </div><!-- card content -->
                            <div class="card-action">
                                <span class="text-muted" title="{{ $post['created_time'] ?? 'N/A' }}">
                                  <strong>
                                     {{ \Carbon\Carbon::createFromTimestamp(strtotime($post->created_at))->diffForHumans() }}
                                  </strong>
                                </span>
                                <p>
                                    {!! $post->description ? preg_replace('/(?:^|\s)#(\w+)/', ' <a class="text-info" href="https://www.instagram.com/explore/tags/$1">#$1</a>', $post->description) : '' !!}
                                </p>
                            </div><!-- card actions -->
                            <div class="card-reveal reveal-{{ $key }}">
                                <span class="card-title">Comments (<span class="count-for-{{$key}}">{{ count($post->comments) }}</span>)</span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <div data-pid="{{ $key }}" data-post-id="{{ $post->id }}" class="comments-content">
                                    @if (count($post->comments))
                                        @foreach($post->comments as $comment)
                                            <p>
                                                <strong>{{ $comment[0] }}</strong>
                                                <span>{{ $comment[1] }}</span>
                                            </p>
                                        @endforeach
                                    @else
                                        <p><strong>There are no comments loaded at this moment.</strong></p>
                                    @endif
                                </div>
                            </div><!-- card reveal -->
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>
        var cid = null;
        $(function(){

            $('.show-details').on('click',function() {
                let id = $(this).attr('data-pid');
                $('.reveal-'+id).slideToggle('slow');
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });
        });


    </script>
@endsection