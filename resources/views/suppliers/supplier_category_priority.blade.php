@extends('layouts.app')

@section('favicon' , 'supplierlist.png')


@section('title', 'Supplier Category Priority List')


@section('large_content')
<div class="row">
    <div class="mt-3 col-md-12">
      <table class="table table-bordered table-responsive table-striped">
        <thead>
          <tr>
              <th width="25%">Name</th>
              <th width="25%">Category</th>
              <th width="25%">Priority</th>
              <th width="25%">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($suppliers as $supplier)
			<tr>
				<td>
                    <div class="form-group">
                        <select class="form-control change-whatsapp-no" data-supplier-id="<?php echo $supplier->id; ?>">
                            <option value="">-No Selected-</option>
                            @foreach(array_filter(config("apiwha.instances")) as $number => $apwCate)
                                @if($number != "0")
                                    <option {{ ($number == $supplier->whatsapp_number && $supplier->whatsapp_number != '') ? "selected='selected'" : "" }} value="{{ $number }}">{{ $number }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    {{ $supplier->supplier }}
                    @if ($supplier->has_error == 1)
                        <span class="text-danger">!!!</span>
                    @endif
				</td>
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
                    <select name="supplier_cat" class="form-control supplier_cat" data-supplier-id="{{ $supplier->id }}">
                        <option value="">Select</option>
                        @forelse ($suppliercategory as $key => $item)
                            <option value="{{ $key }}" {{ ($supplier->supplier_category_id == $key) ? 'selected' : ''}} >{{ $item }}</option>
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
				{{-- <td class="expand-row {{ $supplier->last_type == "email" && $supplier->email_seen == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
					@if($supplier->phone)
						<input type="text" name="message" id="message_{{$supplier->id}}" placeholder="whatsapp message..." class="form-control send-message" data-id="{{$supplier->id}}">
					@endif
					@if ($supplier->last_type == "email")
					Email
					@elseif ($supplier->last_type == "message")
						{{ strlen($supplier->message) > 10 ? substr($supplier->message, 0, 10).'...' : $supplier->message }}
					@endif
					<a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHRM }}" data-object="supplier" data-id="{{$supplier->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('/images/chat.png')}}" alt=""></a>
					<a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHRM }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="{{asset('/images/archive.png')}}" alt=""></a>
					<a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHRM }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load Auto PDF"><img src="{{asset('/images/icon-pdf.svg')}}" alt=""></a>
					<a type="button" class="btn btn-xs btn-image show-translate-history"  data-id="{{$supplier->id}}"  title="Show history"><img src="{{asset('/images/history.svg')}}" alt=""></a>
				</td> --}}
					<!--td>
						<input class="supplier-update-status" type="checkbox" data-id="{{ $supplier->id }}" <?php echo ($supplier->supplier_status_id == 1) ? "checked" : "" ?> data-toggle="toggle" data-onstyle="secondary" data-width="10">
					</td-->
					{{-- <td>{{ $supplier->created_at }}</td> --}}
					{{-- <td>{{ $supplier->updated_at }}</td> --}}
			  <td>
					<div class="form-group">
						<select name="autoTranslate" data-id="{{ $supplier->id }}" class="form-control input-sm mb-3 autoTranslate">
							<option value="">Translations Languages</option>
							<option value="fr" {{ $supplier->language === 'fr'  ? 'selected' : '' }}>French</option>
							<option value="de" {{ $supplier->language === 'de'  ? 'selected' : '' }}>German</option>
							<option value="it" {{ $supplier->language === 'it'  ? 'selected' : '' }}>Italian</option>
						</select>
					</div>
				</td>
                <td>
                <div class="form-group">
						<select name="priority" data-id="{{ $supplier->id }}" class="form-control input-sm mb-3 priority">
							<option value="">Priority</option>
							<option value="1" {{ $supplier->priority === '1'  ? 'selected' : '' }}>Critical</option>
							<option value="2" {{ $supplier->priority === '2'  ? 'selected' : '' }}>High</option>
							<option value="3" {{ $supplier->priority === '3'  ? 'selected' : '' }}>Medium</option>
                            <option value="4" {{ $supplier->priority === '4'  ? 'selected' : '' }}>Low</option>
						</select>
					</div>       
                </td>


                <td>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Supplierbtn({{$supplier->id}})"><i class="fa fa-arrow-down"></i></button>
                </td>

            </tr>
            <tr class="action-supplierbtn-tr-{{$supplier->id}} d-none">
                <td class="font-weight-bold">Action</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    </div>
    {{-- {!! $suppliers->appends(Request::except('page'))->links() !!} --}}
@endsection