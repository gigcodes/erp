@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Twilio Delivery Logs</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <table class="table table-responsive table-bordered">
                <tr>
                    <th style="width: 20%">Message Id</th>
                    <th style="width: 20%">Customer Email</th>
                    <th style="width: 10%">To</th>
                    <th style="width: 10%">From</th>
                    <th style="width: 10%">Delivery Status</th>
                    <th style="width: 15%">Created At</th>
                </tr>
                @foreach ($twilioDeliveryLogs as $val)
                    <tr>
                        <td>{{ $val->message_sid }}</td>
                        <td>{{ $val->customers ? $val->customers->email : '' }}</td>
                        <td>{{ $val->to }}</td>
                        <td>{{ $val->from }}</td>
                        <td>{{ $val->delivery_status }}</td>
                        <td>{{ $val->created_at }}</td>
                    </tr>
                @endforeach
            </table>
            {{ $twilioDeliveryLogs->links() }}
        </div>
    </div>
@endsection
