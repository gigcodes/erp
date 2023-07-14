@extends('layouts.app')



@section('title', 'magento-frontent-documentation')

@section('styles')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style>
        .general-remarks {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .gap-5 {
            gap: 5px;
        }

        .module-text {
            width: 80px;
        }

        .users {
            display: none;
        }

        table.dataTable thead th {
            padding: 5px 5px !important;
        }

        table.dataTable tbody th,
        table.dataTable tbody td {
            padding: 5px 5px !important;
        }

        .copy_remark {
            cursor: pointer;
        }

        .multiselect-native-select .btn-group {
            width: 100%;
            margin: 0px;
            padding: 0;
        }

        .multiselect-native-select .checkbox input {
            margin-top: -5px !important;
        }

        .multiselect-native-select .btn-group button.multiselect {
            width: 100%;

        }
    </style>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.jqueryui.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        .disabled {
            pointer-events: none;
            background: #bababa;
        }

        .glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }

        @-webkit-keyframes spin2 {
            from {
                -webkit-transform: rotate(0deg);
            }

            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            from {
                transform: scale(1) rotate(0deg);
            }

            to {
                transform: scale(1) rotate(360deg);
            }
        }

        @media(max-width:1200px) {
            .action_button {
                display: block;
                width: 100%;
            }
        }

        .table select.form-control {
            width: 130px !important;
            padding: 5px;
        }

    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;z-index: 9999;" />
    </div>

    <div class="row ">
        <div class="col-lg-12 ">
            <h2 class="page-heading">
                magento documenetation (<span id="total-count"></span>)
            </h2>
            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <select class="form-control select-multiple category_name" id="category-select" name="magento_docs_category_id">
                                @php
                                 $storecategories = \App\StoreWebsiteCategory::select('category_name', 'id')->wherenotNull('category_name')->get();
                                 @endphp
     
                                 <option value="">Select Category</option>
                                 @foreach ($storecategories as $storecategory)
                                     <option value="{{ $storecategory->id }}">{{ $storecategory->category_name }}</option>
                                 @endforeach
                             </select>
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <select class="form-control select-multiple location_name" id="location_select" name="location_name">
                                @php
                                 $locations = \App\Models\MagentoFrontendDocumentation::select('location', 'id')->get();
                                 @endphp
     
                                 <option value="">Select locations</option>
                                 @foreach ($locations as $location)
                                     <option value="{{ $location->location }}">{{ $location->location }}</option>
                                 @endforeach
                             </select>
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <input name="search_admin_config" type="text" class="form-control search_admin_config" value="{{ request('status') }}"
                                    placeholder="search Admin Config" id="search_admin_config">
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <input name="search_frontend_confid" type="text" class="form-control search_frontend_config" value="{{ request('status') }}"
                            placeholder="search Frontend Config" id="search_frontend_config">                     
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

                    <div class="pull-right pr-5">
                        <button type="button" class="btn btn-secondary" data-toggle="modal"
                            data-target="#create-magento-frontend-docs"> Magento Module Create </button>
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
        <div class="erp_table_data">
            <table class="table table-bordered" id="magento_frontend_docs_table">
                <thead>
                    <tr>
                        <th> Id </th>
                        <th> Category </th>
                        <th> Remark </th>
                        <th> Location </th>
                        <th> Admin Configuration </th>
                        <th> Frontend configuration </th>    
                        <th> File Name </th>                 
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>

    @include('magento-frontend-documentation.partials.magento-fronent-create')
    @include('magento-frontend-documentation.remark_list')
 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="{{ env('APP_URL') }}/js/bootstrap-multiselect.min.js"></script>
    <script>

        $("#id_label_file_permission_read").select2();
        $("#id_label_file_permission_write").select2();
        // START Print Table Using datatable
        var magentofrontendTable;
        $(document).ready(function() {
            magentofrontendTable = $('#magento_frontend_docs_table').DataTable({
                pageLength: 10,
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                // sScrollX: true,
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
                    if (data["row_bg_colour"] != "") {
                        $(row).css("background-color", data["row_bg_colour"]);
                    }
                },
                ajax: {
                    "url": "{{ route('magento_frontend_listing') }}",
                    data: function(d) {
                        d.categoryname = $('.category_name').val();
                        d.frontend_configuration = $('.search_frontend_config').val();
                        d.admin_configuration = $('.search_admin_config').val();
                        d.location = $('.location_name').val();   
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: true,
                    className: 'mdl-data-table__cell--non-numeric'
                }],
                columns: [{
                        data: 'id',
                        name: 'magento_frontend_docs.id',
                        render: function(data, type, row, meta) {
                            var html = '<input type="hidden" name="mm_id" class="data_id" value="' +
                                data + '">';
                            return html + data;
                        }
                    },

                    {
                        data: 'store_website_categories.category_name',
                        name: 'store_website_categories.category_name',
                        render: function(data, type, row, meta) {
                            var categories = JSON.parse(row['categories']);
                            if (!categories || categories.length === 0) {
                                return '<div class="flex items-center justify-left">' + data + '</div>';
                            }

                            var selectedCategoryId = row['store_website_category_id'];
                            var categoriesHtml = '<select id="module_category_id" class="form-control edit_mm" required="required" name="module_category_id">';
                            categoriesHtml += '<option value="" selected>Select Module Category</option>'; // Add default option

                            categories.forEach(function(category) {
                                if (category.id === selectedCategoryId) {
                                    categoriesHtml += '<option value="' + category.id + '" selected>' + category.category_name + '</option>';
                                } else {
                                    categoriesHtml += '<option value="' + category.id + '">' + category.category_name + '</option>';
                                }
                            });

                            categoriesHtml += '</select>';
                            return categoriesHtml;
                        }
                    },

                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="remark_${row['id']}" name="remark" class="form-control remark-input" placeholder="Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-type="general" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';
                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${remark_history_button} </div>`;

                            return retun_data;
                        }
                    },
                    {
                        data: 'location',
                        name: 'magento_frontend_docs.location',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 5)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'admin_configuration',
                        name: 'magento_frontend_docs.admin_configuration',
                        render: function(data, type, row, meta) {
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 5)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'frontend_configuration',
                        name: 'magento_frontend_docs.frontend_configuration',
                        render: function(data, type, row, meta) {
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 5)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'file_name',
                        name: 'magento_frontend_docs.file_name',
                        render: function(data, type, row, meta) {
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 15)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },


                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var recordsTotal = api.page.info().recordsTotal;
                    var recordsFiltered = api.page.info().recordsFiltered;
                    $('#total-count').text(recordsTotal);
                },
            });

        });

        $('#dateform').on('submit', function(e) {
            e.preventDefault();
            magentofrontendTable.draw();

            return false;
        });

        $(document).on('click', '#searchReset', function(e) {
            $('#dateform').trigger("reset");
            e.preventDefault();
            magentofrontendTable.draw();
        });


         // Store Reark
         function saveRemarks(row_id, selector = 'remark') {
            var remark = $("#"+selector+"_" + row_id).val();
            var val = $("#"+selector+"_" + row_id).val();

            $.ajax({
                url: `{{ route('magento-frontend-remark-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    magento_front_end_id: row_id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#"+selector+"_" + row_id).val('');
                    $("#send_to_" + row_id).val('');
                    toastr["success"](response.message);
                    magentofrontendTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        toastr["warning"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }

        $(document).on('click', '.load-module-remark', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-frontend-get-remarks') }}`,
                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            remarkText=v.remark;
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${remarkText}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${remarkText}'></i></td>
                                    </tr>`;
                        });
                        $("#remark-magneto-frontend-list").find(".remark-magnetolist-view").html(html);
                        $("#remark-magneto-frontend-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });


    </script>

@endsection
