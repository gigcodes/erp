@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Orders List</h2>
        <!--<div class="pull-left">

            <form action="/order/" method="GET">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <input name="term" type="text" class="form-control"
                                   value="{{ isset($term) ? $term : '' }}"
                                   placeholder="Search">
                        </div>
                        <div class="col-md-4">
                            <button hidden type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>-->
    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th style="width: 10%">Lead ID</th>
            <th style="width: 30%">Client</th>
            <th style="width: 50%">Message</th>
            <th class="text-right" style="width: 10%">Action</th>
        </tr>
        @foreach ($callBusyMessages as $key => $callBusyMessage)
        <tr class="">
            <td>{{ $callBusyMessage->lead_id }}</td>
            <td>{{ $callBusyMessage->client_name }}</td>
            <td>{{ $callBusyMessage->message }}</td>
            <td>
                <a class="btn btn-image" href="{{ route('leads.show',$callBusyMessage->lead_id) }}"><img src="/images/view.png" /></a>
            </td>
        </tr>
        @endforeach
    </table>
</div>


{!! $callBusyMessages->links() !!}
@endsection
