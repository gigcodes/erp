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

@section('large_content')

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
        <div class="col col-md-7">
            <div class="col-md-4">
                Status Ok count = {{\App\Scraper::join("suppliers as s","s.id","scrapers.supplier_id")->where('scrapers.status', 'Ok')->where('supplier_status_id', 1)->count()}}
            </div>
            <div class="col-md-4">
                Status Rework count = {{\App\Scraper::join("suppliers as s","s.id","scrapers.supplier_id")->where('scrapers.status', 'Rework')->where('supplier_status_id', 1)->count()}}
            </div>
            <div class="col-md-4">
                Status In Process count = {{\App\Scraper::join("suppliers as s","s.id","scrapers.supplier_id")->where('scrapers.status', 'In Process')->where('supplier_status_id', 1)->count()}}
            </div>
        </div>    
        <div class="col col-md-5">
            <div class="row">
                <div class="col-md-4 mt-1">
                    <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#addChildScraper">
                      <span class="glyphicon glyphicon-th-plus"></span> Add Child Scraper
                    </button>
                </div>
                <div class="col-md-4 mt-1">
                    <button type="button" class="btn btn-default btn-sm add-remark" data-toggle="modal" data-target="#addRemarkModal">
                      <span class="glyphicon glyphicon-th-plus"></span> Add Note
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mt-1">
                    <button type="button" class="btn btn-default btn-sm get-latest-remark">
                      <span class="glyphicon glyphicon-th-list"></span> Latest Remarks
                    </button>
                </div>
                <div class="col-md-4 mt-1">
                    <a href="{{ route('scrap.latest-remark') }}?download=true">
                        <button type="button" class="btn btn-default btn-sm download-latest-remark">
                          <span class="glyphicon glyphicon-th-list"></span> Download Latest Remarks
                        </button>
                    </a>
                </div>
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
                        <!-- <th>Server</th> -->
                        <th>Server ID</th>
                        <th>Run Time</th>
                        <th>Start Scrap</th>
                        <th>Stock</th>
                        <th>URL Count</th>
                        <th>Errors</th>
                        <th>Warnings</th>
                        <th>URL Count Scraper</th>
                        <th>Existing URLs</th>
                        <th>New URLs</th>
                        <!-- <th>Made By</th>
                        <th>Type</th>
                        <th>Parent Scrapper</th>
                        <th>Next Step</th> -->
                        <th>Status</th>
                        <th>Remarks</th>
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
                            <td width="1%">{{ ++$i }} <br>@if($supplier->getChildrenScraperCount($supplier->scraper_name) != 0) <button onclick="showHidden('{{ $supplier->scraper_name }}')" class="btn btn-link"><i class="fa fa-caret-down" style="font-size:24px"></i>  </button> @endif</td>
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
                            <!-- <td width="10%">{{ !empty($data) ? $data->ip_address : '' }}</td> -->
                            <td width="10%">
                            <div class="form-group">
                                    <select style="width:80% !important;" name="server_id" class="form-control select2 scraper_field_change" data-id="{{$supplier->scrapper_id}}" data-field="server_id">
                                        <option value="">Select</option>
                                        @foreach($serverIds as $serverId)
                                        <option value="{{$serverId}}" {{$supplier->server_id == $serverId ? 'selected' : ''}}>{{$serverId}}</option>
                                        @endforeach
                                    </select>
                                      <button style="padding-right:0px;" type="button" class="btn btn-xs show-history" title="Show History" data-field="server_id" data-id="{{$supplier->scrapper_id}}"><i class="fa fa-info-circle"></i></button>
                            </div>
                            </td>
                            <td width="10%" style="text-right">
                                <div class="form-group">
                                        <select style="width:85% !important;display:inline;" name="scraper_start_time" class="form-control scraper_field_change" data-id="{{$supplier->scrapper_id}}" data-field="scraper_start_time">
                                        <option value="">Select</option>
                                        @for($i=1; $i<=24;$i++)
                                        <option value="{{$i}}" {{$supplier->scraper_start_time == $i ? 'selected' : ''}}>{{$i}} h</option>
                                        @endfor
                                        </select>
                                        <button style="padding-right:0px;width:10%;display:inline-block;" type="button" class="btn btn-xs show-history" title="Show History" data-field="scraper_start_time" data-id="{{$supplier->scrapper_id}}"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </td>
                            <td width="10%" data-start-time="@if($supplier->last_started_at){{$supplier->last_started_at }}@endif" data-end-time="@if($supplier->last_completed_at){{$supplier->last_completed_at }}@endif" class="show-scraper-detail">
                                @if(isset($supplier->scraper_name) && !empty($supplier->scraper_name) &&  isset($lastRunAt[$supplier->scraper_name]))
                                    {!! str_replace(' ', '<br/>', date('d-M-y H:i', strtotime($lastRunAt[$supplier->scraper_name]))) !!}
                                    <br/>
                                @endif
                            </td>
                            <td width="3%">{{ !empty($data) ? $data->total - $data->errors : '' }}</td>
                            <?php $totalCountedUrl += !empty($data) ? $data->total : 0; ?>
                            <td width="3%">{{ !empty($data) ? $data->total : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->errors : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->warnings : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->scraper_total_urls : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->scraper_existing_urls : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->scraper_new_urls : '' }}</td>
                            <!-- <td width="10%">
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
                            </td> -->
                            <td width="10%">
                                {{ !empty($supplier->scrapers_status) ? $supplier->scrapers_status : "N/A" }}
                            </td>
                            <td width="10%">
                                <?php
                                    $remark = $supplier->scraperRemark();
                                    if($remark) {
                                        echo (strlen($remark->remark) > 15) ? substr($remark->remark, 0, 15).".." : $remark->remark;
                                    }
                                 ?>
                                <button style="padding:3px;" type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $supplier->scraper_name }}"><img width="2px;" src="/images/remark.png"/></button>
                            </td>
                            <td width="10%">
                                <button style="padding:3px;" type="button" class="btn btn-image d-inline toggle-class" data-id="{{ $supplier->id }}"><img width="2px;" src="/images/forward.png"/></button>
                                <a style="padding:3px;" class="btn  d-inline btn-image" href="{{ get_server_last_log_file($supplier->scraper_name,$supplier->server_id) }}" id="link" target="-blank"><img src="/images/view.png" /></a>
                                <button style="padding:3px;" type="button" class="btn btn-image d-inline" onclick="restartScript('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}' )"><img width="2px;" src="/images/resend2.png"/></button>
                                <button style="padding:3px;" type="button" class="btn btn-image d-inline" onclick="getRunningStatus('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}' )"><img width="2px;" src="/images/resend.png"/></button>
                                <button style="padding: 3px" data-id="{{ $supplier->scrapper_id }}" type="button" class="btn btn-image d-inline get-screenshot">
                                     <i class="fa fa-desktop"></i>
                                </button>
                                <button style="padding: 3px" data-id="{{ $supplier->scrapper_id }}" type="button" class="btn btn-image d-inline get-tasks-remote">
                                     <i class="fa fa-tasks"></i>
                                </button>
                            </td>
                            </tr>
                            <tr class="hidden_row_{{ $supplier->id  }} dis-none" data-eleid="{{ $supplier->id }}">
                                <td colspan="2">
                                    <label>Logic:</label>
                                    <div class="input-group">
                                        <textarea class="form-control scraper_logic" name="scraper_logic"><?php echo $supplier->scraper_logic; ?></textarea>
                                        <button class="btn btn-sm btn-image submit-logic" data-vendorid="1"><img src="/images/filled-sent.png"></button>
                                    </div>
                                </td>
                                <td colspan="1">
                                    <label>Start Time:</label>
                                    <div class="input-group">
                                        <?php echo Form::select("start_time", ['' => "--Time--"] + $timeDropDown, $supplier->scraper_start_time, ["class" => "form-control start_time select2", "style" => "width:100%;"]); ?>
                                    </div>
                                </td>
                                <td colspan="2">
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
                            @if($supplier->getChildrenScraper($supplier->scraper_name))
                                @if(count($supplier->getChildrenScraper($supplier->scraper_name)) != 0)
                                    <?php $childCount = 0; ?>
                                    @foreach($supplier->getChildrenScraper($supplier->scraper_name) as $childSupplier)
                                    @php $data = null; @endphp
                                    @foreach($scrapeData as $tmpData)
                                        @if ( !empty($tmpData->website) && $tmpData->website == $childSupplier->scraper_name )
                                            @php $data = $tmpData; $arMatchedScrapers[] = $childSupplier->scraper_name @endphp
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
                                    <tr style="display: none;" class="{{ $supplier->scraper_name }}">
                                    <td width="1%">{{ ++$childCount }}</td>
                                    <td width="8%"><a href="/supplier/{{$childSupplier->supplier_id}}">{{ ucwords(strtolower($childSupplier->scraper_name)) }}
                                    </td>
                                    <!-- <td width="10%">{{ !empty($data) ? $data->ip_address : '' }}</td>
                                     -->
                                    <td width="10%">
                                        <div class="form-group">
                                                <select style="width:80% !important;" name="server_id" class="form-control select2 scraper_field_change" data-id="{{$childSupplier->id}}" data-field="server_id">
                                                    <option value="">Select</option>
                                                    @foreach($serverIds as $serverId)
                                                    <option value="{{$serverId}}" {{$childSupplier->server_id == $serverId ? 'selected' : ''}}>{{$serverId}}</option>
                                                    @endforeach
                                                </select>
                                                  <button style="padding-right:0px;" type="button" class="btn btn-xs show-history" title="Show History" data-field="server_id" data-id="{{$childSupplier->id}}"><i class="fa fa-info-circle"></i></button>
                                        </div>
                                    </td>

                                    <td width="10%" style="text-right">
                                        <div class="form-group">
                                                <select style="width:85% !important;display:inline;" name="scraper_start_time" class="form-control scraper_field_change" data-id="{{$childSupplier->id}}" data-field="scraper_start_time">
                                                <option value="">Select</option>
                                                @for($i=1; $i<=24;$i++)
                                                <option value="{{$i}}" {{$childSupplier->scraper_start_time == $i ? 'selected' : ''}}>{{$i}} h</option>
                                                @endfor
                                                </select>
                                                <button style="padding-right:0px;width:10%;display:inline-block;" type="button" class="btn btn-xs show-history" title="Show History" data-field="scraper_start_time" data-id="{{$childSupplier->id}}"><i class="fa fa-info-circle"></i></button>
                                        </div>
                                    </td>
                                    
                                    <td width="10%">
                                        @if(isset($childSupplier->scraper_name) && !empty($childSupplier->scraper_name) &&  isset($lastRunAt[$childSupplier->scraper_name]))
                                            {!! str_replace(' ', '<br/>', date('d-M-y H:i', strtotime($lastRunAt[$childSupplier->scraper_name]))) !!}
                                            <br/>
                                        @endif
                                        {{ $childSupplier->last_completed_at }} 
                                    </td>
                                    <td width="3%">{{ !empty($data) ? $data->total - $data->errors : '' }}</td>
                                    <?php $totalCountedUrl += !empty($data) ? $data->total : 0; ?>
                                    <td width="3%">{{ !empty($data) ? $data->total : '' }}</td>
                                    <td width="3%">{{ !empty($data) ? $data->errors : '' }}</td>
                                    <td width="3%">{{ !empty($data) ? $data->warnings : '' }}</td>
                                    <td width="3%">{{ $childSupplier->scraper_total_urls }}</td>
                                    <td width="3%">{{ $childSupplier->scraper_existing_urls }}</td>
                                    <td width="3%">{{  $childSupplier->scraper_new_urls }}</td>
                                    <td width="10%">
                                        {{ !empty($childSupplier->scrapers_status) ? $childSupplier->scrapers_status : "N/A" }}
                                    </td>
                                    <td width="10%">
                                        <button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $childSupplier->scraper_name }}"><img width="2px;" src="/images/remark.png"/></button>
                                        <button type="button" class="btn btn-image d-inline toggle-class" data-id="{{ $childSupplier->id }}"><img width="2px;" src="/images/forward.png"/></button>
                                        <a class="btn  d-inline btn-image" href="{{ get_server_last_log_file($childSupplier->scraper_name,$childSupplier->server_id) }}" id="link" target="-blank"><img src="/images/view.png" /></a>
                                        <button type="button" class="btn btn-image d-inline" onclick="restartScript('{{ $childSupplier->scraper_name }}' , '{{ $childSupplier->server_id }}' )"><img width="2px;" src="/images/resend2.png"/></button>
                                        <button type="button" class="btn btn-image d-inline" onclick="getRunningStatus('{{ $childSupplier->scraper_name }}' , '{{ $childSupplier->server_id }}' )"><img width="2px;" src="/images/resend2.png"/></button>
                                        
                                    </td>
                                    </tr>
                                    <tr class="hidden_row_{{ $childSupplier->id  }} dis-none" data-eleid="{{ $childSupplier->id }}">
                                        <td colspan="3">
                                            <label>Logic:</label>
                                            <div class="input-group">
                                                <textarea class="form-control scraper_logic" name="scraper_logic"><?php echo $childSupplier->scraper_logic; ?></textarea>
                                                <button class="btn btn-sm btn-image submit-logic" data-vendorid="1"><img src="/images/filled-sent.png"></button>
                                            </div>
                                        </td>
                                        <td colspan="3">
                                            <label>Start Time:</label>
                                            <div class="input-group">
                                                <?php echo Form::select("start_time", ['' => "--Time--"] + $timeDropDown, $childSupplier->scraper_start_time, ["class" => "form-control start_time select2", "style" => "width:100%;"]); ?>
                                            </div>
                                        </td>
                                        <td colspan="3">
                                            <label>Made By:</label>
                                            <div class="form-group">
                                                <?php echo Form::select("scraper_made_by", ["" => "N/A"] + $users, $childSupplier->scraper_made_by, ["class" => "form-control scraper_made_by select2", "style" => "width:100%;"]); ?>
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <label>Type:</label>
                                            <div class="form-group">
                                                <?php echo Form::select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), $childSupplier->scraper_type, ["class" => "form-control scraper_type select2", "style" => "width:100%;"]) ?>
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <label>Parent Scrapper:</label>
                                            <div class="form-group">
                                                <?php echo Form::select("parent_supplier_id", [0 => "N/A"] + $allScrapperName, $childSupplier->parent_supplier_id, ["class" => "form-control parent_supplier_id select2", "style" => "width:100%;"]); ?>
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <label>Next Step:</label>
                                            <div class="form-group">
                                                <?php echo Form::select("next_step_in_product_flow", [0 => "N/A"] + \App\Helpers\StatusHelper::getStatus(), $childSupplier->next_step_in_product_flow, ["class" => "form-control next_step_in_product_flow select2", "style" => "width:100%;"]); ?>
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <label>Server Id:</label>
                                            <div class="form-group">
                                                <?php echo Form::text("server_id",$childSupplier->server_id, ["class" => "form-control server-id-update"]); ?>
                                                <button class="btn btn-sm btn-image server-id-update-btn" data-vendorid="<?php echo $childSupplier->id; ?>"><img src="/images/filled-sent.png" style="cursor: default;"></button>
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <label>Status:</label>
                                            <div class="form-group">
                                                <?php echo Form::select("status", ['' => "N/A", 'Ok' => 'Ok', 'Rework' => 'Rework', 'In Process' => 'In Process'], $childSupplier->scrapers_status, ["class" => "form-control scrapers_status", "style" => "width:100%;"]); ?>
                                            </div>
                                        </td>
                                     </tr>
                                    @endforeach
                                   
                                @endif
                            @endif
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

    <div id="addRemarkModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
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
        <div class="modal-dialog">
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
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
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

            console.log(name)
            
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
                table = table + '<table class="table table-bordered table-striped" ><tr><th>From/To</th><th>Date</th><th>By</th></tr>';

                for(var i=0;i<response.length;i++) {
                    table = table + '<tr><td>'+response[i].remark+'</td><td>'+response[i].created_at+'</td><td>'+response[i].user_name+'</td></tr>';
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

        $(document).on("click",".btn-create-task",function (e){
            e.preventDefault();
            var $this = $(this).closest("form");
            $.ajax({
                url: $this.attr("action"),
                type: $this.attr("method"),
                data: $this.serialize(),
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


        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var task = $(this).data('task-id');
            var message = $("#messageid_"+task).val();
            data.append("issue_id", task);
            data.append("message", message);
            data.append("status", 1);

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

    </script>
@endsection