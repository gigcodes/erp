@extends('layouts.app')

@section('title', 'Account History')

@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Account History : {{$account->name}} ({{$history->total()}})</h2>
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-12 border">
                    <div class="clearfix"></div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Sr. no</th>
                                <th>Module Id</th>
                                <th>Module Type</th>
                                <th>Amount</th>
                                <th>Note</th>
                                <th>Date</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($history as $h)
                                <tr>
                                    <td>{{ $h->id }}</td>
                                    <td>{{ $h->model_id }}</td>
                                    <td>{{ $h->model_type }}</td>
                                    <td>{{ $h->amount }}</td>
                                    <td>{{ $h->note }}</td>
                                    <td>{{ $h->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <th colspan="6" class="text-center text-danger">No Account History Found.</th>
                                </tr>
                            @endforelse
                            </tbody>
                            {{ $history->render() }}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! $history->appends(Request::except('page'))->links() !!}
@endsection
