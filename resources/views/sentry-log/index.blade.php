@extends('layouts.app')
@section('content')

<div class="row">
  <div class="col-lg-12 margin-tb">
    <h2 class="page-heading">SENTRY logs</h2>
  </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif

<div class="table-responsive">
  <table class="table table-bordered" id="sentry_log_table">
    <thead>
      <tr>
        <th style="width: 5%">#</th>
        <th style="width: 5%">Id</th>
        <th style="width: 40%">Title</th>
        <th style="width: 10%">Issue Type</th>
        <th style="width: 10%">Issue Category</th>
        <th style="width: 10%">Is Unhandled</th>
        <th style="width: 10%">First Seen</th>
        <th style="width: 10%">Last Seen</th>
      </tr>
    </thead>
    @foreach ($sentryLogsData as $key => $row)
    <tr>
      <td>{{ $key+1 }}</td>
      <td>{{ $row['id'] }}</td>
      <td>{{ $row['title'] }}</td>
      <td>{{ $row['issue_type'] }}</td>
      <td>{{ $row['issue_category'] }}</td>
      <td>{{ ($row['is_unhandled']) ? "true":"false" }}</td>
      <td>{{ date("d-m-y H:i:s", strtotime($row['first_seen'])) }}</td>
      <td>{{ date("d-m-y H:i:s", strtotime($row['last_seen'])) }}</td>
    </tr>
    @endforeach
  </table>
</div>
@endsection
