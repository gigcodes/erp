@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Posts</h2>
        </div>
        <div class="col-md-12">
            <div class="row">
                <form action="{{ action('InstagramPostsController@store') }}" method="post">
                    <div class="col-md-3">
                        <label for="caption">Caption</label>
                        <textarea name="caption" id="caption" rows="5" class="form-control" name="caption"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12">
            <table></table>
        </div>
    </div>
@endsection