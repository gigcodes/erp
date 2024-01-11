@extends('layouts.app')
@section('favicon' , 'user-management.png')

@section('title', 'Bank statement')

@section('styles')

<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
</style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Bank statements >> Import Mapping</h2>
        </div>
        <div class="col-lg-12 margin-tb ml-2 mb-2">
            <a href="{{ route('bank-statement.index') }}" class="btn btn-default">
                {{__('Imported file listing')}}
            </a>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row">
        
        <div class="col-md-12 p-4">    
        <div class="card ml-3">
            <div class="card-body">
                <h2>{{$bankStatement->filename}} (Status: {{$bankStatement->status}})</h2> <br/>    
                <form action="{{ route('bank-statement.import.map.submit', ['id'=>$id]) }}" method="post">
                    @csrf
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" width="30%">{{__('Database Field')}}</th>
                                <th scope="col" width="70%">{{__('Excel Field')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dbFields as $key=>$field)
                            <tr>
                                <td><label for="{{ $key }}" class="form-label">{{ $field }}:</label></td>
                                <td>
                                    <select name="{{ trim($key) }}" class="form-control">
                                        <option value="">-- {{__('Select Excel Field')}} --</option>
                                        @foreach($excelHeaders as $header)
                                            <option value="{{ $header }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                   
                    <button type="submit" class="btn btn-primary mt-2">Import</button>
                </form>
                
            </div>
        </div>        
        </div>    
    </div>
@endsection