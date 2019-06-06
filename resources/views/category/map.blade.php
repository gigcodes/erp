
@extends('layouts.app')

@section('large_content')

    <link href="{{ asset('css/treeview.css') }}" rel="stylesheet">
    <br>
    <div class="panel panel-primary">
        <div class="panel-heading">Manage Category</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    @foreach($items as $item)
                        @foreach($item as $value)
                            <input type="checkbox" id="gender_{{$value}}" name="gender_id[]" value="{{$value}}">
                            <label for="gender_{{$value}}">{{$value}}</label>
                        @endforeach
                            <br>
                    @endforeach
                </div>
                <div class="col-md-10">
                    <div class="form-group">
                        <select name="category" id="category" class="form-control">
                            <option value="0">Select category...</option>
                            @foreach($categories as $category)
                                <option value="{{$category->title}}">{{$category->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-info">Set Defaults</button>
                </div>
            </div>
        </div>
    </div>
@endsection
