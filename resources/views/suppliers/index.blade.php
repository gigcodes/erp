@extends('layouts.app')

@section('favicon' , 'supplierlist.png')


@section('title', 'Suppliers List')

@section('styles')
  <style type="text/css">
    .numberSend {
          width: 160px;
          background-color: transparent;
          color: transparent;
          text-align: center;
          border-radius: 6px;
          position: absolute;
          z-index: 1;
          left: 23%;
          margin-left: -80px;
          display: none;
    }

    .wrapword {
        white-space: -moz-pre-wrap !important;  /* Mozilla, since 1999 */
        white-space: -webkit-pre-wrap;          /* Chrome & Safari */
        white-space: -pre-wrap;                 /* Opera 4-6 */
        white-space: -o-pre-wrap;               /* Opera 7 */
        white-space: pre-wrap;                  /* CSS3 */
        word-wrap: break-word;                  /* Internet Explorer 5.5+ */
        word-break: break-all;
        white-space: normal;
    }
    .erp-btn-list{
      display: flex;
    }
  </style>


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@php
    $isAdmin = \Auth::user()->hasRole('Admin');
    $isHRM = \Auth::user()->hasRole('HOD of CRM');
@endphp

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Suppliers List ({{ $suppliers->total() }})</h2>
			  <form class="" action="{{ route('supplier.index') }}" method="GET">
				<div class="row">
                  	<div class="form-group col-md-3">
						<div class="">
							<input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" placeholder="Search">
						</div>
                  	</div>
					{{-- <div class="form-group">
						<div class="col-md-3">
							<input type="text" class="form-control" name="source" id="source" placeholder="Source..">
						</div>
					</div> --}}
					<div class="form-group col-md-3">
							<select class="form-control select-multiple2" style="width:100%" name="supplier_filter[]" data-placeholder="Search Supplier By Name.." multiple>
								@foreach($suppliers_all as $supplier)
									<option value="{{ $supplier->id }}" @if(is_array($supplier_filter) && in_array($supplier->id,$supplier_filter)) selected @endif>{{ $supplier->supplier }}</option>
								@endforeach
							</select>
					</div>
					<div class="form-group col-md-3">
							<select class="form-control" name="type">
							<option value="">Select Type</option>
							<option value="has_error" {{ isset($type) && $type == 'has_error' ? 'selected' : '' }}>Has Error</option>
							<option value="not_updated" {{ isset($type) && $type == 'not_updated' ? 'selected' : '' }}>Not Updated</option>
							<option value="updated" {{ isset($type) && $type == 'updated' ? 'selected' : '' }}>Updated</option>
							</select>
          </div>

                    <div class="form-group col-md-3">
                        {!!Form::select('supplier_status_id', ["" => "select supplier status"] + $supplierstatus,request()->get('supplier_status_id'), ['class' => 'form-control form-control-sm'])!!}
                    </div>
                </div>
                <div class="row">

					<div class="form-group col-md-3">
							{!!Form::select('supplier_category_id', ["" => "select category"] + $suppliercategory, request()->get('supplier_category_id'), ['class' => 'form-control form-control-sm'])!!}

					</div>
					{{-- <div class="form-group col-md-3">
							<select class="form-control select-multiple2" style="width: 100%" name="brand[]" data-placeholder="Select brand.." multiple>
							<optgroup label="Brands">
								@foreach ($brands as $key => $value)
								<option value="{{ $value->id }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $value->name }}</option>
								@endforeach
							</optgroup>
							</select>
					</div> --}}
					{{-- <div class="form-group col-md-3">
							<select  style="width: 100%" class="form-control select-multiple2" name="scrapedBrand[]" data-placeholder="Select ScrapedBrand.." multiple>
							<optgroup label="Brands">
								@foreach ($scrapedBrands as $key => $value)
								@if(!in_array($value, $selectedBrands))
									<option value="{{ $value }}"> {{ $value}}</option>
								@endif
								@endforeach
							</optgroup>
							</select>
					</div> --}}
					<div class="form-group col-md-3">
						<?php echo Form::select("updated_by",
							["" => "-- Select Updated By--"] +\App\User::pluck("name","id")->toArray(),
							request('updated_by'),
							["class"=> "form-control select-multiple2", "style" => "width: 100%"]
						); ?>
                    </div>
          <div class="form-group col-md-3">
							<select class="form-control" name="scrappertype">
							<option value="">Select Scrapper</option>
							<option value="1" {{ isset($scrappertype) && $scrappertype == '1' ? 'selected' : '' }}>SCRAPPER</option>
							<option value="2" {{ isset($scrappertype) && $scrappertype == '2' ? 'selected' : '' }}>EXCEL</option>
							<option value="3" {{ isset($scrappertype) && $scrappertype == '3' ? 'selected' : '' }}>NONE</option>
							</select>
					</div>

					<div class="form-group ml-3">
						<div class="col-md-3">
							<input type="checkbox" name="status" id="status" value="1" {{ request()->get('status') == '1' ? 'checked' : ''}}><label for="status">Active</label>
						</div>
                    </div>
                    <div class="form-group">
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                    </div>
				</div>
				</form>
        </div>
        <div class="col-lg-12 margin-tb mt-3">
          <button type="button" class="btn btn-secondary manage-scraped-brand-raw" data-toggle="modal" data-target="#manageScrapedBrandsRaw">
            Manage Scraped Brands Raw
          </button>
          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#emailToAllModal">Bulk Email</button>
          <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#supplierCreateModal">+</button>
          <button type="button" class="btn btn-secondary ml-3" id="add_category_btn" data-toggle="modal" data-target="#categoryCreateModal">Add Category</button>
          <button type="button" class="btn btn-secondary ml-3" id="add_subcategory_btn" data-toggle="modal" data-target="#subcategoryCreateModal">Add Sub Category</button>
          <button type="button" class="btn btn-secondary ml-3" id="add_status_btn" data-toggle="modal" data-target="#statusCreateModal">Add Status</button>
          <button type="button" class="btn btn-secondary ml-3" id="add_suppliersize_btn" data-toggle="modal" data-target="#suppliersizeCreateModal">Add Supplier Size</button>
          <a class="btn btn-secondary create_broadcast" href="javascript:;">Create Broadcast</a>
        </div>
    </div>

    @include('partials.flash_messages')

    @include('purchase.partials.modal-email')
    @include('suppliers.partials.modal-emailToAll')
    <div class="row">
    <div class="mt-3 col-md-12">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="10%">Name</th>
            <th width="10%">Telephone Number</th>
            <th width="10%">Whatsup Number</th>
            <th width="30%">Email</th>
            <th width="5%">Scrapper</th>
              {{-- <th>Source</th> --}}
              <th>Designers</th>
              <th>Supplier Status</th>
              <th>Supplier Size</th>
              <th>Category</th>
              <th>Sub Category</th>
              {{-- <th>No.of Brands</th> --}}
            {{-- <th width="10%">Social handle</th> --}}
            {{-- <th>Agents</th> --}}
            {{-- <th width="5%">GST</th> --}}
            {{-- <th width="20%">Order</th> --}}
            {{-- <th width="20%">Emails</th> --}}
            <th width="45%">Communication</th>
            <th>Status</th>
            {{-- <th>Created At</th> --}}
            {{-- <th>Updated At</th> --}}
            <th>Inventory Lifetime (Days)</th>
            <th width="15%">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($suppliers as $supplier)
			<tr>
				<td>{{ $supplier->id }}
        <input type="checkbox" name="supplier_message[]" class="d-inline supplier_message" value="{{$supplier->id}}">
        </td>
				<td>
					{{ $supplier->supplier }} <br>
            <div class="erp-btn-list">
					@if ($supplier->is_flagged == 1)
					<button type="button" class="btn btn-image flag-supplier" data-id="{{ $supplier->id }}"><img src="/images/flagged.png" /></button>
					@else
					<button type="button" class="btn btn-image flag-supplier" data-id="{{ $supplier->id }}"><img src="/images/unflagged.png" /></button>
					@endif
					@if($supplier->phone)
					<button type="button" class="btn btn-image call-select popup" data-id="{{ $supplier->id }}"><img src="/images/call.png"/></button>

					@if ($supplier->is_blocked == 1)
						<button type="button" class="btn btn-image block-twilio" data-id="{{ $supplier->id }}"><img src="/images/blocked-twilio.png"/></button>
					@else
						<button type="button" class="btn btn-image block-twilio" data-id="{{ $supplier->id }}"><img src="/images/unblocked-twilio.png"/></button>
					@endif
					@endif
					<button data-toggle="modal" data-target="#reminderModal" class="btn btn-image set-reminder" data-id="{{ $supplier->id }}" data-frequency="{{ $supplier->frequency ?? '0' }}" data-reminder_message="{{ $supplier->reminder_message }}">
						<img src="{{ asset('images/alarm.png') }}" alt=""  style="width: 18px;">
          </button>
          </div>
          <div class="numberSend" id="show{{ $supplier->id }}">
					<select class="form-control call-twilio" data-context="suppliers" data-id="{{ $supplier->id }}" data-phone="{{ $supplier->phone }}">
						<option disabled selected>Select Number</option>
						@foreach(\Config::get("twilio.caller_id") as $caller)
						<option value="{{ $caller }}">{{ $caller }}</option>
						@endforeach
					</select>
					</div>
					@if ($supplier->has_error == 1)
						<span class="text-danger">!!!</span>
					@endif

					<p>
						<div class="form-group">
							<select class="form-control change-whatsapp-no" data-supplier-id="<?php echo $supplier->id; ?>">
								<option value="">-No Selected-</option>
								@foreach($whatsappConfigs as $whatsappConfig)
									@if($whatsappConfig->number != "0")
										<option {{ ($whatsappConfig->number == $supplier->whatsapp_number && $supplier->whatsapp_number != '') ? "selected='selected'" : "" }} value="{{ $whatsappConfig->number }}">{{ $whatsappConfig->number }}</option>
									@endif
								@endforeach
							</select>
						</div>
					</p>
					<p>
					<div class="form-group">
						<select name="autoTranslate" data-id="{{ $supplier->id }}" class="form-control input-sm mb-3 autoTranslate">
							<option value="">Translations Languages</option>
							<option value="fr" {{ $supplier->language === 'fr'  ? 'selected' : '' }}>French</option>
							<option value="de" {{ $supplier->language === 'de'  ? 'selected' : '' }}>German</option>
							<option value="it" {{ $supplier->language === 'it'  ? 'selected' : '' }}>Italian</option>
						</select>
					</div>
					</p>
					</span>
				</td>
				<td class="expand-row">
          <div>
          <input type="text" name="phone" id="phone{{$supplier->id}}" data-phone-id="{{ $supplier->id }}"placeholder="Phone" class="form-control send-supplier-phone" value="{{ $supplier->phone }}">
          </div>
        </td>
        <td class="expand-row">
          <div>
          <input type="text" name="whatsapp" id="whatsapp{{$supplier->id}}" data-whatsapp-id="{{ $supplier->id }}"placeholder="Whatsapp" class="form-control send-supplier-whatsapp" value="{{ $supplier->whatsapp_number }}">
          </div>
        </td>
        <td>
        <div>
          <input type="text" name="email" id="email{{$supplier->id}}" data-mail-id="{{ $supplier->id }}"placeholder="Email" class="form-control send-supplier-email" value="{{ $supplier->email }}">
          </div>
        </td>
        <td>
        <select name="scrapper" class="form-control scrapper" data-scrapper-id="{{ $supplier->id }}">
              <option value="">Select</option>
              <option value="1" {{ ($supplier->scrapper == '1') ? 'selected' : ''}} >SCRAPPER</option>
              <option value="2" {{ ($supplier->scrapper == '2') ? 'selected' : ''}}>EXCEL</option>
              <option value="3" {{ ($supplier->scrapper == '3') ? 'selected' : ''}}>NONE</option>
        </select>
        </td>
				{{-- <td>{{ $supplier->source }}</td> --}}
				<td class="expand-row">
					@if(strlen($supplier->brands) > 4)
						@php
							$dns = $supplier->brands;
							$dns = str_replace('"[', '', $dns);
							$dns = str_replace(']"', '', $dns);
						@endphp

						<div class="td-mini-container brand-supplier-mini-{{ $supplier->id }}">
							{{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
						</div>
						<div class="td-full-container hidden brand-supplier-full-{{ $supplier->id }}">
							{{ $dns }}
						</div>
					@else
						N/A
					@endif
                </td>
                <td>
                    <select name="supplier_status" class="form-control supplier_status" data-status-id="{{ $supplier->id }}">
                        <option value="">Select</option>
                        @forelse ($supplierstatus as $supplierstatus_key => $item)
                            <option value="{{ $supplierstatus_key }}" {{ ($supplier->supplier_status_id == $supplierstatus_key) ? 'selected' : ''}} >{{ $item }}</option>
                        @empty
                        @endforelse
                    </select>
                </td>
                <td>
                    <select name="supplier_size" class="form-control supplier_size" data-size-id="{{ $supplier->id }}">
                        <option value="">Select</option>
                        @forelse ($suppliersize as $suppliersize_key => $item)
                            <option value="{{ $suppliersize_key }}" {{ ($supplier->supplier_size_id == $suppliersize_key) ? 'selected' : ''}} >{{ $item }}</option>
                        @empty
                        @endforelse
                    </select>
                </td>
                <td>
                    <select name="supplier_cat" class="form-control supplier_cat" data-supplier-id="{{ $supplier->id }}">
                        <option value="">Select</option>
                        @forelse ($suppliercategory as $key => $item)
                            <option value="{{ $key }}" {{ ($supplier->supplier_category_id == $key) ? 'selected' : ''}} >{{ $item }}</option>
                        @empty
                        @endforelse
                    </select>
                </td>
                <td>
                    <select name="supplier_subcat" class="form-control supplier_subcat" data-supplier-id="{{ $supplier->id }}">
                        <option value="">Select</option>
                        @forelse ($suppliersubcategory as $key => $item)
                            <option value="{{ $key }}" {{ ($supplier->supplier_sub_category_id == $key) ? 'selected' : ''}} >{{ $item }}</option>
                        @empty
                        @endforelse
                    </select>
                </td>

				{{-- <td>{{count(array_filter(explode(',',$supplier->brands)))}}</td> --}}
				{{-- <td class="expand-row" style="word-break: break-all;">
					<div class="td-mini-container">
						{{ strlen($supplier->social_handle) > 10 ? substr($supplier->social_handle, 0, 10).'...' : $supplier->social_handle }}
					</div>
					<div class="td-full-container hidden">
						{{ $supplier->social_handle }}
					</div>
				</td> --}}
				{{-- <td>
					@if ($supplier->agents)
					<ul>
						@foreach ($supplier->agents as $agent)
						<li>
							<strong>{{ $agent->name }}</strong> <br>
							{{ $agent->phone }} - {{ $agent->email }} <br>
							<span class="text-muted">{{ $agent->address }}</span> <br>
							<button type="button" class="btn btn-xs btn-secondary edit-agent-button" data-toggle="modal" data-target="#editAgentModal" data-agent="{{ $agent }}">Edit</button>
						</li>
						@endforeach
					</ul>
					@endif
				</td> --}}

				{{-- <td>{{ $supplier->gst }}</td> --}}
				{{-- <td>
					@if ($supplier->purchase_id != '')
					<a href="{{ route('purchase.show', $supplier->purchase_id) }}" target="_blank">Purchase ID {{ $supplier->purchase_id }}</a>
					<br>
					{{ \Carbon\Carbon::parse($supplier->purchase_created_at)->format('H:m d-m') }}
					@endif
				</td> --}}
				{{-- <td class="{{ $supplier->email_seen == 0 ? 'text-danger' : '' }}"  style="word-break: break-all;">
					{{ strlen(strip_tags($supplier->email_message)) > 0 ? 'Email' : '' }}
				</td> --}}
				<td class="expand-row {{ $supplier->last_type == "email" && $supplier->email_seen == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
					@if($supplier->phone)
						<input type="text" name="message" id="message_{{$supplier->id}}" placeholder="whatsapp message..." class="form-control send-message" data-id="{{$supplier->id}}">
					@endif
					@if ($supplier->last_type == "email")
					Email
					@elseif ($supplier->last_type == "message")
						<div class="td-mini-container">
							{{ strlen($supplier->message) > 10 ? substr($supplier->message, 0, 10).'...' : $supplier->message }}
						</div>
						<div class="td-full-container hidden">
							{{ $supplier->message }}
						</div>

					@if ($supplier->message != '')
						<button type="button" class="btn btn-xs btn-secondary load-more-communication" data-id="{{ $supplier->id }}">Load More</button>

						<ul class="more-communication-container">

						</ul>
					@endif
					@endif
					<a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHRM }}" data-object="supplier" data-id="{{$supplier->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></a>
					<a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHRM }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></a>
					<a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHRM }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load Auto PDF"><img src="/images/icon-pdf.svg" alt=""></a>
				</td>
					<td>
						<input class="supplier-update-status" type="checkbox" data-id="{{ $supplier->id }}" <?php echo ($supplier->supplier_status_id == 1) ? "checked" : "" ?> data-toggle="toggle" data-onstyle="secondary" data-width="10">
					</td>
					{{-- <td>{{ $supplier->created_at }}</td> --}}
					{{-- <td>{{ $supplier->updated_at }}</td> --}}
					<td>
          <input class="inventory_lifetime" style="width:50px;"  type="text" data-supplier-id="{{ $supplier->id }}" value="{{ $supplier->inventory_lifetime }}" data-width="10">
          </td>
				<td>
					<div style="min-width: 100px;">
						<a href="{{ route('supplier.show', $supplier->id) }}" class="btn  d-inline btn-image" href=""><img src="/images/view.png" /></a>

						{{-- <button type="button" class="btn btn-xs create-agent" data-toggle="modal" data-target="#createAgentModal" data-id="{{ $supplier->id }}">Add Agent</button> --}}
						<button data-toggle="modal" data-target="#zoomModal" class="btn btn-image set-meetings" data-id="{{ $supplier->id }}" data-type="supplier"><i class="fa fa-video-camera" aria-hidden="true"></i></button>
						<button type="button" class="btn btn-image edit-supplier d-inline" data-toggle="modal" data-target="#supplierEditModal" data-supplier="{{ json_encode($supplier) }}"><img src="/images/edit.png" /></button>
						<button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $supplier->id }}"><img src="/images/remark.png" /></button>

						{!! Form::open(['method' => 'DELETE','route' => ['supplier.destroy', $supplier->id],'style'=>'display:inline']) !!}
						<button type="submit" class="btn btn-image d-inline"><img src="/images/delete.png" /></button>
						{!! Form::close() !!}

						@if ($supplier->scraped_brands_raw != '')
						<button data-toggle="modal" data-target="#updateBrand" class="btn btn-image update-brand" data-id="{{ $supplier->id }}" title="Update Brands">
						<img src="{{ asset('images/list-128x128.png') }}" alt="" style="width: 18px;">
						</button>
                        @endif
					</div>
				</td>
				</tr>
          @endforeach
        </tbody>
      </table>
    </div>
    </div>
    {!! $suppliers->appends(Request::except('page'))->links() !!}

    @include('partials.modals.remarks')

    @include('suppliers.partials.supplier-modals')
    {{-- @include('suppliers.partials.agent-modals') --}}
    @include('suppliers.partials.modals')


    <div id="reminderModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Set/Edit Reminder</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="frequency">Frequency (in Minutes)</label>
                        <select class="form-control" name="frequency" id="frequency">
                            <option value="0">Disabled</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="45">45</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                            <option value="60">60</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reminder_message">Reminder Message</label>
                        <textarea name="reminder_message" id="reminder_message" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary save-reminder">Save</button>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="updateBrand" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Scraped Brands</h4>
                </div>
                <div class="modal-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th width="50%">Pick Brands</th>
                        <th width="50%">Existing Brands <img src="{{ asset('images/copy_256.png') }}" id="copyScrapedBrands" style="cursor: pointer; width: 18px; float: right;" alt="Copy" title="Copy selected scraped brands to brands"></th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td>
                            <div style="overflow-y: scroll; height: 250px">
                              <div id="brandRawList"></div>
                            </div>
                          </td>
                          <td>
                            <div style="overflow-y: scroll; height: 250px">
                              <div id="selectedBrands"></div>
                            </div>
                          </td>
                        </tr>
                    </tbody>
                  </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-default" id="doUpdateBrand">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div id="manageScrapedBrandsRaw"  class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Manage Scraped Brands Raw (<?php echo count($scrapedBrands); ?>)</h4>
                </div>
                <div class="modal-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th width="50%">Pick ScrapedBrand Brands Raw To Hide Or Remove</th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td>
                            <div style="overflow-y: scroll; height: 250px; width: 465px">
                              @foreach ($scrapedBrands as $key => $value)
                               <input type="checkbox" class="newBrandSelection wrapword" name="scrapedBrands[]" value="{{$value}}" style="margin-right:10px" {{ in_array($value, $selectedBrands) ? 'checked' : ''}}>{{ $value }}<br>
                              @endforeach
                            </div>
                          </td>
                        </tr>
                    </tbody>
                  </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-default manageScrapedBrandsSave">Save</button>
                </div>
            </div>
        </div>
    </div>
@include('customers.zoomMeeting');
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script src="{{asset('js/zoom-meetings.js')}}"></script>
  <script type="text/javascript">

      var supplierToRemind = null;
        $(document).ready(function() {
           $(".select-multiple").multiselect({
             buttonWidth: '100%',
             includeSelectAllOption: true
           });
        });
    $(document).on('click', '.set-reminder', function() {
        let supplierId = $(this).data('id');
        let frequency = $(this).data('frequency');
        let message = $(this).data('reminder_message');

        $('#frequency').val(frequency);
        $('#reminder_message').val(message);
        supplierToRemind = supplierId;

    });

    $(document).on('click', '.save-reminder', function() {
        let frequency = $('#frequency').val();
        let message = $('#reminder_message').val();

        $.ajax({
            url: "{{action('SupplierController@updateReminder')}}",
            type: 'POST',
            success: function() {
                toastr['success']('Reminder updated successfully!');
            },
            data: {
                supplier_id: supplierToRemind,
                frequency: frequency,
                message: message,
                _token: "{{ csrf_token() }}"
            }
        });
    });

    // cc

    $(document).on('click', '.add-cc', function (e) {
        e.preventDefault();

        if ($('#cc-label').is(':hidden')) {
            $('#cc-label').fadeIn();
        }

        var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

        $('#cc-list').append(el);
    });

    $(document).on('click', '.cc-delete-button', function (e) {
        e.preventDefault();
        var parent = $(this).parent().parent();

        parent.hide(300, function () {
            parent.remove();
            var n = 0;

            $('.cc-input').each(function () {
                n++;
            });

            if (n == 0) {
                $('#cc-label').fadeOut();
            }
        });
    });

    // bcc

    $(document).on('click', '.add-bcc', function (e) {
        e.preventDefault();

        if ($('#bcc-label').is(':hidden')) {
            $('#bcc-label').fadeIn();
        }

        var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

        $('#bcc-list').append(el);
    });

    $(document).on('click', '.bcc-delete-button', function (e) {
        e.preventDefault();
        var parent = $(this).parent().parent();

        parent.hide(300, function () {
            parent.remove();
            var n = 0;

            $('.bcc-input').each(function () {
                n++;
            });

            if (n == 0) {
                $('#bcc-label').fadeOut();
            }
        });
    });

    //

    $(document).on('click', '.edit-supplier', function() {
      var supplier = $(this).data('supplier');
      console.log(supplier)
      var url = "{{ url('supplier') }}/" + supplier.id;

      $('#supplierEditModal form').attr('action', url);
      $('#supplier_supplier').val(supplier.supplier);
      $('#supplier_address').val(supplier.address);
      $('#supplier_phone').val(supplier.phone);
      $('#supplier_email').val(supplier.email);
      $('#supplier_social_handle').val(supplier.social_handle);
      $('#supplier_gst').val(supplier.gst);
      $('#status').val(supplier.status);
      $('#supplier_status_id').val(supplier.supplier_status_id);
      $('#supplier_category_id').val(supplier.supplier_category_id);
      $('#scraper_name').val(supplier.scraper_name);
      $('#inventory_lifetime').val(supplier.inventory_lifetime);
    });

    $(document).on('click', '.send-supplier-email', function() {
      var id = $(this).data('id');

      $('#emailSendModal').find('input[name="supplier_id"]').val(id);
    });

    $(document).on('click', '.load-more-communication', function() {
      var thiss = $(this);
      var supplier_id = $(this).data('id');

      $.ajax({
        type: "GET",
        url: "{{ url('supplier') }}/" + supplier_id + '/loadMoreMessages',
        data: {
          supplier_id: supplier_id
        },
        beforeSend: function() {
          $(thiss).text('Loading...');
        }
      }).done(function(response) {
        (response.messages).forEach(function(index) {
          var li = '<li>' + index + '</li>';

          $(thiss).closest('td').find('.more-communication-container').append(li);
        });

        $(thiss).remove();
      }).fail(function(response) {
        $(thiss).text('Load More');

        alert('Could not load more messages');

        console.log(response);
      });
    });

    // $(document).on('click', '.create-agent', function() {
    //   var id = $(this).data('id');
    //
    //   $('#agent_supplier_id').val(id);
    // });

    // $(document).on('click', '.edit-agent-button', function() {
    //   var agent = $(this).data('agent');
    //   var url = "{{ url('agent') }}/" + agent.id;
    //   $('#agent_whatsapp_number option[value=""]').prop('selected', 'selected');
    //
    //   $('#editAgentModal form').attr('action', url);
    //   $('#agent_name').val(agent.name);
    //   $('#agent_address').val(agent.address);
    //   $('#agent_phone').val(agent.phone);
    //   $('#agent_whatsapp_number option[value="' + agent.whatsapp_number + '"]').prop('selected', 'selected');
    //   $('#agent_email').val(agent.email);
    // });

    $(document).on('click', '.flag-supplier', function() {
      var supplier_id = $(this).data('id');
      var thiss = $(this);

      $.ajax({
        type: "POST",
        url: "{{ route('supplier.flag') }}",
        data: {
          _token: "{{ csrf_token() }}",
          supplier_id: supplier_id
        },
        beforeSend: function() {
          $(thiss).text('Flagging...');
        }
      }).done(function(response) {
        if (response.is_flagged == 1) {
          $(thiss).html('<img src="/images/flagged.png" />');
        } else {
          $(thiss).html('<img src="/images/unflagged.png" />');
        }

      }).fail(function(response) {
        $(thiss).html('<img src="/images/unflagged.png" />');

        alert('Could not flag supplier!');

        console.log(response);
      });
    });

    $(document).on('click', '.make-remark', function(e) {
      e.preventDefault();

      var id = $(this).data('id');
      $('#add-remark input[name="id"]').val(id);

      $.ajax({
          type: 'GET',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.gettaskremark') }}',
          data: {
            id:id,
            module_type: "supplier"
          },
      }).done(response => {
          var html='';

          $.each(response, function( index, value ) {
            html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            html+"<hr>";
          });
          $("#makeRemarkModal").find('#remark-list').html(html);
      });
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark').find('textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'supplier'
          },
      }).done(response => {
          $('#add-remark').find('textarea[name="remark"]').val('');

          var html =' <p> '+ remark +' <br> <small>By You updated on '+ moment().format('DD-M H:mm') +' </small></p>';

          $("#makeRemarkModal").find('#remark-list').append(html);
      }).fail(function(response) {
        console.log(response);

        alert('Could not fetch remarks');
      });
    });

    $(document).on('click', '.expand-row', function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            // if ($(this).data('switch') == 0) {
            //   $(this).text($(this).data('details'));
            //   $(this).data('switch', 1);
            // } else {
            //   $(this).text($(this).data('subject'));
            //   $(this).data('switch', 0);
            // }
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on('keyup', '.send-message', function(event) {
        if (event.keyCode != 13) {
            return;
        }

        let supplierId = $(this).attr('data-id');
        let message = $(this).val();
        let self = this;

        if (message == '') {
            return;
        }

        $.ajax({
            url: "{{action('WhatsAppController@sendMessage', 'supplier')}}",
            type: 'post',
            data: {
                message: message,
                supplier_id: supplierId,
                _token: "{{csrf_token()}}",
                status: 2
            },
            success: function() {
                $(self).removeAttr('disabled');
                $(self).val('');
                toastr['success']("Message sent successfully!", "Success");
            },
            beforeSend: function() {
                $(self).attr('disabled', true);
            },
            error: function() {
                $(self).removeAttr('disabled');
            }
        });

    });

      $(document).on('click', '.block-twilio', function () {
          var supplier_id = $(this).data('id');
          var thiss = $(this);

          $.ajax({
              type: "POST",
              url: "{{ route('supplier.block') }}",
              data: {
                  _token: "{{ csrf_token() }}",
                  supplier_id: supplier_id
              },
              beforeSend: function () {
                  $(thiss).text('Blocking...');
              }
          }).done(function (response) {
              if (response.is_blocked == 1) {
                  $(thiss).html('<img src="/images/blocked-twilio.png" />');
              } else {
                  $(thiss).html('<img src="/images/unblocked-twilio.png" />');
              }
          }).fail(function (response) {
              $(thiss).html('<img src="/images/unblocked-twilio.png" />');

              alert('Could not block customer!');

              console.log(response);
          });
      });

      $(document).on('click', '.call-select', function() {
        var id = $(this).data('id');
        $('#show'+id).toggle();
        console.log('#show'+id);
      });

      // $(document).on('change', '.call-twilio1', function() {

      //   console.log('hello');
      //   var id = $(this).data('id');
      //   var numberToCall = $(this).data('phone');
      //   var context = $(this).data('context');
      //   var numberCallFrom = $(this).children("option:selected").val();
      //   //$('#show'+id).hide();
      //   console.log(id);
      //   console.log(numberToCall);
      //   console.log(context);
      //   console.log(numberCallFrom);

      // });

      //function to display existing scraped brands
      function showScrapedBrands(scrapedBrands){
          var existingScrapedBrands = '';
          if (scrapedBrands.length > 0) {
              var delImg = "{{ asset('images/delete-red-cross.png') }}";
              $.each(scrapedBrands, function( index, value ) {
                  existingScrapedBrands += '<li style="display: block; margin: 3px 0;"><div style="display: block; width:85%; float:left;">' + value + '</div><div style="display: block; width:15%; float:left; padding-left:10px;"><img src="' + delImg + '" class="removeExistingBrand" data-value="' + value + '" alt="Remove scraped brand" style="cursor: pointer; width: 12px;"></div></li>';
              });
              existingScrapedBrands = '<ul style="list-style:none; margin:0; padding:0;">' + existingScrapedBrands + '</ul>';
          }

          $('#selectedBrands').html(existingScrapedBrands);
      }

      //function to display raw scraped brands
      function showRawScrapedBrands(scrapedBrands, rawScrapedBrands){
          var rawBrands = '';
          var existingBrandCnt = 0;
          if (rawScrapedBrands.length > 0) {
              $.each(rawScrapedBrands, function( index, value ) {
                  rawBrands += '<input type="checkbox" class="newBrandSelection" name="newBrands[]" value="' + value + '"';
                  if (scrapedBrands.indexOf(value) > -1){
                    rawBrands += ' checked ';
                    existingBrandCnt++;
                  }
                  rawBrands += ' style="margin-right:10px">' + value + '<br>';
              });

              var selectAllBrands = '<input type="checkbox" class="selectAllScrapedBrands" name="selectAllScrapBrands" style="margin-right:10px"';
              if (rawScrapedBrands.length == existingBrandCnt) {
                  selectAllBrands += ' checked ';
              }
              selectAllBrands += '>Select All<br>';

              rawBrands = selectAllBrands + ' ' + rawBrands;
          }

          $('#brandRawList').html(rawBrands);
      }

      //Show selected brand and raw brands after opening the update brand modal
      var brandUpdateSupplierId = 0;
      $('.update-brand').on('click', function() {
          brandUpdateSupplierId = $(this).data('id');

          $('#doUpdateBrand').prop('disabled', false);

          $('#brandRawList').html('');
          $('#selectedBrands').html('');
          $.ajax({
              url: "{{ route('supplier.scrapedbrands.list') }}",
              type: 'GET',
              data: {
                  id: brandUpdateSupplierId
              },
              success: function(data) {
                  showScrapedBrands(data.scrapedBrands);
                  showRawScrapedBrands(data.scrapedBrands, data.scrapedBrandsRaw);
              }
          });
      });

      //Select / unselect all scraped brands
      $('#brandRawList').on('click', '.selectAllScrapedBrands', function(){
          $('.newBrandSelection').prop('checked', $(this).prop('checked'));
      })

      //Send selected brands to backend and update supplier brands
      $('#doUpdateBrand').on('click', function() {
          $('#doUpdateBrand').prop('disabled', true);

          //Send data to server and close modal
          var newBrands = [];
          $('.newBrandSelection').each(function(){
              if($(this).prop('checked') == true){
                  newBrands.push($(this).val());
              }
          });

          //ajax call coming here...
          $.ajax({
              url: "{{ route('supplier.scrapedbrands.update') }}",
              type: 'POST',
              data: {
                  id: brandUpdateSupplierId,
                  newBrandData: newBrands,
                  _token: "{{ csrf_token() }}"
              },
              success: function() {
                  alert('Brands updated successfully');
                  $('#updateBrand').modal('hide');
                  $('#doUpdateBrand').prop('disabled', false);
                  brandUpdateSupplierId = 0;
              }
          });
      });

      //Delete Srcaped brands
      $('#selectedBrands').on('click', '.removeExistingBrand', function(){
          var removeBrand = $(this).data('value');
          if(confirm('Are you sure to remove ' + removeBrand + '?')){
              //call delete function
              $.ajax({
                  url: "{{ route('supplier.scrapedbrands.remove') }}",
                  type: 'POST',
                  data: {
                      id: brandUpdateSupplierId,
                      removeBrandData: removeBrand,
                      _token: "{{ csrf_token() }}"
                  },
                  success: function(data) {
                      showScrapedBrands(data.scrapedBrands);
                      showRawScrapedBrands(data.scrapedBrands, data.scrapedBrandsRaw);
                      alert('Brands removed successfully');
                  }
              });
          }
      });

      $(document).ready(function() {
          $(".select-multiple").multiselect();
          $(".select-multiple2").select2();
      });

      $('.manageScrapedBrandsSave').on('click', function() {
        if(confirm("Are you sure you want to perform this operation?")){
            $('#manageScrapedBrandsRaw').modal('toggle');
            $.ajax({
                url: "{{ route('manageScrapedBrands') }}",
                type: 'POST',
                data: {
                    selectedBrands: $('.newBrandSelection:checked').serializeArray
                    ().map(function(obj) {
                      return obj.value;
                    }),
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                   alert(data);
                   location.reload();
                }
            });
          }
      });

      $(document).on('change', '.change-whatsapp-no', function () {
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('supplier.change.whatsapp') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    supplier_id : $this.data("supplier-id"),
                    number: $this.val()
                }
            }).done(function () {
                alert('Number updated successfully!');
            }).fail(function (response) {
               alert('Please check entry for supplier');
            });
        });

      $(document).on('change', '.autoTranslate', function () {
            var $this = $(this);
            var supplier_id = $this.data("id");
            var language = $this.val();
            $.ajax({
                type: "PUT",
                url: "/supplier/language-translate/"+supplier_id,
                data: {
                    _token: "{{ csrf_token() }}",
                    supplier_id : supplier_id,
                    language: language
                }
            }).done(function () {
                alert('Language updated successfully!');
            }).fail(function (response) {
               alert('Please check entry for supplier');
            });
        });

      $(document).on("change",".supplier-update-status",function(){
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: "supplier/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    supplier_id: $this.data("id"),
                    supplier_status_id : $this.prop('checked')
                }
            }).done(function(data){
                if(data.code == 200) {
                    toastr["success"](data.message);
                }
            }).fail(function(error) {

            })
      });

    $(document).on('change', '.supplier_subcat', function() {
        var sub_cat = $(this).val();
        var supplierId = $(this).data('supplier-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/subcategory') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                supplier_sub_category_id : sub_cat
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
            //location.reload();
        }).fail(function(error) {

        })
    });

   $(document).on('keypress', '.inventory_lifetime', function(e) {
    if(e.which == 13) {
        var inventory_lifetime = $(this).val();
        var supplierId = $(this).data('supplier-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/inventorylifetime') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                inventory_lifetime : inventory_lifetime
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
            //location.reload();
        }).fail(function(error) {

        })
      }
    });

    $(document).on('keypress', '.send-supplier-email', function(e) {
    if(e.which == 13) {
        var email = $(this).val();
        var supplierId = $(this).data('mail-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/mail') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                email : email
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
            //location.reload();
        }).fail(function(error) {

        })
      }
    });

    $(document).on('keypress', '.send-supplier-phone', function(e) {
    if(e.which == 13) {
        var phone = $(this).val();
        var supplierId = $(this).data('phone-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/phone') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                phone : phone
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
            //location.reload();
        }).fail(function(error) {

        })
      }
    });

    $(document).on('keypress', '.send-supplier-whatsapp', function(e) {
    if(e.which == 13) {
        var whatsapp = $(this).val();
        var supplierId = $(this).data('whatsapp-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/whatsapp') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                whatsapp : whatsapp
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
            //location.reload();
        }).fail(function(error) {

        })
      }
    });

    $(document).on('change', '.supplier_cat', function() {
        var cat = $(this).val();
        var supplierId = $(this).data('supplier-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/category') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                supplier_category_id : cat
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
          //  location.reload();
        }).fail(function(error) {
        })
    });

    $(document).on('change', '.supplier_status', function() {
        var status = $(this).val();
        var supplierId = $(this).data('status-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/status') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                status : status
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
            //location.reload();
        }).fail(function(error) {
        })
    });


    $(document).on('change', '.supplier_size', function() {
        var size = $(this).val();
        var supplierId = $(this).data('size-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/size') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                size : size
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
            //location.reload();
        }).fail(function(error) {
        })
    });

    $(document).on('change', '.scrapper', function() {
        var scrapper = $(this).val();
        var supplierId = $(this).data('scrapper-id');
        $.ajax({
            type: "POST",
            url: "{{ route('supplier/change/scrapper') }}",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                scrapper : scrapper
            }
        }).done(function(data){
            if(data.code == 200) {
                toastr["success"](data.message);
            }
            //location.reload();
        }).fail(function(error) {

        })
    });

    $(document).on('click', '.create_broadcast', function () {
        var suppliers = [];
        $(".supplier_message").each(function () {
            if ($(this).prop("checked") == true) {
                suppliers.push($(this).val());
            }
        });
        if (suppliers.length == 0) {
            alert('Please select supplier');
            return false;
        }
        $("#create_broadcast").modal("show");
    });

    $("#send_message").submit(function (e) {
        e.preventDefault();
        var suppliers = [];
        $(".supplier_message").each(function () {
            if ($(this).prop("checked") == true) {
                suppliers.push($(this).val());
            }
        });
        if (suppliers.length == 0) {
            alert('Please select supplier');
            return false;
        }

        if ($("#send_message").find("#message_to_all_field").val() == "") {
            alert('Please type message ');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('supplier/send/message') }}",
            data: {
                _token: "{{ csrf_token() }}",
                message: $("#send_message").find("#message_to_all_field").val(),
                suppliers: suppliers
            }
        }).done(function () {
            window.location.reload();
        }).fail(function (response) {
            $(thiss).text('No');

            alert('Could not say No!');
            console.log(response);
        });
    });
  </script>
@endsection
