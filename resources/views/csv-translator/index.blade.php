@extends('layouts.app')

@section('large_content')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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

        #csvData-table {
            width: 100% !important;
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

@if ($message = Session::get('message'))
<div class="alert alert-success mt-3">
  <p>{{ $message }}</p>
</div>
@endif

<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">Csv Translator Languages List</h2>
    </div>
</div>
<div class="float-end">
    <button id="OpenImgUpload" class="btn btn-primary">Import CSV</button>

    <input type="file" class="d-none" id="imgupload" />

</div>
<div class="table-responsive mt-3" style="margin-top:20px;">
    <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="csvData-table">
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
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    $('#OpenImgUpload').click(function() {
        $('#imgupload').trigger('click');
    });

    $('#imgupload').on('change', function() {
        var files = $('#imgupload')[0].files;
        debugger

        if (files.length > 0) {
            var fd = new FormData();
            fd.append('file', files[0]);
            fd.append('_token', "{{ csrf_token() }}");

            $.ajax({
                url: "{{ route('csv-translator.uploadFile') }}",
                method: 'post',
                data: fd,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status == 200) {
                      window.location.reload();

                    }
                }
            })
        }
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
                "url": "{{ route('csv-translator.list') }}",
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
