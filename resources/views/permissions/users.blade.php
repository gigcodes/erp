@extends('layouts.app')


@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<style type="text/css">
	.dtHorizontalExampleWrapper {
max-width: 600px;
margin: 0 auto;
}
#dtHorizontalExample th, td {
white-space: nowrap;
}

table.dataTable thead .sorting:after,
table.dataTable thead .sorting:before,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_asc:before,
table.dataTable thead .sorting_asc_disabled:after,
table.dataTable thead .sorting_asc_disabled:before,
table.dataTable thead .sorting_desc:after,
table.dataTable thead .sorting_desc:before,
table.dataTable thead .sorting_desc_disabled:after,
table.dataTable thead .sorting_desc_disabled:before {
bottom: .5em;
}
</style>

@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> Grand Permission To Users</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('permissions.index') }}"> Back</a>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
               <div class="table-wrapper-scroll-y my-custom-scrollbar">

  <!-- <table class="table table-bordered table-striped mb-0">
    <thead>
      <tr>
        <th scope="col">Sr.</th>
        <th scope="col">Users</th>
       @foreach($permissions as $permission)
        <th scope="col">Otto</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
    	@foreach($users as $user)
      <tr>
        <th scope="row">{{ ++$i }}</th>
        <td>{{ $user->name }}</td>
        @foreach($permissions as $permission)
        <td>Otto</td>
        @endforeach
        
      </tr>
      	@endforeach
    </tbody>
  </table> -->

  <table id="dtHorizontalExample" class="table table-striped table-bordered table-sm" cellspacing="0"
  width="100%">
  <thead>
    <tr>
      <th>Sr</th>
      <th>Users</th>
      @foreach($permissions as $permission)
        <th>{{ $permission->name }}</th>
        @endforeach
      >
    </tr>
  </thead>
  <tbody>
  	@foreach($users as $user)
    <tr>
      <td>{{++$i }}</td>
      <td>{{ $user->name }}n</td>
      @foreach($permissions as $permission)
        <td><inpit type="checbox" />{{ $permission->name }}</td>
        @endforeach
   </tr>
   @endforeach
  </tbody>
</table>
{!! $users->render() !!}
</div>
               
            </div>
        </div>
      
    </div>
@endsection

@section('scripts')

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('#dtHorizontalExample').DataTable({
			"scrollX": true
		});
		$('.dataTables_length').addClass('bs-select');
	});
</script>
@endsection
