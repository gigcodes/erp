@extends('layouts.app')



@section('title', $title)

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

    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>

    @php
    $message_send_to = [
        'to_master' => 'Send To Master Developer',
        'to_developer' => 'Send To Developer',
        'to_team_lead' => 'Send To Team Lead',
        'to_tester' => 'Send To Tester',
    ];
    @endphp

    <div class="row ">
        <div class="col-lg-12 ">
            <h2 class="page-heading">{{ $title }}
            </h2>

            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            {!! Form::text('module', null, ['placeholder' => 'Module Name', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {!! Form::select('module_type', $magento_module_types, null, ['placeholder' => 'Select Module Type', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {!! Form::select('module_category_id', $module_categories, null, ['placeholder' => 'Select Module Category', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {!! Form::select('is_customized', ['No', 'Yes'], null, ['placeholder' => 'Customized', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {!! Form::select('store_website_id', $store_websites, null, ['placeholder' => 'Store Website', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-2 col-sm-1 pt-2 ">
                        <div class="d-flex" >
                            <div class="form-group pull-left ">
                                <button type="submit" class="btn btn-image search">
                                    <img src="/images/search.png" alt="Search" style="cursor: inherit;">
                                </button>
                            </div>
                            <div class="form-group pull-left ">
                                <button type="submit" id="searchReset" class="btn btn-image search ml-3">
                                    <img src="/images/resend2.png" alt="Search" style="cursor: inherit;">
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group pull-right ml-3 mt-3">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleTypeCreateModal"> Module Type Create </button>
                    
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleCategoryCreateModal"> Module Category Create </button>

                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleCreateModal"> Magneto Module Create </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive mt-3 pr-2 pl-2">
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
                    <th> Id </th>
                    <th width="270px"> Remark </th>
                    <th> Category </th>
                    <th> Website </th>
                    <th> API </th>
                    <th> Cron </th>
                    <th> Name </th>
                    <th> Version </th>
                    <th> Type </th>
                    <th> Payment Status</th>
                    <th> Status </th>
                    <th> Developer Name</th>
                    <th> Customized </th>
                    <th> Action </th>

                </tr>
            </thead>
        </table>
    </div>

    {{-- #blank-modal --}}
    @include('partials.plain-modal')
    {{-- #remark-area-list --}}
    @include('magento_module.partials.remark_list')
    {{-- moduleTypeCreateModal --}} {{-- moduleTypeEditModal --}}
    @include('magento_module_type.partials.form_modals')
    {{-- moduleCategoryCreateModal --}} {{-- moduleCategoryEditModal --}}
    @include('magento_module_category.partials.form_modals')
    {{-- moduleCreateModal --}} {{-- moduleEditModal --}}
    @include('magento_module.partials.form_modals')
    {{-- apiDataAddModal --}}
    @include('magento_module.partials.api_form_modals')
    {{-- cronJobDataAddModal --}}
    @include('magento_module.partials.cron_form_modals')
    {{-- apiDataShowModal --}}
    @include('magento_module.partials.api_data_show_modals')
    {{-- cronJobDataShowModal --}}
    @include('magento_module.partials.cron_data_show_modals')

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
                order: [
                    [0, 'desc']
                ],
                targets: 'no-sort',
                bSort: false,

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
                        d.task_status = $('select[name=task_status]').val();
                        d.store_website_id = $('select[name=store_website_id]').val();
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
                        data: 'last_message',
                        name: 'magento_modules.last_message',
                        render: function(data, type, row, meta) {

                            let message = `<input type="text" id="remark_${row['id']}" name="remark" class="form-control" placeholder="Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                                data = (data == null) ? '' : data;
                            let retun_data = `${data} <div class="d-flex"> ${message} ${remark_send_button} ${remark_history_button} </div>`;
                            
                            return retun_data;
                        }
                    },
                    {
                        data: 'category_name',
                        name: 'magento_module_categories.category_name',
                    },
                    {
                        data: 'website',
                        name: 'store_websites.website',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            return `<div class="flex items-center justify-left" title="${data}">${setStringLength(data, 15)}</div>`;
                        }
                    },
                    {
                        data: 'api',
                        name: 'magento_modules.api',
                        render: function(data, type, row, meta) {
                            let add_button = `<button type="button" class="btn btn-xs add-api-data-modal" title="Show History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-api-modal" title="Show History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            data = (data == 1) ? 'Yes' : 'No';
                            let html_data = ``;
                            if(data){
                                html_data = `<div class="d-flex"> ${data} ${add_button} ${show_button} </div>`;
                            }else{
                                html_data = `<div class="d-flex"> ${data} ${show_button} </div>`;
                            }
                            return html_data;
                        }
                    },
                    {
                        data: 'cron_job',
                        name: 'magento_modules.cron_job',
                        render: function(data, type, row, meta) {
                            let add_button = `<button type="button" class="btn btn-xs add-cron_job-modal" title="Show History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-cron_job-modal" title="Show History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            data = (data == 1) ? 'Yes' : 'No';
                            let html_data = `<div class="d-flex"> ${data} ${add_button} ${show_button} </div>`;
                            return html_data;
                        }
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
                        data: 'magento_module_type',
                        name: 'magento_module_types.magento_module_type',
                        render: function(data, type, row, meta) {
                            return `<div class="flex items-center justify-left">${data}</div>`;
                        }
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
                        name: 'users.name',
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

                            var show_data = actionShowButtonWithClass('show-details', row['id']);
                            var edit_data = actionEditButtonWithClass('edit-magento-module', JSON.stringify(row));

                            var del_data = actionDeleteButton(row['id']);
                            return `<div class="flex justify-left items-center"> ${show_data} ${edit_data} ${del_data} </div>`;
                        }
                    },
                ],
            });
        });
        // END Print Table Using datatable

        // Delete Module 
        $(document).on('click', '.clsdelete', function() {
            var id = $(this).attr('data-id');
            var e = $(this).parent().parent();
            var url = `{{ url('/') }}/magento_modules/` + id;
            tableDeleteRow(url, oTable);
        });

        //Status Update 
        $(document).on('click', '.clsstatus', function() {
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            var url = `{{ url('/') }}/magento_modules/status/` + id + `/` + status;
            tableChnageStatus(url, oTable);
        });

        // Load All Module Details
        $(document).on("click", ".show-details", function() {

            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ url('/') }}/magento_modules/` + id,
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    if (response.code == 200) {
                        $("#blank-modal").find(".modal-title").html(response.title);
                        $("#blank-modal").find(".modal-body").html(response.data);
                        $("#blank-modal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Store Reark
        function saveRemarks(row_id) {
            console.log(row_id);
            var remark = $("#remark_" + row_id).val();
            // var send_to = $("#send_to_" + row_id).val();

            var val = $("#remark_" + row_id).val();

            $.ajax({
                url: `{{ route('magento_module_remark.store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    // send_to: send_to,
                    magento_module_id: row_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#remark_" + row_id).val('');
                    $("#send_to_" + row_id).val('');
                    toastr["success"](response.message);
                    oTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                        toastr["error"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }

        // Load Remark
        $(document).on('click', '.load-module-remark', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_remark.get_remarks', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html = `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${v.remark } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at } </td>
                                    </tr>`;
                        });
                        $("#remark-area-list").find(".remark-action-list-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#remark-area-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });


        $(document).on("click", ".add-api-data-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#apiDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#apiDataAddModal').modal('show');
        });

        $(document).on("click", ".add-cron_job-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#cronJobDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#cronJobDataAddModal').modal('show');
        });

          // Load Api Modal
          $(document).on('click', '.show-api-modal', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_api_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html = `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${v.resources } </td>
                                        <td> ${v.frequency } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at } </td>
                                    </tr>`;
                        });
                        $("#apiDataShowModal").find(".api-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#apiDataShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Load cron job Modal
        $(document).on('click', '.show-cron_job-modal', function() {
            var id = $(this).attr('data-id');
            
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_cron_job_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html = `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${v.cron_time } </td>
                                        <td> ${v.frequency } </td>
                                        <td> ${v.cpu_memory } </td>
                                        <td> ${v.comments } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at } </td>
                                    </tr>`;
                        });
                        $("#cronJobDataShowModal").find(".cron-job-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#cronJobDataShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
    </script>

@endsection
