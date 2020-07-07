@extends('layouts.app')

@section('title', 'Supplier Page')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">

    <style>
        #chat-history {
            background-color: #EEEEEE;
            height: 450px;
            overflow-y: scroll;
            overflow-x: hidden;
            width: 100%;
        }

        .speech-wrapper .bubble.alt {
            margin: 0 0 25px 20% !important;
        }

        .show-images-wrapper {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .label-attached-img {
            border: 1px solid #fff;
            display: block;
            position: relative;
            cursor: pointer;
        }

        .label-attached-img:before {
            background-color: white;
            color: white;
            content: " ";
            display: block;
            border-radius: 50%;
            border: 1px solid grey;
            position: absolute;
            top: -5px;
            left: -5px;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 28px;
            transition-duration: 0.4s;
            transform: scale(0);
        }

        :checked + .label-attached-img {
            border-color: #ddd;
        }

        :checked + .label-attached-img:before {
            content: "✓";
            background-color: grey;
            transform: scale(1);
        }

        :checked + .label-attached-img img {
            transform: scale(0.9);
            box-shadow: 0 0 5px #333;
            z-index: -1;
        }

    </style>
@endsection

@section('content')


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h3>Supplier Page</h3>
            </div>
            <div class="pull-right mt-4">
                <a class="btn btn-xs btn-secondary" href="{{ route('supplier.index') }}">Back</a>
                {{-- <a class="btn btn-xs btn-secondary" href="#" id="quick_add_lead">+ Lead</a>
                <a class="btn btn-xs btn-secondary" href="#" id="quick_add_order">+ Order</a>
                <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#privateViewingModal">Set Up for Private Viewing</button> --}}
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div id="exTab2" class="container">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#info-tab" data-toggle="tab">Supplier Info</a>
            </li>
            <li>
                <a href="#agents-tab" data-toggle="tab">Agents</a>
            </li>
            <li>
                <a href="#email-tab" data-toggle="tab" data-supplierid="{{ $supplier->id }}" data-type="inbox">Emails</a>
            </li>
            <li>
                <a href="#brands-tab" data-toggle="tab" data-supplierid="{{ $supplier->id }}" data-type="inbox">Brands</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-4 border">
            <div class="tab-content">
                <div class="tab-pane mt-3" id="brands-tab">
                    <h2 class="page-heading">Brands</h2>
                    @if(strlen($supplier->brands) > 4)
                        @php
                            $dns = $supplier->brands;
                            $dns = str_replace('"[', '', $dns);
                            $dns = str_replace(']"', '', $dns);
                            $dns = explode(',', $dns);
                        @endphp

                        @foreach($dns as $dn)
                            <li>{{ $dn }}</li>
                        @endforeach
                    @else
                        N/A
                    @endif
                </div>
                <div class="tab-pane active mt-3" id="info-tab">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group form-inline">
                                <input type="text" name="supplier" id="supplier_supplier" class="form-control input-sm" placeholder="Supplier" value="{{ $supplier->supplier }}">

                                @if ($supplier->is_flagged == 1)
                                    <button type="button" class="btn btn-image flag-supplier" data-id="{{ $supplier->id }}"><img src="/images/flagged.png"/></button>
                                @else
                                    <button type="button" class="btn btn-image flag-supplier" data-id="{{ $supplier->id }}"><img src="/images/unflagged.png"/></button>
                                @endif
                            </div>

                            <div class="form-group form-inline">
                                <input type="number" id="supplier_phone" name="phone" class="form-control input-sm" placeholder="910000000000" value="{{ $supplier->phone }}">
                            </div>

                            <div class="form-group">
                                <select class="form-control input-sm" name="default_phone" id="supplier_default_phone">
                                    <option value="">Select Default Phone</option>
                                    @if ($supplier->phone != '')
                                        <option value="{{ $supplier->phone }}" {{ $supplier->phone == $supplier->default_phone ? 'selected' : '' }}>{{ $supplier->phone }} - Supplier's Phone</option>
                                    @endif

                                    @if ($supplier->agents)
                                        @foreach ($supplier->agents as $agent)
                                            <option value="{{ $agent->phone }}" {{ $agent->phone == $supplier->default_phone ? 'selected' : '' }}>{{ $agent->phone }} - {{ $agent->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- <div class="form-group">
                                <select class="form-control form-control-sm" name="status" id="status">
                                    <option {{ !$supplier->status ? 'selected' : '' }} value="0">Inactive</option>
                                    <option {{ $supplier->status ? 'selected' : '' }} value="1">Active</option>
                                </select>
                            </div> -->

                            {{-- <div class="form-group">
                              <input type="number" id="supplier_whatsapp_number" name="whatsapp_number" class="form-control input-sm" placeholder="Whatsapp Number" value="{{ $supplier->whatsapp_number }}">
                            </div> --}}

                            <div class="form-group">
                                <textarea name="address" id="supplier_address" class="form-control input-sm" rows="3" cols="80" placeholder="Address">{{ $supplier->address }}</textarea>
                            </div>

                            {{-- @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM')) --}}
                            <div class="form-group">
                                <input type="email" name="email" id="supplier_email" class="form-control input-sm" placeholder="Email" value="{{ $supplier->email }}">
                            </div>

                            {{-- <div class="form-group">
                              <select class="form-control input-sm" name="default_email" id="supplier_default_email">
                                <option value="">Select Default Email</option>
                                @if ($supplier->email != '')
                                  <option value="{{ $supplier->email }}" {{ $supplier->email == $supplier->default_email ? 'selected' : '' }}>{{ $supplier->email }} - Supplier's Email</option>
                                @endif

                                @if ($supplier->agents)
                                  @foreach ($supplier->agents as $agent)
                                    <option value="{{ $agent->email }}" {{ $agent->email == $supplier->default_email ? 'selected' : '' }}>{{ $agent->email }} - {{ $agent->name }}</option>
                                  @endforeach
                                @endif
                              </select>
                            </div> --}}

                            <div class="form-group">
                                <input type="text" name="instagram_handle" id="supplier_instagram_handle" class="form-control input-sm" placeholder="Instagram Handle" value="{{ $supplier->instagram_handle }}">
                            </div>

                            <div class="form-group">
                                <input type="text" name="social_handle" id="supplier_social_handle" class="form-control input-sm" placeholder="Social Handle" value="{{ $supplier->social_handle }}">
                            </div>

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
                            

                            <div class="form-group">
                                <input type="text" name="website" id="supplier_website" class="form-control input-sm" placeholder="Website" value="{{ $supplier->website }}">
                            </div>

                            <div class="form-group">
                                <input type="text" name="gst" id="supplier_gst" class="form-control input-sm" placeholder="GST" value="{{ $supplier->gst }}">
                            </div>
                            <div class="form-group">
                                {!!Form::select('supplier_category_id', [null=>'Select a category'] + $suppliercategory->toArray(), $supplier->supplier_category_id, ['class' => 'form-control form-control-sm' , 'id' => 'supplier_category_id'])!!}
                            </div>
                            <div class="form-group">
                                {!!Form::select('supplier_status_id', $supplierstatus, $supplier->supplier_status_id, ['class' => 'form-control form-control-sm', 'id' => 'supplier_status_id'])!!}
                            </div>

                            <div class="form-group">
                                <input type="text" name="scraper_name" id="supplier_scraper_name" class="form-control input-sm" placeholder="Scraper Name" value="{{ ($supplier->scraper) ? $supplier->scraper->scraper_name : '' }}">
                            </div>

                            <div class="form-group">
                                <input type="text" name="inventory_lifetime" id="supplier_inventory_lifetime" class="form-control input-sm" placeholder="Inventory Lifetime (in days)" value="{{ $supplier->inventory_lifetime }}">
                            </div>

                            <div class="form-group">
                                <button type="button" id="updateSupplierButton" class="btn btn-xs btn-secondary">Save</button>
                            </div>
                        </div>

                    </div>
                </div>

                @include('suppliers.partials.agent-modals')

                <div class="tab-pane mt-3" id="agents-tab">
                    <button type="button" class="btn btn-xs btn-secondary mb-3 create-agent" data-toggle="modal" data-target="#createAgentModal" data-id="{{ $supplier->id }}">Add Agent</button>

                    <div id="agentAccordion">
                        @foreach ($supplier->agents as $key => $agent)
                            <div class="card">
                                <div class="card-header" id="headingAgent{{ $key + 1 }}">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed collapse-fix" data-toggle="collapse" data-target="#agent{{ $key + 1 }}" aria-expanded="false" aria-controls="agent{{ $key + 1 }}">
                                            {{ $key + 1 }} {{ $agent->name }}
                                        </button>
                                    </h5>
                                </div>
                                <div id="agent{{ $key + 1 }}" class="collapse collapse-element" aria-labelledby="headingAgent{{ $key + 1 }}" data-parent="#agentAccordion">
                                    <div class="card-body">
                                        <form action="{{ route('agent.update', $agent->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="model_id" value="{{ $supplier->id }}">
                                            <input type="hidden" name="model_type" value="App\Supplier">

                                            <div class="form-group">
                                                <strong>Name</strong>
                                                <input type="text" name="name" class="form-control input-sm" value="{{ $agent->name }}">
                                            </div>

                                            <div class="form-group">
                                                <strong>Phone:</strong>
                                                <input type="number" name="phone" class="form-control input-sm" value="{{ $agent->phone }}">

                                                @if ($errors->has('phone'))
                                                    <div class="alert alert-danger">{{$errors->first('phone')}}</div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <strong>Address:</strong>
                                                <input type="text" name="address" class="form-control input-sm" value="{{ $agent->address }}">

                                                @if ($errors->has('address'))
                                                    <div class="alert alert-danger">{{$errors->first('address')}}</div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <strong>Email:</strong>
                                                <input type="email" name="email" class="form-control input-sm" value="{{ $agent->email }}">

                                                @if ($errors->has('email'))
                                                    <div class="alert alert-danger">{{$errors->first('email')}}</div>
                                                @endif
                                            </div>

                                            <div class="form-group text-center">
                                                <button type="submit" class="btn btn-xs btn-secondary">Update</button>
                                            </div>
                                        </form>

                                        <form action="{{ route('agent.destroy', $agent->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane mt-3" id="email-tab">
                    <div id="exTab3" class="mb-3">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#email-inbox" data-toggle="tab" id="email-inbox-tab" data-supplierid="{{ $supplier->id }}" data-type="inbox">Inbox</a>
                            </li>
                            <li>
                                <a href="#email-sent" data-toggle="tab" id="email-sent-tab" data-supplierid="{{ $supplier->id }}" data-type="sent">Sent</a>
                            </li>
                            <li class="nav-item ml-auto">
                                <button type="button" class="btn btn-image" data-toggle="modal" data-target="#emailSendModal"><img src="{{ asset('images/filled-sent.png') }}"/></button>
                            </li>
                        </ul>
                    </div>

                    <div id="email-container">
                        @include('purchase.partials.email')
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-4 mb-3">
            <div class="border">
                <form action="{{ route('whatsapp.send', 'supplier') }}" method="POST" enctype="multipart/form-data">
                    <div class="d-flex">
                        @csrf

                        <div class="form-group">
                            <div class="upload-btn-wrapper btn-group pr-0 d-flex">
                                <button class="btn btn-image px-1"><img src="/images/upload.png"/></button>
                                <input type="file" name="image"/>

                                <button type="submit" class="btn btn-image px-1 send-communication received-customer"><img src="/images/filled-sent.png"/></button>
                            </div>
                        </div>

                        <div class="form-group flex-fill mr-3">
                            <button type="button" id="supplierMessageButton" class="btn btn-image"><img src="/images/support.png"/></button>
                            <textarea class="form-control mb-3 hidden" style="height: 110px;" name="body" placeholder="Received from Supplier"></textarea>
                            <input type="hidden" name="status" value="0"/>
                        </div>
                    </div>

                </form>

                <form action="{{ route('whatsapp.send', 'supplier') }}" method="POST" enctype="multipart/form-data">
                    <div id="paste-container" style="width: 200px;">

                    </div>

                    <div class="d-flex">
                        @csrf

                        <div class="form-group">
                            <div class=" d-flex flex-column">
                                <div class="">
                                    <div class="upload-btn-wrapper btn-group px-0">
                                        <button class="btn btn-image px-1"><img src="/images/upload.png"/></button>
                                        <input type="file" name="image"/>

                                    </div>
                                    <button type="submit" class="btn btn-image px-1 send-communication"><img src="/images/filled-sent.png"/></button>

                                </div>
                            </div>
                        </div>

                        <div class="form-group flex-fill mr-3">
                            <textarea id="message-body" class="form-control mb-3" style="height: 110px;" name="body" placeholder="Send for approval"></textarea>

                            <input type="hidden" name="screenshot_path" value="" id="screenshot_path"/>
                            <input type="hidden" name="status" value="1"/>

                            <div class="paste-container"></div>


                        </div>
                    </div>

                    <div class="pb-4 mt-3">
                        <div class="row">
                            <div class="col">
                                <select name="quickCategory" id="quickCategory" class="form-control input-sm mb-3">
                                    <option value="">Select Category</option>
                                    @foreach($reply_categories as $category)
                                        <option value="{{ $category->approval_leads }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>

                                <select name="quickComment" id="quickComment" class="form-control input-sm">
                                    <option value="">Quick Reply</option>
                                </select>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#ReplyModal" id="approval_reply">Create Quick Reply</button>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="row">
                    <div class="col">
                        <select name="autoTranslate" id="autoTranslate" class="form-control input-sm mb-3">
                            <option value="">Translations Languages</option>
                            <option value="fr" {{ $supplier->language === 'fr'  ? 'selected' : '' }}>French</option>
                            <option value="de" {{ $supplier->language === 'de'  ? 'selected' : '' }}>German</option>
                            <option value="it" {{ $supplier->language === 'it'  ? 'selected' : '' }}>Italian</option>
                        </select>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-xs btn-secondary" id="auto-translate">Add translation language</button>
                    </div>
                </div>
            </div>
            <div id="notes" class="mt-3">
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#collapse1">Remarks ({{ is_array($supplier->notes) ? count($supplier->notes) : 0 }})</a>
                            </h4>
                        </div>
                        <div id="collapse1" class="panel-collapse collapse">
                            <div class="panel-body" id="note_list">
                                @if($supplier->notes && is_array($supplier->notes))
                                    @foreach($supplier->notes as $note)
                                        <li>{{ $note }}</li>
                                    @endforeach
                                @endif
                            </div>
                            <div class="panel-footer">
                                <input name="add_new_remark" id="add_new_remark" type="text" placeholder="Type new remark..." class="form-control add-new-remark">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="excel" class="mt-3">
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#collapse-excel">Excel Importer </a>
                            </h4>
                        </div>
                        <div id="collapse-excel" class="panel-collapse collapse">

                            <div class="panel-footer">
                                <form action="/supplier/excel-import" method="POST" enctype="multipart/form-data">
                                    @csrf
                                <input name="excel_file" type="file" class="form-control">
                                <input type="hidden" name="id" value="{{ $supplier->id }}">
                                <button type="submit" class="btn btn-secondary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-4">
            <div class="border">
                {{-- <h4>Messages</h4> --}}

                <div class="row">
                    <form action="{{ route('supplier.image') }}" method="post" enctype="multipart/form-data" style="width: 100%">
                        @csrf
                        <button type="buttin" class="btn btn-xs btn-secondary" value="1" name="type" id="createProduct">Create Product</button>
                        <button type="button" class="btn btn-xs btn-secondary" value="2" name="type" id="createGroup">Create Product Group</button>
                        <button type="button" class="btn btn-xs btn-secondary" value="3" name="type" id="createInStockProduct">Create InStock Product</button>
                        <a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></a>
                        <a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></a>
                        <a type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load PDF"><img src="/images/icon-pdf.svg" alt=""></a>
                        <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message">
                        <div class="load-communication-modal chat-history-load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}"  style="display: none;" data-object="supplier" data-attached="1" data-id="{{ $supplier->id }}"></div>
                        <div class="col-12" id="chat-history"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('suppliers.partials.modal-email')

    @include('customers.partials.modal-reply')

    @include('suppliers.partials.modal-create-group')

    @include('suppliers.partials.instock-product')


    <div class="row mt-5">
        <div class="col-xs-12">

            {{-- @include('customers.partials.modal-instruction') --}}

            <div class="table-responsive">
                <table class="table table-sm table-bordered m-0">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Purchase NO</th>
                        <th>Shipping Cost</th>
                        <th>Final Price</th>
                        <th>Delivery</th>
                        <th>Products</th>
                        <th>Retail</th>
                        <th>Discounted Price</th>
                    </tr>
                    @foreach ($supplier->purchases()->orderBy('created_at', 'DESC')->limit(3)->get() as $key => $purchase)
                        @php
                            $products_count = 1;
                            if ($purchase->products) {
                              $products_count = count($purchase->products) + 1;
                            }
                        @endphp
                        <tr>
                            <td rowspan="{{ $products_count }}">{{ $key + 1 }}</td>
                            <td rowspan="{{ $products_count }}">{{ \Carbon\Carbon::parse($supplier->created_at)->format('H:i d-m') }}</td>
                            <td rowspan="{{ $products_count }}"><a href="{{ route('purchase.show', $purchase->id) }}">{{ $purchase->id }}</a></td>
                            <td rowspan="{{ $products_count }}">{{ $purchase->shipment_cost }}</td>
                            <td rowspan="{{ $products_count }}">
                                @php
                                    $total_purchase_price = 0;
                                    if ($purchase->products) {
                                      foreach ($purchase->products as $product) {
                                        $total_purchase_price += $product->price_special;
                                      }
                                    }
                                @endphp

                                {{ $total_purchase_price + $purchase->shipment_cost }}
                            </td>
                            <td rowspan="{{ $products_count }}">{{ $purchase->shipment_status }}</td>
                        </tr>

                        @if ($purchase->products)
                            @foreach ($purchase->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->price_inr }}</td>
                                    <td>{{ $product->price_special }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </table>
            </div>

            <div id="supplierAccordion">
                <div class="card mb-5">
                    <div class="card-header" id="headingSupplier">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed collapse-fix" data-toggle="collapse" data-target="#supplierAcc" aria-expanded="false" aria-controls="">
                                Rest of Purchases
                            </button>
                        </h5>
                    </div>
                    <div id="supplierAcc" class="collapse collapse-element" aria-labelledby="headingSupplier" data-parent="#supplierAccordion">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                    @foreach ($supplier->purchases()->orderBy('created_at', 'DESC')->offset(3)->limit(100)->get() as $key => $purchase)
                                        @php
                                            $products_count = 1;
                                            if ($purchase->products) {
                                              $products_count = count($purchase->products) + 1;
                                            }
                                        @endphp
                                        <tr>
                                            <td rowspan="{{ $products_count }}">{{ $key + 1 }}</td>
                                            <td rowspan="{{ $products_count }}">{{ \Carbon\Carbon::parse($supplier->created_at)->format('H:i d-m') }}</td>
                                            <td rowspan="{{ $products_count }}"><a href="{{ route('purchase.show', $purchase->id) }}">{{ $purchase->id }}</a></td>
                                            <td rowspan="{{ $products_count }}">{{ $purchase->shipment_cost }}</td>
                                            <td rowspan="{{ $products_count }}">
                                                @php
                                                    $total_purchase_price = 0;
                                                    if ($purchase->products) {
                                                      foreach ($purchase->products as $product) {
                                                        $total_purchase_price += $product->price_special;
                                                      }
                                                    }
                                                @endphp

                                                {{ $total_purchase_price + $purchase->shipment_cost }}
                                            </td>
                                            <td rowspan="{{ $products_count }}">{{ $purchase->shipment_status }}</td>
                                        </tr>

                                        @if ($purchase->products)
                                            @foreach ($purchase->products as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->price_inr }}</td>
                                                    <td>{{ $product->price_special }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('customers.partials.modal-remark')

        </div>
    </div>



    <form action="" method="POST" id="product-remove-form">
        @csrf
    </form>

    {{-- @include('customers.partials.modal-forward') --}}

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>

@endsection
