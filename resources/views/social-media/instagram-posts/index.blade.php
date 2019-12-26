@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Posts</h2>
        </div>
        <div class="col-md-12 mt-4">
            {{ $posts->render() }}
            @foreach($posts as $key=>$post)
                <div class="instagram-post" style="display: flex; width: 935px; height: 598px; margin: 0px auto; border: 1px solid #eeeeee;">
                    @if($post->hasMedia('instagram-post') )
                        <div style="display: flex; width: 598px; height: 598px; background: url('{{ $post->getMedia('instagram-post')->first()->getUrl() }}'); background-size: cover;">
                            &nbsp;
                        </div>
                    @else
                        <div style="display: flex; width: 598px; height: 598px; background-color: #eee;">
                            &nbsp;
                        </div>
                    @endif
                    <div style='display: inline-block; width: 336px; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;'>
                        <span style="padding: 20px; float: right;"><a href="{{ $post->location }}" target="_blank"><i class="fa fa-external-link fa-2x"></i></a></span>
                        <p style="padding: 20px;">
                            <a style="font-weight: bold; color: #000;" title="{{ $post->username }}" target="_blank" href="https://instagram.com/{{ $post->username }}/">{{ $post->username }}</a><br/>
                            <span style="color: #999;">{{ date('d-M-Y H:i:s', strtotime($post->posted_at)) }}</span>
                        </p>
                        <hr style="width: 100%;"/>
                        <p style="padding: 20px; font-size: 0.9em;">
                            <a style="font-weight: bold; color: #000;" title="{{ $post->username }}" target="_blank" href="https://instagram.com/{{ $post->username }}/">{{ $post->username }}</a> {{ $post->caption }}<br/>
                            <span style="color: #999;">{{ date('d-M-Y', strtotime($post->posted_at)) }}</span>
                        </p>
                        @php
                            $instagramPostsComments = \App\InstagramPostsComments::where('instagram_post_id', $post->id)->get();
                        @endphp
                        @if ($instagramPostsComments != null)
                            @foreach($instagramPostsComments as $comment)
                                <p style="padding: 0 20px 0 20px; font-size: 0.9em;">
                                    <a style="font-weight: bold; color: #000;" title="{{ $comment->username }}" target="_blank" href="https://instagram.com/{{ $comment->username }}/">{{ $comment->username }}</a> {{ $comment->comment }}<br/>
                                    <span style="color: #999;">{{ date('d-M-Y', strtotime($post->posted_at)) }}</span>
                                </p>
                            @endforeach
                        @endif
                    </div>
                </div>
                <br/>
                <br/>
            @endforeach
            {{ $posts->render() }}
        </div>
    </div>
@endsection

@section('scripts')
    @if(Session::has('message'))

    @endif
@endsection