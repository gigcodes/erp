@extends('layouts.app')

@section('favicon' , 'supplierstats.png')

@section('title', 'Scrape Brand')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style type="text/css">
        .dis-none {
            display: none;
        }

        #remark-list li {
            width: 100%;
            float: left;
        }

        .fixed_header {
            table-layout: fixed;
            border-collapse: collapse;
        }

        .fixed_header tbody {
            width: 100%;
            overflow: auto;
            height: 250px;
        }

        .fixed_header thead {
            background: black;
            color: #fff;
        }
        .modal-lg{
            max-width: 1500px !important; 
        }

        .remark-width{
            white-space: nowrap;
            overflow-x: auto;
            max-width: 20px;
        }
    </style>
@endsection

@section('large_content')
        @php
            $user = auth()->user();
            $isAdmin = $user->isAdmin();
            $hod = $user->hasRole('HOD of CRM');
        @endphp

    <div class="row mb-5">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scrapped Brand <span class="total-info-"></span></h2>
        </div>
    </div>

    @include('partials.flash_messages')
    <?php $status = request()->get('status', ''); ?>
    <?php $excelOnly = request()->get('excelOnly', ''); ?>
    <form class="" action="/scrap/scrap-brand">
        <div class="row">
            <div class="form-group mb-3 col-md-2">
                <input name="term" type="text" class="form-control" id="brand-search" value="{{ request()->get('term','') }}" placeholder="Enter Brand name">
            </div>
            <div class="form-group mb-3 col-md-2">
                <?php echo Form::select("scraper_made_by", ['' => '-- Select Made By --'] + \App\User::all()->pluck("name", "id")->toArray(), request("scraper_made_by"), ["class" => "form-control select2"]) ?>
            </div>
            <div class="form-group mb-3 col-md-2">
                <?php echo Form::select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), request("scraper_type"), ["class" => "form-control select2"]) ?>
            </div>
            <!-- <div class="form-group mb-3 col-md-2">
                <select name="excelOnly" class="form-control form-group select2">
                    <option <?php echo $excelOnly == '' ? 'selected=selected' : '' ?> value="">All scrapers</option>
                    <option <?php echo $excelOnly == -1 ? 'selected=selected' : '' ?> value="-1">Without Excel</option>
                    <option <?php echo $excelOnly == 1 ? 'selected=selected' : '' ?> value="1">Excel only</option>
                </select>
            </div> -->
            <div class="form-group mb-3 col-md-2">
                <select name="scrapers_status" class="form-control form-group">
                    @foreach(\App\Scraper::STATUS as $k => $v)
                        <option <?php echo request()->get('scrapers_status','') == $k ? 'selected=selected' : '' ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3 col-md-2">
                <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
            </div>
        </div>
    </form>
    <!-- <div class="row">
        <div class="col col-md-12">
            @foreach(\App\Scraper::STATUS as $k => $v)
                <div class="col-md-2">
                    Status {{$v}} count = {{\App\Scraper::join("suppliers as s","s.id","scrapers.supplier_id")->where('scrapers.status', $v)->where('supplier_status_id', 1)->count()}}
                </div>
            @endforeach
        </div>    
    </div><br> -->
    <!-- <div class="row">
        <div class="col col-md-12">
            <div class="row">
                <div class="col-md-2 mt-1">
                    <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#addChildScraper">
                      <span class="glyphicon glyphicon-th-plus"></span> Add Child Scraper
                    </button>
                </div>
                <div class="col-md-2 mt-1">
                    <button type="button" class="btn btn-default btn-sm add-remark" data-toggle="modal" data-target="#addRemarkModal">
                      <span class="glyphicon glyphicon-th-plus"></span> Add Note
                    </button>
                </div>
                <div class="col-md-2 mt-1">
                    <a href="{{ route('scrap.auto-restart') }}?status=on">
                        <button type="button" class="btn btn-default btn-sm auto-restart-all">
                            <span class="glyphicon glyphicon-th-list"></span> Auto Restart On
                        </button>
                    </a>
                </div>
            {{-- </div>
            <div class="row"> --}}
                <div class="col-md-2 mt-1">
                    <button type="button" class="btn btn-default btn-sm get-latest-remark">
                      <span class="glyphicon glyphicon-th-list"></span> Latest Remarks
                    </button>
                </div>
                <div class="col-md-2 mt-1">
                    <a href="{{ route('scrap.latest-remark') }}?download=true">
                        <button type="button" class="btn btn-default btn-sm download-latest-remark">
                          <span class="glyphicon glyphicon-th-list"></span> Download Latest Remarks
                        </button>
                    </a>
                </div>
                <div class="col-md-2 mt-1">
                    <a href="{{ route('scrap.auto-restart') }}?status=off">
                        <button type="button" class="btn btn-default btn-sm auto-restart-all">
                            <span class="glyphicon glyphicon-th-list"></span> Auto Restart Off
                        </button>
                    </a>
                </div>
            </div>
         </div>   
   </div>
   <br> -->
   <!-- <div class="row no-gutters mt-3">
        <div class="col-md-12">
            <table class="table table-bordered table-striped sort-priority-scrapper">
                <thead>
                <tr>
                    <th>Scraper History</th>
                    <?php for ($i=0; $i < 7; $i++) { 
                        
                        if(!isset($date)) {
                            $date = date("Y-m-d");
                        }
                        echo '<th>'.$date.' <button style="padding-right:0px;" type="button" class="btn btn-xs show-scraper-history" title="Show Scraper server history"  data-date="'.$date.'"><i class="fa fa-info-circle"></i></button></th>';
                        $date = date("Y-m-d",strtotime('-1 day', strtotime($date)));
                        # code...
                    } ?>
                </tr>
                </thead>
            </table>
        </div>    
   </div> -->
   <?php $totalCountedUrl = 0; ?>
    <div class="row no-gutters mt-3">
        <div class="col-md-12" id="plannerColumn">
            <div class="">
                <table class="table table-bordered table-striped sort-priority-scrapper">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Brand</th>
                        <th>Brand Qty</th>
                        <th>Functions</th>
                        <!-- <th>Supplier</th> -->
                        <!-- <th>Server</th> -->
                        <!-- <th>Server ID</th>
                        <th>Auto Restart</th>
                        <th>Run Time</th>
                        <th>Start Scrap</th>
                        <th>Stock</th>
                        <th>URL Count</th>
                        <th>YDay New</th> -->
                        <!-- <th>Errors</th>
                        <th>Warnings</th> -->
                        <!-- <th>URL Count Scrap</th>
                        <th>Existing URLs</th>
                        <th>New URLs</th> -->
                        <!-- <th>Made By</th>
                        <th>Type</th>
                        <th>Parent Scrapper</th>
                        <th>Next Step</th> -->
                        <!-- <th>Status</th>
                        <th>Remarks</th> -->
                        <?php /*
                        <th>Devtask</th>
                        <th>Logs</th>
                        */ ?>
                        <!-- <th>Full scrap</th>
                        <th>Functions</th> -->
                    </tr>
                    </thead>
                    <tbody>
                    @php $arMatchedScrapers = []; $i=0; @endphp
                    @foreach ($activeSuppliers as $supplier)
                    <?php $totalCountedUrl += !empty($data) ? $data->total : 0; ?>
                            <td width="2%">
                            {{$supplier->id}}
                            </td>
                            <td width="14%">
                            {{$supplier->name}}
                            </td>
                            <td width="5%">
                            {{$supplier->products->count()}}
                            </td>
                            <td width="14%">
                                <button style="padding: 3px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-tasks-remote" title="Task list">
                                     <i class="fa fa-tasks"></i>
                                </button> 
                            </td>
                            </tr>
                               
                            </tr>
                            
                    @endforeach
                </table>
                <?php /* no needed
                <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Supplier</th>
                                <th>Server</th>
                                <th>Last Scraped</th>
                                <th>Inventory</th>
                                <th>Total</th>
                                <th>Errors</th>
                                <th>Warnings</th>
                                <th>Total Url's</th>
                                <th>Existing URLs</th>
                                <th>New URLs</th>
                                <th>Functions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $i=0; @endphp
                        @foreach ($scrapeData as $data )
                            @if ( !in_array($data->website, $arMatchedScrapers) )
                                <tr data-id="<?php echo $data->id ?>" <?php  $percentage = ($data->errors * 100) / $data->total; echo (!empty($percentage) && $percentage >= 25) ? 'style="background-color: orange; color: white;"' : '' ?>>
                                    @php
                                        $remark = \App\ScrapRemark::select('remark')->where('scraper_name',$data->website)->orderBy('created_at','desc')->first();
                                        $count = \App\ScraperResult::where('scraper_name',$data->website)->orderBy('created_at','desc')->first();
                                    @endphp
                                    <td>{{ ++$i }}</td>
                                    <td class="p-2">{{ $data->website }}<br>{{ \App\Helpers\ProductHelper::getScraperIcon($data->website) }}</td>
                                    <td class="p-2">{{ $data->ip_address }}</td>
                                    <td class="p-2">{{ !empty($data) ? date('d-m-Y H:i:s', strtotime($data->last_scrape_date)) : '' }}</td>
                                    <td class="p-2 text-right">{{ !empty($data) ? $data->total - $data->errors : '' }}</td>
                                    <td class="p-2 text-right">{{ !empty($data) ? $data->total : '' }}</td>
                                    <td class="p-2 text-right">{{ !empty($data) ? $data->errors : '' }}</td>
                                    <td class="p-2 text-right">{{ !empty($data) ? $data->warnings : '' }}</td>
                                    <td class="p-2 text-right">{{ !empty($count) ? $count->total_urls : '' }}</td>
                                    <td class="p-2 text-right">{{ !empty($count) ? $count->existing_urls : '' }}</td>
                                    <td class="p-2 text-right">{{ !empty($count) ? $count->new_urls : '' }}</td>

                                    <td>
                                        <button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $supplier->scraper_name }}"><img src="/images/remark.png"/></button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                </table>
                */ ?>
                @include('partials.modals.remarks',['type' => 'scrap'])
                @include('partials.modals.latest-remarks',[])
            </div>
        </div>
    </div>

    <div id="addRemarkModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Note</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('scrap/add/note') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>Scraper Name</label>
                            <select name="scraper_name" class="form-control select2" required>
                                @forelse ($allScrapper as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Note</label>
                            <textarea rows="2" name="remark" class="form-control" placeholder="Note" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Screenshot</label>
                            <input type="file" class="form-control" name="image">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>



      <div id="remarkHistory" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remark History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                    <div class="modal-body" id="remark-history-content">
                      
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
      </div>


    <div id="addChildScraper" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Child Scraper</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('save.childrenScraper') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>Select Scraper</label>
                            <select name="scraper_name" class="form-control select2" required>
                                @forelse ($allScrapper as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Scraper Name</label>
                            <input type="integer" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <strong>Run Gap:</strong>
                            <input type="integer" name="run_gap" class="form-control">
                        </div>
                        <div class="form-group">
                            <strong>Start Time:</strong>
                            <div class="input-group">
                                <?php echo Form::select("start_time", ['' => "--Time--"] + $timeDropDown,'', ["class" => "form-control start_time select2", "style" => "width:100%;"]); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <strong>Made By:</strong>
                            <div class="form-group">
                                <?php echo Form::select("scraper_made_by", ["" => "N/A"] + $users, '', ["class" => "form-control scraper_made_by select2", "style" => "width:100%;"]); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <strong>Server Id:</strong>
                            <div class="form-group">
                                <?php echo Form::text("server_id",'', ["class" => "form-control server-id-update"]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>  
      <div id="show-content-model" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
      </div>
      <div id="show-content-model-table" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                       
                    </div>
                </div>
            </div>
      </div>
      <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="remark-confirmation-box" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Note</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="?" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>Note</label>
                            <textarea id="confirmation-remark-note" rows="2" name="remark" class="form-control" placeholder="Note" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default btn-confirm-remark">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="/js/bootstrap-datepicker.min.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script type="text/javascript">

        $(".total-info").html("({{$totalCountedUrl}})");

         $(document).on("change", ".quickComments", function (e) {
            var message = $(this).val();
            var select = $(this);

            if ($.isNumeric(message) == false) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/scrap/statistics/reply/add",
                    dataType: "json",
                    method: "POST",
                    data: {reply: message}
                }).done(function (data) {
                    var vendors_id =$(select).find("option[value='']").data("vendorid");
                    var message_re = data.data.reply;
                    $("textarea#messageid_"+vendors_id).val(message_re);

                    console.log(data)
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
            //$(this).closest("td").find(".quick-message-field").val($(this).find("option:selected").text());
            var vendors_id =$(select).find("option[value='']").data("vendorid");
            var message_re = $(this).find("option:selected").html();

            $("textarea#messageid_"+vendors_id).val($.trim(message_re));

        });

        $(document).on("click", ".delete_quick_comment-scrapp", function (e) {
            var deleteAuto = $(this).closest(".d-flex").find(".quickComments").find("option:selected").val();
            if (typeof deleteAuto != "undefined") {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: BASE_URL+"/scrap/statistics/reply/delete",
                    dataType: "json",
                    method: "POST",
                    data: {id: deleteAuto}
                }).done(function (data) {
                    if (data.code == 200) {
                        // $(".quickComment ")
                        //     .find('option').not(':first').remove();

                        $(".quickComment").each(function(){
                        var selecto=  $(this)
                            $(this).children("option").not(':first').each(function(){
                            $(this).remove();


                            });
                            $.each(data.data, function (k, v) {
                                $(selecto).append("<option  value='" + k + "'>" + v + "</option>");
                            });
                            $(selecto).select2({tags: true});
                        });


                    }

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
        });


        $(".scrapers_status").select2();

        $(document).on("click", ".toggle-class", function () {
            $(".hidden_row_" + $(this).data("id")).toggleClass("dis-none");
        });

        $(document).on("keyup",".table-full-search",function() {
            var input, filter, table, tr, td, i, txtValue;
              input = document.getElementById("table-full-search");
              filter = input.value.toUpperCase();
              table = document.getElementById("latest-remark-records");
              tr = table.getElementsByTagName("tr");

              // Loop through all table rows, and hide those who don't match the search query
              for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                  txtValue = td.textContent || td.innerText;
                  if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                  } else {
                    tr[i].style.display = "none";
                  }
                }
              }
        });

        $(document).on("click",".get-latest-remark",function(e) {
            $.ajax({
                type: 'GET',
                url: '{{ route('scrap.latest-remark') }}',
                dataType:"json"
            }).done(response => {
                var html = '';
                var no = 1;
                if(response.code == 200) {
                    $.each(response.data, function (index, value) {
                        html += '<tr><td>' + value.scraper_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td><td>' + value.user_name + '</td><td>' + value.remark + '</td></tr>';
                        no++;
                    });
                    $("#latestRemark").find('.show-list-records').html(html);
                    $("#latestRemark").modal("show");
                }else{
                    toastr['error']('Oops, something went wrong', 'error');
                }
            });
        });

        $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();

            var name = $(this).data('name');

            $('#add-remark input[name="id"]').val(name);

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.getremark') }}',
                data: {
                    name: name
                },
            }).done(response => {
                var html = '';
                var no = 1;
                $.each(response, function (index, value) {
                    /*html += '<li><span class="float-left">' + value.remark + '</span><span class="float-right"><small>' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></span></li>';
                    html + "<hr>";*/
                    html += '<tr><td>' + value.remark + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
                    no++;
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $(document).on('click', '.filter-auto-remark', function (e) {
            var name = $('#add-remark input[name="id"]').val();
            var auto = $(this).is(":checked");
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.getremark') }}',
                data: {
                    name: name,
                    auto : auto
                },
            }).done(response => {
                var html = '';
                var no = 1;
                $.each(response, function (index, value) {
                    /*html += '<li><span class="float-left">' + value.remark + '</span><span class="float-right"><small>' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></span></li>';
                    html + "<hr>";*/
                    html += '<tr><td>' + value.remark + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
                    no++;
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $('#scrapAddRemarkbutton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    need_to_send: ($(".need_to_send").is(":checked")) ? 1 : 0,
                    inlcude_made_by: ($(".inlcude_made_by").is(":checked")) ? 1 : 0
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');

                /*var html = '<li><span class="float-left">' + remark + '</span><span class="float-right">You updated on ' + moment().format('DD-M H:mm') + ' </span></li>';
                html + "<hr>";
*/
                var no = $("#remark-list").find("tr").length + 1;
                html = '<tr><td>' + remark + '</td><td>You</td><td>' + moment().format('DD-M H:mm') + '</td></tr>';
                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                alert('Could not fetch remarks');
            });

        });

        /*$(".sort-priority-scrapper").sortable({
            items: $(".sort-priority-scrapper").find(".history-item-scrap"),
            start: function (event, ui) {
                //console.log(ui.item);
            },
            update: function (e, ui) {

                var itemMoving = ui.item;
                var itemEle = itemMoving.data("id");
                var needToMove = $(".hidden_row_" + itemEle);
                needToMove.detach().insertAfter(itemMoving);

                var lis = $(".sort-priority-scrapper tbody tr");
                var ids = lis.map(function (i, el) {
                    return {id: el.dataset.id}
                }).get();
                $.ajax({
                    url: '/scrap/statistics/update-priority',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    data: {
                        ids: ids,
                    }
                }).done(response => {
                    toastr['success']('Priority updated Successfully', 'success');
                }).fail(function (response) {
                });
            }
        });*/

        $(document).on("click", ".btn-set-priorities", function () {

        });

        $(document).on("change", ".start_time", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "scraper_start_time",
                    field_value: $(this).val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".scraper_field_change", function () {
            // var tr = $(this).closest("tr");
            var id = $(this).data("id");
            var field = $(this).data("field");
            var value = $(this).val();
            if(!value || value == '') {
                return;
            }
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-scrap-field',
                data: {
                    search: id,
                    field: field,
                    field_value: value
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {
                toastr['error']('Data not updated', 'error');
            });
        });

        
        $(document).on("click", ".show-history", function () {
            var id = $(this).data("id");
            var field = $(this).data("field");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/show-history',
                data: {
                    search: id,
                    field: field
                },
            }).done(function (response) {
                $("#remarkHistory").modal("show");
                var table = '';
                if( field == "update-restart-time") {
                    table = table + '<table class="table table-bordered table-striped" ><tr><th>Date</th></tr>';
                }else{
                    table = table + '<table class="table table-bordered table-striped" ><tr><th>From</th><th>To</th><th>Date</th><th>By</th></tr>';
                }

                for(var i=0;i<response.length;i++) {
                    if( field == "update-restart-time") {
                        table = table + '<tr><td>'+response[i].new_value+'</td></tr>';
                    }else{
                        table = table + '<tr><td>'+response[i].old_value+'</td><td>'+response[i].new_value+'</td></td><td>'+response[i].created_at+'</td><td>'+response[i].user_name+'</td></tr>';
                    }    
                }
                table = table + '</table>';

                $("#remark-history-content").html(table);
            }).fail(function (response) {
            });
        });
        

        $(document).on("click", ".submit-logic", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "scraper_logic",
                    field_value: tr.find(".scraper_logic").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });


        $(document).on("change", ".scraper_type", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "scraper_type",
                    field_value: tr.find(".scraper_type").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".scraper_made_by", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "scraper_made_by",
                    field_value: tr.find(".scraper_made_by").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".next_step_in_product_flow", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "next_step_in_product_flow",
                    field_value: tr.find(".next_step_in_product_flow").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".scrapers_status", function (e) {
            e.preventDefault();
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $("#remark-confirmation-box").modal("show").on("click",".btn-confirm-remark",function(e) {
                e.preventDefault();
                 var remark =  $("#confirmation-remark-note").val();
                 if($.trim(remark) == "") {
                    alert("Please Enter remark");
                    return false;
                 }
                 $.ajax({
                    type: 'GET',
                    url: '/scrap/statistics/update-field',
                    data: {
                        search: id,
                        field: "status",
                        field_value: tr.find(".scrapers_status").val(),
                        remark : remark    
                    },
                }).done(function (response) {
                    toastr['success']('Data updated Successfully', 'success');
                    $("#remark-confirmation-box").modal("hide");
                }).fail(function (response) {
                });
            });

            return false;
        });

        $(document).on("change", ".full_scrape", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "full_scrape",
                    field_value: tr.find(".full_scrape").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".auto_restart", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "auto_restart",
                    field_value: tr.find(".auto_restart").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".parent_supplier_id", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "parent_supplier_id",
                    field_value: tr.find(".parent_supplier_id").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("click",".server-id-update-btn",function() {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "server_id",
                    field_value: tr.find(".server-id-update").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        function restartScript(name,server_id) {
            var x = confirm("Are you sure you want to restart script?");
            if (x)
                  $.ajax({
                    url: '/api/node/restart-script',
                    type: 'POST',
                    dataType: 'json',
                    data: {name: name ,server_id : server_id, "_token": "{{ csrf_token() }}"},
                })
                .done(function(response) {
                    if(response.code == 200){
                        alert('Script Restarted Successfully')
                    }else{
                        alert('Please check if server is running')
                    }
                })
                .error(function() {
                    alert('Please check if server is running')
                });
            else
                return false;    
            
        }


        function getRunningStatus(name,server_id) {
            var x = confirm("Are you sure you want to restart script?");
            if (x)
                  $.ajax({
                    url: '/api/node/get-status',
                    type: 'POST',
                    dataType: 'json',
                    data: {name: name ,server_id : server_id, "_token": "{{ csrf_token() }}"},
                })
                .done(function(response) {
                    if(response.code == 200){
                        alert(response.message)
                    }else{
                        alert('Please check if server is running')
                    }
                })
                .error(function() {
                    alert('Please check if server is running')
                });
            else
                return false;    
            
        }


        function showHidden(name) {
            $("."+name).toggle();
        }


        $(".select2").select2();

        $(document).on("click",".show-scraper-detail",function (e){
            e.preventDefault();
            var startime = $(this).data("start-time");
            var endtime = $(this).data("end-time");

            var model  = $("#show-content-model");
            var html = `<div class="row">
                <div class="col-md-12">
                    <p>Star Time : `+startime+`</p>
                    <p>End Time : `+endtime+`</p>
                </div>
            </div>`;
            model.find(".modal-title").html("Scraper Start time details");
            model.find(".modal-body").html(html);
            model.modal("show");
        });

        $(document).on("click",".get-screenshot",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '/scrap/screenshot',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Scraper screenshots");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".get-last-errors",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '/scrap/get-last-errors',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Last Errors");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        

        $(document).on("click",".show-scraper-history",function (e){
            e.preventDefault();
            var date = $(this).data("date");
            $.ajax({
                url: '/scrap/server-status-history',
                type: 'GET',
                data: {date: date},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Server status history");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        

        $(document).on("click",".get-tasks-remote",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '/scrap/task-list',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Task List");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });


        $(document).on("click",".get-position-history",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '/scrap/position-history',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Scraper Position History");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".get-scraper-server-timing",function (e){
            e.preventDefault();
            var scraper_name = $(this).data("name");
            $.ajax({
                url: '/scrap/get-server-scraper-timing',
                type: 'GET',
                data: {scraper_name: scraper_name},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Scraper Server History");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });



        $(document).on("click",".btn-create-task",function (e){
            e.preventDefault();
            var $this = $(this).closest("form");
            $.ajax({
                url: $this.attr("action"),
                type: $this.attr("method"),
                data: $this.serialize() + "&type=scrap",
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Task List");
                model.find(".modal-body").html(response);
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });


        $(document).on("click",".scraper-log-details",function(e) {
            var $this = $(this);
            $.ajax({
                type: 'GET',
                url: '{{ route('scrap.log-details') }}',
                data: {
                    id : $this.data("scrapper-id")
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Log History");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });
        

        

        $(document).on("click","#show-content-model-table li",function (e){
            e.preventDefault();
            var a = $(this).find("a");
            if(typeof a != "undefined") {
                $.ajax({
                    url: a.attr("href"),
                    type: 'GET',
                    beforeSend: function () {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                     $("#loading-image").hide();
                    var model  = $("#show-content-model-table");
                    model.find(".modal-body").html(response);
                }).fail(function() {
                    $("#loading-image").hide();
                    alert('Please check laravel log for more information')
                });
            }
        });

        $(document).on("click",".flag-scraper",function (e){
            e.preventDefault();
            var flag = $(this).data("flag");
            var id = $(this).data("id");
            var $this =  $(this);
            $.ajax({
                url: "/scrap/statistics/update-field",
                type: 'GET',
                data: {
                    search: id,
                    field: "flag",
                    field_value: flag
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                 $("#loading-image").hide();
                 if(response.data.flag == 1) {
                    $this.closest(".flag-scraper-div").append('<button type="button" class="btn btn-image flag-scraper" data-flag="0" data-id="'+response.data.supplier_id+'"><img src="/images/flagged.png" /></button>');
                 }else{
                    $this.closest(".flag-scraper-div").append('<button type="button" class="btn btn-image flag-scraper" data-flag="1" data-id="'+response.data.supplier_id+'"><img src="/images/unflagged.png" /></button>');
                 }
                 $this.remove();
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".flag-scraper-developer",function (e){
            e.preventDefault();
            var flag = $(this).data("flag");
            var id = $(this).data("id");
            var $this =  $(this);
            $.ajax({
                url: "/scrap/statistics/update-field",
                type: 'GET',
                data: {
                    search: id,
                    field: "developer_flag",
                    field_value: flag
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                 $("#loading-image").hide();
                 if(response.data.developer_flag == 1) {
                    $this.closest(".flag-scraper-developer-div").append('<button type="button" class="btn btn-image flag-scraper-developer" data-flag="0" data-id="'+response.data.supplier_id+'"><img src="/images/flagged-green.png" /></button>');
                 }else{
                    $this.closest(".flag-scraper-developer-div").append('<button type="button" class="btn btn-image flag-scraper-developer" data-flag="1" data-id="'+response.data.supplier_id+'"><img src="/images/flagged-yellow.png" /></button>');
                 }
                 $this.remove();
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });


        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var task = $(this).data('task-id');
            var message = $("#messageid_"+task).val();
            data.append("issue_id", task);
            data.append("message", message);
            data.append("status", 1);
            data.append("sendTo", $(".send-message-number-"+task).val());

            if (message.length > 0) {
                if (!$(this).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/issue',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                            $("#loading-image").show();
                        }
                    }).done(function (response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        $("#message-chat-txt-"+task).html(response.message.message);
                        $("#messageid_"+task).val('');
                        $("#loading-image").hide();
                        $(this).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(this).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                        $("#loading-image").hide();
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

        $(document).on("click",".toggle-title-box",function(ele) {
            var $this = $(this);
            if($this.hasClass("has-small")){
                $this.html($this.data("full-title"));
                $this.removeClass("has-small")
            }else{
                $this.addClass("has-small")
                $this.html($this.data("small-title"));
            }
        });

    </script>
@endsection
