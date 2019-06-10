@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Quick Reply Comments</h2>
        </div>

        <div class="col-md-12">
            <form action="{{ action('InstagramAutoCommentsController@store') }}" method="post">
                @csrf
                <divr class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" name="text" id="text" placeholder="Quick reply.." class="form-control" >
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <input type="text" name="source" id="source" placeholder="Source.." class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="gender" id="gender">
                                <option value="all">All Gender</option>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="country" id="country" class="form-control">
                                <option value="">All</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->region }}">{{$country->region}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-info">Save It!</button>
                        </div>
                    </div>
                </divr>
            </form>
        </div>

        <div class="col-md-12">
            <form action="{{ action('InstagramAutoCommentsController@show', 'delete') }}">
                <table class="table table-striped">
                    <tr>
                        <th>S.N</th>
                        <th>Text</th>
                        <th>Gender</th>
                        <th>Country</th>
                        <th>Source</th>
                        <th>Use Count</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    @foreach($comments as $key=>$reply)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $reply->comment }}</td>
                            <td>{{ $reply->gender }}</td>
                            <td>{{ $reply->country ?? 'All' }}</td>
                            <td>{{ $reply->source ?? 'N/A' }}</td>
                            <td>{{ $reply->use_count ?? 0 }}</td>
                            <td>{{ $reply->created_at->format('Y-m-d') }}</td>
                            <td>
                                <input value="{{$reply->id}}" type="checkbox" name="comments[]" id="comments">
                                <a href="{{ action('InstagramAutoCommentsController@edit', $reply->id) }}" class="btn btn-info">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
