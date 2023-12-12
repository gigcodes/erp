@extends('layouts.app')

@section('favicon' , 'development-issue.png')

@section('title', 'Development Scrapper List')

<style> 
    .status-selection .btn-group {
        padding: 0;
        width: 100%;
    }
    .status-selection .multiselect {
        width : 100%;
    }
    .pd-sm {
        padding: 0px 8px !important;
    }
    tr {
        background-color: #f9f9f9;
    }
    .mr-t-5 {
        margin-top:5px !important;
    }
    /* START - Pupose : Set Loader image - DEVTASK-4359*/
    #myDiv{
        position: fixed;
        z-index: 99;
        text-align: center;
    }
    #myDiv img{
        position: fixed;
        top: 50%;
        left: 50%;
        right: 50%;
        bottom: 50%;
    }
    /* END - DEVTASK-4359*/
</style>
@section('content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb pr-0">
                    <h2 class="page-heading">Scrapper Verification Data</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12  pl-5">
    	<div class="row mb-3">
    		<div class="mt-3 col-md-12">
    		    <table class="table table-bordered table-striped"style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th width="3%">Id</th>
                                <th width="8%">Task Id</th>
                                <th width="8%">Scrapper</th>
                                <th width="7%">Title</th>
                                <th width="7%">Website</th>
                                <th width="7%">Sku</th>
                                <th width="5%">Url</th>
                                <th width="4%">Images</th>
                                <th width="5%">Description</th>
                                <th width="5%">Properties</th>
                                <th width="5%">Currency</th>
                                <th width="4%">Size System</th>
                                <th width="3%">Price</th>
                                <th width="5%">Discounted Price</th>
                                <th width="5%">Discounted Percentage</th>
                                <th width="3%">B2b Price</th>
                                <th width="3%">Brand</th>
                                <th width="3%">Is Sale</th> 
                            </tr>
                        </thead>
                        <tbody class="text-center task_queue_list">
                            @foreach($records as $i=>$record) 

                                @php
                                $returnData = [];
                                $jsonString = $record['scrapper_values'];
                                $phpArray = json_decode($jsonString, true);
                                if(!empty($phpArray)){
                                    if(!empty($phpArray)){
                                        foreach ($phpArray as $key_json => $value_json) {
                                            $returnData[$key_json] = $value_json;         
                                        }
                                    }                                   
                                }   
                                @endphp         
                                <tr>
    								<td>{{ $record['id'] }}</td>
                                    <td>#DEVTASK-{{ $record['task_id'] }}</td>
                                    <td class="expand-row-msg" data-name="subject" data-id="{{$i}}">
                                        @if(!empty($record['tasks']['subject']))
                                            <span class="show-short-subject-{{$i}}">{{ Str::limit($record['tasks']['subject'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-subject-{{$i}} hidden">{{ $record['tasks']['subject'] }}</span>
                                        @endif
                                    </td>
                                    <td class="expand-row-msg" data-name="title" data-id="{{$i}}">
                                        @if(!empty($returnData['title']))
                                            <span class="show-short-title-{{$i}}">{{ Str::limit($returnData['title'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-title-{{$i}} hidden">{{ $returnData['title'] }}</span>
                                        @endif
                                    </td>
                                    <td class="expand-row-msg" data-name="website" data-id="{{$i}}">
                                        @if(!empty($returnData['website']))
                                            <span class="show-short-website-{{$i}}">{{ Str::limit($returnData['website'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-website-{{$i}} hidden">{{ $returnData['website'] }}</span>
                                        @endif
                                    </td>
                                    <td class="expand-row-msg" data-name="sku" data-id="{{$i}}">
                                        @if(!empty($returnData['sku']))
                                            <span class="show-short-sku-{{$i}}">{{ Str::limit($returnData['sku'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-sku-{{$i}} hidden">{{ $returnData['sku'] }}</span>
                                        @endif
                                    </td>
                                    <td class="expand-row-msg" data-name="url" data-id="{{$i}}">
                                        @if(!empty($returnData['url']))
                                            <span class="show-short-url-{{$i}}">{{ Str::limit($returnData['url'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-url-{{$i}} hidden">{{ $returnData['url'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" data-id="<?php echo $record['id'];  ?>" class="btn scrapper-images" style="padding:1px 0px;">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td class="expand-row-msg" data-name="description" data-id="{{$i}}">
                                        @if(!empty($returnData['description']))
                                            <span class="show-short-description-{{$i}}">{{ Str::limit($returnData['description'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-description-{{$i}} hidden">{{ $returnData['description'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" data-id="<?php echo $record['id'];  ?>" class="btn scrapper-properties" style="padding:1px 0px;">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td>@if(!empty($returnData['currency'])) {{ $returnData['currency'] }} @endif</td>
                                    <td class="expand-row-msg" data-name="size_system" data-id="{{$i}}">
                                        @if(!empty($returnData['size_system']))
                                            <span class="show-short-size_system-{{$i}}">{{ Str::limit($returnData['size_system'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-size_system-{{$i}} hidden">{{ $returnData['size_system'] }}</span>
                                        @endif
                                    </td>
                                    <td>@if(!empty($returnData['price'])) {{ $returnData['price'] }} @endif</td>
                                    <td>@if(!empty($returnData['discounted_price'])) {{ $returnData['discounted_price'] }} @endif</td>
                                    <td>@if(!empty($returnData['discounted_percentage'])) {{ $returnData['discounted_percentage'] }} @endif</td>
                                    <td>@if(!empty($returnData['b2b_price'])) {{ $returnData['b2b_price'] }} @endif</td>
                                    <td>@if(!empty($returnData['brand'])) {{ $returnData['brand'] }} @endif</td>
                                    <td>@if(!empty($returnData['is_sale'])) {{ $returnData['is_sale'] }} @endif</td>
    							</tr>
                            @endforeach
                        </tbody>
                </table>
    			{{$records->links()}}
            </div>
        </div> 
    </div>    
    @include('development.partials.scrapper-properties')
    @include('development.partials.scrapper-images')
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
});

$(document).on('click','.scrapper-properties',function(){
    id = $(this).data('id');
    $.ajax({
        method: "GET",
        url: `{{ route('development.scrapper_data', [""]) }}/` + id,
        dataType: "json",
        success: function(response) {
           
            $("#scrapper-properties-data").find(".scrapper-properties-data-view").html(response.html);
            $("#scrapper-properties-data").modal("show");
     
        }
    });
});

$(document).on('click','.scrapper-images',function(){
    id = $(this).data('id');
    $.ajax({
        method: "GET",
        url: `{{ route('development.scrapper_images_data', [""]) }}/` + id,
        dataType: "json",
        success: function(response) {
           
            $("#scrapper-images-data").find(".scrapper-images-data-view").html(response.html);
            $("#scrapper-images-data").modal("show");
     
        }
    });
});

</script>
@endsection
