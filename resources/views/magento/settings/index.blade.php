@extends('layouts.app')

@section('title', 'Magento Settings')

@section('content')
<link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<div class="row m-0">
    <div class="col-12 p-0">
<style>
div#settingsPushLogsModal .modal-dialog { width: auto; max-width: 60%; }
.checkbox input {
    height: unset;
}
.multiselect-native-select .multiselect {
    width: 200px;
}
.select2-search--inline {
    display: contents; /*this will make the container disappear, making the child the one who sets the width of the element*/
}

.select2-search__field:placeholder-shown {
    width: 100% !important; /*makes the placeholder to be 100% of the width while there are no options selected*/
}
</style>
        <h2 class="page-heading">Magento Settings ({{$counter}})</h2>
    </div>
    @if($errors->any())
        <div class="row m-2">
          {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
        </div>
    @endif
    @if (session('success'))
        <div class="col-12">
          <div class="alert alert-success">{{session('success')}}</div>
        </div>
    @endif
    @if (session('error'))
        <div class="col-12">
          <div class="alert alert-danger">{{session('error')}}</div>
        </div>
    @endif
   
    <div class="col-lg-12 margin-tb pl-3">
        <?php $base_url = URL::to('/');?> 
        <div class="pull-left cls_filter_box">
                <form class="form-inline" action="{{ route('magento.setting.index') }}" method="GET" style="width: 100%;"> 
                <div class="form-group cls_filter_inputbox" >
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#add-setting-popup">Add Setting</button>
                    <a href="{{ route('magento.setting.sync-logs') }}" class="btn btn-secondary" id=""  target="_blank">Sync Logs</a>
                </div>  
                <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <select class="form-control select2" name="scope" data-placeholder="scope" style="width: 200px !important;">
                        <option value="">All</option> 
                        <option value="default"  {{ request('scope') && request('scope') == 'default' ? 'selected' : '' }} >default</option> 
                        <option value="websites"  {{ request('scope') && request('scope') == 'websites' ? 'selected' : '' }} >websites</option> 
                        <option value="stores"  {{ request('scope') && request('scope') == 'stores' ? 'selected' : '' }} >stores</option> 
                    </select>
                </div> 
                <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <?php $webArr = request('website') ? request('website') : [];?>
                    <select class="form-control multiselect" multiple name="website[]"  style="width: 200px !important;">
                        @foreach($storeWebsites as $w)
                            <?php $selected = '';?>
                            @if(in_array($w->id, $webArr))
                                <?php $selected = 'selected';?>
                            @endif
                            <option value="{{ $w->id }}" {{ $selected }}>{{ $w->title }}</option>
                        @endforeach
                    </select>
                </div>  
                <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <?php $nameArr = request('name') ? request('name') : [];?>
                    <select name="name[]" multiple class="form-control select2"  style="width: 162px!important;" data-placeholder="name">
                        <option value="">Name</option>
                        @foreach ($all_names as $name) 
                            <?php $selected = '';?>
                            @if(in_array($name, $nameArr))
                                <?php $selected = 'selected';?>
                            @endif
                            <option value="{{$name}}" {{$selected}}>{{$name}}</option>
                        @endforeach
                    </select>  
                </div>  
                <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <?php $pathArr = request('path') ? request('path') : [];?>
                    <select name="path[]" multiple class="form-control select2"  style="width: 162px!important;" data-placeholder="Path">
                    <option value="">Path</option>
                    @foreach ($all_paths as $path) 
                        <?php $selected = '';?>
                        @if(in_array($path, $pathArr))
                            <?php $selected = 'selected';?>
                        @endif
                        <option value="{{$path}}" {{$selected}}>{{$path}}</option>
                    @endforeach
                </select>  
                </div> 
                <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <input class="form-control" name="status" placeholder="status"  value="{{ request('status')  ? request('status') : '' }}"style="width: 160px!important;">
                    </div> 
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <button type="submit" style="" class="btn btn-image pl-0"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
                    <a href="{{ route('magento.setting.index') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                    
                    </div> 
            {{ Form::close() }}
        </div> 
        <div class="pull-right cls_filter_box">
            {{Form::open(array('url'=>route('magento.setting.pushMagentoSettings'), 'class'=>'form-inline'))}}
                <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <select class="form-control websites select2" name="store_website_id" data-placeholder="Please select website" style="width:200px !important;">
                        <option value=""></option>
                        @foreach($storeWebsites as $w)
                            <option value="{{ $w->id }}" {{ request('website') && request('website') == $w->id ? 'selected' : '' }}>{{ $w->title }}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <button title="Update Magento Settings" type="submit" style="" class="btn btn-default"><i class="fa fa-upload" aria-hidden="true"></i></button>
                </div> 
                
            {{ Form::close() }}    
        </div>
    </div>
    
     

    <div class="col-12 mb-3 mt-3 p-0">

        <div class="pull-left"></div>
        <div class="pull-right"></div>
        <div class="col-12 pl-3 pr-3">
            <div class="table-responsive">
                  <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="email-table">
                    <thead>
                        <tr>
                            <th style="display:block;">ID</th>
                            <th>Website</th>
                            <th>Store</th>
                            <th>Store View</th>
                            <th>Scope</th>
                            <th>Name</th>
                            <th>Path</th>
                            <th>Value</th>
                            <th style="width:6% !important;">Value On Magento</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Created By</th>
							{{-- <th>Data Type</th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
                        @foreach ($magentoSettings as $magentoSetting) 
                            <tr>
                                <td>{{ $magentoSetting->id }}</td>

                                @if($magentoSetting->scope === 'default')
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->website->title ?? $magentoSetting->fromStoreId->title; ?>')" >{{  substr($magentoSetting->website->title ?? $magentoSetting->fromStoreId->title, 0,10)  }} @if(strlen($magentoSetting->website->title ?? $magentoSetting->fromStoreId->title) > 10) ... @endif</td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal(' ')" >-</td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal(' ')" >-</td>

                                @elseif($magentoSetting->scope === 'websites')
                                        
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->store &&  $magentoSetting->store->website &&  $magentoSetting->store->website->storeWebsite ? $magentoSetting->store->website->storeWebsite->title : $magentoSetting->fromStoreId->title ; ?>')" >
                                            {{ $magentoSetting->store &&  $magentoSetting->store->website &&  $magentoSetting->store->website->storeWebsite ? $magentoSetting->store->website->storeWebsite->title : $magentoSetting->fromStoreId->title }} ...
                                        </td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->store->website->name ?? $magentoSetting->fromStoreId->title; ?>')" >{{ substr($magentoSetting->store->website->name ?? $magentoSetting->fromStoreId->title, 0,10) }} @if(strlen($magentoSetting->store->website->name ?? $magentoSetting->fromStoreId->website) > 10) ... @endif</td>
                                        <td>-</td>
                                        
                                @else 
                                        <td>{{ $magentoSetting->storeview && $magentoSetting->storeview->websiteStore && $magentoSetting->storeview->websiteStore->website && $magentoSetting->storeview->websiteStore->website->storeWebsite ? $magentoSetting->storeview->websiteStore->website->storeWebsite->website : $magentoSetting->fromStoreId->website }}</td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('{{$magentoSetting->storeview && $magentoSetting->storeview->websiteStore ? $magentoSetting->storeview->websiteStore->name : $magentoSetting->fromStoreId->title}}')" >  {{   substr($magentoSetting->storeview && $magentoSetting->storeview->websiteStore ? $magentoSetting->storeview->websiteStore->name : $magentoSetting->fromStoreId->title, 0,10) }}</td>
                                        <td>{{ $magentoSetting->storeview->code ?? ''}}</td>
                                @endif

                                <td>{{ $magentoSetting->scope }}</td>
                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->name ; ?>')" >{{ substr($magentoSetting->name,0,12) }} @if(strlen($magentoSetting->name) > 12) ... @endif</td>

                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->path ; ?>')" >{{ substr($magentoSetting->path,0,12) }} @if(strlen($magentoSetting->path) > 12) ... @endif</td>

                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->value ; ?>')" >{{ substr($magentoSetting->value,0,12) }} @if(strlen($magentoSetting->value) > 12) ... @endif</td>

                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php if(isset($magentoSetting->value_on_magento)) {echo  $magentoSetting->value_on_magento;   } ?>')">
                                    @if(isset($magentoSetting->value_on_magento)) {{ substr( $magentoSetting->value_on_magento, 0,10) }} @if(strlen($magentoSetting->value_on_magento) > 10) ... @endif @endif
                                </td>

                                <td>{{ $magentoSetting->created_at->format('Y-m-d') }}</td>
                                <td>{{ $magentoSetting->status }}</td>
                                <td>{{ $magentoSetting->uname }}</td>
								{{-- <td>{{ $magentoSetting->data_type }}</td> --}}
                                <td>
                                    <button type="button" value="{{ $magentoSetting->scope }}" class="btn btn-image edit-setting p-0" data-setting="{{ json_encode($magentoSetting) }}" ><img src="/images/edit.png"></button>
                                    <button type="button" data-id="{{ $magentoSetting->id }}" class="btn btn-image delete-setting p-0" ><img src="/images/delete.png"></button>
                                    <button type="button" data-id="{{ $magentoSetting->id }}" class="btn btn-image push_logs p-0" ><i class="fa fa-eye"></i></button>
                                    <button type="button" data-id="{{ $magentoSetting->id }}" class="btn btn-image push-setting p-0" title="Update Magento Settings" ><i class="fa fa-upload"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $magentoSettings->links() }}
        </div>
    </div>

</div>
<img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />


<div id="add-setting-popup" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form name="add-magento-setting-form" method="post" action="{{ route('magento.setting.create') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Add Magento Setting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <label for="">Scope</label>
                        <div class="form-group form-check">
                                
                                <input class="form-check-input scope" type="radio" value="default" name="scope[]" checked>
                                <label class="form-check-label pl-4" for="flexCheckDefault">
                                  Default
                                </label>
                              </div>
                              <div class="form-group form-check">
                                <input class="form-check-input scope" type="radio" value="websites" name="scope[]" >
                                <label class="form-check-label pl-4" for="flexCheckChecked">
                                  Websites
                                </label>
                              </div>
                              <div class="form-group form-check">
                                <input class="form-check-input scope" type="radio" value="stores" name="scope[]" >
                                <label class="form-check-label pl-4" for="flexCheckChecked">
                                  Stores
                                </label>
                              </div>
                        

                    <div class="form-group">
                        <label for="single_website">Website</label><br>
                        <select class="form-control website store-website-select" name="single_website" data-placeholder="Select setting website" style="width: 100%">
                            <option value="">Select Website</option>
                            @foreach($storeWebsites as $w)
                                <option value="{{ $w->id }}">{{ $w->title }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="single_website" name="website[]" value="" />
                    </div>
                    
                    <div class="form-group d-none website_store_form">
                        <label for="">Website Store  </label><br>
                        <select class="form-control website_store select2 " name="website_store[]" multiple data-placeholder="Select setting website store" style="width: 100%">
							
					   </select>
                    </div>       

                    <div class="form-group d-none website_store_view_form">
                        <label for="">Website Store View</label><br>
                        <select class="form-control website_store_view select2" name="website_store_view[]"  data-placeholder="Select setting website store view" style="width: 100%">
                        </select>
                    </div>                       
                    
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter setting name">

                    </div>
                    <div class="form-group">
                        <label for="">Path</label>
                        <input type="text" class="form-control" name="path" placeholder="Enter setting path">

                    </div>
                    <div class="form-group">
                        <label for="">Value</label>
                        <input type="text" class="form-control" name="value" placeholder="Enter setting value">
                    </div>
                        
                    <div class="form-group">
                        <label for="">Websites (This setting will apply to following websites)</label><br>
                        <select class="form-control website select2" name="websites[]" multiple data-placeholder="Select setting websites" style="width: 100%">
                            <option value=""></option>
                            @foreach($storeWebsites as $w)
                                <option value="{{ $w->id }}">{{ $w->title }}</option>
                            @endforeach
                        </select>
                    </div> 
					{{-- <div class="form-group">
						<label for="">Data Type</label><br>
                        <input type="radio" name="datatype" id="sensitive" value="sensitive" checked>
                        <label for="sensitive">sensitive</label><br>
                        <input type="radio" name="datatype" id="shared" value="shared">
                        <label for="shared">Shared</label><br>
                    </div> --}}
                        
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary form-save-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



<div id="edit-setting-popup" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form name="edit-magento-setting-form" class="edit-magento-setting-form" method="post" action="{{ route('magento.setting.update') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Edit Magento Setting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Scope</label>
                        <input type="text" class="form-control scope" name="scope" placeholder="Enter setting scope" readonly>
                    </div> 

                    <div class="form-group website_form d-none">
                        <label for="">Website</label><br> 
                        <input type="text" class="form-control website" name="website"  readonly>
                    </div>
                    <div class="form-group website_store_form d-none">
                        <label for="">Store</label><br> 
                        <input type="text" class="form-control website_store" name="store" readonly>
                    </div>
                    <div class="form-group website_store_view_form d-none">
                        <label for="">Store View</label><br> 
                        <input type="text" class="form-control website_store_view" name="store_view" readonly>
                    </div> 
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter setting name">
                        <button type="button" value="Log" class="btn btn-image" onclick="showlog();" data-setting="" >Log</button>
                    </div>
                    <div class="form-group">
                        <label for="">Path</label>
                        <input type="text" class="form-control" name="path" placeholder="Enter setting path">
                    </div>
                    <div class="form-group">
                        <label for="">Value</label>
                        <input type="text" class="form-control" name="value" placeholder="Enter setting value">
                    </div>
                   
                    {{-- #DEVTASK-23690-Magento Admin Settings - The below logic was not need, So I hide that.  --}}
                    {{-- <div class="form-group">
                        <label for="">Websites (This setting will apply to following websites)</label><br>
                        <select id="apply_websites"class="form-control website select2 websites" name="websites[]" multiple data-placeholder="Select setting websites" style="width: 100%">
                            <option value=""></option>
                            @foreach($storeWebsites as $w)
                                <option value="{{ $w->id }}">{{ $w->title }}</option>
                            @endforeach
                        </select>
                    </div> 
                    <div class="form-group">
                        <input type="checkbox" name="development" id="development" checked>
                        <label for="development">Devlopment</label><br>
                        <input type="checkbox" name="stage" id="stage" checked>
                        <label for="stage">Stage</label><br>
                        <input type="checkbox" name="live" id="live" checked>
                        <label for="live">Live</label>
                    </div>
					<div class="form-group">
						<label for="">Data Type</label><br>
                        <input type="radio" name="datatype" id="sensitive" value="sensitive" checked>
                        <label for="sensitive">Sensitive</label><br>
                        <input type="radio" name="datatype" id="shared" value="shared">
                        <label for="shared">Shared</label><br>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary form-save-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="push-setting-popup" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form name="push-setting-form" class="push-setting-form" method="post" action="{{ route('magento.setting.push-row-magento-settings') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Push Magento Setting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Websites</label><br>
                        {!! Form::hidden('row_id', null, ['id' => 'row_id', 'class' => 'form-control']) !!}
                        <select id="apply_tagged_websites"class="form-control website select2 websites" name="tagged_websites[]" multiple style="width: 100%">
                            @foreach($storeWebsites as $w)
                                <option value="{{ $w->id }}">{{ $w->title }}</option>
                            @endforeach
                        </select>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary form-save-btn">Update Magento Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="namepopup" class="modal fade" role="dialog">
    <!-- <div class="modal-dialog for-name-history" role="document" style="margin-right: 50%;"> -->
    <div class="modal-dialog for-name-history" role="document" style="margin-right: 50%;">
        <div class="modal-content" style="width: fit-content;" >
                <div class="modal-header">
                    <h5 class="modal-title">Name History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">
                    
                   
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                   
                </div>
          
        </div>
    </div>
</div>
<div id="push_logs" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Sync Logs</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-header">
                    <input type="date" name="sync_search_date" class="form-control sync_search_date" placeholder="Please select Date" style="width: 200px;">&nbsp;
                    <button type="button" class="btn btn-default search_sync" >Search</button>
                    <button type="button" class="btn btn-default search_sync_clear" >Clear</button>
                </div>
                <div class="modal-body" id="modal-body">
                    <table class="table table-bordered">
						<thead>
							<tr>
								<th>Website </th>
								<th>Synced on</th>
                                <th>Error Status</th>
							</tr>
						</thead>
						<tbody id="sync_data_log">
						@foreach($pushLogs as $pushLog)
							<tr>
								<td>{{$pushLog['website']}}</td>
								<td>{{$pushLog['created_at'] }}</td>
                                <td>@if($pushLog['status'] == '') Success @else {{$pushLog['status']}} @endif</td>
							</tr>
						@endforeach
						</tbody>
					</table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                   
                </div>
          
        </div>
    </div>
</div>

<div id="viewMore" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View More</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <p><span id="more-content"></span> </p>
            </div>
        </div>
    </div>
</div>

<div id="settingsPushLogsModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="max-width: 90%;width: 90%;">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Magento Push Logs</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			  <div class="modal-body">
				<div class="table-responsive mt-3">
				  <table class="table table-bordered"style="table-layout: fixed;">
					<thead>
					  <tr>
						<th width="10%">Date</th>
						<th width="30%">URL</th>
                        <th width="30%">Request Data</th>
						<th width="10%">Response</th>
                        <th width="10%">Status Code</th>
                        <th width="10%">Status</th>
                      </tr>
					</thead>
					<tbody id="settingsPushLogs">

					</tbody>
				  </table>
				</div>
			  </div>    
		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
50% 50% no-repeat;display:none;"></div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script> 
<script type="text/javascript">
    $(function (){
        $('.store-website-select').on('change', function() {
            var selected_website = $(this).val();
            if(selected_website != '') {
                $.ajax({
                    url: '{{ url("get-all/store-websites/") }}/' + $(this).val(),
                    method: 'get',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    }
                }).done(function (response) {
                    let childWebsites = new Array();
                    $.each(response, function (key, val) {
                        childWebsites[key] = val.id;
                    });
                    $('.website.select2-hidden-accessible').select2().val(childWebsites).trigger("change")
                    $("#loading-image").hide();
                });
            } else {
                let childWebsites = new Array();
                $('.website.select2-hidden-accessible').select2().val(childWebsites).trigger("change")
            }
            
        });
    })
    $(document).ready(function () {
        $(".multiselect").multiselect({
            nonSelectedText: 'Website',
            allSelectedText: 'All',
            includeSelectAllOption: true
        });
    });

    $(document).on('change', '[name="single_website"]', function (e) {
        //$('#add-setting-popup [name="website[]"]').select2("val", this.value);
        $('#single_website').val(this.value);
    });

    $(".select2").select2();

    $(document).on('submit', '[name="add-magento-setting-form"]', function (e) {
        e.preventDefault();

        if ($('#add-setting-popup .website').val() == '') {
            toastr['error']('please select the website.');
            return false;
        }

        if ($('#add-setting-popup input[name="name"]').val() == '') {
            toastr['error']('please add the name.');
            return false;
        }

        if ($('#add-setting-popup input[name="path"]').val() == '') {
            toastr['error']('please add the path.');
            return false;
        }

        if ($('#add-setting-popup input[name="value"]').val() == '') {
            toastr['error']('please add the value.');
            return false;
        }

        let formData = new FormData(this);

        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            enctype: 'multipart/form-data',
            //     dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                toastr['success'](response.message);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                toastr['error'](response.message);
            }
            
        }).fail(function () {
            console.log("error");
            $("#loading-image").hide();
        });

        return false;
    });

    $(document).on('click', '.push_logs', function (e) {
        var settingId = $(this).data('id');
        $.ajax({
            url: 'magento-admin-settings/pushLogs/' + $(this).data('id'),
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (data) {
                console.log(data);
                $("#loading-image").hide();
                $('#settingsPushLogs').html(data);
                $('#settingsPushLogsModal').modal('show');
            },
        });
    });

    $(document).on('click', '.edit-setting', function (e) {
        $('.edit-magento-setting-form select[name="websites"]').val('');
        $('.edit-magento-setting-form select[name="websites"]').trigger('change');

        var data = $(this).data('setting');
        $('.edit-magento-setting-form input[name="name"]').val(data.name);
        $('.edit-magento-setting-form input[name="path"]').val(data.path);
        $('.edit-magento-setting-form input[name="value"]').val(data.value);
        var scope = $('.scope').val(data.scope);
        if (data.scope == 'default') {
            if (jQuery.isEmptyObject(data.website) == false)
                $('#edit-setting-popup .website').val(data.website.website);
            else
                $('#edit-setting-popup .website').val(data.from_store_id.website);
            //$('#edit-setting-popup .website').val(data.website.website);
            $('#edit-setting-popup .website_form').removeClass('d-none');
            $('#edit-setting-popup .website_store_form').addClass('d-none');
            $('#edit-setting-popup .website_store_view_form').addClass('d-none');
        } else if (data.scope == 'websites') {
            if (jQuery.isEmptyObject(data.store) == false)
                $('#edit-setting-popup .website').val(data.store.website.store_website.website);
            else
                $('#edit-setting-popup .website').val(data.from_store_id.website);
            if (jQuery.isEmptyObject(data.store) == false)
                $('#edit-setting-popup .website_store').val(data.store.website.name);
            else
                $('#edit-setting-popup .website_store').val(data.from_store_id.title);
            $('#edit-setting-popup .website_form').removeClass('d-none');
            $('#edit-setting-popup .website_store_form').removeClass('d-none');
            $('#edit-setting-popup .website_store_view_form').addClass('d-none');
        } else {

            $('#edit-setting-popup .website').val(data.storeview.website_store.website.store_website.website);
            $('#edit-setting-popup .website_store').val(data.storeview.website_store.website.name);
            $('#edit-setting-popup .website_store_view').val(data.storeview.code);
            $('#edit-setting-popup .website_form').removeClass('d-none');
            $('#edit-setting-popup .website_store_form').removeClass('d-none');
            $('#edit-setting-popup .website_store_view_form').removeClass('d-none');
        }
        //$('.websites').trigger('change.select2');

        $('#edit-setting-popup').attr('data-id', data.id).modal('show');
    });

    $(document).on('submit', '[name="edit-magento-setting-form"]', function (e) {
        e.preventDefault();

        if ($('#edit-setting-popup input[name="name"]').val() == '') {
            toastr['error']('please add the name.');
            return false;
        }
        ;

        if ($('#edit-setting-popup input[name="path"]').val() == '') {
            toastr['error']('please add the path.');
            return false;
        }
        ;

        if ($('#edit-setting-popup input[name="value"]').val() == '') {
            toastr['error']('please add the value.');
            return false;
        }
        ;

        let formData = new FormData(this);

        formData.append('_token', "{{ csrf_token() }}");
        formData.append('id', $('#edit-setting-popup').attr('data-id'));

        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            //enctype: 'multipart/form-data',
            dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                toastr['success'](response.message);
            } else {
                toastr['error'](response.message);
            }
            location.reload();
        }).fail(function () {
            console.log("error");
            $("#loading-image").hide();
        });

        return false;
    });


    $(document).on('change', '#add-setting-popup .scope', function () {
        var scope = $(this).val();
        if (scope == 'default') {
            $('#add-setting-popup .website_store').addClass('d-none');
            $('#add-setting-popup .website_store_form').addClass('d-none');
            $('#add-setting-popup .website_store_view_form').addClass('d-none');
            $('#add-setting-popup .website_store').trigger('change');
            $('#add-setting-popup .website_store_view').trigger('change');
            return false;
        } else if (scope == 'websites') {
            //$('#add-setting-popup .website').attr('multiple', false).val('');
            $('#add-setting-popup .website').trigger('change');
            $('#add-setting-popup .website_store').attr('multiple', true);
            $('#add-setting-popup .website_store').trigger('change');
            $('#add-setting-popup .website_store_form').removeClass('d-none');
            $('#add-setting-popup .website_store_view_form').addClass('d-none');
        } else if (scope == 'stores') {
            //$('#add-setting-popup .website').attr('multiple', false).val('');
            $('#add-setting-popup .website').trigger('change');
            $('#add-setting-popup .website_store').attr('multiple', false).val('');
            $('#add-setting-popup .website_store').trigger('change');
            $('#add-setting-popup .website_store_view').attr('multiple', true);
            $('#add-setting-popup .website_store_view').trigger('change');
            $('#add-setting-popup .website_store_form').removeClass('d-none');
            $('#add-setting-popup .website_store_view_form').removeClass('d-none');
        }
    })
    $('#add-setting-popup').on('hidden.bs.modal', function () {
        $("form[name=add-magento-setting-form]")[0].reset();
        $(".website.select2-hidden-accessible").val('').trigger('change');
    });
    
    $(document).on('click', '.search_sync_clear', function () {
        var syncDate = $('.sync_search_date').val();

        $.ajax({
            url: '{{ route("get.magento.sync.data") }}',
            method: 'get',
            data: {
                _token: '{{ csrf_token() }}',
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            $("#loading-image").hide();
            var html = '';
            response.data.forEach(function (value, index) {
                var statusError = 'Success';
                if (value.status) {
                    statusError = value.status;
                }
                html += `<tr><td>${value.website}</td>`;
                html += `<td>${value.created_at}</td>`;
                html += `<td>${statusError}</td></tr>`;
            })
            $('#sync_data_log').html(html);
            toastr['success'](response.msg);
        }).fail(function () {
            $("#loading-image").hide();
            toastr['error'](response.msg);
        });
    });

    $(document).on('click', '.search_sync', function () {
        var syncDate = $('.sync_search_date').val();

        $.ajax({
            url: '{{ route("get.magento.sync.data") }}',
            method: 'get',
            data: {
                _token: '{{ csrf_token() }}',
                sync_date: syncDate
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            $("#loading-image").hide();
            var html = '';
            response.data.forEach(function (value, index) {
                var statusError = 'Success';
                if (value.status) {
                    statusError = value.status;
                }
                html += `<tr><td>${value.website}</td>`;
                html += `<td>${value.created_at}</td>`;
                html += `<td>${statusError}</td></tr>`;
            })
            $('#sync_data_log').html(html);
            toastr['success'](response.msg);
        }).fail(function () {
            $("#loading-image").hide();
            toastr['error'](response.msg);
        });
    });

    $(document).on('change', '#add-setting-popup .website', function () {
        var scope = $('#add-setting-popup .scope:checked').val();
        if (scope == 'default') {
            return false;
        }
        var website_id = $(this).val();
        $.ajax({
            url: '{{ route("get.website.stores") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                website_id: website_id
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            $("#loading-image").hide();
            var html = '';
            response.data.forEach(function (value, index) {
                html += `<option value="${value.id}">${value.name}</option>`
            })
            $('#add-setting-popup .website_store').append(html);
            $('#add-setting-popup .website_store').attr('multiple', 'multiple');
            $('#add-setting-popup .website_store').select2();
        }).fail(function () {
            console.log("error");
        });
    });

    function opnModal(message) {
        $(document).find('#more-content').html(message);
    }

    $(document).on('change', '#add-setting-popup .website_store', function () {
        var scope = $('#add-setting-popup .scope:checked').val();
        if (scope == 'websites') {
            return false;
        }
        var website_id = $(this).val();
        $.ajax({
            url: '{{ route("get.website.store.views") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                website_id: website_id
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            $("#loading-image").hide();
            var html = '';
            response.data.forEach(function (value, index) {
                html += `<option value="${value.id}">${value.code}</option>`
            })
            $('#add-setting-popup .website_store_view').html(html);
            $('#add-setting-popup .website_store_view').select2();
        }).fail(function () {
            console.log("error");
        });
    });

    $(".push-setting").on('click', function(e) {
        var row_id = $(this).data("id");

        var url = "{{ route('magento.setting.get-magento-setting', '') }}/" + row_id;
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: url,
        }).done(function(response) {
            if(response.code == '500') {
                toastr['error'](response.error);
            } else {
                $(".push-setting-form #row_id").val(row_id);
                var taggedWebsites = [];
                $(response.taggedWebsites).each(function(index, store_websites) {
                    taggedWebsites.push(store_websites.id);
                });
                $(".push-setting-form #apply_tagged_websites").val(taggedWebsites).trigger('change');
                $("#push-setting-popup").modal("show");
            }
        }).fail(function(response) {});
    });


    $(document).on('click', '.delete-setting', function () {
        var id = $(this).data('id');
        if (confirm('Do you really want to delete this magento-setting?')) {
            $.ajax({
                url: '/magento-admin-settings/delete/' + id,
            }).done(function (response) {
                $("#loading-image").hide();
                location.reload();
            }).fail(function () {
                console.log("error");
            });
        }
    });


    var isLoading = false;
    var page = 1;
    $(document).ready(function () {

        $(window).scroll(function () {
            if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                loadMore();
            }
        });

        function loadMore() {
            var searchOpt = location.search
            if (isLoading)
                return;
            isLoading = true;
            var $loader = $('.infinite-scroll-products-loader');
            page = page + 1;
            if (searchOpt) {
                searchOptUrl = searchOpt + "&ajax=1&page=" + page;
            } else {
                searchOptUrl = "?ajax=1&page=" + page;
            }
            $.ajax({
                url: "{{url('magento-admin-settings')}}" + searchOptUrl,
                type: 'GET',
                data: $('.form-search-data').serialize(),
                beforeSend: function () {
                    $loader.show();
                },
                success: function (data) {

                    $loader.hide();
                    if ('' === data.trim())
                        return;
                    $('.infinite-scroll-cashflow-inner').append(data);


                    isLoading = false;
                },
                error: function () {
                    $loader.hide();
                    isLoading = false;
                }
            });
        }
    });

    function showlog() {
        id = $('#edit-setting-popup').attr('data-id');
        $.ajax({
            url: '{{url("/magento-admin-settings/namehistrory/")}}/' + id,
        }).done(function (response) {
            $('#modal-body').html(response);
            $('#namepopup').modal('show');

        }).fail(function () {
            console.log("error");
        });


    }
</script>
@endsection
