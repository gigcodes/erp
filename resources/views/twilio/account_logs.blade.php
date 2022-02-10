@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Twilio Account Logs</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <table class="table table-responsive table-bordered">
                <tr>
                    <th style="width: 10%">Email</th>
                    <th style="width: 15%">SID</th>
                    <th style="width: 50%">Log</th>
                    <th style="width: 10%">Created At</th>
                </tr>
                @foreach ($accountLogs as $val)
                    <tr>
                        <td>{{ $val->email }}</td>
                        <td>{{ $val->sid }}</td>
                        <td>{{ $val->log }}</td>
                        <td>{{ $val->created_at }}</td>
                    </tr>
                @endforeach
            </table>
            {{ $accountLogs->links() }}
        </div>
    </div>
@endsection
