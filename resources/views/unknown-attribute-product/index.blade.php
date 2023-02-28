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
            <div class="row pl-4 pr-4">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Filter by attributes</label>
                        <br>
                        <select data-placeholder="Select Attribute"  class="form-control select-multiple2" id="filter_status">
                            <option value="0">Select All</option>
                            @foreach($status_list as $status_id => $status_name)
                                <option value="{{ $status_id }}">{{ $status_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-6 border">
                    <form id="attribute_form">
                        <div class="row p-2">
                            <div class="form-group col-md-4">
                                <label>Attribute assignment:</label>
                                <select data-placeholder="Select Attribute"  class="form-control select-multiple2" id="attribute_id"  name="attribute_id" required>
                                    @foreach($status_list as $status_id => $status_name)
                                        <option value="{{ $status_id }}">{{ $status_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <br>
                                <input type="text" name="attribute_value" class="form-control" placeholder="Attribute Value" required>
                            </div>
                            <div class="form-group col-md-2">
                                <br>
                                <button id="btn_assign" type="submit" class="btn btn-default ml-2 small-field-btn btn-secondary ">Assign</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table-striped table-bordered table" id="unknown-attribute-products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Supplier</th>
                            <th>Attribute</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="product-attribute-detail" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="update_attribute_form">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Attribute</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product_id" name="product_id"/>
                        <input type="hidden" id="update_attribute_id" name="attribute_id"/>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Product ID: </label> <span id="lbl_product_id"></span>
                            </div>
                            <div class="col-md-12">
                                <label>Name: </label> <span id="lbl_product_name"></span>
                            </div>
                            <div class="col-md-12">
                                <label>SKU: </label> <span id="lbl_sku"></span>
                            </div>
                            <div class="col-md-12">
                                <label>Supplier: </label> <span id="lbl_supplier"></span>
                            </div>
                            <div class="col-md-12">
                                <label>Attribute: </label> <span id="lbl_attribute_name"></span>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                <label>Attribute Value: </label>
                                    <input type="text" name="attribute_value" class="form-control" placeholder="Enter Attribute Value" required="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="update_attribute" type="submit" class="btn btn-success" >Update</button>
                        <button type="reset" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
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
                {data: 'supplier', name: 'supplier'},
                {data: 'attribute_name', name: 'attribute_name',orderable: false, searchable: false},
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
    
    $("#attribute_form").on('submit',function(e){
        e.preventDefault();

        var $this = $(this);
        var formData = new FormData($this[0]);
            formData.append('_token',"{{ csrf_token() }}");
        
        $.ajax({
            type: 'POST',
            url: "{{ route('unknown.attribute.products.attribute-assignment') }}",
            beforeSend: function () {
                $("#btn_assign").attr('disabled',true);
            },
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function (response) {
            $("#btn_assign").removeAttr('disabled');
            if (response.code == 200) {
                toastr['success'](response.message, 'success');
                $(this)[0].reset();
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function (response) {
            $("#btn_assign").removeAttr('disabled');
            console.log("Sorry, something went wrong");
        });
        
       
    });
    
    
    $(document).on('click','.get-product-attribute-detail',function(e){

        var product_id = $(this).data('id');
        var $this = $(this);
        var formData = new FormData();
            formData.append('product_id',product_id);
            formData.append('_token',"{{ csrf_token() }}");
        
        $.ajax({
            type: 'POST',
            url: "{{ route('unknown.attribute.products.get_product_attribute_detail') }}",
            beforeSend: function () {
                $this.attr('disabled',true);
            },
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function (response) {
            $this.removeAttr('disabled');
            if (response.code == 200) {
                $("#product-attribute-detail").modal('show');
                $("#update_attribute_id").val(response.results.status_id);
                $("#product_id").val(response.results.id);
                $("#lbl_product_id").text(response.results.id);
                $("#lbl_product_name").text(response.results.name);
                $("#lbl_sku").text(response.results.sku);
                $("#lbl_supplier").text(response.results.supplier);
                $("#lbl_attribute_name").text(response.results.attribute_name);
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function (response) {
            $this.removeAttr('disabled');
            console.log("Sorry, something went wrong");
        });
       
    });
    
    $("#update_attribute_form").on('submit',function(e){
        e.preventDefault();

        var $this = $(this);
        var formData = new FormData($this[0]);
            formData.append('_token',"{{ csrf_token() }}");
        
        $.ajax({
            type: 'POST',
            url: "{{ route('unknown.attribute.products.attribute-assignment') }}",
            beforeSend: function () {
                $("#update_attribute").attr('disabled',true);
            },
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function (response) {
            $("#update_attribute").removeAttr('disabled');
            if (response.code == 200) {
                toastr['success'](response.message, 'success');
                $this[0].reset();
                $("#product-attribute-detail").modal('hide');
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function (response) {
            $("#update_attribute").removeAttr('disabled');
            console.log("Sorry, something went wrong");
        });
        
       
    });
    
  });
  
</script>
@endsection