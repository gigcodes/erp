@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scrap Stats</h2>
        </div>
        <div class="col-md-12 mb-4">
            <form action="{{ action('ScrapStatisticsController@index') }}" method="get">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control" name="supplier" id="supplier">
                            <option value="">All</option>
                            @foreach($suppliers as $s)
                                <option value="{{$s->supplier}}">{{$s->supplier}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="type" id="type">
                            <option value="">All</option>
                            @foreach($type as $t)
                                <option value="{{$t->type}}">{{$t->type}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" value="{{$request->get('date') ?? date('Y-m-d')}}">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-default">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Supplier</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Url</th>
                </tr>
                @foreach($statistics as $static)
                    <tr>
                        <th>{{ $static->supplier }}</th>
                        <th>{{ $static->type }}</th>
                        <th>{{ $static->description }}</th>
                        <th>{{ $static->url }}</th>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

@section('styles')
@endsection

@section('scripts')
@endsection
