@extends('layouts.app')

@section('large_content')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>

    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        tr th {
            width: 20px;
        }

        .nav-item a {
            color: #555;
        }

        a.btn-image {
            padding: 2px 2px;
        }

        .text-nowrap {
            white-space: nowrap;
        }

        .search-rows .btn-image img {
            width: 12px !important;
        }

        .search-rows .make-remark {
            border: none;
            background: none
        }

        .table-responsive select.select {
            width: 110px !important;
        }

        @media (max-width: 1280px) {
            table.table {
                width: 0px;
                margin: 0 auto;
            }

            /** only for the head of the table. */
            table.table thead th {
                padding: 10px;
            }

            /** only for the body of the table. */
            table.table tbody td {
                padding: 10 px;
            }

            .text-nowrap {
                white-space: normal !important;
            }
        }
    </style>
@endsection

<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">Csv Translator Languages List</h2>
    </div>
</div>
<div class="row">
@if(session()->has('success'))
    <div class="alert alert-success w-100">
        {{session()->get('success')}}
    </div>
    @endif

</div>
<div class="float-right">
    <button data-toggle="modal" data-target="#csv_import_model" class="btn btn-primary btn_import">Import CSV</button>
    <a class="btn btn-secondary btn_export" href="{{ route('csvTranslator.export') }}" target="_blank">Export CSV</a>
    <a class="btn btn-info btn_select_user" data-toggle="modal" data-target="#permissions_model">Permission</a>
</div>
<div class="table-responsive mt-3" style="margin-top:20px;">
    <table class="table table-bordered text-wrap" style="border: 1px solid #ddd;" id="csvData-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Keyword</th>
                <th>En</th>
                <th>ES</th>
                <th>RU</th>
                <th>KO</th>
                <th>JA</th>
                <th>IT</th>
                <th>DE</th>
                <th>FR</th>
                <th>NL</th>
                <th>ZH</th>
                <th>AR</th>
                <th>UR</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="pagination-custom">

    </div>
</div>

<div class="modal fade" id="permissions_model" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Edit Permission</h4>
            </div>
            <div class="modal-body">
                <form class="permission_form">
                    <div class="form-group">
                        <label>Select User :</label>
                        <select class="form-control" id="selectUserId" name="user">
                            <option>Select</option>
                            @foreach (App\User::where('is_active', '1')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Lanuage</label>
                        <select class="form-control" name="lang" id="selectLangId">
                            <option>Select</option>
                            <option value="en">EN</option>
                            <option value="es">ES</option>
                            <option value="ru">RU</option>
                            <option value="ko">KO</option>
                            <option value="ja">JA</option>
                            <option value="it">IT</option>
                            <option value="de">DE</option>
                            <option value="fr">FR</option>
                            <option value="nl">NL</option>
                            <option value="zh">ZH</option>
                            <option value="ar">AR</option>
                            <option value="ur">UR</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-submit-form" data-dismiss="modal">Add
                    Permission</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="csv_import_model" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Upload Csv</h4>
            </div>
            <div class="modal-body">
                <form action="#"  class="dropzone" id="my-dropzone">
                    @csrf
                </form>
                <div class="alert alert-success d-none success-alert">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="edit_model" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        <form method="post" class="form-update" action="{{route('csvTranslator.update')}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Update Value</h4>
            </div>
            <div class="modal-body edit_model_body">
              
                    @csrf
                    <input type="text" name="update_record" class="form-control update_record" />
                    <div class="d-none add_hidden_data"></div>
                   
              
            </div>
            <div class="modal-footer">
            <input type="submit" value="update" name="update" class="btn btn-primary" />
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>

@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    $(document).on('click', '.btn_import', function() {
        var myDropzone = new Dropzone("form#my-dropzone", {
            url: "{{ route('csvTranslator.uploadFile') }}"
        });
        myDropzone.on('complete', function() {
            $(".success-alert").removeClass('d-none');
            $(".success-alert").addClass('mt-2');
            $(".success-alert").text('Successfully Imported');
            setTimeout(function() {
                $("#csv_import_model").modal('hide');
                window.location.reload();
            }, 500);
        })
    });
    var userId;
    var langId;


    var oTable;
    $(document).ready(function() {
        oTable = $('#csvData-table').DataTable({
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            sScrollX: true,
            searching: false,
            targets: 'no-sort',
            bSort: false,
            ajax: {
                "url": "{{ route('csvTranslator.list') }}",
                data: function(d) {

                },
            },
            columnDefs: [{
                targets: [],
                orderable: false,
                searchable: false,
            }],
            columns: [{
                    data: 'id',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'key',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'en',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "en") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.en +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }

                    }
                },
                {
                    data: 'es',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "es") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.es +
                                '  data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'ru',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "ru") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.eu +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'ko',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "ko") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.ko +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'ja',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "ja") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.ja +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'it',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "it") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.it +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'de',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "de") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.de +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'fr',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "fr") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.fr +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'nl',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "nl") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.nl +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'zh',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "zh") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.zh +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'ar',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "ar") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.ar +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'ur',
                    render: function(data, type, row, meta) {
                        if (userId != null && langId === "ur") {
                            return data + ' <a href="#" class="editbtn_model" data-lang=' +
                                langId + ' data-user=' + userId + ' data-id=' + row.id +
                                ' data-value=' +
                                row.ur +
                                ' data-toggle="modal" data-target="#edit_model"> <i class="fa fa-pencil"></i> </a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'status',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }

            ],
        });
    });
    $(document).on('click', ".btn-submit-form", function() {
        userId = $("#selectUserId").val();
        langId = $("#selectLangId").val();
        oTable.clear().draw();
    });


    $(document).on('click', ".editbtn_model", function() {
        var id = $(this).data('id');
        var formValue = $(this).data('value');
        var userId = $(this).data('user');
        var langId = $(this).data('lang');
        $(".update_record").val(formValue);
        let html = `<input type="hidden" name="update_by_user_id" value='`+userId+`'>
        <input type="hidden" name="lang_id" value='`+langId+`'>
        <input type="hidden" name="record_id" value='`+id+`'>`;
        $(".add_hidden_data").html(html);
        
    });
</script>
@endsection
