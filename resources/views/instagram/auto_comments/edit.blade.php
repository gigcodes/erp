@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Edit Comment</h2>
        </div>

        <div class="col-md-12">
            <form action="{{ action('InstagramAutoCommentsController@update', $comment->id) }}" method="post">
                @csrf
                @method('PUT')
                <divr class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input value="{{$comment->comment}}" type="text" name="text" id="text" placeholder="Quick reply.." class="form-control" >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input value="{{$comment->source}}" type="text" name="source" id="source" placeholder="Source.." class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button class="btn btn-info">Update It!</button>
                        </div>
                    </div>
                </divr>
            </form>
        </div>

    </div>

@endsection

@section('scripts')

@endsection
