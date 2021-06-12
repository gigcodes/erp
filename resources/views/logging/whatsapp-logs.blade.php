@extends('layouts.app')

@section('title', 'SKU log')

@section("styles")
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 48;
        }

        input {
            width: 100px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading"> Whatsapp Logs (<span id="count">{{ count($array) }}</span>)</h2>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" onclick="sendMulti()" style="display: none;" id="nulti">
                    Send Selected
                </button>
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png"/>
                </button>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row">
        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel mt-5 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#collapse1">Pending Issues</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="pull-right">
                                <form action="{{ route('broadcasts.index') }}" method="GET">
                                    <div class="form-group">
                                        <div class="row">

                                        </div>
                                    </div>
                                </form>
                            </div>
                            <table class="table table-bordered table-striped" id="phone-table">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>User</th>
                                    <!-- <th>Count</th> -->

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($array as $row)
                                    <tr>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th style="width: 20% !important;">Date</th>
                <th style="width: 20% !important;">Sent ?</th>
                <th style="width: 40% !important;">Log</th>
                <th>Action</th>
            </tr>
            <tr>
            </tr>
            </thead>
            <tbody id="content_data">
            @foreach($array as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['type'] == 1 ? 'Yes' : 'No'}}</td>
                    @if($row['type'] == 1)
                        <td>
                            Receiver No. : {{ $row['number']}} <br>
                            ID : {{ $row['id']}} <br>
                            Message : {{$row['message']}}
                        </td>
                    @elseif($row['type'] == 2 || $row['type'] == 3)
                        <td class="errorLog">
                            Message1 : {{$row['error_message1']}} <br>
                            Message2 : {{$row['error_message2']}}
                        </td>
                    @elseif($row['type'] == 4)
                        <td>
                            Message : {{$row['error_message1']}}
                        </td>
                    @endif
                    <td>
                        <button class="btn btn-success sentMessage text-center" {{$row['type'] == 1 ? 'disabled' : ""}}>
                            Resend
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modals.task-module')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript">


        $(document).on('click', '.sentMessage', function () {

            var msg = $(this).parents('tr').find('.errorLog').text();


            var myStr = msg;
            var matches = myStr.match(/\[(.*?)\]/);
            var submatch = ''
            if (matches) {
                submatch = matches[1];
            }

            var chat_id = null;
            submatch = '{"number":"$number","whatsapp_number":"$sendNumber","message":"$text","validation":"$validation","chat_message_id":"1786391"}'
            if (submatch !== '') {
                var json = JSON.parse(submatch)
                if (typeof json.chat_message_id !== 'undefined') {
                    chat_id = json.chat_message_id;
                }
            }


            console.log(json.chat_message_id);

            if (chat_id !== null) {

                $.ajax({
                    url: 'whatsapp/' + chat_id + '/resendMessage',
                    dataType: "json",
                    type: 'post',
                }).done(function (data) {

                    console.log(data);

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                });

            }


        })


        $(document).ready(function () {
            $(".select-multiple").multiselect();
            $(".select-multiple2").select2();
        });

        $(document).ready(function () {
            $('#sku,#category').on('blur', function () {
                $.ajax({
                    url: '/logging/sku-logs-errors',
                    dataType: "json",
                    data: {
                        sku: $('#sku').val(),
                        brand: $('#brand').val(),
                        category: $('#category').val()
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                }).done(function (data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#count").text(data.totalFailed);
                    $("#log-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    alert('No response from server');
                });
            });

            $('#brand,#category,#supplier,#custom').on('change', function () {
                $.ajax({
                    url: '/logging/sku-logs-errors',
                    dataType: "json",
                    data: {
                        sku: $('#sku').val(),
                        brand: $('#brand').val(),
                        category: $('#category').val(),
                        supplier: $('#supplier').val(),
                        custom: $('#custom').val(),
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                }).done(function (data) {
                    $("#loading-image").hide();
                    $("#nulti").show();
                    console.log(data);
                    $("#count").text(data.totalFailed);
                    $("#log-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    alert('No response from server');
                });
            });


        });

        function refreshPage() {
            blank = '';
            $.ajax({
                url: '/logging/sku-logs-errors',
                dataType: "json",
                data: {
                    blank: blank
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                $("#nulti").show();
                console.log(data);
                $("#count").text(data.totalFailed);
                $("#log-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
        }

        function addTask(supplier, category, sku, brand) {
            $('#taskModal').modal('show');
            $('#task_subject').val('Supplier :' + supplier + ' Category ' + category + ' Brand : ' + brand);
            $('#references').val(supplier + '' + category + '' + brand);
        }

        $(".checkbox").change(function () {
            if (this.checked) {
                validate = 1;
                $.ajax({
                    url: '/logging/sku-logs-errors',
                    dataType: "json",
                    data: {
                        sku: $('#sku').val(),
                        brand: $('#brand').val(),
                        category: $('#category').val(),
                        supplier: $('#supplier').val(),
                        validate: validate,
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                }).done(function (data) {
                    $("#loading-image").hide();
                    $("#nulti").show();
                    console.log(data);
                    $("#count").text(data.totalFailed);
                    $("#log-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    alert('No response from server');
                });

            } else {
                validate = 2;
                $.ajax({
                    url: '/logging/sku-logs-errors',
                    dataType: "json",
                    data: {
                        sku: $('#sku').val(),
                        brand: $('#brand').val(),
                        category: $('#category').val(),
                        supplier: $('#supplier').val(),
                        validate: validate,
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                }).done(function (data) {
                    $("#loading-image").hide();
                    $("#nulti").show();
                    console.log(data);
                    $("#count").text(data.totalFailed);
                    $("#log-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    alert('No response from server');
                });

            }
        });

        function sendMulti() {
            brand = $('#brand').val();
            category = $('#category').val();
            supplier = $('#supplier').val();
            if (brand == '') {
                alert('Please Select Brand');
            }
            if (category == '') {
                alert('Please Select Category');
            }
            if (supplier == '') {
                alert('Please Select Supplier');
            }
            if (brand != '' && category != '' && supplier != '') {
                $('#taskModal').modal('show');
                $('#task_subject').val(supplier + ' ' + category + ' multi');
                $('#references').val(supplier + '' + category + '' + brand);
            }

        }

        function changeOrder() {
            order = $('#order_count').val();
            $.ajax({
                url: '/logging/sku-logs-errors',
                dataType: "json",
                data: {
                    sku: $('#sku').val(),
                    brand: $('#brand').val(),
                    category: $('#category').val(),
                    supplier: $('#supplier').val(),
                    order: order,
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                $("#nulti").show();
                if (order == 1) {
                    $('#order_count').val('0');
                } else {
                    $('#order_count').val(1);
                }
                $("#count").text(data.totalFailed);
                $("#log-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
        }

    </script>
@endsection