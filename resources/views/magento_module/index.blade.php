@extends('layouts.app')



@section('title', 'Language Manager')

@section('styles')

    <style>
        .users {
            display: none;
        }

    </style>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.jqueryui.min.css"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>

    <div class="row ">
        <div class="col-lg-12 ">
            <h2 class="page-heading">Magento Modules

                <div class="pull-right">
                    <a href="{{ route('magento_modules.create') }}" class="btn btn-secondary">+</a>
                </div>
            </h2>

            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            <strong>Module Name:</strong>
                            {!! Form::text('module', null, ['placeholder' => 'Module Name', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            <strong>Module Category:</strong>
                            {!! Form::select('module_type', ['3rd Party' => '3rd Party', 'Custom' => 'Custom'], null, ['placeholder' => 'Select Module Type', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            <strong>Module Category:</strong>
                            {!! Form::select('module_category_id', $module_categories, null, ['placeholder' => 'Select Module Category', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            <strong>Customized:</strong>
                            {!! Form::select('is_customized', ['No', 'Yes'], null, ['placeholder' => 'Customized', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="row ml-4 mr-4">
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group pull-right ">
                            <button type="submit" class="btn btn-secondary ml-3" style="width:100px">Search</button>
                        </div>

                        <div class="form-group  pull-right">
                            <button type="reset" id="searchReset" class="btn btn-secondary ml-3 "
                                style="width:100px">Reset</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>






    <div class="table-responsive mt-3 mr-5 ml-5">
        @if ($message = Session::get('success'))
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <table class="table table-bordered" id="erp_table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Version</th>
                    <th>Type</th>
                    <th>Payment Status</th>
                    <th>Status</th>
                    <th>Developer Name</th>
                    <th>Customized</th>
                    <th>Action</th>

                </tr>
            </thead>
        </table>
    </div>

@endsection


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    {{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap.min.js"></script> --}}
    <script type="text/javascript" src="{{ asset('js/common-function.js') }}"></script>

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

        var oTable;
        $(document).ready(function() {
            oTable = $('#erp_table').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                oLanguage: {
                    sLengthMenu: "Show _MENU_",
                },
                createdRow: function(row, data, dataIndex) {
                    // Set the data-status attribute, and add a class
                    $(row).attr('role', 'row');
                    $(row).find("td").last().addClass('text-danger');
                },
                ajax: {
                    "url": "{{ route('magento_modules.index') }}",
                    data: function(d) {
                        d.module = $('input[name=module]').val();
                        d.module_type = $('select[name=module_type]').val();
                        d.is_customized = $('select[name=is_customized]').val();
                        d.module_category_id = $('select[name=module_category_id]').val();
                        // d.view_all = $('input[name=view_all]:checked').val(); // for Check box
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: false,
                    // className: 'mdl-data-table__cell--non-numeric'
                }],
                columns: [{
                        data: 'id',
                        name: 'magento_modules.id',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'category_name',
                        name: 'module_categories.category_name',
                    },
                    {
                        data: 'module',
                        name: 'magento_modules.module',
                    },
                    {
                        data: 'current_version',
                        name: 'magento_modules.current_version',
                    },
                    {
                        data: 'module_type',
                        name: 'magento_modules.module_type',
                    },
                    {
                        data: 'payment_status',
                        name: 'magento_modules.payment_status',
                    },
                    {
                        data: 'status',
                        name: 'magento_modules.status',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            return `<div class="flex items-center justify-left">${status_array[data]}</div>`;
                        }
                    },
                    {
                        data: 'developer_name',
                        name: 'magento_modules.developer_name',
                    },

                    {
                        data: 'is_customized',
                        name: 'magento_modules.is_customized',
                        render: function(data, type, row, meta) {
                            return (data == 1) ? 'Yes' : 'No';
                        }
                    },
                    {
                        data: 'id',
                        name: 'magento_modules.id',
                        // visible:false,
                        render: function(data, type, row, meta) {
                            var edit_url = `{{ url('/') }}/magento_modules/` + row['id'] +
                                `/edit/`;
                            var show_url = `{{ url('/') }}/magento_modules/` + row['id'] +
                                ``;
                            var edit_data = actionEditButton(edit_url, row['id']);
                            var show_data = actionShowButton(show_url);

                            var del_data = actionDeleteButton(row['id']);
                            return `<div class="flex justify-left items-center"> ${show_data} ${edit_data} ${del_data} </div>`;
                        }
                    },
                ],
            });
        });

        $(document).on('click', '.clsdelete', function() {
            var id = $(this).attr('data-id');
            var e = $(this).parent().parent();
            var url = `{{ url('/') }}/magento_modules/` + id;
            tableDeleteRow(url, oTable);
        });

        $(document).on('click', '.clsstatus', function() {
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            var url = `{{ url('/') }}/magento_modules/status/` + id + `/` + status;
            tableChnageStatus(url, oTable);
        });
    </script>

@endsection
