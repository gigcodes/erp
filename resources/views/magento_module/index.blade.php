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
                            <strong>Module Type:</strong>
                            {!! Form::select('module_type', $magento_module_types, null, ['placeholder' => 'Select Module Type', 'class' => 'form-control']) !!}
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
                            <strong>Task Status:</strong>
                            {!! Form::select('task_status', $task_statuses, null, ['placeholder' => 'Select Task Status', 'class' => 'form-control']) !!}
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
                    <th> Id </th>
                    <th> Communication </th>
                    <th> Category </th>
                    <th> Name </th>
                    <th> Version </th>
                    <th> Type </th>
                    <th> Payment Status</th>
                    <th> Status </th>
                    <th> Task Status </th>
                    <th> Developer Name</th>
                    <th> Customized </th>
                    <th> Action </th>

                </tr>
            </thead>
        </table>
    </div>

    @include('partials.plain-modal');
    @include('magento_module.partials.remark_list');
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

        var message_send_to_array = {
            'to_master': 'Send To Master Developer',
            'to_developer': 'Send To Developer',
            'to_team_lead': 'Send To Team Lead',
            'to_tester': 'Send To Tester',
        };

        var dropdown_options = '';
        @foreach ($message_send_to as $key => $value)
            dropdown_options +=
            `<option value="{{ $key }}"> {{ $value }} </option>`;
        @endforeach

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
                        d.task_status = $('select[name=task_status]').val();
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

                            var message =
                                `<input type="text" id="remark_${row['id']}" name="remark" class="form-control" placeholder="Remark" />`

                            var dropdown =
                                `<select id="send_to_${row['id']}" name="send_to" class="form-control mt-3" style="width:85% !important;display: inline;">`;
                            dropdown += dropdown_options;
                            dropdown += `</select>`;

                            var remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark" data-id="${row['id']}" style="margin-top: 2%;" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            var remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;

                            data = (data == null) ? '' : data;
                            return data + message + dropdown + remark_send_button +
                                remark_history_button;
                        }
                    },
                    {
                        data: 'category_name',
                        name: 'magento_module_categories.category_name',
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
                        data: 'task_name',
                        name: 'task_statuses.name',
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
                            // var show_url = `{{ url('/') }}/magento_modules/` + row['id'] +
                            //     ``;
                            var edit_data = actionEditButton(edit_url, row['id']);
                            var show_data = actionShowButtonWithClass('show-details', row['id']);

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
                                        <td> ${ message_send_to_array[v.send_to] } </td>
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


        function saveRemarks(row_id) {
            console.log(row_id);
            var remark = $("#remark_" + row_id).val();
            var send_to = $("#send_to_" + row_id).val();

            var val = $("#remark_" + row_id).val();

            $.ajax({
                url: `{{ route('magento_module_remark.store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    send_to: send_to,
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
    </script>

@endsection
