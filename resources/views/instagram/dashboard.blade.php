@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Dashboard</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-dark mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Automated Reply / DM</h3>
                    <h1 class="card-title">{{ $automatedMessages }}</h1>
                    <div class="text-right">
                        <a class="text-light" href="{{ action('InstagramAutomatedMessagesController@index') }}">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Accounts</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Comments Today</h3>
                    <h1 class="card-title">{{ $commentsToday }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Comments Total</h3>
                    <h1 class="card-title">{{ $commentsTotal }}</h1>
                    <div class="text-right">
                        <a class="text-dark">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">DM Today</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">DM Total</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Posts Today</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Posts Total</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Filtered Comments</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
    <style>
        .card {
            display: inline-block;
        }
    </style>
@endsection

@section('scripts')

@endsection