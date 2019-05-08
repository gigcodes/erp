@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h1>Grid For: {{ $hashtag->hashtag }}</h1>
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
                    <th>Number of Likes</th>
                    <th>Number Of Comments (Post)</th>
                    <th>Number Of Comments (Scraped)</th>
                    <th>Comments</th>
                    <th>Actions</th>
                </tr>
                @foreach($hashtag->posts as $key=>$post)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td><a href="https://instagram.com/{{$post->username}}">{{$post->username}}</a></td>
                        <td><a href="{{$post->post_url}}">Visit Post</a></td>
                        <td><a href="{{$post->image_url}}"><img src="{{ $post->image_url }}" style="width: 100px;"></a></td>
                        <td>{{ $post->likes }}</td>
                        <td>{{ $post->number_comments }}</td>
                        <td>{{ $post->comments()->count() }}</td>
                        <td style="width: 600px;">
                            @if ($post->comments()->count())
                                <table class="table">
                                    <tr>
                                        <th>S.N</th>
                                        <th>Username</th>
                                        <th>Comment</th>
                                        <th>Commented On</th>
                                        <th>Action</th>
                                    </tr>
                                    @foreach($post->comments as $keyy=>$comment)
                                        <tr>
                                            <td>{{ $keyy+1 }}</td>
                                            <td><a href="https://instagram.com/{{$comment->username}}">{{$comment->username}}</a></td>
                                            <td>{{$comment->comment}}</td>
                                            <td>{{$comment->date_commented}}</td>
                                            <td>
                                                <form method="post" action="{{ action('HashtagPostCommentController@destroy', $comment->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                <strong>N/A</strong>
                            @endif
                        </td>
                        <td>
                            <form method="post" action="{{ action('HashtagPostsController@destroy', $post->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
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