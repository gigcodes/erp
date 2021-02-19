@extends('layouts.app')

@section('title', 'Scraper Log List')

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
<div class="container-fluid">
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scraper Logs ( {{ $scraperLogs->total() }})</h2>
             <div class="pull-right">
                <button type="button" class="btn btn-image" onclick="location.reload()"><img src="/images/resend2.png" /></button>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel-body">
                    <div class="row">
                        <form id="message-fiter-handler" action="{{ route('log-scraper.index') }}" method="GET">
                            <div class="pull-left">
                                <div class="form-group">
                                    @php
                                    if(isset($customrange)){
                                         $range = explode(' - ', $customrange);
                                            $from = \Carbon\Carbon::parse($range[0])->format('F d, Y'); 
                                            $to = \Carbon\Carbon::parse(end($range))->format('F d, Y'); 
                                        }
                                    @endphp    
                                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <input type="hidden" name="customrange" id="custom" value="{{ isset($customrange) ? $customrange : '' }}">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span @if(isset($customrange)) style="display:none;" @endif id="date_current_show"></span> <p style="display:contents;" id="date_value_show"> {{ isset($customrange) ? $from .' '.$to : '' }}</p><i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                        <select class="form-control" name="is_external_scraper">
                                            <option value="">Select type of logs</option>
                                            <option value="" >All</option>
                                            <option value="1">External Scrapper only</option>
                                        </select>
                                </div>
                            </div>  
                            <div class="pull-right">
                                <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-filter-report">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                                <button style="float:right;" data-toggle="collapse" href="#pending-error-list"  class="btn btn-secondary">
                                    Show Errors
                                </button>
                            </div>
                        </form>
                    </div>  
                    <div id="pending-error-list" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                        <div class="card card-body">
                          <?php if(!empty($logsByGroup)) { ?>
                            <div class="row col-md-12">
                                <?php foreach($logsByGroup as $logsBy) { ?>
                                  <div class="col-md-2">
                                        <div class="card">
                                          <div class="card-header">
                                            <?php echo $logsBy->website; ?>
                                          </div>
                                          <div class="card-body">
                                              <?php echo $logsBy->total_error; ?>
                                          </div>
                                      </div>
                                   </div> 
                              <?php } ?>
                            </div>
                          <?php } else  { echo "Sorry , No data available"; } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th>Id</th>
                <th>Ip address</th>
                <th>Website</th>
                <th>Url</th>
                <th>Sku</th>
                <th>Original sku</th>
                <th>Title</th>
                <th>Validation result</th>
                <th>Size</th>
                <th>Composition</th>
                <th>Country</th>
                <th>Supplier</th>        
                <th>Created at</th>
            </tr>
            <tr>
                <th><input type="text" class="search form-control filter-serach-string" data-id="id"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="ip_address"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="website"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="url"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="sku"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="original_sku"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="title"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="validation_result"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="size"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="composition"></th>

                <th><input type="text" class="search form-control filter-serach-string" data-id="country"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="supplier"></th>
                <th></th>
            </tr>
            </thead>

            <tbody id="content_data" class="infinite-scroll">
                @include('logging.partials.scraper-logs')
            </tbody>

            {!! $scraperLogs->render() !!}

        </table>
    </div>
</div>


@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript">

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#custom').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        }

        var start = moment().subtract(29, 'days');
        var end = moment();

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
        }, cb);

        cb(start, end);

        var callResult = function(url,sendingPost,append) {
            $.ajax({
                url: url,
                dataType: "json",
                data: sendingPost,
                beforeSend: function() {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                
                if(append) {
                    $("#log-table tbody").append(data.tbody);
                }else{
                    $("#log-table tbody").empty().html(data.tbody);
                }

                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        };

         $(".search").autocomplete({
            source: function(request, response) {
                var fields = $(".filter-serach-string");
                    var sendingPost = {};
                    $.each(fields, function(k,v){
                        sendingPost[$(v).data("id")] = $(v).val();
                    });
                    callResult("{{ route('log-scraper.index') }}",sendingPost,false);
            },
            minLength: 1,
        });

        $(window).scroll(function() {
            if($(window).scrollTop() >= ($(document).height() - $(window).height() - 5)) {
               $(".pagination").find(".active").next().find("a").trigger("click");
            }
        });

        //initialize pagination
        $(document).on("click",".page-link",function(e) {
            e.preventDefault();
            var activePage = $(this).closest(".pagination").find(".active").text();
            var clickedPage = $(this).text();
            var append = true;
            if(clickedPage == "‹" || clickedPage < activePage) {
                $('html, body').animate({scrollTop: ($(window).scrollTop() - 500) + "px"}, 200);
                append = false;
            }
            
            callResult($(this).attr("href"),{},append);
        });


    </script>
@endsection
