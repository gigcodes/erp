@extends('layouts.app')

@section('styles')

    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Maililng list</h2>
                </div>
            </div>
        </div>

    </div>
    <div class="table-responsive mt-3">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <th style="">Name</th>
                <th style="">Email</th>
                <th style="">Actions</th>
            </thead>
            <tbody>
            @foreach($customers as $value)
                <tr>
                    <td>{{$value["name"]}}</td>
                    <td>{{$value["email"]}}</td>
                    <td>@if(!in_array($value['email'], $contacts))<a href="{{route('mailingList.add_to_list', [$id, $value['email']])}}"><i class="fa fa-plus"></i></a>@else<a href="{{route('mailingList.delete', $value['email'])}}"><i class="fa fa-minus"></i></a>@endif</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{$customers->links()}}
    </div>
@endsection


@section('scripts')

@endsection