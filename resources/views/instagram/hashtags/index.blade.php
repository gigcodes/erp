@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Instagram HashTags</h1>
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{ Session::get('message') }}
                </div>
            @endif
            <form method="post" action="{{ action('HashtagController@store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Hashtag (without # symbol)</label>
                    <input type="text" name="name" id="name" placeholder="sololuxuryindia (without hash)" class="form-control">
                </div>
                <div class="form-group">
                    <button class="btn btn-success">Add Hashtag</button>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <table class="table-striped table">
                <tr>
                    <th>S.N</th>
                    <th>Tag Name</th>
                    <th>Number Of Posts</th>
                    <th>Actions</th>
                </tr>
                @foreach($hashtags as $key=>$hashtag)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $hashtag->hashtag }}</td>
                        <td>{{ $hashtag->posts()->count() }}</td>
                        <td>
                            <a class="btn btn-info" href="{{ action('HashtagController@showGrid', $hashtag->id) }}">Show In Grid</a>
                            <form method="post" action="{{ action('HashtagController@destroy', $hashtag->id) }}">
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