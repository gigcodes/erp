
@extends('layouts.app')

@section('large_content')

    <link href="{{ asset('css/treeview.css') }}" rel="stylesheet">
    <br>
    <table class="table table-striped table-sm">
        <tr>
            <th>S.N</th>
            <th>Category Name</th>
            <th>Alternatives</th>
        </tr>
        @foreach($maps as $key=>$map)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{ $map->title }}</td>
                <td>
                    @foreach($map->alternatives as $alt)
                        <span class="label label-info">{{$alt}}</span>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </table>
    <div class="panel panel-primary">
        <div class="panel-heading">Manage Category</div>
        <div class="panel-body">
            <form method="post" action="{{ action('CategoryMapController@store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        @foreach($items as $item)
                            @foreach($item as $value)
                                <input type="checkbox" id="cats{{$value}}" name="cats[]" value="{{$value}}">
                                <label for="cats{{$value}}">{{$value}}</label>
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
            </form>
        </div>
    </div>
@endsection
