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
        /* CSS for positioning the eye and copy icons in the corner */
        .file-info-container {
            position: relative;
        }

        .action-buttons-container {
            position: absolute;
            top: 0;
            right: 0;
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
                Magento Documentation(<span id="total-count"></span>)
            </h2>
            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <select class="form-control select-multiple category_name" id="category-select" name="magento_docs_category_id">
                                @php
                                 $storecategories = \App\SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();
                                 @endphp
     
                                 <option value="">Select Category</option>
                                 @foreach ($storecategories as $storecategory)
                                     <option value="{{ $storecategory->id }}">{{ $storecategory->title }}</option>
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

        <div class="erp_table_data">
            <table class="table table-bordered" id="magento_frontend_docs_table">
                <thead>
                    <tr>
                        <th> Id </th>
                        <th width="10%"> Category </th>
                        <th> Parent folder </th>
                        <th> child folder </th>
                        <th> Remark </th>
                        <th> Location </th>
                        <th width="10%"> Admin Configuration </th>
                        <th width="10%"> Frontend configuration </th>    
                        <th width="10%"> File Name </th>   
                        <th width="10%"> Updated by </th>   
                        <th width="10%"> Created At </th>   
                        <th> Action </th>              
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>


    <div id="moduleEditModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="magento_module_edit_form" method="POST">
                    @csrf
                    {!! Form::hidden('id', null, ['id'=>'id']) !!}
                    <div class="modal-header">
                        <h4 class="modal-title">Update Magneto Frontend Documentation</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @include('magento-frontend-documentation.partials.edit-form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="parentImageAddModal" class="modal fade " role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="magento_frontend_parent_image_form" class="form mb-15" enctype="multipart/form-data">
                @csrf
                {!! Form::hidden('magento_frontend_id', null, ['id'=>'magento_frontend_id']) !!}
                <div class="modal-header">
                    <h4 class="modal-title">Add Parent Folder Image</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>parent Folder Image</label>
                                    <input type="file" name="parent_folder_image" id="parent_folder_image">
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Add</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    @include('magento-frontend-documentation.partials.magento-fronent-create')
    @include('magento-frontend-documentation.remark_list')
    @include('magento-frontend-documentation.magento-frontend-history')
    @include('magento-frontend-documentation.partials.magento-frontend-category-history')
    @include('magento-frontend-documentation.partials.magento-frontend-parent-folder-history')
    {{-- @include('magento-frontend-documentation.partials.child-folder-image') --}}
    @include('magento-frontend-documentation.partials.parent-folder-image')

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
                searching: false,
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
                            var categoriesHtml = '<select id="store_website_category_id" class="form-control edit_mm" required="required" name="store_website_category_id">';
                            categoriesHtml += '<option value="" selected>Select Module Category</option>'; // Add default option

                            categories.forEach(function(category) {
                                if (category.id === selectedCategoryId) {
                                    categoriesHtml += '<option value="' + category.id + '" selected>' + category.title + '</option>';
                                } else {
                                    categoriesHtml += '<option value="' + category.id + '">' + category.title + '</option>';
                                }
                            });

                            categoriesHtml += '</select>';

                            let category_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-category-history ml-2"  data-id="${row['id']}" title="Load messages">
                                    <i class="fa fa-info-circle" style="position: absolute; top: 5px; right: 5px;"></i>
                                </button>`;

                            return `<div class="flex justify-left items-center" style="position: relative;">
                                        ${categoriesHtml} ${category_history_button}
                                    </div>`;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="remark_${row['id']}" name="remark" class="form-control remark-input" placeholder="parent folder" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-parent-folder ml-2"  data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let Upload_button =  `<button style="display: inline-block;width: 10%" class="btn btn-sm upload-parent-folder-modal" type="submit" id="submit_message"  data-id="${row['id']}"> <i class="fa fa-upload" aria-hidden="true"></i></button>`;
                            
                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveparentFolder(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';                      
                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${Upload_button} ${remark_history_button} </div>`;

                            return retun_data;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="child_folder" name="child_folder" class="form-control child_folder-input" placeholder="child folder" />`;

                            let Upload_button =  `<button style="display: inline-block;width: 10%" class="btn btn-sm upload-child-folder-image-modal" type="submit" id="submit_message"  data-target="#childImageAddModal" data-id="${row['id']}"> <i class="fa fa-upload" aria-hidden="true"></i></button>`;
                            
                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveChildFolder(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';
                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${Upload_button} </div>`;

                            return retun_data;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="remark_${row['id']}" name="remark" class="form-control remark-input" placeholder="Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark" data-type="general" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

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
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 15)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'admin_configuration',
                        name: 'magento_frontend_docs.admin_configuration',
                        render: function(data, type, row, meta) {
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 20)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'frontend_configuration',
                        name: 'magento_frontend_docs.frontend_configuration',
                        render: function(data, type, row, meta) {
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 15)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            // Extract file_name and google_drive_file_id from the row data
                            let file_name = data.file_name;
                            let google_drive_file_id = data.google_drive_file_id;

                            let file_name_html = (file_name == null) ? '' : `
                                <div class="expand-row module-text" style="word-break: break-all">
                                    <div class="flex items-center justify-left td-mini-container" title="${file_name}">
                                        ${setStringLength(file_name, 15)}
                                    </div>
                                    <div class="flex items-center justify-left td-full-container hidden" title="${file_name}">
                                        ${file_name}
                                    </div>
                                </div>`;

                            let action_buttons = '';
                            if (google_drive_file_id) {
                                let documentUrl = `https://drive.google.com/file/d/${google_drive_file_id}/view?usp=sharing`;
                                action_buttons = `
                                    <a target="_blank" href="${documentUrl}" class="btn btn-image padding-10-3 show-details">
                                        <img src="/images/view.png" style="cursor: default;">
                                    </a>
                                    <button class="copy-button btn btn-xs text-dark" data-message="${documentUrl}" title="Copy document URL">
                                        <i class="fa fa-copy"></i>
                                    </button>`;
                            }

                            // Combine both file_name_html and action_buttons in the same TD
                            return `
                                <div class="file-info-container">
                                    ${file_name_html}
                                    <div class="action-buttons-container">
                                        ${action_buttons}
                                    </div>
                                </div>`;
                        }
                    },
                    {
                        data: 'user.name',
                        name: 'magento_frontend_docs.user_id',
                        render: function(data, type, row, meta) {
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 20)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'magento_frontend_docs.created_at',
                        render: function(data, type, row, meta) {
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 20)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let edit_button =
                                `<button type="button" class="btn btn-xs btn-image edit-module ml-2" data-type="general" data-id="${row['id']}" title="Edit messages"> <img src="/images/edit.png" alt="" style="cursor: default;"> </button>`;
                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-frontend-history ml-2" data-type="general" data-id="${row['id']}" title="View History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;
                               
                            return `<div class="flex justify-left items-center">${edit_button} ${remark_history_button} </div>`;
        
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
                
        $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

     //edit module
     $(document).on('click', '.edit-module', function() {
        var moduleId = $(this).data("id");
        var url = "{{ route('magento_frontend_edit', ['id' => ':id']) }}";
        url = url.replace(':id', moduleId);    
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: url,
        }).done(function(response) {
            console.log(response.data.child_folder_image);
            $("#magento_module_edit_form #id").val(response.data.id);
            $("#magento_module_edit_form #location").val(response.data.location);
            $("#magento_module_edit_form #admin_configuration").val(response.data.admin_configuration);
            $("#magento_module_edit_form #frontend_configuration").val(response.data.frontend_configuration);
            $("#magento_module_edit_form #filename").val(response.data.child_folder_image);
			var image = "/magentofrontend-child-image/" + response.data.child_folder_image; 
			$('#magento_module_edit_form #filename').attr('src', image);
            $("#moduleEditModal").modal("show");
        }).fail(function (response) {
            $("#loading-image-preview").hide();
            console.log("Sorry, something went wrong");
        });
    });


    $(document).on('change', '.edit_mm', function() {
            var  column = $(this).attr('name');
            var value = $(this).val();
            var data_id = $(this).parents('tr').find('.data_id').val();
            
            $.ajax({
                type: "POST",
                url: "{{route('magento_frontend.update.option')}}",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    columnName : column,
                    data : value,
                    id : data_id
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (response) {
                if(response.code == 200) {
                    $("#loading-image").hide();
                    toastr['success'](response.message);
                }else{
                    $("#loading-image").hide();
                    toastr['error'](response.message);
                }
                
            }).fail(function (response) {
                $("#loading-image").hide();
                console.log("failed");
                toastr['error'](response.message);
            });
        });


        $(document).on('submit', '#magento_module_edit_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_edit_form"));
        var magento_module_id = $('#magento_module_edit_form #id').val();
        console.log(formData, magento_module_id);
        var button = $(this).find('[type="submit"]');
        console.log("#magento_module_edit_form submit");
        $.ajax({
            url: '{{ route("magento_frontend.update", '') }}/' + magento_module_id,
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Update');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#moduleCreateModal #magento_module_edit_form').find('.error-help-block').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.alert').remove();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });



    $(document).on('click', '.load-frontend-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: '{{ route("magentofrontend_histories.show", '') }}/' + id,

                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#magneto-frontend-historylist").modal("show");

                    if (response) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.location} </td>
                                        <td> ${v.admin_configuration} </td>
                                        <td> ${v.frontend_configuration} </td>
                                        <td> ${v.user.name} </td>
                                    </tr>`;
                        });
                        $("#magneto-frontend-historylist").find(".magneto-historylist-view").html(html);
                        $("#magneto-frontend-historylist").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.load-category-history', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                url: '{{ route("magentofrontend_category.histories.show", '') }}/' + id,
                dataType: "json",
                data: {
                    id:id,

                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.new_category ? v.new_category.title : ''} </td>
                                        <td> ${v.old_category ? v.old_category.title : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#category-listing").find(".category-listing-view").html(html);
                        $("#category-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        //Store Parent folder
        function saveparentFolder(row_id, selector = 'remark') {
            var folderName = $("#"+selector+"_" + row_id).val();
            var val = $("#"+selector+"_" + row_id).val();
            $.ajax({
                url: `{{ route('magento-frontend-parent-folder-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    folderName: folderName,
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
        
        function saveChildFolder(row_id) {
            alert(row_id);
            // let inputValue = $(`#child_folder_${id}`).val();

            let childFolderName = $("#child_folder").val();

            $.ajax({
                url: `{{ route('magento-frontend-child-folder-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    folderName: childFolderName,
                    magento_front_end_id: row_id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                console.log(response);
                if (response.status) {
                    
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

        $(document).on("click", ".upload-child-folder-image-modal", function() {
            let magento_frontend_id = $(this).data('id');
            $("#childImageAddModal").find('[name="magento_frontend_id"]').val(magento_frontend_id);
            $('#childImageAddModal').modal('show');
        });

        $(document).on('click', '.load-module-parent-folder', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-frontend-get-parent-folder') }}`,
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
                            console.log(v);
                            folderName = v.parent_folder_name;
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${folderName}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${folderName}'></i></td>
                                    </tr>`;
                        });
                        $("#magneto-frontend-parent-folder-list").find(".magneto-frontend-parent-view").html(html);
                        $("#magneto-frontend-parent-folder-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

       
        $(document).on('click', '.upload-parent-folder-modal', function() {
            let magento_frontend_id = $(this).data('id');
            $("#parentImageAddModal").find('[name="magento_frontend_id"]').val(magento_frontend_id);
            $('#parentImageAddModal').modal('show');
        });

        $(document).on('submit', '#magento_frontend_parent_image_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_frontend_parent_image_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento-frontend-parent-folder-image.store") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#apiDataAddModal #magento_frontend_parent_image_form').trigger('reset');
                magentofrontendTable.draw();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });

    $(document).on('submit', '#magento_frontend_child_image_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_frontend_child_image_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento-frontend-child-image-store") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#apiDataAddModal #magento_frontend_child_image_form').trigger('reset');
                magentofrontendTable.draw();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });

    </script>

@endsection
