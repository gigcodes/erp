@extends('layouts.app')

@section('title', 'Product Out Of Stock History')

@section('styles')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('large_content')
    {{-- <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Out Of Stock Products({{ $products_count }})</h2>
        </div>
    </div> --}}
    <div class="modal fade" id="productLogs" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Product History Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="font-weight-bold">Product Name: <span id="name"></span></h5>
                    <h5 class="font-weight-bold">Sku: <span id="sku"></span></h5>
                    <table class="table-striped table-bordered table out-of-stock-product-log-details">
                        <thead>
                            <tr>
                                <th>In Stock</th>
                                <th>Previous Stock</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table out-of-stock-products-table"
                    id="out-of-stock-products-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Product Id</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>In Stock</th>
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
        $(function() {
            var table = $('#out-of-stock-products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('productinventory.out-of-stock') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'product_id',
                        name: 'product_id'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'in_stock',
                        name: 'in_stock'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('click', '.get-product-log-detail', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var product_id = $(this).data('product-id');
                $.ajax({
                    type: 'GET',
                    url: "{{ route('productinventory.out-of-stock-product-log') }}",
                    data: {
                        'id': id,
                        'product': product_id,
                    },
                    // dataType : "json",
                    success: function(res) {
                        if (res.success === true) {
                            $('#productLogs').modal('show');
                            let data = res.data;
                            $("#name").text(res.productName);
                            $("#sku").text(res.productSku);
                            $("#productLogs tbody").html(data);
                        } else {
                            toastr.error(res.msg);
                        }
                    }
                });
            })
        });
    </script>
@endsection
