@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h1>Grid For: #{{ $hashtag }} ({{ $media_count }} Posts)</h1>
        </div>
        <div class="col-md-12 text-center">
            @if ($maxId !== '' || $maxId = 'END')
                <a class="btn btn-info mb-4" href="{{ action('HashtagController@showGrid', $hashtag) }}">FIRST PAGE</a>
                <a class="btn btn-info mb-4" href="{{ action('HashtagController@showGrid', $hashtag) }}?maxId={{($maxId && $maxId != 'END') ? $maxId : ''}}">NEXT</a>
            @endif
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{ Session::get('message') }}
                </div>
            @endif
        </div>
        <div class="col-md-12">
            <table class="table-striped table">
                <tr>
                    <th>S.N</th>
                    <th>User</th>
                    <th>Post URL</th>
                    <th>Image</th>
                    <th style="width: 400px;">Caption</th>
                    <th>Number of Likes</th>
                    <th>Number Of Comments</th>
                    <th>Location</th>
                    <th>Created At</th>
                    <th>Comments</th>
                </tr>
                @foreach($medias as $key=>$post)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td><a href="https://instagram.com/{{$post['username']}}">{{$post['username']}}</a></td>
                        <td><a href="https://instagram.com/p/{{$post['code']}}">Visit Post</a></td>
                        <td>
                            @if ($post['media_type'] === 1)
                                <a href="{{$post['media']}}"><img src="{{ $post['media'] }}" style="width: 200px;"></a>
                            @elseif ($post['media_type'] === 2)
                                <video controls src="{{ $post['media'] }}" style="width: 200px"></video>
                            @elseif ($post['media_type'] === 8)
                                @foreach($post['media'] as $m)
                                    @if ($m['media_type'] === 1)
                                        <a href="{{$m['url']}}"><img src="{{ $m['url'] }}" style="width: 100px;"></a>
                                    @elseif($m['media_type'] === 2)
                                        <video controls src="{{ $m['url'] }}" style="width: 200px"></video>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td style="word-wrap: break-word">
                            <div style="width:390px;">
                                {{ $post['caption'] }}
                            </div>
                        </td>
                        <td>{{ $post['like_count'] }}</td>
                        <td>{{ $post['comment_count'] }}</td>
                        <td>{!! $post['location']['name'] . '<br>' . $post['location']['city']  !!}</td>
                        <td>{{ $post['created_at'] }}</td>
                        <td style="width: 600px;">
                            @if ($post['comments'])
                                <table class="table">
                                    <tr>
                                        <th>Username</th>
                                        <th style="width: 350px">Comment</th>
                                        <th>Commented On</th>
                                        <th>Action</th>
                                    </tr>
                                    <tbody id="comments-{{$post['media_id']}}">
                                        @foreach($post['comments'] as $keyy=>$comment)
                                            <tr>
                                                <td><a href="https://instagram.com/{{$comment['user']['username']}}">{{$comment['user']['username']}}</a></td>
                                                <td style="word-wrap: break-word;word-break: break-all">{{$comment['text']}}</td>
                                                <td>{{\Carbon\Carbon::createFromTimestamp($comment['created_at'])->diffForHumans()}}</td>
                                                <td>
                                                    <form action="{{ action('ReviewController@createFromInstagramHashtag') }}" method="post">@csrf<input type="hidden" name="code" value="{{$post['code']}}"> <input type="hidden" name="date" value="{{ \Carbon\Carbon::createFromTimestamp($comment['created_at'])->toDateTimeString() }}"> <input type="hidden" name="post" value="{{ $post['caption'] }}"><input type="hidden" name="comment" value="{{ $comment['text'] }}"><input type="hidden" name="poster" value="{{ $post['username'] }}"><input type="hidden" name="commenter" value="{{ $comment['user']['username'] }}"><input type="hidden" name="media_id" value="{{ $post['media_id'] }}"><button class="btn btn-sm btn-success"><i class="fa fa-check"></i></button></form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tr>
                                        <td colspan="5" class="text-center" class="load-more-{{$post['media_id']}}">
                                            <button data-post-code="{{ $post['code'] }}" class="btn btn-sm load-comment" id="load-more-{{$post['media_id']}}" data-media-id="{{$post['media_id']}}"> Load More...</button>
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <strong>N/A</strong>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="col-md-12 text-center">
            @if ($maxId !== '' || $maxId = 'END')
                <a class="btn btn-info mb-4" href="{{ action('HashtagController@showGrid', $hashtag) }}">FIRST PAGE</a>
                <a class="btn btn-info mb-4" href="{{ action('HashtagController@showGrid', $hashtag) }}?maxId={{($maxId && $maxId != 'END') ? $maxId : ''}}">NEXT</a>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
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

        $(document).on('click', '.load-comment', function() {
            let mediaId = $(this).attr('data-media-id');
            let postCode = $(this).attr('data-post-code');
            $.ajax({
                url: '{{ action('HashtagController@loadComments', '') }}'+'/'+mediaId,
                success: function(response) {
                    let comments = response.comments;
                    if (response.has_more_comments==false) {
                        $('.load-more-'+mediaId).hide();
                    }
                    $('#comments-'+mediaId).html('');
                    comments.forEach(function(comment) {
                        let form = '<form action="{{ action('ReviewController@createFromInstagramHashtag') }}" method="post">@csrf<input type="hidden" name="date" value="'+comment.created_at_time+'"><input type="hidden" name="code" value="'+postCode+'"><input type="hidden" name="post" value="'+response.caption.text+'"><input type="hidden" name="comment" value="'+comment.text+'"><input type="hidden" name="poster" value="'+response.caption.user.username+'"><input type="hidden" name="commenter" value="'+comment.user.username+'"><input type="hidden" name="media_id" value="'+mediaId+'"><button class="btn btn-sm btn-success"><i class="fa fa-check"></i></button></form>';
                        let data = '<tr><td>'+comment.user.username+'</td><td>'+comment.text+'</td><td>'+comment.created_at+'</td><td>'+form+'</td></tr>';
                        $('#comments-'+mediaId).append(data);
                    });
                }
            });
        });

        function loadComments(postId) {

        }
    </script>
@endsection