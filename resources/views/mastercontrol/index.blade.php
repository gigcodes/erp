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
            <form class="form-inline" action="{{ route('mastercontrol.index') }}" method="GET">
              <div class="form-group ml-3">
                <input type="text" value="" name="range_start" hidden/>
                <input type="text" value="" name="range_end" hidden/>
                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                  <i class="fa fa-calendar"></i>&nbsp;
                  <span></span> <i class="fa fa-caret-down"></i>
                </div>
              </div>

              <button type="submit" class="btn btn-secondary ml-3">Submit</button>
            </form>
          </div>

          <div class="pull-right mt-4">
            {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#mergeModal">Merge Customers</button> --}}
            {{-- <a class="btn btn-secondary" href="{{ route('customer.create') }}">+</a> --}}
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
               <tr>
                <td>Recent Vendor Chats</td>
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
                    <td>{{ $vendor->name }}</td>
                    <td>{{ $vendor->phone }}</td>
                    <td>
                       <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
                                    <button class="btn btn-sm btn-image send-message" data-vendorid="{{ $vendor->id }}"><img src="/images/filled-sent.png"/></button>
                                </div>
                           </div>
                           <div style="margin-top:5px;" class="col-md-6">
                                <div class="d-flex">
                                    <?php echo Form::select("quickComment",["" => "--Auto Reply--"]+$replies, null, ["class" => "form-control quickComment select2-quick-reply","style" => "width:100%" ]); ?>
                                    <a class="btn btn-image delete_quick_comment"><img src="/images/delete.png" style="cursor: default; width: 16px;"></a>
                                </div>
                            </div> 
                        </div>
                    </td>
                    <td class="table-hover-cell {{ $vendor->message_status == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
                        <span class="td-full-container">
                            <div class="chat_messages">
                                @if(isset($vendor->chat_messages[0])) {{ $vendor->chat_messages[0]->message }} @endif
                                @if(isset($vendor->message)) {{ $vendor->message }} @endif    
                            </div>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load PDF"><img src="/images/icon-pdf.svg" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-email-modal" title="Load Email" data-id="{{$vendor->id}}"><i class="fa fa-envelope-square"></i></button>
                        </span>
                    </td>
                   </tr>
                   @endforeach
                  </div>
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
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>
                       <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
                                    <button class="btn btn-sm btn-image send-message" data-vendorid="{{ $customer->id }}"><img src="/images/filled-sent.png"/></button>
                                </div>
                           </div>
                           <div style="margin-top:5px;" class="col-md-6">
                                <div class="d-flex">
                                    <?php echo Form::select("quickComment",["" => "--Auto Reply--"]+$replies, null, ["class" => "form-control quickComment select2-quick-reply","style" => "width:100%" ]); ?>
                                    <a class="btn btn-image delete_quick_comment"><img src="/images/delete.png" style="cursor: default; width: 16px;"></a>
                                </div>
                            </div> 
                        </div>
                    </td>
                    <td class="table-hover-cell {{ $customer->message_status == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
                        <span class="td-full-container">
                            <div class="chat_messages">
                                @if(isset($customer->chat_messages[0])) {{ $customer->chat_messages[0]->message }} @endif
                                @if(isset($customer->message)) {{ $customer->message }} @endif    
                            </div>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="customer" data-id="{{$customer->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="customer" data-id="{{$customer->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="customer" data-id="{{$customer->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load PDF"><img src="/images/icon-pdf.svg" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-email-modal" title="Load Email" data-id="{{$customer->id}}"><i class="fa fa-envelope-square"></i></button>
                        </span>
                    </td>
                   </tr>
                   @endforeach
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
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>
                       <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
                                    <button class="btn btn-sm btn-image send-message" data-vendorid="{{ $supplier->id }}"><img src="/images/filled-sent.png"/></button>
                                </div>
                           </div>
                           <div style="margin-top:5px;" class="col-md-6">
                                <div class="d-flex">
                                    <?php echo Form::select("quickComment",["" => "--Auto Reply--"]+$replies, null, ["class" => "form-control quickComment select2-quick-reply","style" => "width:100%" ]); ?>
                                    <a class="btn btn-image delete_quick_comment"><img src="/images/delete.png" style="cursor: default; width: 16px;"></a>
                                </div>
                            </div> 
                        </div>
                    </td>
                    <td class="table-hover-cell {{ $supplier->message_status == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
                        <span class="td-full-container">
                            <div class="chat_messages">
                                @if(isset($supplier->chat_messages[0])) {{ $supplier->chat_messages[0]->message }} @endif
                                @if(isset($supplier->message)) {{ $supplier->message }} @endif    
                            </div>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="supplier" data-id="{{$supplier->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load PDF"><img src="/images/icon-pdf.svg" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-email-modal" title="Load Email" data-id="{{$supplier->id}}"><i class="fa fa-envelope-square"></i></button>
                        </span>
                    </td>
                   </tr>
                   @endforeach
                  </div>
                </td>
              </tr>
           </tbody>
        </table>
        
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
    </div>

  


@endsection

@section('scripts')
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script type="text/javascript">

   
  $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
  });

 

    $(document).on('click', '.quick-shortcut-button', function(e) {
      e.preventDefault();

      var customer_id = $(this).parent().find('input[name="customer_id"]').val();
      var instruction = $(this).parent().find('input[name="instruction"]').val();
      var category_id = $(this).parent().find('input[name="category_id"]').val();
      var assigned_to = $(this).parent().find('input[name="assigned_to"]').val();
      var thiss = $(this);
      var text = $(this).text();

      $.ajax({
        type: "POST",
        url: "{{ route('instruction.store') }}",
        data: {
          _token: "{{ csrf_token() }}",
          customer_id: customer_id,
          instruction: instruction,
          category_id: category_id,
          assigned_to: assigned_to,
        },
        beforeSend: function() {
          $(thiss).text('Loading...');
        }
      }).done(function(response) {
        $(thiss).text(text);
      }).fail(function(response) {
        $(thiss).text(text);

        alert('Could not execute shortcut!');

        console.log(response);
      });
    });

    let r_s = '{{ $start }}';
    let r_e = '{{ $end }}';

    let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(1, 'days');
    let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

    jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
    jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    cb(start, end);

    $(function() {
    $(".sub-table").find(".table").hide();
    $("table").click(function(event) {
        event.stopPropagation();
        var $target = $(event.target);
        if ( $target.closest("td").attr("colspan") > 1 ) {
            $target.slideUp();
        } else {
            $target.closest("tr").next().find(".table").slideToggle();
        }                    
    });
    });
 
  </script>
@endsection
