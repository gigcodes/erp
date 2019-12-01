@extends('layouts.app')


@section('favicon' , 'broadcast.png')

@section('title', 'Broadcast Info')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style type="text/css">
        .switch {
            position: relative;
            display: inline-block;
            width: 58px;
            height: 27px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider.round {
            border-radius: 36px;
            height: 28px;
            width: 57px;
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
            height: 20px;
            width: 19px;
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

        .show_select{
            display: none;
        }

        }
    </style>
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
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#settingModal">Global Setting</button>
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
                            <div class="pull-right">
                            <form action="{{ route('broadcasts.index') }}" method="GET">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input name="phone_term" type="text" class="form-control phone_global"
                                            value="{{ isset($phone_term) ? $phone_term : '' }}"
                                            placeholder="whatsapp number , broadcast id , remark" id="phone_term">
                                        </div>
                                        <div class="col-md-3">
                                            <div class='input-group date' id='filter-phone-date'>
                                                <input type='text' class="form-control phone_global" name="phone_date" value="{{ isset($date) ? $date : '' }}" placeholder="Date" id="phone_date" />

                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div id="reportrange_phone" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                <input type="hidden" name="phone_customrange" id="custom_phone">
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-1">
                                         <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                                     </div>
                                 </div>
                             </div>
                         </form>
                     </div>
                            <table class="table table-bordered table-striped" id="phone-table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>D. Name</th>
                                    <th>Number</th>
                                    <th>Ttl Cust</th>
                                    <th>Msg/Day</th>
                                    <th>Pend</th>
                                    <th>Last Check</th>
                                    <th>L. Sent</th>
                                    <th>D.O.A</th>
                                    <th>Status</th>
                                    <th>Freq</th>
                                    <th>Send Time</th>
                                    <th>End Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @include('marketing.broadcasts.partials.phone-data')
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="pull-right">
                <button type="button" class="btn btn-secondary" id="select">Select</button>
                <button type="button" class="btn btn-secondary" id="enable">Enable</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#mergeModal">Merge Customers</button>
            </div>
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="customers-table">
            <thead>
            <tr>
                <th class="show_select">Select All</th>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Customer Number</th>
                <th>DND</th>
                <!-- <th>Status</th> -->
                <th>Manual Approval</th>
                <th>Broadcast Sent</th>
                <th>Last Broadcast ID</th>
                <th>Phone No. Assign WhatsApp</th>
                <th>Remarks</th>
            </tr>
            <tr>
                <th class="show_select"><input type="checkbox" class="form-control" id="select_all"></th>
                <th></th>
                <th><input type="text" class="search form-control" id="name"></th>
                <th><input type="text" class="search form-control" id="number"></th>
                <th><select class="form-control search" id="dnd">
                        <option>Select DND Users</option>
                        <option value="0">Active Users</option>
                        <option value="1">DND Users</option>
                    </select></th>
               <!--  <th>
                    <select class="form-control">
                        <option>Asked Price</option>
                        <option>Communication Done Removed</option>
                        <option>Due to not delivered</option>
                        <option>Manual Reject</option>
                    </select>
                </th> -->
                <th>
                    <select class="form-control search" id="manual">
                        <option value="">Select Manual</option>
                        <option value="1">Active</option>
                        <option value="0">All</option>
                    </select>
                </th>
                <th></th>
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
@include('marketing.broadcasts.partials.modal-merge')
@include('marketing.broadcasts.partials.message')
@include('marketing.broadcasts.partials.setting')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript" src="/js/common-helper.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
   
    <script>
        $('.multiselect-2').select2({width:'92%'});
        $('.select-multiple').select2({width: '100%'});

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

            function showMessage(id,number){
                date = $("#date"+id).val();
                    $.ajax({
                    url: "{{ route('broadcast.message.send.list') }}",
                    dataType: "json",
                    data: {
                        number: number,
                        date: date,

                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                    }).done(function (data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#message").empty().html(data.data);
                        $('#sendMessageModal').modal('show');
                    

                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });  
            }


            $('#filter-phone-date').datetimepicker(
            { format: 'YYYY/MM/DD' }).on('dp.change', 
            function (e) 
            { var formatedValue = e.date.format(e.date._f);


                phone_term = $('#phone_term').val();
                phone_date = $('#phone_date').val();


                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        phone_term: phone_term,
                        phone_date: phone_date,

                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#phone-table tbody").empty().html(data.tbody);
                    

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

                 var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#reportrange_phone span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#custom_phone').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
                }

                $('#reportrange_phone').daterangepicker({
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
                    dnd =  $('#dnd').val();
                    number = $('#number').val();
                    broadcast = $('#broadcast').val();
                    manual = $('#manual').val();
                    remark = $('#remark').val();
                    name = $('#name').val();


                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            dnd: dnd,
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

        //resetFilter

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

        //select all checkboxes
        $("#select_all").change(function(){  //"select all" change 
        $(".checkbox_select").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
        });

        //".checkbox" change 
        $('.checkbox_select').change(function(){ 
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if(false == $(this).prop("checked")){ //if this item is unchecked
        $("#select_all").prop('checked', false); //change "select all" checked status to false
        }
        //check "select all" if all checkbox items are checked
        if ($('.checkbox_select:checked').length == $('.checkbox_select').length ){
        $("#select_all").prop('checked', true);
        }
        });


        $(document).ready(
        function() {
            $("#select").click(function() {
            $(".show_select").toggle();
            });
        });

        $("#enable").click(function(){
            val = $('input[name="select"]:checked');
            if(val.length == 0){
                alert('Please Select Customer');
            }else{
                $('input[name="select"]:checked').each(function() {
                    id = this.value;
                    $.ajax({
                        url: "{{ route('broadcast.add.manual') }}",
                        dataType: "json",
                        data: {
                            id: id,
                        },
                        beforeSend: function () {
                            $("#loading-image").show();
                            $("#marketing"+id).prop('checked', true);
                        },
                    }).done(function (data) {
                        $("#loading-image").hide();
                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                });
                alert('Customer Updated');
            }
        });

        
          $(document).ready(function () {
            src = "{{ route('broadcasts.index') }}";
            $(".phone_global").autocomplete({
                source: function (request, response) {
                    phone_term = $('#phone_term').val();
                    phone_date = $('#phone_date').val();


                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            phone_term: phone_term,
                            phone_date: phone_date,

                        },
                        beforeSend: function () {
                            $("#loading-image").show();
                        },

                    }).done(function (data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#phone-table tbody").empty().html(data.tbody);
                        

                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                },
                minLength: 1,

            });
        });
        
    </script>
       <script type="text/javascript" src="/js/common-helper.js"></script>
    <script type="text/javascript">


       

        $('.multiselect-2').select2({width:'92%'});
        $('.select-multiple').select2({width: '100%'});
        
        var siteHelpers = {
            customerSearch : function(ele) {
                ele.select2({
                    tags: true,
                    width : '100%',
                    ajax: {
                        url: '/erp-leads/customer-search',
                        dataType: 'json',
                        delay: 750,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                    },
                    placeholder: 'Search for Customer by id, Name, No',
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    minimumInputLength: 1,
                    templateResult: function (customer) {
                        if (customer.loading) {
                            return customer.name;
                        }

                        if (customer.name) {
                            return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                        }
                    },
                    templateSelection: (customer) => customer.text || customer.name,

                });
            },
            userSearch : function(ele) {
                ele.select2({
                    ajax: {
                        url: '/user-search',
                        dataType: 'json',
                        delay: 750,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                            };
                        },
                        processResults: function (data, params) {

                            params.page = params.page || 1;

                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                    },
                    placeholder: 'Search for User by Name',
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    minimumInputLength: 2,
                    width: '100%',
                    templateResult: function (user) {
                        return user.name;

                    },
                    templateSelection: function (user) {
                        return user.name;
                    },

                });
            },
           loadCustomers : function (ele) {
                var first_customer = $('#first_customer').val();
                var second_customer = $('#second_customer').val();

                if (first_customer == second_customer) {
                    alert('You selected the same customers');

                    return;
                }
                var params = {
                    data : {
                        first_customer: first_customer,
                        second_customer: second_customer
                    },
                    url: "/customers-load",
                    beforeSend : function() {
                        ele.text('Loading...');
                    },
                    doneAjax : function(response) {
                        $('#first_customer_id').val(response.first_customer.id);
                        $('#second_customer_id').val(response.second_customer.id);

                        $('#first_customer_name').val(response.first_customer.name);
                        $('#first_customer_email').val(response.first_customer.email);
                        $('#first_customer_phone').val(response.first_customer.phone ? (response.first_customer.phone).replace(/[\s+]/g, '') : '');
                        $('#first_customer_instahandler').val(response.first_customer.instahandler);
                        $('#first_customer_rating').val(response.first_customer.rating);
                        $('#first_customer_address').val(response.first_customer.address);
                        $('#first_customer_city').val(response.first_customer.city);
                        $('#first_customer_country').val(response.first_customer.country);
                        $('#first_customer_pincode').val(response.first_customer.pincode);

                        $('#second_customer_name').val(response.second_customer.name);
                        $('#second_customer_email').val(response.second_customer.email);
                        $('#second_customer_phone').val(response.second_customer.phone ? (response.second_customer.phone).replace(/[\s+]/g, '') : '');
                        $('#second_customer_instahandler').val(response.second_customer.instahandler);
                        $('#second_customer_rating').val(response.second_customer.rating);
                        $('#second_customer_address').val(response.second_customer.address);
                        $('#second_customer_city').val(response.second_customer.city);
                        $('#second_customer_country').val(response.second_customer.country);
                        $('#second_customer_pincode').val(response.second_customer.pincode);

                        $('#customers-data').show();
                        $('#mergeButton').prop('disabled', false);

                        ele.text('Load Data');
                    },
                };
                siteHelpers.sendAjax(params);
            }
            
        };

        $.extend(siteHelpers, common);

        
       
        siteHelpers.customerSearch($('#first_customer'));
        siteHelpers.customerSearch($('#second_customer'));
        siteHelpers.customerSearch($('#forword_customer'));

        $(".multi_brand_select").change(function() {
            var brand_segment = [];
            $(this).find(':selected').each(function() {
                if ($(this).data('brand-segment') && brand_segment.indexOf($(this).data('brand-segment')) == '-1') {
                  brand_segment.push($(this).data('brand-segment'));
                }
            })
            $(this).closest('form').find(".brand_segment_select").val(brand_segment).trigger('change');
        });

        $('#customer-search').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: '/erp-leads/customer-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].name ? data[i].name : data[i].text;
                    }

                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for Customer by id, Name, No',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {
                if (customer.loading) {
                    return customer.name;
                }

                if (customer.name) {
                    return "<p> " + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,

        });

        $('.select-instruction-search').select2({
            ajax: {
                width : "100%",
                url: '/erp-customer/instruction-search/',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    searhData = [];
                    $.each(data, function(i, value){
                        searhData.push({id:value.instruction, name:value.instruction});
                    })
                    params.page = params.page || 1;

                    return {
                        results: searhData,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for User by Name',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 2,
            width: '100%',
            templateResult: function (instruction) {
                return instruction.name;

            },
            templateSelection: function (instruction) {
                return instruction.name;
            }
        });

        var all_customers = [];
        <?php if(request()->get('all_customer') != '1') { ?>
            setTimeout(function(){siteHelpers.autoRefreshColumn();}, 15000);
        <?php } ?>

        $('#schedule-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });

        $('.dd-datepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });

     

        $(document).on('click', '.load-customers', function () {
            siteHelpers.loadCustomers($(this));
        });

    
      function updateCustomer(id){
        phone = $("#phone"+id).val();

        $.ajax({
                url: "/customer/"+id+"/updatePhone",
                type: 'POST',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function () {
                    $("#loading-image").hide();
                    alert('Number Updated SucessFully');
                },
                error: function() {
                    $("#loading-image").hide();
                    alert('Customer Number Already Exist');
                },
                data: {
                    phone: phone,
                     _token: "{{ csrf_token() }}",
                }
            });
        };

        function showBroadcast(id){
            
            $.ajax({
                url: "{{ route('broadcast.customer.list') }}",
                type: 'POST',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    $("#broadcast"+id).hide();
                    $("#broadcastList"+id).empty().append(response.data);
                    $("#broadcastList"+id).show();
                },
                data: {
                    id: id,
                     _token: "{{ csrf_token() }}",
                }
            });
        };


        function hideBroadcastList(id){
            $("#broadcast"+id).show();
            $("#broadcastList"+id).hide();
        }
        
    </script>
@endsection
