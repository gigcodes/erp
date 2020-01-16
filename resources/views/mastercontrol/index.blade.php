@extends('layouts.app')

@section('title', 'Master Control')

@section('styles')
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <style type="text/css">
   .sub-table{
    padding-top: 0 !important;
    padding-bottom: 0 !important;
   }
  </style>
  
@endsection

@section('content')

  <div class="row mb-5">
      <div class="col-lg-12 margin-tb">
          <h2 class="page-heading">Master Control - {{ date('Y-m-d') }}</h2>

          <div class="pull-left">
          </div>

          <div class="pull-right mt-4">
          </div>
      </div>
  </div>

    @include('partials.flash_messages')

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="documents-table">
            <thead>
            <tr>
                <th>Columns</th>
                <th>S. No</th>
                <th>Page Name</th>
                <th>Particulars</th>
                <th>Time Spent</th>
                <th>Remarks</th>
                <th>Action / Time</th>
            </tr>
            </thead>
            <tbody>
              <tr>
                <td>Broadcasts</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Tasks</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Statutory Tasks</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Orders</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Purchases</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Scraping</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Reviews</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Emails</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Accounting</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Suppliers</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Vendors</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Customer</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Old issues</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Excel Scrapping</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Recent Customer Chats</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table">
                  <div class="table">
                    <table>
                      <tr>
                        <th width="25%">Name</th>
                        <th width="25%">Phone</th>
                        <th width="50%">Send</th>
                        <th width="40%">Communication</th>
                      </tr>
                      @foreach($chatCustomers as $customer)
                      <tr>
                        <td width="25%">{{ $customer->name }}</td>
                        <td width="25%">{{ $customer->phone }}</td>
                        <td width="60%">
                          <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <input type="text" class="form-control" name="customer" placeholder="Message" value="" id="textAreaCustomer{{ $customer->id }}">
                                    <button class="btn btn-sm btn-image send-message" data-customerid="{{ $customer->id }}" data-type="customer"><img src="/images/filled-sent.png"/></button>
                                </div>
                           </div>
                           <div style="margin-top:5px;" class="col-md-6">
                                <div class="d-flex">
                                    <select name="quickCategory" class="quickCategory form-control input-sm mb-3" data-type="customer" data-id="{{ $customer->id }}">
                                    <option value="">Select Category</option>
                                    @foreach($reply_categories as $category)
                                        <option value="{{ $category->approval_leads }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <select name="quickComment" id="quickCommentCustomer{{ $customer->id }}" class="form-control input-sm" onchange="messageToTextArea(this,'customer',{{ $customer->id }})">
                                    <option value="">Quick Reply</option>
                                </select>
                                </div>
                            </div> 
                           </div>
                        </td>
                        <td width="40%" class="table-hover-cell  @if(isset($customer->whatsappAll[0])) @if($customer->whatsappAll[0]->status == 0) text-danger @endif @endif" style="word-break: break-all;">
                          <span class="td-full-container">
                            <div class="chat_messages expand-row ">
                                @if(isset($customer->whatsappAll[0])) 
                                <span class="chat-mini-container"> 
                                {{ strlen($customer->whatsappAll[0]->message) > 10 ? substr($customer->whatsappAll[0]->message , 0, 10) : $customer->whatsappAll[0]->message }}
                                </span>
                                <span class="chat-full-container hidden">
                                  {{ $customer->whatsappAll[0]->message }}
                                </span>
                                @endif
                            </div>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="customer" data-id="{{ $customer->id }}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="customer" data-id="{{$customer->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="customer" data-id="{{$customer->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load PDF"><img src="/images/icon-pdf.svg" alt=""></button>
                        </span>
                        </td>
                      </tr>
                      @endforeach

                    </table>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Recent Vendors Chats</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table">
                  <div class="table">
                    <table>
                      <tr>
                        <th width="25%">Name</th>
                        <th width="25%">Phone</th>
                        <th width="50%">Send</th>
                        <th width="40%">Communication</th>
                      </tr>
                      @foreach($chatVendors as $vendor)
                      <tr>
                        <td width="25%">{{ $vendor->name }}</td>
                        <td width="25%">{{ $vendor->phone }}</td>
                        <td width="50%">
                          <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="" id="textAreaVendor{{ $vendor->id }}">
                                    <button class="btn btn-sm btn-image send-message" data-vendorid="{{ $vendor->id }}" data-type="vendor"><img src="/images/filled-sent.png"/></button>
                                </div>
                           </div>
                           <div style="margin-top:5px;" class="col-md-6">
                                <div class="d-flex">
                                    <select name="quickCategory" class="quickCategory form-control input-sm mb-3" data-type="vendor" data-id="{{ $vendor->id }}">
                                    <option value="">Select Category</option>
                                    @foreach($reply_categories as $category)
                                        <option value="{{ $category->approval_leads }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <select name="quickComment" id="quickCommentVendor{{ $vendor->id }}" class="form-control input-sm" onchange="messageToTextArea(this,'vendor',{{ $vendor->id }})">
                                    <option value="">Quick Reply</option>
                                </select>
                                </div>
                            </div> 
                           </div>
                        </td>
                        <td width="40%" class="table-hover-cell  @if(isset($vendor->chat_messages[0])) @if($vendor->chat_messages[0]->status == 0) text-danger @endif @endif" style="word-break: break-all;">
                          <span class="td-full-container">
                            <div class="chat_messages expand-row">
                              <span class="chat-mini-container"> 
                                @if(isset($vendor->chat_messages[0])) 
                                {{ strlen($vendor->chat_messages[0]->message) > 10 ? substr($vendor->chat_messages[0]->message , 0, 10) : $vendor->chat_messages[0]->message }}
                                @endif
                              </span>
                               <span class="chat-full-container hidden">
                                {{ $vendor->chat_messages[0]->message }}
                               </span>
                            </div>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load PDF"><img src="/images/icon-pdf.svg" alt=""></button>
                        </span>
                        </td>
                      </tr>
                      @endforeach
                    </table>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Recent Supplier Chats</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table">
                  <div class="table">
                    <table>
                      <tr>
                        <th width="25%">Name</th>
                        <th width="25%">Phone</th>
                        <th width="50%">Send</th>
                        <th width="40%">Communication</th>
                      </tr>
                      @foreach($chatSuppliers as $supplier)
                      <tr>
                        <td width="25%">{{ $supplier->supplier }}</td>
                        <td width="25%">{{ $supplier->phone }}</td>
                        <td width="50%">
                          <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="" id="textAreaSupplier{{ $supplier->id }}">
                                    <button class="btn btn-sm btn-image send-message" data-supplierid="{{ $supplier->id }}" data-type="supplier"><img src="/images/filled-sent.png"/></button>
                                </div>
                           </div>
                           <div style="margin-top:5px;" class="col-md-6">
                                <div class="d-flex">
                                    <select name="quickCategory" class="quickCategory form-control input-sm mb-3" data-type="supplier" data-id="{{ $supplier->id }}">
                                    <option value="">Select Category</option>
                                    @foreach($reply_categories as $category)
                                        <option value="{{ $category->approval_leads }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <select name="quickComment" id="quickCommentSupplier{{ $supplier->id }}" class="form-control input-sm" onchange="messageToTextArea(this,'supplier',{{ $supplier->id }})">
                                    <option value="">Quick Reply</option>
                                </select>
                                </div>
                            </div> 
                           </div>
                        </td>
                        <td width="40%" class="table-hover-cell  @if(isset($supplier->whatsappAll[0])) @if($supplier->whatsappAll[0]->status == 0) text-danger @endif @endif" style="word-break: break-all;">
                          <span class="td-full-container">
                            <div class="chat_messages expand-row">
                                @if(isset($supplier->whatsappAll[0]))
                                 <span class="chat-mini-container"> 
                                 {{ strlen($supplier->whatsappAll[0]->message) > 10 ? substr($supplier->whatsappAll[0]->message , 0, 10) : $supplier->whatsappAll[0]->message }}
                                 </span>
                                 <span class="chat-full-container hidden">
                                  {{ $supplier->whatsappAll[0]->message }}
                                 </span>
                                @endif
                            </div>

                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load PDF"><img src="/images/icon-pdf.svg" alt=""></button>
                        </span>
                        </td>
                      </tr>
                      @endforeach
                    </table>
                  </div>
                </td>
              </tr>


              <tr>
                <td>Crop Reference Grid</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table">
                  <div class="table">
                   <table>
                   <tr>
                    <th width="32%"></th>
                    <th>Today Cropped</th>
                    <th>Last 7 Days</th>
                    <th>Total Crop Reference</th>
                    <th>Pending Products</th>
                    <th>Products With Out Category</th>
                    
                   </tr>
                   <tr>
                    <td width="32%"></td>
                    <td>{{ $cropReferenceDailyCount }}</td>
                    <td>{{ $cropReferenceWeekCount }}</td>
                    <td>{{ $cropReference }}</td>
                    <td>{{ $pendingCropReferenceProducts }}</td>
                    <td>{{ $pendingCropReferenceCategory }}</td>
                    
                   </tr>
                 </table>
                   </div> 
                </td>
              </tr>
              <tr>
                <td>Product Stats</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table">
                  <table class="table table-striped table-bordered">
                    <tr>
                        <th>Import</th>
                        <th>Scraping</th>
                        <th>Is being scraped</th>
                        <th>Queued for AI</th>
                        <th>Auto crop</th>
                    </tr>
                    @php
                    $count = 0;
                    @endphp
                    <tr>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$import]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$import] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                         </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$scrape]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$scrape] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$isBeingScraped]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$isBeingScraped] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$AI]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$AI] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$autoCrop]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$autoCrop] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Is being cropped</th>
                        <th>Crop Approval</th>
                        <th>Crop Sequencing</th>
                        <th>Is being sequenced</th>
                        <th>Image Enhancement</th>
                    </tr>
                    <tr>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$isBeingCropped]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$isBeingCropped] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$cropApproval]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$cropApproval] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>

                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$cropSequencing]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$cropSequencing] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>

                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$isBeingSequenced]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$isBeingSequenced] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$imageEnhancement]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$imageEnhancement] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Is being enhanced</th>
                        <th>Manual Attribute</th>
                        <th>Final Approval</th>
                        <th>Queued for Magento</th>
                        <th>In Magento</th>
                    </tr>
                    <tr>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$isBeingEnhanced]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$isBeingEnhanced] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$manualAttribute]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$manualAttribute] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$finalApproval]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$finalApproval] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$pushToMagento]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$pushToMagento] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$inMagento]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$inMagento] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Unable to scrape</th>
                        <th>Unable to scrape images</th>
                        <th>Crop Rejected</th>
                        <th>Crop Skipped</th>
                    </tr>
                    <tr>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$unableToScrape]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$unableToScrape] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$unableToScrapeImages]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$unableToScrapeImages] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>

                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$cropRejected]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$cropRejected] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$cropSkipped]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$cropSkipped] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">&nbsp;</th>
                        <th>Others</th>
                        <th>Scraped Total</th>
                        <th>Total</th>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td style="background-color: #eee;"><strong style="font-size: 1.5em; text-align: center;">{{ $count }}</strong></td>
                        <td style="background-color: #eee;"><strong style="font-size: 1.5em; text-align: center;">{{ isset($resultScrapedProductsInStock[0]->ttl) ? (int) $resultScrapedProductsInStock[0]->ttl : '-' }}</strong></td>
                        <td style="background-color: #eee;"><strong style="font-size: 1.5em; text-align: center;">{{ (int) array_sum($productStats) }}</strong></td>
                    </tr>
                </table>
                </div>
                </td>
              </tr>
           </tbody>
        </table>
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

    <div id="email-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Email Communication</h4>
                    <input type="text" name="search_email_pop"  class="form-control search_email_pop" placeholder="Search Email" style="width: 200px;">
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

     @include('customers.zoomMeeting');

@endsection

@section('scripts')
  

  <script type="text/javascript">
  
  $(document).on('click', '.send-message', function () {
            var thiss = $(this);
            var data = new FormData();
            var type = $(this).attr('data-type');
            if(type == 'vendor'){
              var vendor_id = $(this).data('vendorid');
              send_url = '/whatsapp/sendMessage/vendor';
              data.append("vendor_id", vendor_id);
            }else if(type == 'customer'){
              send_url = '/whatsapp/sendMessage/customer';
              var customerId = $(this).attr('data-customerid');
              data.append("customer_id", customerId); 
            }else if(type == 'supplier'){
              send_url = '/whatsapp/sendMessage/supplier';
              var supplierId = $(this).attr('data-supplierid');
              data.append("supplier_id", supplierId);  
            }
            
            var message = $(this).siblings('input').val();
            data.append("message", message);
            data.append("status", 1);
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: send_url,
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        thiss.closest('tr').find('.chat_messages').html(thiss.siblings('input').val());
                        $(thiss).siblings('input').val('');
                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);
                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
 
  $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
  });
 $(document).ready(function() {
    $(".sub-table").find(".table").hide();
    $(".table").click(function() {
        var $target = $(event.target);
        if ( $target.closest("td").attr("colspan") > 1 ) {
            $target.slideUp();
        } else {
            $target.closest("tr").next().find(".table").slideToggle();
        }                    
    });
    });
 $('.quickCategory').on('change', function () {
            var type = $(this).attr('data-type');
            var id = $(this).attr('data-id');
            if(type == 'customer'){
              name = 'Customer';
            }else if(type == 'vendor'){
              name = 'Vendor';
            }else if(type == 'supplier'){
              name = 'Supplier';
            }
  
            var replies = JSON.parse($(this).val());
            $('#quickComment'+name+id).empty();
            $('#quickComment'+name+id).append($('<option>', {
                value: '',
                text: 'Quick Reply'
            }));
            replies.forEach(function (reply) {
                $('#quickComment'+name+id).append($('<option>', {
                    value: reply.reply,
                    text: reply.reply
                }));
            });
        });
 function messageToTextArea(text,type,id){
    if(type == 'customer'){
              name = 'Customer';
    }else if(type == 'vendor'){
      name = 'Vendor';
    }else if(type == 'supplier'){
      name = 'Supplier';
    }
    $('#textArea'+name+id).val(text.value);
 }
 $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.chat-mini-container').toggleClass('hidden');
                $(this).find('.chat-full-container').toggleClass('hidden');
            }
        });
</script>


@endsection

