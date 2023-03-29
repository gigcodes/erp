@extends('layouts.app')

@section('favicon', 'supplierlist.png')

@section('title', 'Supplier Priority List')

@section('styles')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('large_content')

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Supplier Priority List (<span id="lbl_supplier_count">0</span>)</h2>
            <button type="button" class="btn btn-secondary ml-3" id="btn_manage_supplier_priority">Manage Supplier Priority</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table supplier-priority-table"
                    id="supplier-priority-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="manageSupplierPriorityModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Manage Supplier Priority</h4>
                </div>
                <div class="modal-body">
                    <form class="form-inline" id="frm_manage_supplier_priority">
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="priority" class="sr-only">Enter Priority</label>
                            <input type="text" class="form-control" id="priority" name="priority" placeholder="Enter Priority">
                        </div>
                        <button type="submit" class="btn btn-primary mb-2" id="btn_add_priority">Add</button>
                    </form>
                    <hr>
                    
                    <table class="table table-bordered table-striped" id="priority_list_table">
                        <thead>
                            <tr>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        $(function() {
            var table = $('.supplier-priority-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('supplier-priority.list') }}",
                columns: [{
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'supplier_category_name',
                        name: 'supplier_category.name'
                    },
                    {
                        data: 'priority',
                        name: 'priority'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function() {
                    var api = this.api();
                    var recordsTotal = api.page.info().recordsTotal;
                    var records_displayed = api.page.info().recordsDisplay;
                    $("#lbl_supplier_count").text(recordsTotal);
                },
            });
        });
    </script>

    <script>
        $("#btn_manage_supplier_priority").on('click',function() {
            $("#manageSupplierPriorityModal").modal('show');
            getSupplierPriorityList();
        });
        $("#frm_manage_supplier_priority").on('submit',function(e) {
            e.preventDefault();
            
            var formData = new FormData($("#frm_manage_supplier_priority")[0]);
            formData.append('_token',"{{ csrf_token() }}");
            
            $.ajax({
                url: '{{ route("supplier.add_new_priority")}}',
                type: 'POST',
                data :formData,
                dataType: 'json',
                contentType:false,
                processData:false,
                beforeSend: function () {
                },
                success: function(result){
                    if(result.code == 200) {
                        toastr["success"](result.message);
                        $("#frm_manage_supplier_priority")[0].reset();
                        getSupplierPriorityList();
                    }
                    
                    if(result.code == 500) {
                        toastr["error"](result.message);
                    }
                },
                error: function (){
                
                }
            });
        });
        
        function getSupplierPriorityList() {
            
            $.ajax({
                url: '{{ route("supplier.get_supplier_priority_list")}}',
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function(result){
                    if(result.code == 200) {
                        $("#priority_list_table tbody").html(result.html);
                    } 
                    
                    if(result.code == 500) {
                        $("#priority_list_table tbody").html('<tr><td>'+result.message+'</td></tr>');
                    }
                },
                error: function (){
                
                }
            });
        }
    </script>
@endsection
