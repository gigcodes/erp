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
@endsection
