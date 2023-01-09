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
<div class="float-right">
    <button data-toggle="modal" data-target="#csv_import_model" class="btn btn-primary btnImport">Import CSV</button>
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="pagination-custom">

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
                <form action="#" class="dropzone" id="my-dropzone">
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

@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    $(document).on('click', '.btnImport', function() {
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
            sScrollX: false,
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
                searchable: false
            }],
            columns: [{
                    data: 'id',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }, {
                    data: 'key',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'en',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'es',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'ru',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'ko',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'ja',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'it',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'de',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'fr',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'nl',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'zh',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'ar',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'ur',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'status',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    data: 'action',
                    render: function(data, type, row, meta) {
                        return '<a href="#"> <i class="fa fa-pencil"></i></a>';
                    }
                },

            ],
        });
    });
</script>
@endsection
