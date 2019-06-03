@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center"><a href="{{ action('AutoCommentHistoryController@index') }}">Go BACK</a> | Auto comment posts For: #{{ $hashtag }} ({{ $media_count }} Posts)</h1>
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{ Session::get('message') }}
                </div>
            @endif
        </div>

        <div class="col-md-12 text-center">
            @if ($maxId !== '' || $maxId = 'END')
                <a class="btn btn-info mb-4" href="{{ action('HashtagController@edit', $hashtag) }}">FIRST PAGE</a>
                <a class="btn btn-info mb-4" href="{{ action('HashtagController@edit', $hashtag) }}?maxId={{($maxId && $maxId != 'END') ? $maxId : ''}}">NEXT</a>
            @endif
        </div>

        <form method="post" action="{{ action('AutoReplyHashtagsController@update', $hid) }}">
            @csrf
            @method('PUT')
            <div class="col-md-12">
                <button class="btn btn-info">Attach Posts To Auto-Comment</button>
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
                    </tr>
                    @foreach($medias as $key=>$post)
                        <tr>
                            <td>
                                {{ $key+1 }}
                                @if(\App\AutoCommentHistory::where('post_id', $post['media_id'])->first())
                                    <span class="label label-warning">Used</span>
                                @else
                                    <input type="checkbox" name="posts[]" value="{{$post['media_id']}}">
                                    <input type="hidden" name="caption_{{$post['media_id']}}" value="{{$post['caption']}}">
                                    <input type="hidden" name="code_{{$post['media_id']}}" value="{{$post['code']}}">
                                @endif
                            </td>
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
                            <td>{!! ($post['location']['name'] ?? 'N/A') . '<br>' . ($post['location']['city']  ?? 'N/A') !!}</td>
                            <td>{{ $post['created_at'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="col-md-12">
                <button class="btn btn-info">Attach Posts To Auto-Comment</button>
            </div>
        </form>

        <div class="col-md-12 text-center">
            @if ($maxId !== '' || $maxId = 'END')
                <a class="btn btn-info mb-4" href="{{ action('HashtagController@edit', $hashtag) }}">FIRST PAGE</a>
                <a class="btn btn-info mb-4" href="{{ action('HashtagController@edit', $hashtag) }}?maxId={{($maxId && $maxId != 'END') ? $maxId : ''}}">NEXT</a>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
@endsection

@section('scripts')
@endsection