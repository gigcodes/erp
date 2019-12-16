@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Posts</h2>
        </div>
        <div class="col-md-12 mt-4">
            <table class="table-striped table table-bordered">
                <tr>
                    <th>S.N</th>
                    <th>Username</th>
                    <th>Caption</th>
                    <th>Image</th>
                    <th>Posted On</th>
                </tr>

                @foreach($posts as $key=>$post)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{ $post->username }}</td>
                        <td>{{ $post->caption }}</td>
                        <td>
                            @if($post->hasMedia('instagram-post') )
                                <img style="width: 100px;" src="{{ $post->getMedia('instagram-post')->first()->getUrl() }}" alt="">
                            @else
                                <a href="{{ $post->location }}" target="_blank">visit post</a>
                            @endif
                        </td>
                        <td>{{ date('d-M-Y', strtotime($post->posted_at)) }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    @if(Session::has('message'))

    @endif
@endsection