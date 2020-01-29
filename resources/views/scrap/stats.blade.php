@extends('layouts.app')

@section('favicon' , 'supplierstats.png')

@section('title', 'Scrape Statistics')

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
            display: block;
            width: 100%;
            overflow: auto;
            height: 250px;
        }

        .fixed_header thead tr {
            display: block;
        }

        .fixed_header thead {
            background: black;
            color: #fff;
        }

        .fixed_header th, .fixed_header td {
            padding: 5px;
            text-align: left;
            width: 200px;
        }
    </style>
@endsection

@section('content')

    <div class="row mb-5">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Supplier Scrapping Info <span class="total-info"></span></h2>
        </div>
    </div>

    @include('partials.flash_messages')
    <?php $status = request()->get('status', ''); ?>
    <?php $excelOnly = request()->get('excelOnly', ''); ?>
    <form class="" action="/scrap/statistics">
        <div class="row">
            <div class="form-group mb-3 col-md-3">
                <input name="term" type="text" class="form-control" id="product-search" value="{{ request()->get('term','') }}" placeholder="Enter Supplier name">
            </div>
            <div class="form-group mb-3 col-md-3">
                <?php echo Form::select("scraper_made_by", ['' => '-- Select Made By --'] + \App\User::all()->pluck("name", "id")->toArray(), request("scraper_made_by"), ["class" => "form-control select2"]) ?>
            </div>
            <div class="form-group mb-3 col-md-3">
                <?php echo Form::select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), request("scraper_type"), ["class" => "form-control select2"]) ?>
            </div>
            <div class="form-group mb-3 col-md-3">
                <select name="status" class="form-control form-group select2">
                    <option <?php echo $status == '' ? 'selected=selected' : '' ?> value="">-- Select Status --</option>
                    <option <?php echo $status == 1 ? 'selected=selected' : '' ?> value="1">Has Error ?</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group mb-3 col-md-3">
                <select name="excelOnly" class="form-control form-group select2">
                    <option <?php echo $excelOnly == '' ? 'selected=selected' : '' ?> value="">All scrapers</option>
                    <option <?php echo $excelOnly == -1 ? 'selected=selected' : '' ?> value="-1">Without Excel</option>
                    <option <?php echo $excelOnly == 1 ? 'selected=selected' : '' ?> value="1">Excel only</option>
                </select>
            </div>
            <div class="form-group mb-3 col-md-3">
                <select name="scrapers_status" class="form-control form-group">
                    <option value="">Status</option>
                    <option <?php echo request()->get('scrapers_status','') == 'Ok' ? 'selected=selected' : '' ?> value="Ok">Ok</option>
                    <option <?php echo request()->get('scrapers_status','') == 'Rework' ? 'selected=selected' : '' ?> value="Rework">Rework</option>
                    <option <?php echo request()->get('scrapers_status','') == 'In Process'? 'selected=selected' : '' ?> value="In Process">In Process</option>
                </select>
            </div>
            <div class="form-group mr-3 mb-3 col-md-3">
                <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col col-md-9">
            <div class="col-md-3">
                Status Ok count = {{\App\Scraper::join("suppliers as s","s.id","scrapers.supplier_id")->where('scrapers.status', 'Ok')->where('supplier_status_id', 1)->count()}}
            </div>
            <div class="col-md-3">
                Status Rework count = {{\App\Scraper::join("suppliers as s","s.id","scrapers.supplier_id")->where('scrapers.status', 'Rework')->where('supplier_status_id', 1)->count()}}
            </div>
            <div class="col-md-3">
                Status In Process count = {{\App\Scraper::join("suppliers as s","s.id","scrapers.supplier_id")->where('scrapers.status', 'In Process')->where('supplier_status_id', 1)->count()}}
            </div>
        </div>    
        <div class="col">
            <div style="float: right;" class="col-md-3">
                <button type="button" class="btn btn-default btn-sm get-latest-remark">
                  <span class="glyphicon glyphicon-th-list"></span> Latest Remarks
                </button>
            </div>
         </div>   
    </div>
    <?php $totalCountedUrl = 0; ?>
    <div class="row no-gutters mt-3">
        <div class="col-md-12" id="plannerColumn">
            <div class="">
                <table class="table table-bordered table-striped sort-priority-scrapper">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier</th>
                        <th>Server</th>
                        <th>Server ID</th>
                        <th>Run Time</th>
                        <th>Last Scraped</th>
                        <th>Stock</th>
                        <th>URL Count</th>
                        <th>Errors</th>
                        <th>Warnings</th>
                        <th>URL Count Scraper</th>
                        <th>Existing URLs</th>
                        <th>New URLs</th>
                        <th>Made By</th>
                        <th>Type</th>
                        <th>Parent Scrapper</th>
                        <th>Next Step</th>
                        <th>Status</th>
                        <th>Functions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $arMatchedScrapers = []; $i=0; @endphp
                    @foreach ($activeSuppliers as $supplier)
                        @if ( (stristr($supplier->scraper_name, '_excel') && (int) $excelOnly > -1 ) || (!stristr($supplier->scraper_name, '_excel') && (int) $excelOnly < 1 ) )
                            @php $data = null; @endphp
                            @foreach($scrapeData as $tmpData)
                                @if ( !empty($tmpData->website) && $tmpData->website == $supplier->scraper_name )
                                    @php $data = $tmpData; $arMatchedScrapers[] = $supplier->scraper_name @endphp
                                @endif
                            @endforeach
                            @php
                                // Set percentage
                                if ( isset($data->errors) && isset($data->total) ) {
                                    $percentage = ($data->errors * 100) / $data->total;
                                } else {
                                    $percentage = 0;
                                }

                                // Show correct background color
                                $hasError =  false;
                                $hasWarning = false;
                                if ( (!empty($data) && $data->running == 0) || $data == null ) {
                                    $hasError =  true;
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'">';
                                } elseif ( $percentage > 25 ) {
                                    $hasWarning = true;
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'">';
                                } else {
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'">';
                                }

                                if($status == 1 && !$hasError) {
                                    continue;
                                }

                                $remark = \App\ScrapRemark::select('remark')->where('scraper_name',$supplier->scraper_name)->orderBy('created_at','desc')->first();
                            @endphp
                            <td width="1%">{{ ++$i }}</td>
                            <td width="8%"><a href="/supplier/{{$supplier->id}}">{{ ucwords(strtolower($supplier->supplier)) }}<br>{{ \App\Helpers\ProductHelper::getScraperIcon($supplier->scraper_name) }}</a>
                                @if(substr(strtolower($supplier->supplier), 0, 6)  == 'excel_')
                                    &nbsp;<i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                @endif
                                <?php if($hasError){ ?>
                                <i style="color: red;" class="fa fa-exclamation-triangle"></i>
                                <?php } ?>
                                <?php if($hasWarning){ ?>
                                <i style="color: orange;" class="fa fa-exclamation-triangle"></i>
                                <?php } ?>
                            </td>
                            <td width="10%">{{ !empty($data) ? $data->ip_address : '' }}</td>
                            <td width="10%">{{ $supplier->server_id }}</td>
                            <td width="10%" style="text-right">
                                {{ $supplier->scraper_start_time }}h
                            </td>
                            <td width="10%">{!! !empty($data) ? str_replace(' ', '<br/>', date('d-M-y H:i', strtotime($data->last_scrape_date))) : '' !!}</td>
                            <td width="3%">{{ !empty($data) ? $data->total - $data->errors : '' }}</td>
                            <?php $totalCountedUrl += !empty($data) ? $data->total : 0; ?>
                            <td width="3%">{{ !empty($data) ? $data->total : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->errors : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->warnings : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->scraper_total_urls : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->scraper_existing_urls : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->scraper_new_urls : '' }}</td>
                            <td width="10%">
                                {{ ($supplier->scraperMadeBy) ? $supplier->scraperMadeBy->name : "N/A" }}
                            </td>
                            <td width="10%">
                                {{ \App\Helpers\DevelopmentHelper::scrapTypeById($supplier->scraper_type) }}
                            </td>
                            <td width="10%">
                                {{ ($supplier->scraperParent) ? $supplier->scraperParent->scraper_name : "N/A" }}
                            </td>
                            <td width="10%">
                                {{ isset(\App\Helpers\StatusHelper::getStatus()[$supplier->next_step_in_product_flow]) ? \App\Helpers\StatusHelper::getStatus()[$supplier->next_step_in_product_flow] : "N/A" }}
                            </td>
                            <td width="10%">
                                {{ !empty($supplier->scrapers_status) ? $supplier->scrapers_status : "N/A" }}
                            </td>
                            <td width="10%">
                                <button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $supplier->scraper_name }}"><img width="2px;" src="/images/remark.png"/></button>
                                <button type="button" class="btn btn-image d-inline toggle-class" data-id="{{ $supplier->id }}"><img width="2px;" src="/images/forward.png"/></button>
                                <a class="btn  d-inline btn-image" href="{{ get_server_last_log_file($supplier->scraper_name,$supplier->server_id) }}" id="link" target="-blank"><img src="/images/view.png" /></a>
                            </td>
                            </tr>
                            <tr class="hidden_row_{{ $supplier->id  }} dis-none" data-eleid="{{ $supplier->id }}">
                                <td colspan="3">
                                    <label>Logic:</label>
                                    <div class="input-group">
                                        <textarea class="form-control scraper_logic" name="scraper_logic"><?php echo $supplier->scraper_logic; ?></textarea>
                                        <button class="btn btn-sm btn-image submit-logic" data-vendorid="1"><img src="/images/filled-sent.png"></button>
                                    </div>
                                </td>
                                <td colspan="3">
                                    <label>Start Time:</label>
                                    <div class="input-group">
                                        <?php echo Form::select("start_time", ['' => "--Time--"] + $timeDropDown, $supplier->scraper_start_time, ["class" => "form-control start_time select2", "style" => "width:100%;"]); ?>
                                    </div>
                                </td>
                                <td colspan="3">
                                    <label>Made By:</label>
                                    <div class="form-group">
                                        <?php echo Form::select("scraper_made_by", ["" => "N/A"] + $users, $supplier->scraper_made_by, ["class" => "form-control scraper_made_by select2", "style" => "width:100%;"]); ?>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Type:</label>
                                    <div class="form-group">
                                        <?php echo Form::select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), $supplier->scraper_type, ["class" => "form-control scraper_type select2", "style" => "width:100%;"]) ?>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Parent Scrapper:</label>
                                    <div class="form-group">
                                        <?php echo Form::select("parent_supplier_id", [0 => "N/A"] + $allScrapperName, $supplier->parent_supplier_id, ["class" => "form-control parent_supplier_id select2", "style" => "width:100%;"]); ?>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Next Step:</label>
                                    <div class="form-group">
                                        <?php echo Form::select("next_step_in_product_flow", [0 => "N/A"] + \App\Helpers\StatusHelper::getStatus(), $supplier->next_step_in_product_flow, ["class" => "form-control next_step_in_product_flow select2", "style" => "width:100%;"]); ?>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Server Id:</label>
                                    <div class="form-group">
                                        <?php echo Form::text("server_id",$supplier->server_id, ["class" => "form-control server-id-update"]); ?>
                                        <button class="btn btn-sm btn-image server-id-update-btn" data-vendorid="<?php echo $supplier->id; ?>"><img src="/images/filled-sent.png" style="cursor: default;"></button>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Status:</label>
                                    <div class="form-group">
                                        <?php echo Form::select("status", ['' => "N/A", 'Ok' => 'Ok', 'Rework' => 'Rework', 'In Process' => 'In Process'], $supplier->scrapers_status, ["class" => "form-control scrapers_status", "style" => "width:100%;"]); ?>
                                    </div>
                                </td>
                            </tr>
                    @endif
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
@endsection

@section('scripts')
    <script type="text/javascript" src="/js/bootstrap-datepicker.min.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script type="text/javascript">

        $(".total-info").html("({{$totalCountedUrl}})");

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
                        html += '<tr><th scope="row">' + no + '</th><td>' + value.scraper_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td><td>' + value.user_name + '</td><td>' + value.remark + '</td></tr>';
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
                    html += '<tr><th scope="row">' + no + '</th><td>' + value.remark + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
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
                html = '<tr><th scope="row">' + no + '</th><td>' + remark + '</td><td>You</td><td>' + moment().format('DD-M H:mm') + '</td></tr>';
                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                alert('Could not fetch remarks');
            });

        });

        $(".sort-priority-scrapper").sortable({
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
        });

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

        $(document).on("change", ".scrapers_status", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "status",
                    field_value: tr.find(".scrapers_status").val()
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

        $(".select2").select2();
    </script>
@endsection