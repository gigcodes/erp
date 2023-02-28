@extends('layouts.app')
@section('styles')
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">

<style type="text/css">
    .table-responsive {
        overflow-x: auto !important;
        padding: 0 20px 0;
    }
</style>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h2 class="page-heading">
                    <a class="text-dark">Unknown Attribute Products</a>
                </h2>
            </div>
            
            <div class="pull-left">
                <div class="form-group">
                    <label>Status</label>
                    <br>
                    <select data-placeholder="Select Status"  class="form-control select-multiple2" id="filter_status">
                        <option value="0">Select All</option>
                        @foreach($status_list as $status_id => $status_name)
                            <option value="{{ $status_id }}">{{ $status_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table-striped table-bordered table" id="unknown-attribute-products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Action</th>
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


<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
  $(function () {
    $(".select-multiple2").select2();
    getAttributes();
    
    function getAttributes() {
        var status_id =  $("#filter_status").val();
        var table = $('#unknown-attribute-products-table').DataTable({
            destroy:true,
            processing: true,
            serverSide: true,
            ajax: { 
                url:"{{ route('unknown.attribute.products') }}",
                data:{status_id:status_id}
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'sku', name: 'sku'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false
                },
            ]
        });
    }
    
    $("#filter_status").on('change',function(){
        getAttributes();
    });
    
  });
  
</script>
@endsection