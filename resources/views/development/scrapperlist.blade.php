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

    .tablescrapper .btn-sm{padding: 2px;}
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
                    <h2 class="page-heading">Scrapper Verification Data <button type="button" class="btn custom-button float-right ml-10" data-toggle="modal" data-target="#scrapperdatatablecolumnvisibilityList">Column Visiblity</button></h2>
                    <div class="pull-left cls_filter_box">
                        {{Form::model( [], array('method'=>'get', 'class'=>'form-inline')) }}

                            <div class="form-group ml-3 cls_filter_inputbox">
                                {{Form::text('keywords', @$inputs['keywords'], array('class'=>'form-control', 'placeholder'=>'Enter Keywords'))}}
                            </div>

                            <div class="form-group  cls_filter_inputbox">
                                <button type="submit" class="btn custom-button ml-3" style="width:100px">Search</button>
                            </div>

                            <div class="form-group  cls_filter_inputbox">
                                <button type="button" class="btn custom-button ml-3 reset" style="width:100px">Reset</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12  pl-5 tablescrapper">
    	<div class="row mb-3">
    		<div class="mt-3 col-md-12">
    		    <table class="table table-bordered table-striped" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                @if(!empty($dynamicColumnsToShowscrapper))
                                    @if (!in_array('Id', $dynamicColumnsToShowscrapper))
                                        <th width="3%">Id</th>
                                    @endif

                                    @if (!in_array('Task Id', $dynamicColumnsToShowscrapper))
                                        <th width="8%">Task Id</th>
                                    @endif

                                    @if (!in_array('Scrapper', $dynamicColumnsToShowscrapper))
                                        <th width="8%">Scrapper</th>
                                    @endif

                                    @if (!in_array('Title', $dynamicColumnsToShowscrapper))
                                        <th width="7%">Title</th>
                                    @endif

                                    @if (!in_array('Website', $dynamicColumnsToShowscrapper))
                                        <th width="7%">Website</th>
                                    @endif

                                    @if (!in_array('Sku', $dynamicColumnsToShowscrapper))
                                        <th width="7%">Sku</th>
                                    @endif

                                    @if (!in_array('Url', $dynamicColumnsToShowscrapper))
                                        <th width="5%">Url</th>
                                    @endif

                                    @if (!in_array('Images', $dynamicColumnsToShowscrapper))
                                        <th width="4%">Images</th>
                                    @endif

                                    @if (!in_array('Description', $dynamicColumnsToShowscrapper))
                                        <th width="5%">Description</th>
                                    @endif

                                    @if (!in_array('Properties', $dynamicColumnsToShowscrapper))
                                        <th width="5%">Properties</th>
                                    @endif

                                    @if (!in_array('Currency', $dynamicColumnsToShowscrapper))
                                        <th width="5%">Currency</th>
                                    @endif

                                    @if (!in_array('Size System', $dynamicColumnsToShowscrapper))
                                        <th width="4%">Size System</th>
                                    @endif

                                    @if (!in_array('Price', $dynamicColumnsToShowscrapper))
                                        <th width="3%">Price</th>
                                    @endif

                                    @if (!in_array('Discounted Price', $dynamicColumnsToShowscrapper))
                                        <th width="5%">Discounted Price</th>
                                    @endif

                                    @if (!in_array('Discounted Percentage', $dynamicColumnsToShowscrapper))
                                        <th width="5%">Discounted Percentage</th>
                                    @endif

                                    @if (!in_array('B2b Price', $dynamicColumnsToShowscrapper))
                                        <th width="3%">B2b Price</th>
                                    @endif

                                    @if (!in_array('Brand', $dynamicColumnsToShowscrapper))
                                        <th width="3%">Brand</th>
                                    @endif

                                    @if (!in_array('Is Sale', $dynamicColumnsToShowscrapper))
                                        <th width="3%">Is Sale</th>
                                    @endif

                                    @if (!in_array('Date', $dynamicColumnsToShowscrapper))
                                        <th width="7%">Date</th>
                                    @endif
                                @else 
                                    <th width="3%">Id</th>
                                    <th width="8%">Task Id</th>
                                    <th width="6%">Scrapper</th>
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
                                    <th width="7%">Date</th> 
                                @endif
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

                                $ScrapperValuesHistory = App\Models\ScrapperValuesHistory::where('task_id', $record['task_id'])->get();
                                $ScrapperValuesRemarksHistory = App\Models\ScrapperValuesRemarksHistory::where('task_id', $record['task_id'])->get();

                                $columnArray = ['title', 'website', 'sku', 'url', 'images', 'description', 'properties', 'currency', 'size_system', 'price', 'discounted_price', 'discounted_percentage', 'b2b_price', 'brand', 'is_sale'];

                                $titleRecord= $websiteRecord= $skuRecord= $urlRecord= $imagesRecord= $descriptionRecord= $propertiesRecord= $currencyRecord= $size_systemRecord= $priceRecord= $discounted_priceRecord= $discounted_percentageRecord= $b2b_priceRecord= $brandRecord= $is_saleRecord= [];

                                $titleVar= $websiteVar= $skuVar= $urlVar= $imagesVar= $descriptionVar= $propertiesVar= $currencyVar= $size_systemVar= $priceVar= $discounted_priceVar= $discounted_percentageVar= $b2b_priceVar= $brandVar= $is_saleVar= 'btn-default';

                                $titleIcon= $websiteIcon= $skuIcon= $urlIcon= $imagesIcon= $descriptionIcon= $propertiesIcon= $currencyIcon= $size_systemIcon= $priceIcon= $discounted_priceIcon= $discounted_percentageIcon= $b2b_priceIcon= $brandIcon= $is_saleIcon= 'fa fa-check';

                                foreach ($ScrapperValuesHistory as $data) {
                                    if (in_array($data['column_name'],$columnArray)) {

                                        if($data['status']=='Approve'){
                                            ${$data['column_name'] . 'Var'} = 'btn-success';
                                            ${$data['column_name'] . 'Icon'} = 'fa fa-check'; 
                                        } else {
                                            ${$data['column_name'] . 'Var'} = 'btn-danger';
                                            ${$data['column_name'] . 'Icon'} = 'fa fa-times';
                                        }
                                    }
                                }

                                foreach ($ScrapperValuesRemarksHistory as $data) {
                                    if (in_array($data['column_name'],$columnArray)) {
                                       ${$data['column_name'] . 'Record'} = $data;
                                    }
                                }
                                @endphp       

                                @if(!empty($dynamicColumnsToShowscrapper))
                                    <tr>
                                        @if (!in_array('Id', $dynamicColumnsToShowscrapper))
                                            <td>{{ $record['id'] }}</td>
                                        @endif

                                        @if (!in_array('Task Id', $dynamicColumnsToShowscrapper))
                                            <td>#DEVTASK-{{ $record['task_id'] }}</td>
                                        @endif

                                        @if (!in_array('Scrapper', $dynamicColumnsToShowscrapper))
                                            <td class="expand-row-msg" data-name="subject" data-id="{{$i}}">
                                                @if(!empty($record['tasks']['subject']))
                                                    <span class="show-short-subject-{{$i}}">{{ Str::limit($record['tasks']['subject'], 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-subject-{{$i}} hidden">{{ $record['tasks']['subject'] }}</span>
                                                @endif
                                            </td>
                                        @endif


                                        @if (!in_array('Title', $dynamicColumnsToShowscrapper))
                                            <td class="expand-row-msg" data-name="title" data-id="{{$i}}">
                                                @if(!empty($returnData['title']))
                                                    <span class="show-short-title-{{$i}}">{{ Str::limit($returnData['title'], 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-title-{{$i}} hidden">{{ $returnData['title'] }}</span>
                                                @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'title', 'taskss_id' => $record['task_id']])

                                                <!-- </br>
                                                <button type="button" class="btn {{$titleVar}} btn-sm update-scrapper-status" title="Status" data-task_id="{{$record['task_id']}}" data-column_name="title">
                                                    <i class="fa {{$titleIcon}}" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" class="btn btn-default btn-sm update-scrapper-remarks" title="Remarks" data-task_id="{{ $record['task_id'] }}" data-column_name="title">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </button>

                                                @if(!empty($titleRecord))
                                                    <button type="button" class="btn btn-default btn-sm view-scrapper-remarks" title="Remarks" data-task_id="{{ $record['task_id'] }}" data-column_name="title" data-remarks="{{$titleRecord['remarks']}}">
                                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                                    </button>
                                                @endif -->
                                            </td>
                                        @endif


                                        @if (!in_array('Website', $dynamicColumnsToShowscrapper))
                                            <td class="expand-row-msg" data-name="website" data-id="{{$i}}">
                                                @if(!empty($returnData['website']))
                                                    <span class="show-short-website-{{$i}}">{{ Str::limit($returnData['website'], 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-website-{{$i}} hidden">{{ $returnData['website'] }}</span>
                                                @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'website', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif


                                        @if (!in_array('Sku', $dynamicColumnsToShowscrapper))
                                            <td class="expand-row-msg" data-name="sku" data-id="{{$i}}">
                                                @if(!empty($returnData['sku']))
                                                    <span class="show-short-sku-{{$i}}">{{ Str::limit($returnData['sku'], 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-sku-{{$i}} hidden">{{ $returnData['sku'] }}</span>
                                                @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'sku', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif


                                        @if (!in_array('Url', $dynamicColumnsToShowscrapper))
                                            <td class="expand-row-msg" data-name="url" data-id="{{$i}}">
                                                @if(!empty($returnData['url']))
                                                    <span class="show-short-url-{{$i}}">{{ Str::limit($returnData['url'], 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-url-{{$i}} hidden">{{ $returnData['url'] }}</span>
                                                @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'url', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Images', $dynamicColumnsToShowscrapper))
                                            <td>
                                                <button type="button" data-id="<?php echo $record['id'];  ?>" class="btn scrapper-images" style="padding:1px 0px;">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </button>

                                                @include('development.partials.dynamic-column', ['columnname' => 'images', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Description', $dynamicColumnsToShowscrapper))
                                            <td class="expand-row-msg" data-name="description" data-id="{{$i}}">
                                                @if(!empty($returnData['description']))
                                                    <span class="show-short-description-{{$i}}">{{ Str::limit($returnData['description'], 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-description-{{$i}} hidden">{{ $returnData['description'] }}</span>
                                                @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'description', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Properties', $dynamicColumnsToShowscrapper))
                                            <td>
                                                <button type="button" data-id="<?php echo $record['id'];  ?>" class="btn scrapper-properties" style="padding:1px 0px;">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </button>

                                                @include('development.partials.dynamic-column', ['columnname' => 'properties', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Currency', $dynamicColumnsToShowscrapper))
                                            <td>
                                                @if(!empty($returnData['currency'])) {{ $returnData['currency'] }} @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'currency', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Size System', $dynamicColumnsToShowscrapper))
                                            <td class="expand-row-msg" data-name="size_system" data-id="{{$i}}">
                                                @if(!empty($returnData['size_system']))
                                                    <span class="show-short-size_system-{{$i}}">{{ Str::limit($returnData['size_system'], 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-size_system-{{$i}} hidden">{{ $returnData['size_system'] }}</span>
                                                @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'size_system', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Price', $dynamicColumnsToShowscrapper))
                                            <td>
                                                @if(!empty($returnData['price'])) {{ $returnData['price'] }} @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'price', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Discounted Price', $dynamicColumnsToShowscrapper))
                                            <td>
                                                @if(!empty($returnData['discounted_price'])) {{ $returnData['discounted_price'] }} @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'discounted_price', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Discounted Percentage', $dynamicColumnsToShowscrapper))
                                            <td>
                                                @if(!empty($returnData['discounted_percentage'])) {{ $returnData['discounted_percentage'] }} @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'discounted_percentage', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('B2b Price', $dynamicColumnsToShowscrapper))
                                            <td>
                                                @if(!empty($returnData['b2b_price'])) {{ $returnData['b2b_price'] }} @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'b2b_price', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Brand', $dynamicColumnsToShowscrapper))
                                            <td>
                                                @if(!empty($returnData['brand'])) {{ $returnData['brand'] }} @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'brand', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Is Sale', $dynamicColumnsToShowscrapper))
                                            <td>
                                                @if(!empty($returnData['is_sale'])) {{ $returnData['is_sale'] }} @endif

                                                @include('development.partials.dynamic-column', ['columnname' => 'is_sale', 'taskss_id' => $record['task_id']])
                                            </td>
                                        @endif

                                        @if (!in_array('Date', $dynamicColumnsToShowscrapper))
                                            <td>{{ $record['created_at'] }}</td>
                                        @endif
                                    </tr>
                                @else  
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

                                            @include('development.partials.dynamic-column', ['columnname' => 'title', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td class="expand-row-msg" data-name="website" data-id="{{$i}}">
                                            @if(!empty($returnData['website']))
                                                <span class="show-short-website-{{$i}}">{{ Str::limit($returnData['website'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-website-{{$i}} hidden">{{ $returnData['website'] }}</span>
                                            @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'website', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td class="expand-row-msg" data-name="sku" data-id="{{$i}}">
                                            @if(!empty($returnData['sku']))
                                                <span class="show-short-sku-{{$i}}">{{ Str::limit($returnData['sku'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-sku-{{$i}} hidden">{{ $returnData['sku'] }}</span>
                                            @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'sku', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td class="expand-row-msg" data-name="url" data-id="{{$i}}">
                                            @if(!empty($returnData['url']))
                                                <span class="show-short-url-{{$i}}">{{ Str::limit($returnData['url'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-url-{{$i}} hidden">{{ $returnData['url'] }}</span>
                                            @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'url', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            <button type="button" data-id="<?php echo $record['id'];  ?>" class="btn scrapper-images" style="padding:1px 0px;">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>

                                            @include('development.partials.dynamic-column', ['columnname' => 'images', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td class="expand-row-msg" data-name="description" data-id="{{$i}}">
                                            @if(!empty($returnData['description']))
                                                <span class="show-short-description-{{$i}}">{{ Str::limit($returnData['description'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-description-{{$i}} hidden">{{ $returnData['description'] }}</span>
                                            @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'description', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            <button type="button" data-id="<?php echo $record['id'];  ?>" class="btn scrapper-properties" style="padding:1px 0px;">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>

                                            @include('development.partials.dynamic-column', ['columnname' => 'properties', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            @if(!empty($returnData['currency'])) {{ $returnData['currency'] }} @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'currency', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td class="expand-row-msg" data-name="size_system" data-id="{{$i}}">
                                            @if(!empty($returnData['size_system']))
                                                <span class="show-short-size_system-{{$i}}">{{ Str::limit($returnData['size_system'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-size_system-{{$i}} hidden">{{ $returnData['size_system'] }}</span>
                                            @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'size_system', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            @if(!empty($returnData['price'])) {{ $returnData['price'] }} @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'price', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            @if(!empty($returnData['discounted_price'])) {{ $returnData['discounted_price'] }} @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'discounted_price', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            @if(!empty($returnData['discounted_percentage'])) {{ $returnData['discounted_percentage'] }} @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'discounted_percentage', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            @if(!empty($returnData['b2b_price'])) {{ $returnData['b2b_price'] }} @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'b2b_price', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            @if(!empty($returnData['brand'])) {{ $returnData['brand'] }} @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'brand', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>
                                            @if(!empty($returnData['is_sale'])) {{ $returnData['is_sale'] }} @endif

                                            @include('development.partials.dynamic-column', ['columnname' => 'is_sale', 'taskss_id' => $record['task_id']])
                                        </td>
                                        <td>{{ $record['created_at'] }}</td>
        							</tr>
                                @endif
                            @endforeach
                        </tbody>
                </table>
    			{{$records->links()}}
            </div>
        </div> 
    </div>    
    @include('development.partials.scrapper-properties')
    @include('development.partials.scrapper-images')
    @include('development.partials.column-visibility-scrapper-modal')

    <div id="update-scrapper-status-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="<?php echo route('development.updatescrapperdata'); ?>" method="post">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title text-left">Update Status</h4>
                    </div>
                    <div class="modal-body">

                        <input class="form-control" type="hidden" name="task_id" id="scrapper_task_id" />
                        <input class="form-control" type="hidden" name="column_name" id="scrapper_title" />
                    
                        <div class="form-group">
                            <select class="form-control" name="status">
                                <option>--Select Status--</option>
                                <option value="Approve">Approve</option>
                                <option value="Unapprove">Unapprove</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-default update-scrapper-status-data">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="update-scrapper-remarks-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="<?php echo route('development.updatescrapperremarksdata'); ?>" method="post">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title text-left">Update Remarks</h4>
                    </div>
                    <div class="modal-body">

                        <input class="form-control" type="hidden" name="task_id" id="scrapper_task_id" />
                        <input class="form-control" type="hidden" name="column_name" id="scrapper_title" />
                        
                        <textarea class="form-control" name="remarks" placeholder="Enter Remarks"></textarea>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-default update-scrapper-remarks-data">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="view-scrapper-remarks-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-left">Remarks</h4>
                </div>
                <div class="modal-body">
                    <p id="view-remarks-data"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
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

$(document).on('click', '.update-scrapper-status', function() {
    var $this = $(this);
    column_name = $(this).data("column_name");
    task_id = $(this).data("task_id");
    
    $("#update-scrapper-status-modal").modal("show");
    $("#update-scrapper-status-modal #scrapper_task_id").val(task_id);
    $('#update-scrapper-status-modal #scrapper_title').val(column_name);
});

$(document).on("click", ".update-scrapper-status-data", function(e) {
    e.preventDefault();
    var form = $(this).closest("form");
    $.ajax({
        url: form.attr("action"),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: form.serialize(),
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                form[0].reset();
                toastr['success'](response.message);
                $("#update-scrapper-status-modal").modal("hide");
            } else {
                toastr['error'](response.message);
            }
        }
    }).fail(function(response) {
        $("#loading-image").hide();
        toastr['error'](response.responseJSON.message);
    });
});

$(document).on('click', '.update-scrapper-remarks', function() {
    var $this = $(this);
    column_name = $(this).data("column_name");
    task_id = $(this).data("task_id");
    
    $("#update-scrapper-remarks-modal").modal("show");
    $("#update-scrapper-remarks-modal #scrapper_task_id").val(task_id);
    $('#update-scrapper-remarks-modal #scrapper_title').val(column_name);
});

$(document).on("click", ".update-scrapper-remarks-data", function(e) {
    e.preventDefault();
    var form = $(this).closest("form");
    $.ajax({
        url: form.attr("action"),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: form.serialize(),
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                form[0].reset();
                toastr['success'](response.message);
                $("#update-scrapper-remarks-modal").modal("hide");
            } else {
                toastr['error'](response.message);
            }
        }
    }).fail(function(response) {
        $("#loading-image").hide();
        toastr['error'](response.responseJSON.message);
    });
});

$(document).on('click', '.view-scrapper-remarks', function() {
    var $this = $(this);
    remarks = $(this).data("remarks");
    
    $("#view-scrapper-remarks-modal").modal("show");
    $("#view-scrapper-remarks-modal #view-remarks-data").text(remarks);
});

</script>
@endsection
