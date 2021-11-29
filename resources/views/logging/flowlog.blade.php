@extends('layouts.app')

@section('title', 'Flow Log List')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Flow Logs</h2>
             <div class="pull-right">
                <a href="/logging/live-laravel-logs" type="button" class="btn btn-secondary">Live Logs</a>
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th style="width:7%">ID</th>
                <th width="10%">Flow Name</th>
                <th width="25%">Message</th>
                <th width="10%">LogCreated</th>
                <th width="10%">Action</th>
            </tr>
            <tr>
                
                <th width="10%"><input type="text" name="flow_name" class="search form-control" id="flow_name"></th>
                <th width="10%"><input type="text"  name="message" class="search form-control" id="message"></th>
                <th> <div class='input-group' id='log-created-date'>
                        <input type='text' class="form-control " name="created_at" value="" placeholder="Date" id="created-date" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                </th>
                <th width="10%"><input type="text" class="search form-control" id="action"></th>
            </tr>
            </thead>

            <tbody id="content_data">
             @include('logging.partials.flowlogdata')
            </tbody>

            {!! $logs->render() !!}

        </table>
    </div>
    <div id="ErrorLogModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Flow Log Detail</h4>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                    
                  <th style="width:7%">Flow Action</th>
                  <th style="width:6%">Modal Type </th>
                  <th style="width:30%">Leads</th>
                  <th style="width:30%">Message</th>
                  <th style="width:15%">Website</th>
                  <th style="width:10%">Date</th>
                </thead>
                <tbody class="error-log-data">
    
                </tbody>
              </table>
    
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <script type="text/javascript">



    //Ajax Request For Search
    $(document).ready(function () {
          
        $(document).on("click", ".show_error_logs", function() {
            var id = $(this).data('id');
            $.ajax({
            method: "GET",
            url: "{{ route('logging.flow.detail') }}" ,
            data: {
                "_token": "{{ csrf_token() }}",
                "id" : id,
            },
            dataType: 'html'
            })
            .done(function(result) {
            $('#ErrorLogModal').modal('show');
            $('.error-log-data').html(result);
            });

        });

        //Filter by date
        count = 0;
        $('#created-date').datetimepicker(
            { format: 'YYYY/MM/DD' }).on('dp.change', 
            function (e) 
            {
            if(count > 0){    
             var formatedValue = e.date.format(e.date._f);
                created_at = $('#created_date').val();
                flow_name = $('#flow_name').val();
                message = $('#message').val();
                src = "{{ route('logging.flow.log') }}";
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        created_at : created_at,
                        flow_name : flow_name,
                        message : message,
                      
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                $("#content_data").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                    

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });  

            } 
            count++;       
            });

            
            count = 0;
        $('#updated-date').datetimepicker(
            { format: 'YYYY/MM/DD' }).on('dp.change', 
            function (e) 
            {
            if(count > 0){    
             var formatedValue = e.date.format(e.date._f);
                created = $('#created_date').val();
                updated = $('#updated_date').val();
                filename = $('#filename').val();
                log = $('#log').val();
                website = $('#website').val();

                 src = "{{ route('logging.laravel.log') }}";
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        created : created,
                        updated : updated,
                        filename : filename,
                        log : log,
                        website : website,

                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                $("#content_data").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                    

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });  

            } 
            count++;       
            });

        logcount = 0;
        $('#log-created-date').datetimepicker(
            { format: 'YYYY/MM/DD' }).on('dp.change', 
            function (e) 
            {
            if(logcount > 0){    
             var formatedValue = e.date.format(e.date._f);
                created = $('#created_date').val();
                updated = $('#updated_date').val();
                log_created = $('#log_created').val();

                filename = $('#filename').val();
                log = $('#log').val();
                website = $('#website').val();
                moduleName = $('#moduleName').val();
                controllerName = $('#controllerName').val();
                action = $('#action').val();

                 src = "{{ route('logging.laravel.log') }}";
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        created : created,
                        updated : updated,
                        filename : filename,
                        log : log,
                        log_created : log_created,
                        website : website,
                        modulename : moduleName,
                        controllername : controllerName,
                        action : action,

                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                $("#content_data").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                    

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });  

            } 
            logcount++;       
            });


        //Search    
        src = "{{ route('logging.flow.log') }}";
        $(".search").autocomplete({
        source: function(request, response) {
            message = $('#message').val();
            flow_name = $('#flow_name').val();
         
            
           $.ajax({
                url: src,
                dataType: "json",
                data: {
                    message : message,
                    flow_name : flow_name,
                 
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $("#log-table tbody").empty().html(data.tbody);
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
         src = "{{ route('logging.flow.log') }}";
         function refreshPage() {
             blank = ''
             $.ajax({
                url: src,
                dataType: "json",
                data: {
                    blank : blank
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $("#log-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
         }

 

         function sortByDateCreated() {
            orderCreated = $('#header-created').val();
            filename = $('#filename').val();
            log = $('#log').val();
            website = $('#website').val();

            src = "/scrap/scraped-urls";
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                   filename : filename,
                    log : log,
                    website : website,
                    orderCreated : orderCreated,
                },
                beforeSend: function () {
                    if(orderCreated == 0){
                        $('#header-created').val('1');
                    }else{
                        $('#header-created').val('0');
                    }
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
            $("#content_data").empty().html(data.tbody);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
                

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });  

         }

         
         function sortByDateUpdated() {
            orderUpdated = $('#header-updated').val();
            filename = $('#filename').val();
            log = $('#log').val();
            website = $('#website').val();

            src = "/scrap/scraped-urls";
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    filename : filename,
                    log : log,
                    website : website,
                    orderUpdated : orderUpdated,
                },
                beforeSend: function () {
                    if(orderUpdated == 0){
                        $('#header-updated').val('1');
                    }else{
                        $('#header-updated').val('0');
                    }
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
            $("#content_data").empty().html(data.tbody);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
                

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });  

         }
    </script>
@endsection