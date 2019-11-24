@extends('layouts.app')

@section('styles')
@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style type="text/css">
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }


        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .dot {
            height: 10px;
            width: 10px;
            background-color: green;
            border-radius: 50%;
            display: inline-block;
        }

        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        }
    </style>
@endsection
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Broadcast List</h2>
            <div class="pull-left">
                <form action="{{ route('broadcasts.index') }}" method="GET">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <input name="term" type="text" class="form-control global"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="whatsapp number , broadcast id , remark" id="term">
                            </div>
                            <div class="col-md-3">
                                <div class='input-group date' id='filter-date'>
                                    <input type='text' class="form-control global" name="date" value="{{ isset($date) ? $date : '' }}" placeholder="Date" id="date" />

                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <input type="hidden" name="customrange" id="custom">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                            </div>
     
                            <div class="col-md-1">
                                <button type="button" class="btn btn-image" id="resetFilter"><img src="/images/resend2.png"/></button>    
                            </div>
                            <div class="col-md-1">
                               <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary">Total Customers : {{ $totalCustomers }}</button>
                <button type="button" class="btn btn-secondary">DND Customers : {{ $countDNDCustomers }}</button>
                <button type="button" class="btn btn-secondary">First Broadcast Send : {{ $customerBroadcastSend }}</button>
                <button type="button" class="btn btn-secondary">First Broadcast Pending : {{ $customerBroadcastPending }}</button>
            </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
      <div class="row">
        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel mt-5 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#collapse1">WhatsApp Numbers</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>ID</th>
                                    <th>Number</th>
                                    <th>Total Customers</th>
                                    <th>Message Send Per Day</th>
                                    <th>Last Check</th>
                                    <th>Last Send</th>
                                    <th>Status</th>
                                </tr>
                                @foreach($numbers as $number)
                                    <tr>
                                        <td>{{ $number->id }}</td>
                                        <td>{{ $number->number }}</td>
                                        <td>{{ $number->customer()->count() }}</td>
                                        <td>{{ $number->imQueueCurrentDateMessageSend->count() }}</td>
                                        <td>{{ $number->last_online }}</td>
                                        <td> @if(isset($number->imQueueLastMessageSend)) @if($number->imQueueLastMessageSend->send_after == '2002-02-02 02:02:02') Message Failed @else Send SucessFully @endif @endif</td>
                                        <td>@if($number->status == 0) InActive @else Active @endif</td>
                                        
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="customers-table">
            <thead>
            <tr>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>DND</th>
                <th>Status</th>
                <th>Manual Approval</th>
                <th>Last Broadcast ID / D.Y.N</th>
                <th>Phone No. Assign WhatsApp</th>
                <th>Remarks</th>
            </tr>
            <tr>
                <th></th>
                <th><input type="text" class="search form-control" id="name"></th>
                <th></th>
                <th>
                    <select class="form-control">
                        <option>Asked Price</option>
                        <option>Communication Done Removed</option>
                        <option>Due to not delivered</option>
                        <option>Manual Reject</option>
                    </select>
                </th>
                <th>
                    <select class="form-control search" id="manual">
                        <option value="">Select Manual</option>
                        <option value="1">Active</option>
                        <option value="0">All</option>
                    </select>
                </th>
                <th><input type="text" class="search form-control" id="broadcast"></th>
                <th><select class="form-control search" id="number">
                        <option value="">Select Option</option>
                        @foreach($numbers as $number)
                            <option value="{{ $number->number }}">{{ $number->number }}</option>
                        @endforeach
                    </select></th>
                <th><input type="text" class="search form-control" id="remark"></th>
            </tr>
            </thead>

            <tbody>
            @include('marketing.broadcasts.partials.data')

            {!! $customers->render() !!}
            @include('marketing.broadcasts.partials.remark')
            </tbody>
        </table>
        {!! $customers->render() !!}
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
       
        $('#filter-date').datetimepicker(
            { format: 'YYYY/MM/DD' }).on('dp.change', 
            function (e) 
            { var formatedValue = e.date.format(e.date._f);


                term = $('#term').val();
                date = $('#date').val();


                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        term: term,
                        date: date,

                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#customers-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });  

            });


            $(function() {

                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#custom').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                     'Today': [moment(), moment()],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                 }
             }, cb)
                cb(start, end);

            });
    </script>
    <script type="text/javascript">
        $(".checkbox").change(function () {
            id = $(this).val();

            if (this.checked) {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('broadcast.add.dnd') }}',
                    data: {
                        id: id,
                        type: 1,
                    }, success: function (data) {
                        console.log(data);
                        if (data.status == 'error') {
                            alert('Something went wrong');
                        } else {
                            alert('Customer Added to DND');

                        }

                    },
                    error: function (data) {
                        alert('Something went wrong');
                    }
                });
            } else {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('broadcast.add.dnd') }}',
                    data: {
                        id: id,
                        type: 0
                    },
                }).done(response => {
                    alert('Customer Removed From DND');
                });
            }
        });

        $(".checkboxs").change(function () {
            id = $(this).val();

            if (this.checked) {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('broadcast.add.manual') }}',
                    data: {
                        id: id,
                        type: 1,
                    }, success: function (data) {
                        console.log(data);
                        if (data.status == 'error') {
                            alert('Something went wrong');
                        } else {
                            // alert('Customer Added to Broadcastlist');
                        }

                    },
                    error: function (data) {
                        alert('Something went wrong');
                    }
                });
            } else {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('broadcast.add.manual') }}',
                    data: {
                        id: id,
                        type: 0
                    },
                }).done(response => {
                    //alert('Customer Removed From Broadcastlist');
                });
            }
        });

        $(document).on('click', '.make-remarks', function (e) {
            e.preventDefault();

            var id = $(this).data('id');
            $('.id').val(id);
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('broadcast.gets.remark') }}',
                data: {
                    id: id,
                },
            }).done(response => {
                var html = '';

                $.each(response, function (index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></p>';
                    html + "<hr>";
                });
                $("#makeRemarksModal").find('#remarks-list').html(html);
            });
        });

        $('#addRemarksButton').on('click', function () {
            var id = $('.id').val();
            var remark = $('.remark').val();
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('broadcast.add.remark') }}',
                data: {
                    id: id,
                    remark: remark,
                },
            }).done(response => {
                $('.add-remarks').find('textarea[name="remark"]').val('');

                var html = ' <p> ' + remark + ' <br> <small>By You updated on ' + moment().format('DD-M H:mm') + ' </small></p>';

                $("#makeRemarksModal").find('#remarks-list').append(html);
            }).fail(function (response) {
                console.log(response);

                alert('Could not fetch remarks');
            });
        });

        $('.whatsapp').on('change', function () {
            number = this.value;
            id = $(this).data("id");
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('broadcast.update.whatsappnumber') }}',
                data: {
                    id: id,
                    number: number,
                },
            }).done(response => {
                alert('WhatsApp number updated');
            }).fail(function (response) {
                alert('Something went wrong');
            });

        });

        $(document).ready(function () {
            src = "{{ route('broadcasts.index') }}";
            $(".global").autocomplete({
                source: function (request, response) {
                    term = $('#term').val();
                    date = $('#date').val();


                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            term: term,
                            date: date,

                        },
                        beforeSend: function () {
                            $("#loading-image").show();
                        },

                    }).done(function (data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#customers-table tbody").empty().html(data.tbody);
                        if (data.links.length > 10) {
                            $('ul.pagination').replaceWith(data.links);
                        } else {
                            $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                        }

                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                },
                minLength: 1,

            });
        });

        $(document).ready(function () {
            src = "{{ route('broadcasts.index') }}";
            $(".search").autocomplete({
                source: function (request, response) {
                    number = $('#number').val();
                    broadcast = $('#broadcast').val();
                    manual = $('#manual').val();
                    remark = $('#remark').val();
                    name = $('#name').val();


                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            number: number,
                            broadcast: broadcast,
                            manual: manual,
                            remark: remark,
                            name: name,

                        },
                        beforeSend: function () {
                            $("#loading-image").show();
                        },

                    }).done(function (data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#customers-table tbody").empty().html(data.tbody);
                        if (data.links.length > 10) {
                            $('ul.pagination').replaceWith(data.links);
                        } else {
                            $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                        }

                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                },
                minLength: 1,

            });
        });

        resetFilter

        $("#resetFilter").click(function(){
            src = "{{ route('broadcasts.index') }}";
            reset = '';
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    reset: reset,


                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                console.log(data);
                $("#customers-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        });
    </script>
@endsection
