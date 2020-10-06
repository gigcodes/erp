@extends('layouts.app')
@section('favicon' , 'task.png')

{{--@section('title', $title)--}}

@section('content')

    <div>
        <h2>Google Ads</h2>
        <p>Number of results found: {{$campaignCount}}</p>
    </div>

@endsection

