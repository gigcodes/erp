@extends('layouts.app')

@section('title', 'Page Notes')

@section("styles")
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@section('content')
<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Page Notes</h2>
  </div>
  <div class="col-md-12">
    <div class="table-responsive">
      <table cellspacing="0" role="grid" class="table table-striped table-bordered datatable mdl-data-table dataTable" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>URL</th>
                <th>Note</th>
                <th>User Name</th>
                <th>Created at</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div> 
  </div>
</div> 

@endsection

@section('scripts')
  <script src="//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
  <script src="//cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('pageNotesRecords') }}',
            columns: [
              {data: 'id', name: 'id'},
              {data: 'url', name: 'url'},
              {data: 'note', name: 'note'},
              {data: 'name', name: 'name'},
              {data: 'created_at', name: 'created_at'}
          ]
        });
  });
  </script>
@endsection
