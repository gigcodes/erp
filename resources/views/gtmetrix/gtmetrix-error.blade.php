@extends('layouts.app')



@section('title', $title)

@section('styles')

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
        
        .disabled{
            pointer-events: none;
            background: #bababa;
        }
        .glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg);}
            to { -webkit-transform: rotate(360deg);}
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg);}
            to { transform: scale(1) rotate(360deg);}
        }
        table.dataTable{
          margin:0;

        }
        .dataTables_scrollHeadInner,table.dataTable{
            width: 100% !important;
        }
        table.dataTable .text-danger{
            color:#333 !important;
        }
    </style>
@endsection


@section('content')
    <div class = "row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                GTMetrix Error List
                <div style="float: right;">
                    <button type="button" class="btn btn-secondary truncate-tables-btn" style=" float: right;">
                        Truncate Table
                    </button> 
                </div>
            </h2>
        </div>
    </div>
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="table-responsive mt-3 pr-2 pl-2">
        <div class="erp_table_data">
            <table class="table table-bordered " id="erp_table">
                <thead>
                    <tr>
                        <th width="5%"> Id </th>
                        <th width="10%"> Store view GTM id </th>
                        <th width="20%"> Error title </th>
                        <th width="55%"> Error </th>
                        <th width="10%"> Created at </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).on('click', '#searchReset', function(e) {
            //alert('success');
            $('#dateform').trigger("reset");
            e.preventDefault();
            oTable.draw();
        });

        $('#dateform').on('submit', function(e) {
            e.preventDefault();
            oTable.draw();

            return false;
        });

        $('#extraSearch').on('click', function(e) {
            e.preventDefault();
            oTable.draw();
        }); 

        // START Print Table Using datatable
        var oTable;
        $(document).ready(function() {
            oTable = $('#erp_table').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                sScrollX: true,
                order: [
                    [0, 'desc']
                ],
                targets: 'no-sort',
                bSort: false,
                pageLength: 100,
                oLanguage: {
                    sLengthMenu: "Show _MENU_",
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).attr('role', 'row');
                    $(row).find("td").last().addClass('text-danger');
                },
                ajax: {
                    "url": "{{ route('gtmetrix.error.list') }}",
                },
                
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: false,
                    
                }],
                columns: [{
                        data: 'id',
                        name: 'g_t_matrix_error_logs.id',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'store_viewGTM_id',
                        name: 'g_t_matrix_error_logs.store_viewGTM_id',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'error_title',
                        name: 'g_t_matrix_error_logs.error_title',
                        render: function(data, type, row, meta) {
                            return  data;
                        }
                    },
                    {
                        data: 'error',
                        name: 'g_t_matrix_error_logs.error',
                        render: function(data, type, row, meta) {
                            return  data;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'g_t_matrix_error_logs.created_at',
                        render: function(data, type, row, meta) {
                            return moment(data).format('YYYY-MM-DD HH:mm:ss');
                        }
                    }
                ],
            });
        });
        // END Print Table Using datatable

        $(document).on("click",".truncate-tables-btn",function() {

            if (confirm('Are you sure you want to truncate the GTMetrix Error tables?')) {

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route('gtmetrix.error.truncate-tables') }}',                
                    success: function(response) {
                        toastr["success"]("Your GTMetrix Error tables has been truncate successfully");
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        location.reload();
                    }
                }); 
            }
        });
    </script>

@endsection
