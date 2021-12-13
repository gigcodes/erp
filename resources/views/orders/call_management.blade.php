@extends('layouts.app')
@section('title', 'Call Management')
@section('content')

    <style>
        td audio {
            height: 30px;
        }

        td {
            padding: 5px 8px 0 !important;

        }
        #customer_order_details{
            padding: 10px 0 !important;
        }

    </style>

<h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
    Call Management
    <div class="margin-tb" style="flex-grow: 1;">
        <div class="pull-right ">
            <div class="d-flex justify-content-between  mx-3">
				
            </div>
        </div>
    </div>
</h2>




    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

      
     


    <div class="col-md-6">
        <h3>All waiting calls</h3>
        <div class="table-responsive">
            <table id="show-ordres-table" class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead class="reserved-calls">
                    <tr>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Call Time</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($reservedCalls as $reservedCall)
                        <tr>
                        <td>
                            {{$reservedCall->name}}
                        </td>
                        <td>
                            {{$reservedCall->email}}
                        </td>
                        <td>
                            {{$reservedCall->from}}
                        </td>
                        <td>
                            {{$reservedCall->to}}
                        </td>
                        <td>{{$reservedCall->created_at}}</td>
                        </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <h3>Current call information</h3>
        <div class="table-responsive">
            <div class="col-md-12">
                <h3>Orders</h3>
                <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 10%">Customer Name</th>
                        <th style="width: 8%">Customer Email</th>
                        <th style="width: 10%">Order ID</th>
                        <th style="width: 10%">Client Name</th>
                        <th style="width: 10%">Store Website</th>
                        <th style="width: 10%">Order Status</th>
                        <th style="width: 8%">Order Created Date</th>
                    </tr>

                    @foreach($orders as $_order)
                    <tr>
                    <td>
                        {{@$_order->customer->name}}
                    </td>
                    <td>
                        {{@$_order->customer->email}}
                    </td>
                    <td>
                        {{@$_order->order_id}}
                    </td>
                    <td>
                        {{@$_order->client_name}}
                    </td>
                    <td>
                        {{@$_order->storeWebsite->website}}
                    </td>
                    <td>
                        {{@$_order->order_status}}
                    </td>
                    <td>{{@$_order->created_at}}</td>
                    </tr>
                @endforeach
                </table>
            </div>
            <div class="col-md-12">
                <h3>Leads</h3>
                <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width="2%">ID</th>
                        <th width="4%">Lead ID</th>
                        <th width="2%">Customer Name</th>
                        <th width="2%">Color</th>
                        <th width="2%">Size</th>
                        <th width="2%">Min Price</th>
                        <th width="2%">Max Price</th>
                        <th width="2%">Brand Segment</th>
                        <th width="2%">Gender</th>
                        <th width="2%">Quantity</th>
                        <th width="2%">Product Name</th>
                        <th width="2%">Category</th>
                        <th width="2%">Brand</th>
                        <th width="2%">Status</th>
                    </tr>

                    @foreach($allleads as $leads)
                        @foreach($leads as $_lead)
                        <tr>
                        <td>
                            {{@$_lead->id}}
                        </td>
                        <td>
                            {{@$_lead->lead_status_id}}
                        </td>
                        <td>
                            {{@$_lead->customer_name}}
                        </td>
                        <td>
                            {{@$_lead->color}}
                        </td>
                        <td>
                            {{@$_lead->size}}
                        </td>
                        <td>
                            {{@$_lead->min_price}}
                        </td>
                        <td>{{@$_lead->max_price}}</td>
                        <td>{{@$_lead->brand_segment}}</td>
                        <td>{{@$_lead->gender}}</td>
                        <td>{{@$_lead->qty}}</td>
                        <td>{{@$_lead->product_name}}</td>
                        <td>{{@$_lead->cat_title}}</td>
                        <td>{{@$_lead->brand_name}}</td>
                        <td>{{@$_lead->status_name}}</td>
                        </tr>
                      @endforeach
                    @endforeach
                </table>
            </div>
        </div>
        </div>
    </div>

    

@endsection
