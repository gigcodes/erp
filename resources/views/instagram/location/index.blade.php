@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Targeted Country/Region (<a href="{{ action('TargetLocationController@edit', 1) }}">Show Statistics</a>)</h1>
        </div>
        <div class="col-md-12">

        </div>
        <div class="col-md-12">
            <table class="table-striped table table-sm">
                <tr>
                    <th>S.N</th>
                    <th>Country</th>
                    <th>Region</th>
                    <th>Action</th>
                </tr>
                @foreach($locations as $key=>$location)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{$location->country}}</td>
                        <td>{{$location->region}}</td>
                        <td>
                            <a href="{{ action('TargetLocationController@show',$location->id) }}">
                                Show Users From This Location
                            </a>
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
@endsection