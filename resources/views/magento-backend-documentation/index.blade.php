@extends('layouts.app')



@section('title', 'magento-backend-documentation')

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
                Magento Documentation<span id="total-count"></span>
            </h2>
            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{-- <select class="form-control select-multiple category_name" id="category-select" name="magento_docs_category_id">
                                @php
                                 $storecategories = \App\SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();
                                 @endphp
     
                                 <option value="">Select Category</option>
                                 @foreach ($storecategories as $storecategory)
                                     <option value="{{ $storecategory->id }}">{{ $storecategory->title }}</option>
                                 @endforeach
                             </select> --}}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{-- <select class="form-control select-multiple location_name" id="location_select" name="location_name">
                                @php
                                 $locations = \App\Models\MagentoFrontendDocumentation::select('location', 'id')->get();
                                 @endphp
     
                                 <option value="">Select locations</option>
                                 @foreach ($locations as $location)
                                     <option value="{{ $location->location }}">{{ $location->location }}</option>
                                 @endforeach
                             </select> --}}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{-- <input name="search_admin_config" type="text" class="form-control search_admin_config" value="{{ request('status') }}"
                                    placeholder="search Admin Config" id="search_admin_config"> --}}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{-- <input name="search_frontend_confid" type="text" class="form-control search_frontend_config" value="{{ request('status') }}"
                            placeholder="search Frontend Config" id="search_frontend_config">                      --}}
                          </div>
                    </div>

                    <div class="col-xs-2 col-sm-1 pt-2 ">
                        <div class="d-flex" >
                            <div class="form-group pull-left ">
                                {{-- <button type="submit" class="btn btn-image search">
                                    <img src="/images/search.png" alt="Search" style="cursor: inherit;">
                                </button> --}}
                            </div>
                            <div class="form-group pull-left ">
                                {{-- <button type="submit" id="searchReset" class="btn btn-image search ml-3">
                                    <img src="/images/resend2.png" alt="Search" style="cursor: inherit;">
                                </button> --}}
                            </div>
                        </div>
                    </div>

                    <div class="pull-right pr-5">
                        <button type="button" class="btn btn-secondary" data-toggle="modal"
                            data-target="#create-magento-backend-docs"> Magento Backend Create </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive mt-3 pr-2 pl-2">

        <div class="erp_table_data">
            <table class="table table-bordered" id="magento_backend_docs_table">
                <thead>
                    <tr>
                        <th> Id </th>
                        <th width="10%"> Site developement Category </th>
                        <th width="10%"> PostMan Api </th>
                        <th width="10%"> Magneto Module </th>
                        <th width="20%"> Description </th>
                        <th width="20%"> Admin configuration </th>
                        <th width="20%"> Remark </th>
                        <th width="30%"> Bug </th>
                        <th width="30%"> Bug details </th>
                        <th width="30%"> Bug solutions </th>    
                        <th width="30%"> Updated by </th>   
                        <th width="5%"> Created At </th>   
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

    <div id="category-backend-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Old Category</th>
                                    <th width="30%">New Category</th>
                                    <th width="30%">Updated by</th>
                                    <th width="20%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="category-backend-listing-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="postman-backend-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="50%">Old Api</th>
                                    <th width="50%">New Api</th>
                                    <th width="30%">Updated by</th>
                                    <th width="20%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="postman-backend-listing-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="module-backend-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="50%">Old Module</th>
                                    <th width="50%">New Module</th>
                                    <th width="30%">Updated by</th>
                                    <th width="20%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="module-backend-listing-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="remark-backend-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="50%">Old Remark</th>
                                    <th width="50%">New remark</th>
                                    <th width="30%">Updated by</th>
                                    <th width="20%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="remark-backend-listing-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="description-backend-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="50%">Old Remark</th>
                                    <th width="50%">New remark</th>
                                    <th width="30%">Updated by</th>
                                    <th width="20%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="description-backend-listing-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="admin-backend-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="50%">Old adminConfig</th>
                                    <th width="50%">New adminConfig</th>
                                    <th width="30%">Updated by</th>
                                    <th width="20%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="admin-backend-listing-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="descriptionUploadModal" tabindex="-1" role="dialog" aria-labelledby="descriptionUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form id="magento_backend_description_upload_form" class="form mb-15" enctype="multipart/form-data">
            @csrf
            {!! Form::hidden('magento_backend_id', null, ['id'=>'magento_backend_id']) !!}  
            <div class="modal-header">
              <h5 class="modal-title" id="uploaddescriptionModalLabel">Upload Description Image</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="file" name="upload_description" id="upload_description">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Add</button>
            </div>
        </form>
          </div>
        </div>
      </div>

      
    
    
    @include('magento-backend-documentation.magento-backend-create-modal')
    @include('magento-backend-documentation.admin-config-file')


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="{{ env('APP_URL') }}/js/bootstrap-multiselect.min.js"></script>
    <script>


        // START Print Table Using datatable
        var magentobackendTable;
        $(document).ready(function() {
            magentobackendTable = $('#magento_backend_docs_table').DataTable({
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
                    "url": "{{ route('magento.backend.listing') }}",
                    data: function(d) {
                        // d.categoryname = $('.category_name').val();
                        // d.frontend_configuration = $('.search_frontend_config').val();
                        // d.admin_configuration = $('.search_admin_config').val();
                        // d.location = $('.location_name').val();   
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
                        name: 'magento_backend_docs.id',
                        render: function(data, type, row, meta) {
                            var html = '<input type="hidden" name="mm_id" class="data_id" value="' +
                                data + '">';
                            return html + data;
                        }
                    },

                    {
                        render: function(data, type, row, meta) {
                            var categories = JSON.parse(row['categories']);
                            if (!categories || categories.length === 0) {
                                return '<div class="flex items-center justify-left">' + data + '</div>';
                            }

                            var selectedCategoryId = row['site_development_category_id'];
                            var categoriesHtml = '<select id="site_development_category_id" class="form-control edit_mm" required="required" name="site_development_category_id">';
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
                                `<button type="button" class="btn btn-xs btn-image load-site-category-history ml-2"  data-id="${row['id']}" data-column="site_development_category_id" title="Load messages">
                                    <i class="fa fa-info-circle" style="position: absolute; top: 5px; right: 5px;"></i>
                                </button>`;

                            return `<div class="flex justify-left items-center" style="position: relative;">
                                        ${categoriesHtml} ${category_history_button}
                                    </div>`;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {
                            var categories = JSON.parse(row['postManAPi']);
                            if (!categories || categories.length === 0) {
                                return '<div class="flex items-center justify-left">' + data + '</div>';
                            }

                            var selectedCategoryId = row['post_man_api_id'];
                            var categoriesHtml = '<select id="post_man_api_id" class="form-control edit_mm" required="required" name="post_man_api_id">';
                            categoriesHtml += '<option value="" selected>Select PostMan Api</option>'; // Add default option

                            categories.forEach(function(category) {
                                if (category.id === selectedCategoryId) {
                                    categoriesHtml += '<option value="' + category.id + '" selected>' + category.request_url + '</option>';
                                } else {
                                    categoriesHtml += '<option value="' + category.id + '">' + category.request_url + '</option>';
                                }
                            });

                            categoriesHtml += '</select>';

                            let category_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-postman-api-history ml-2" data-column="post_man_api_id"   data-id="${row['id']}" title="Load messages">
                                    <i class="fa fa-info-circle" style="position: absolute; top: 5px; right: 5px;"></i>
                                </button>`;

                            return `<div class="flex justify-left items-center" style="position: relative;">
                                        ${categoriesHtml} ${category_history_button}
                                    </div>`;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {
                            var categories = JSON.parse(row['magentoModules']);
                            if (!categories || categories.length === 0) {
                                return '<div class="flex items-center justify-left">' + data + '</div>';
                            }

                            var selectedCategoryId = row['mageneto_module_id'];
                            var categoriesHtml = '<select id="mageneto_module_id" class="form-control edit_mm" required="required" name="mageneto_module_id">';
                            categoriesHtml += '<option value="" selected>Select Module Modules</option>'; // Add default option

                            categories.forEach(function(category) {
                                if (category.id === selectedCategoryId) {
                                    categoriesHtml += '<option value="' + category.id + '" selected>' + category.module + '</option>';
                                } else {
                                    categoriesHtml += '<option value="' + category.id + '">' + category.module + '</option>';
                                }
                            });

                            categoriesHtml += '</select>';

                            let category_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-history ml-2" data-column="mageneto_module_id"  data-id="${row['id']}" title="Load messages">
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
                                `<input type="text" id="description_${row['id']}" name="description" class="form-control description_folder-input" placeholder="Description" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-description ml-2"  data-id="${row['id']}" data-column="description" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let Upload_button =  `<button style="display: inline-block;width: 10%" class="btn btn-sm upload-description-modal" type="submit" id="submit_message" data-id="${row['id']}" data-toggle="modal" data-target="#descriptionUploadModal"> <i class="fa fa-upload" aria-hidden="true"></i></button>`;
                            
                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="savedescription(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';                      
                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${Upload_button} ${remark_history_button} </div>`;

                            return retun_data;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="adminConfig_${row['id']}" name="adminConfig" class="form-control adminConfig-input" placeholder="Admin configuration" />`;

                            let Upload_button =  `<button style="display: inline-block;width: 10%" class="btn btn-sm upload-child-folder-image-modal" type="submit" id="submit_message"  data-target="#childImageAddModal" data-id="${row['id']}"> <i class="fa fa-upload" aria-hidden="true"></i></button>`;
                            
                            let remark_history_button =
                            `<button type="button" class="btn btn-xs btn-image load-module-admin_config ml-2"  data-id="${row['id']}"  data-column="admin_configuration" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveadminConfig(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';
                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${Upload_button} ${remark_history_button} </div>`;

                            return retun_data;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="remark_${row['id']}" name="remark" class="form-control remark-input" placeholder="Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-backend-remark" data-column="api_remark" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';
                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${remark_history_button} </div>`;

                            return retun_data;
                        }
                    },
                    {
                        data: 'features',
                        name: 'magento_backend_docs.features',
                        render: function(data, type, row, meta) {
                            if (data !== null) {
                                admin_Config= data.length > 30 ? data.substring(0, 30) + '...' : data;
                            }

                            return `<td class="expand-row" style="word-break: break-all">
                                       <div class="expand-row" style="word-break: break-all">
                                        <span class="td-mini-container">${admin_Config}</span>
                                        <span class="td-full-container hidden">${data}</span>
                                        </div>
                                    </td>`;
                        }
                    },
                    {
                        data: 'bug_details',
                        name: 'magento_backend_docs.bug_details',
                        render: function(data, type, row, meta) {
                            var shortJobName = '';
                            if (data !== null) {
                                shortJobName = data.length > 30 ? data.substring(0, 30) + '...' : data;
                            }

                            return `<td class="expand-row" style="word-break: break-all">
                                <div class="expand-row" style="word-break: break-all">
                                        <span class="td-mini-container">${shortJobName}</span>
                                        <span class="td-full-container hidden">${data}</span>
                                </div>
                                    </td>`;
                        }
                    },
                    {
                        data: 'bug_resolution',
                        name: 'magento_backend_docs.bug_resolution',
                        render: function(data, type, row, meta) {
                            var shortJobName = '';
                            if (data !== null) {
                                shortJobName = data.length > 30 ? data.substring(0, 30) + '...' : data;
                            }

                            return `<td class="expand-row" style="word-break: break-all">
                                <div class="expand-row" style="word-break: break-all">
                                        <span class="td-mini-container">${shortJobName}</span>
                                        <span class="td-full-container hidden">${data}</span>
                                </div>
                                    </td>`;
                        }
                    },
                    {
                        data: 'user.name',
                        name: 'magento_frontend_docs.user_id',
                        render: function(data, type, row, meta) {
                            var userName = '';
                            if (data !== null) {
                                userName = data.length > 30 ? data.substring(0, 30) + '...' : data;
                            }

                            return `<td class="expand-row" style="word-break: break-all">
                                <div class="expand-row" style="word-break: break-all">
                                        <span class="td-mini-container">${userName}</span>
                                        <span class="td-full-container hidden">${data}</span>
                                </div>
                                    </td>`;
                         }
                    },
                    {
                        data: 'created_at',
                        name: 'magento_frontend_docs.created_at',
                        render: function(data, type, row, meta) {
                            var date = '';
                            if (data !== null) {
                                date = data.length > 30 ? data.substring(0, 30) + '...' : data;
                            }

                            return `<td class="expand-row" style="word-break: break-all">
                                <div class="expand-row" style="word-break: break-all">
                                        <span class="td-mini-container">${date}</span>
                                        <span class="td-full-container hidden">${data}</span>
                                </div>
                                    </td>`;
                         }
                    },
                    {
                        render: function(data, type, row, meta) {

                            var del_data = "";
                            <?php if (auth()->user() && auth()->user()->isAdmin()) { ?>
                            del_data =
                                `<button type="button" class="btn btn-xs btn-image load-backend-delete ml-2" data-type="general" data-id="${row['id']}" title="delete"> <img src="/images/delete.png" alt="" style="cursor: default;"> </button>`;
                            <?php } ?>

                        
                            return `<div class="flex justify-left items-center">${del_data} </div>`;
        
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

        $(document).on('change', '.edit_mm', function() {
            var  column = $(this).attr('name');
            var value = $(this).val();
            var data_id = $(this).parents('tr').find('.data_id').val();
            $.ajax({
                type: "POST",
                url: "{{route('magento-backend.update.option')}}",
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
       
        $(document).on('click', '.load-site-category-history', function() {
            var id = $(this).attr('data-id');
            var column = $(this).attr('data-column');
            $.ajax({
                url: '{{ route("magentobackend_category.histories.show") }}',
                dataType: "json",
                data: {
                    id:id,
                    column:column,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.site_developement_new_category ? v.site_developement_new_category.title : ''} </td>
                                        <td> ${v.site_developement_old_category ? v.site_developement_old_category.title : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#category-backend-listing").find(".category-backend-listing-view").html(html);
                        $("#category-backend-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        $(document).on('click', '.load-postman-api-history', function() {
            var id = $(this).attr('data-id');
            var column = $(this).attr('data-column');
            $.ajax({
                url: '{{ route("magentobackend_postman.histories.show") }}',
                dataType: "json",
                data: {
                    id:id,
                    column:column,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.postmanoldrequestapi ? v.postmanoldrequestapi.request_url : ''} </td>
                                        <td> ${v.postmannewrequestapi ? v.postmannewrequestapi.request_url : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#postman-backend-listing").find(".postman-backend-listing-view").html(html);
                        $("#postman-backend-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-module-history ', function() {
            var id = $(this).attr('data-id');
            var column = $(this).attr('data-column');
            $.ajax({
                url: '{{ route("magentobackend_module.histories.show") }}',
                dataType: "json",
                data: {
                    id:id,
                    column:column,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.magneteoldmodule ? v.magneteoldmodule.module : ''} </td>
                                        <td> ${v.magnetenewmodule ? v.magnetenewmodule.module : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#module-backend-listing").find(".module-backend-listing-view").html(html);
                        $("#module-backend-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Store Reark
        function saveRemarks(row_id, selector = 'remark') {
            var remark = $("#"+selector+"_" + row_id).val();

            $.ajax({
                url: `{{ route('magento-backend-remark-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    magento_back_end_id: row_id,
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



        $(document).on('click', '.load-backend-remark', function() {
            var id = $(this).attr('data-id');
            var column = $(this).attr('data-column');
            $.ajax({
                url: '{{ route("magentobackend_remark.histories.show") }}',
                dataType: "json",
                data: {
                    id:id,
                    column:column,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_value ? v.old_value : ''} </td>
                                        <td> ${v.new_value ? v.new_value : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#remark-backend-listing").find(".remark-backend-listing-view").html(html);
                        $("#remark-backend-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });


        //Store Parent folder
        function savedescription(row_id, selector = 'description') {
            var description = $("#"+selector+"_" + row_id).val();
            var val = $("#"+selector+"_" + row_id).val();
            $.ajax({
                url: `{{ route('magento-backend-parent-folder-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    description: description,
                    magento_back_end_id: row_id,
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

        $(document).on('click', '.upload-description-modal', function() {
            let magento_backend_id = $(this).data('id');
            $("#descriptionUploadModal").find('[name="magento_backend_id"]').val(magento_backend_id);
        });

        $(document).on('submit', '#magento_backend_description_upload_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_backend_description_upload_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento-backend-description-upload") }}',
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


    
    //Store Parent folder
    function saveadminConfig(row_id, selector = 'adminConfig') {
            var adminconfig = $("#"+selector+"_" + row_id).val();
            var val = $("#"+selector+"_" + row_id).val();
            $.ajax({
                url: `{{ route('magento-backendadmin-config-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    adminconfig: adminconfig,
                    magento_back_end_id: row_id,
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

        $(document).on('click', '.upload-description-modal', function() {
            let magento_backend_id = $(this).data('id');
            $("#descriptionUploadModal").find('[name="magento_backend_id"]').val(magento_backend_id);
        });

        $(document).on("click", ".upload-child-folder-image-modal", function() {
            let magento_frontend_id = $(this).data('id');
            $("#childImageAddModal").find('[name="magento_frontend_id"]').val(magento_frontend_id);
            $('#childImageAddModal').modal('show');
        });
       
        $(document).on('click', '.load-module-description', function() {
            var id = $(this).attr('data-id');
            var column = $(this).attr('data-column');
            $.ajax({
                url: '{{ route("magentobackend_description.histories.show") }}',
                dataType: "json",
                data: {
                    id:id,
                    column:column,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_value ? v.old_value : ''} </td>
                                        <td> ${v.new_value ? v.new_value : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#description-backend-listing").find(".description-backend-listing-view").html(html);
                        $("#description-backend-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-module-admin_config', function() {
            var id = $(this).attr('data-id');
            var column = $(this).attr('data-column');
            $.ajax({
                url: '{{ route("magentobackend_admin.histories.show") }}',
                dataType: "json",
                data: {
                    id:id,
                    column:column,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_value ? v.old_value : ''} </td>
                                        <td> ${v.new_value ? v.new_value : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#admin-backend-listing").find(".admin-backend-listing-view").html(html);
                        $("#admin-backend-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-backend-delete', function () {
        var id = $(this).attr('data-id');
        if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: '/magento-backend/delete/' + id, // Add a slash before id
                        type: 'DELETE',
                        headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                     },
                        dataType: 'json',
                        success: function(response) {
                            location.reload();
                            toastr["success"](response.message);
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                }
    });

    </script>

@endsection
