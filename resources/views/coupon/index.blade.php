@extends('layouts.app')

@section('link-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.jqueryui.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.0.1/css/scroller.jqueryui.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<style>
    /* */


    .panel-default>.panel-heading {
        color: #333;
        background-color: #fff;
        border-color: #e4e5e7;
        padding: 0;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .panel-default>.panel-heading a {
        display: block;
        padding: 10px 15px;
    }

    .panel-default>.panel-heading a:after {
        content: "";
        position: relative;
        top: 1px;
        display: inline-block;
        font-family: 'Glyphicons Halflings';
        font-style: normal;
        font-weight: 400;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        float: right;
        transition: transform .25s linear;
        -webkit-transition: -webkit-transform .25s linear;
    }

    .panel-default>.panel-heading a[aria-expanded="true"] {
        background-color: #eee;
    }

    .panel-default>.panel-heading a[aria-expanded="true"]:after {
        content: "\2212";
        -webkit-transform: rotate(180deg);
        transform: rotate(180deg);
    }

    .panel-default>.panel-heading a[aria-expanded="false"]:after {
        content: "\002b";
        -webkit-transform: rotate(90deg);
        transform: rotate(90deg);
    }

    .full-rep {
        padding-bottom: 15px;
        width: 100%;
        display: inline-block;
    }

    form label.required:after {
        color: red;
        content: ' *';
    }

    /*PRELOADING------------ */
    #overlayer {
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: 1;
        background: #4a4a4a33;
    }

    .loader {
        display: inline-block;
        width: 30px;
        height: 30px;
        position: absolute;
        z-index: 3;
        border: 4px solid #Fff;
        top: 50%;
        animation: loader 2s infinite ease;
        margin-left: 50%;
    }

    .loader-inner {
        vertical-align: top;
        display: inline-block;
        width: 100%;
        background-color: #fff;
        animation: loader-inner 2s infinite ease-in;
    }
    
    .select-width{
        margin-right: 5px;
    }

    @keyframes loader {
        0% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(180deg);
        }

        50% {
            transform: rotate(180deg);
        }

        75% {
            transform: rotate(360deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes loader-inner {
        0% {
            height: 0%;
        }

        25% {
            height: 0%;
        }

        50% {
            height: 100%;
        }

        75% {
            height: 100%;
        }

        100% {
            height: 0%;
        }
    }

    .select2-container{width: 100% !important;}
    .form-inline .form-control{width: 100% !important;}
    .copy-btn{float: right; padding: 0px !important;}
</style>
@endsection
@section('content')

<!-- <div id="overlayer"></div>
<span class="loader">
  <span class="loader-inner"></span>
</span> -->

<div class="row">
    <div class="col-lg-12 margin-tb m-0">
        <h2 class="page-heading">
            Coupon Management - Coupon Rules

            <div style="float:right;">  
                <button type="button" class="btn custom-button" onclick="showOverallReport()">
                    Overall Report
                </button>
                <span>&nbsp;</span>
                <button type="button" class="btn custom-button" onclick="createCoupon()">
                    New Coupon
                </button>
                <span>&nbsp;</span>
                <button type="button" class="btn custom-button" data-toggle="modal" data-target="#ccrdatatablecolumnvisibilityList">
                    Column Visiblity
                </button>
                <span>&nbsp;</span>
                <button class="btn custom-button" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
            </div>
        </h2>
    </div>
</div>

<!-- Hidden content used to generate dynamic elements (start) -->
<div id="response-alert" style="display:none;" class="alert alert-success">
    <span>You should check in on some of those fields below.</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<table style="display:none;">
    <tr id="coupon-row">
        <td colspan="4"></td>
    </tr>
    <tr id="order-row">
        <td data-identifier="order-id">Order ID</td>
        <td data-identifier="order-date">Order Date</td>
        <td data-identifier="order-client-name">Client Name</td>
        <td data-identifier="order-balance-amount">Balance Amount</td>
    </tr>
    <tr id="no-order-row" class="text-center">
        <td colspan="4">No Order Data</td>
    </tr>
</table>

<!-- Hidden content used to generate dynamic elements (end) -->


<div id="response-alert-container"></div>
<div class="row">
    <div class="col-md-12 pl-5 pr-5">
        <div class="row">
            <div class="cls_filter_box col-md-12">
                <form class="form-inline" action="{{ route('coupons.index')}}" method="GET" id="coupon_code">
                    <div class="col-sm-12 p-0">
                        <div class="col-sm-2 p-0">
                            <div class="form-group cls_filter_inputbox p-0 mr-2">
                                <span>Coupon code:</span>
                                <input type="text" name="flt_coupon" class="form-control-sm form-control" placeholder="Coupon code" value="{{ request('flt_coupon') }}">
                            </div>
                        </div>
                        <div class="col-sm-2 p-0">
                            <div class="form-group cls_filter_inputbox p-0 mr-2">
                                <span>Search Status:</span>
                                <input type="text" name="flt_status" class="form-control-sm form-control" placeholder="Search Status" value="{{ request('flt_status') }}">
                            </div>
                        </div>
                        <div class="col-sm-2 p-0">
                            <div class="form-group cls_filter_inputbox p-0 mr-2">
                                <span>Search Rules:</span>
                                <input type="text" name="flt_rule" class="form-control-sm form-control" placeholder="Search Rules" value="{{ request('flt_rule') }}">
                            </div>
                        </div>
                        <div class="col-sm-2 p-0">
                            <div class="form-group cls_filter_inputbox p-0 mr-2">
                                <span>Start Date:</span>
                                <input type="text" name="flt_start_date" class="form-control-sm form-control" placeholder="Start Date" value="{{ request('flt_start_date') }}">
                            </div>
                        </div>
                        <div class="col-sm-2 p-0">
                            <div class="form-group cls_filter_inputbox p-0 mr-2">
                                <span>End Date:</span>
                                <input type="text" name="flt_end_date" class="form-control-sm form-control" placeholder="End Date" value="{{ request('flt_end_date') }}">
                            </div>
                        </div>
                    </div>
                    <hr style=" width: 100%; margin: 9px 0px;">
                    <div class="col-sm-12 p-0">
                        <!-- <div class="col-sm-4 p-0">
                            <div class="form-group cls_filter_inputbox p-0 mr-2 select-width" style="width: 99%;">
                                <span>Select websites:</span>
                                <br>
                                {{ Form::select("website_ids[]", \App\CouponCodeRules::pluck('website_ids','website_ids')->toArray(), request('website_ids'), ["class" => "form-control globalSelect2", "multiple" , "data-placeholder" => "Select Website"]) }}
                            </div>
                        </div> -->
                        <div class="col-sm-4 p-0">
                            <div class="form-group cls_filter_inputbox p-0 mr-2 select-width" style="width: 99%;">
                                <span>Select websites:</span>
                                <br>
                                <select class="form-control select select2 multiple" name="website_id" placeholder="Store Websites" >
                                    <option value="">Select website</option>
                                    @foreach($store_websites as $ws)
                                    <option value="{{ $ws->id }}" @if(request()->get('website_id')==$ws->id) selected @endif>{{ $ws->website }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4 p-0">
                            <div class="form-group cls_filter_inputbox p-0 mr-2 select-width" style="width: 99%;">
                                <span>Select users:</span>
                                <br>
                                {{ Form::select("usernames[]", \App\User::pluck('name','id')->toArray(), request('usernames'), ["class" => "form-control globalSelect2", "multiple" , "data-placeholder" => "Select User"]) }}
                            </div>
                        </div>
                        <div class="col-sm-1 p-0">
                            <div class="form-group p-0 mr-2">
                                <span>&nbsp;</span>
                                <br>
                                <button type="submit" class="btn btn-xs"><i class="fa fa-filter"></i></button>
                                <a href="{{route('coupons.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include("coupon.column-visibility-modal")
@include("coupon.modal-status-color")

<!-- COUPON DETAIL MODAL -->
<div class="modal fade" id="couponModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- <form id="coupon-form" method="POST" onsubmit="return executeCouponOperation();"> -->
        <form id="coupon-form" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">New Coupon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf

                    <!-- Accordian form start -->
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Rule Information
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group ">
                                                <label>Rule Name:</label>
                                                <input type="text" class="form-control required" name="name" placeholder="Name" value="{{old('name')}}" id="rule_name">
                                                @if ($errors->has('name'))
                                                <div class="alert alert-danger">{{$errors->first('name')}}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Description:</label>
                                                <textarea type="text" class="form-control" name="description" placeholder="Description" id="description" style="height:35px;">{{old('description')}}</textarea>
                                                @if ($errors->has('description'))
                                                <div class="alert alert-danger">{{$errors->first('description')}}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Active:</label>
                                                <select class="form-control select select2 required" name="active" id="is_active" placeholder="Active" >
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group ">
                                                <label>Website:</label>
                                                <select class="form-control select select2" name="store_website_id" onchange="getWebsitesByStoreId(this);" placeholder="Store Websites" >
                                                    <option value="">Please select website</option>
                                                    @foreach($store_websites as $ws)
                                                    <option value="{{ $ws->id }}">{{ $ws->website }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Website store:</label>
                                                <select class="form-control select select2 required websites" name="website_ids" multiple="true" id="website_ids" placeholder="Websites" style="height:35px; ">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Customer Group:</label>
                                                <select class="form-control select select2 required customers" name="customer_groups" multiple="true" id="customer_groups" style="height:35px;" placeholder="Customer Groups">
                                                    <option data-title="NOT LOGGED IN" value="0" selected>NOT LOGGED IN</option>
                                                    <option data-title="General" value="1">General</option>
                                                    <option data-title="Wholesale" value="2">Wholesale</option>
                                                    <option data-title="Retailer" value="3">Retailer</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Coupon:</label>
                                                <select class="form-control select select2 required" name="coupon_type" id="coupon_type" placeholder="Coupon">
                                                    <option value="NO_COUPON">No Coupon</option>
                                                    <option value="SPECIFIC_COUPON">Specific Coupon</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 hide_div">
                                            <div class="form-group">
                                                <label>Coupon Code:</label>
                                                <input type="text" class="form-control" name="code" placeholder="Code" id="coupon_code" / placeholder="Coupon Code">
                                                @if ($errors->has('code'))
                                                <div class="alert alert-danger">{{$errors->first('code')}}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-4 hide_div">
                                            <div class="form-group">
                                                <label>Uses per Coupon:</label>
                                                <input type="text" class="form-control" name="uses_per_coupon" placeholder="" id="use_per_coupon" / placeholder="Uses per Coupon">
                                                @if ($errors->has('uses_per_coupon'))
                                                <div class="alert alert-danger">{{$errors->first('uses_per_coupon')}}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-12 hide_div">
                                            <div class="form-group d-flex">
                                                <input type="checkbox" class="form-control" style="height:20px;width:20px;    margin-right: 10px;" id="disable_coupon_code" value="1" name="auto_generate">
                                                <label for="start" class="col-form-label">If you select and save the rule you will be able to generate multiple coupon codes.</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label>Uses per Coustomer:</label>
                                                <input type="text" class="form-control" name="uses_per_coustomer" placeholder="" id="use_per_coustomer" placeholder="Uses per Coustomer">
                                                <div>Usage limit enforced for logged in customers only.</div>
                                                @if ($errors->has('uses_per_coustomer'))
                                                <div class="alert alert-danger">{{$errors->first('uses_per_coustomer')}}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Start:</label>
                                                <div class='input-group date' id='start'>
                                                    <input type='text' class="form-control" name="start" value="{{old('start')}}" id="start_input" placeholder="Start" >
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                                @if ($errors->has('start'))
                                                <div class="alert alert-danger">{{$errors->first('start')}}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group ">
                                                <label>Expiration :</label>
                                                <div class='input-group date' id='expiration'>
                                                    <input type='text' class="form-control" name="expiration" value="{{old('expiration')}}" id="to_input" placeholder="Expiration" >
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                                @if ($errors->has('expiration'))
                                                <div class="alert alert-danger">{{$errors->first('expiration')}}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group ">
                                                <label>Priority:</label>
                                                <input type="text" class="form-control" name="priority" placeholder="Priority" id="">
                                                @if ($errors->has('priority'))
                                                <div class="alert alert-danger">{{$errors->first('priority')}}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group row d-flex" style="float: right;">
                                            <label for="start" class="col-form-label" style="padding-right: 10px;">Public In RSS Feed</label>
                                            <input type="checkbox" class="form-control" style="height:20px;width:20px;" name="rss" checked />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Labels
                                    </a>
                                </h4>
                                <a href="javascript:void(0);" style="margin-top:-40px;margin-left: 60px;"><i class="fa fa-question" onclick="https://docs.magento.com/user-guide/configuration/scope.html"></i></a>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body" style="overflow:auto;max-height:250px;">

                                    <div class="form-group row">
                                        <label for="code" class="col-sm-3 col-form-label text-right">Default Rule Label for All Store Views</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="store_labels[0]" placeholder="" value="" />
                                        </div>
                                    </div>
                                    <hr>
                                    @foreach($website_stores as $store)
                                    <div class="form-group row" style="align-items: center;">
                                        <div class="col-sm-3">
                                            <label for="code" class="col-sm-12 col-form-label">{{ $store->name }}</label>
                                            <label for="code" class="col-sm-12 col-form-label">{{ $store->name }} Store</label>
                                        </div>
                                        <div class="col-sm-9">
                                            @foreach($store->storeView as $view)
                                            <div class="full-rep">
                                                <label for="code" class="col-sm-3 col-form-label text-right">{{ $view->name }}</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="store_labels[{{$view->id}}]" placeholder="" value="" />
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">


                            <div class="panel-heading" role="tab" id="headingThree">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Manage Coupon Codes
                                    </a>
                                </h4>

                            </div>
                            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                <div class="panel-body">
                                    <div class="form-group row">
                                        <label for="code" class="col-sm-3 col-form-label">Coupon Qty</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="coupon_qty" placeholder="" value="{{old('coupon_qty')}}" id="coupon_qty" />
                                            @if ($errors->has('coupon_qty'))
                                            <div class="alert alert-danger">{{$errors->first('coupon_qty')}}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="description" class="col-sm-3 col-form-label">Code Length</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="code_length" placeholder="" value="{{old('code_length')}}" id="coupon_length" />
                                            <div class="">Excluding prefix, suffix and separators.</div>
                                            @if ($errors->has('code_length'))
                                            <div class="alert alert-danger">{{$errors->first('code_length')}}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label">Code Format</label>
                                        <div class="col-sm-8">
                                            <select class="form-control select select2" name="format" id="format">
                                                <option value="1">Alphanumeric</option>
                                                <option value="2">Alphabetical</option>
                                                <option value="3">Numeric</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label">Code Prefix</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="prefix" placeholder="" value="{{old('prefix')}}" id="prefix" />

                                            @if ($errors->has('prefix'))
                                            <div class="alert alert-danger">{{$errors->first('prefix')}}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label">Code Suffix</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="suffix" placeholder="" value="{{old('suffix')}}" id="suffix" />

                                            @if ($errors->has('suffix'))
                                            <div class="alert alert-danger">{{$errors->first('suffix')}}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label">Dash Every X Characters</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="dash" placeholder="" value="{{old('dash')}}" id="dash" />

                                            @if ($errors->has('dash'))
                                            <div class="alert alert-danger">{{$errors->first('dash')}}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingFour">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        Actions
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                                <div class="panel-body">
                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label ">Apply</label>
                                        <div class="col-sm-8">
                                            <select class="form-control select select2 " name="simple_action" id="simple_action">
                                                <option data-title="Percent of product price discount" value="by_percent">Percent of product price discount</option>
                                                <option data-title="Fixed amount discount" value="by_fixed">Fixed amount discount</option>
                                                <option data-title="Fixed amount discount for whole cart" value="cart_fixed">Fixed amount discount for whole cart</option>
                                                <option data-title="Buy X get Y free (discount amount is Y)" value="buy_x_get_y">Buy X get Y free (discount amount is Y)</option>
                                            </select>
                                        </div>
                                    </div>



                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label required">Discount Amount</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required" name="discount_amount" placeholder="Discount amount" id="discount_amount" />
                                            @if ($errors->has('discount_amount'))
                                            <div class="alert alert-danger">{{$errors->first('discount_amount')}}</div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label">Maximum Qty Discount is Applied To</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="discount_qty" placeholder="" id="discount_qty" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label">Discount Qty Step (Buy X)</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="discount_step" placeholder="" id="discount_step" />
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="start" class="col-sm-3 col-form-label">Apply to Shipping Amount</label>
                                        <div class="col-sm-8">
                                            <select class="form-control select select2 " name="apply_to_shipping" id="apply_to_shipping">
                                                <option data-title="Yes" value="true">Yes</option>
                                                <option data-title="No" value="false" selected>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="expiration" class="col-sm-3 col-form-label">Discard subsequent rules</label>
                                        <div class="col-sm-8">
                                            <select class="form-control select select2 " name="stop_rules_processing" id="stop_rules_processing">
                                                <option data-title="Yes" value="true">Yes</option>
                                                <option data-title="No" value="false" selected>No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Accordian form end here -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <!-- <button type="submit" class="btn btn-primary">Save</button> -->
                    <button type="button" class="btn btn-secondary save-button">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- COUPON REPORT MODAL -->
<div class="modal fade" id="couponReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <input id="report-date" />
                </div>
                <div id="report-progress" class="text-center">
                    <h4>Please wait. Generating report...</h4>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%">
                            <tbody id="report-body">
                                <tr>
                                    <td colspan="3">Coupon : <strong>Coupon Id</strong></td>
                                </tr>
                                <tr>
                                    <td data-identifier="order-id">Order ID</td>
                                    <td data-identifier="order-date">Order Date</td>
                                    <td data-identifier="order-client-name">Client Name</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- COUPON Rule Edit Modal -->
<div class="modal fade" id="couponEditModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- <form id="coupon-form" method="POST" onsubmit="return executeCouponOperation();"> -->
        <form id="coupon-edit-form" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">Edit Coupon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf

                    <!-- Accordian form start -->
                    <input type="hidden" id="rule_id" name="rule_id" value="">
                    <div class="edit-modal-section">

                    </div>
                    <!-- Accordian form end here -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <!-- <button type="submit" class="btn btn-primary">Save</button> -->
                    <button type="button" class="btn btn-primary edit-button">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif
</br>
<div class="row">
    <div class="col-md-12 pl-5 pr-5">
        <div class="table-responsive coupon-rules-table">
            <table class="table table-striped table-bordered" style="width:100%; table-layout: fixed;" id="coupon_rules_table">
                <thead>
                    <tr>
                        @if(!empty($dynamicColumnsToShowccr))
                            @if (!in_array('ID', $dynamicColumnsToShowccr))
                                <th width="5%">ID</th>
                            @endif
                            @if (!in_array('Rule', $dynamicColumnsToShowccr))
                                <th width="15%">Rule</th>
                            @endif
                            @if (!in_array('Description', $dynamicColumnsToShowccr))
                                <th width="15%">Description</th>
                            @endif
                            @if (!in_array('Coupon Code', $dynamicColumnsToShowccr))
                                <th width="10%">Coupon Code</th>
                            @endif
                            @if (!in_array('Websites', $dynamicColumnsToShowccr))
                                <th width="10%">Websites</th>
                            @endif
                            @if (!in_array('Start', $dynamicColumnsToShowccr))
                                <th width="7%">Start</th>
                            @endif
                            @if (!in_array('End', $dynamicColumnsToShowccr))
                                <th width="7%">End</th>
                            @endif
                            @if (!in_array('Created By', $dynamicColumnsToShowccr))
                                <th width="10%">Created By</th>
                            @endif
                            @if (!in_array('Status', $dynamicColumnsToShowccr))
                                <th width="5%">Status</th>
                            @endif
                            @if (!in_array('Remarks', $dynamicColumnsToShowccr))
                                <th width="20%">Remarks</th>
                            @endif
                            @if (!in_array('Action', $dynamicColumnsToShowccr))
                                <th width="10%">Action</th>
                            @endif
                        @else
                            <th width="5%">ID</th>
                            <th width="10%">Rule</th>
                            <th width="20%">Description</th>
                            <th width="15%">Coupon Code</th>
                            <th width="10%">Websites</th>
                            <th width="7%">Start</th>
                            <th width="7%">End</th>
                            <th width="10%">Created By</th>
                            <th width="5%">Status</th>
                            <th width="20%">Remarks</th>
                            <th width="10%">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($rule_lists as $rule_list)
                        @php
                            $status_color = \App\Models\CouponCodeRulesStatus::where('status_alias',$rule_list->is_active)->first();
                            if ($status_color == null) {
                                $status_color = new stdClass();
                            }
                        @endphp
                        @if(!empty($dynamicColumnsToShowccr))
                            <tr style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                @if (!in_array('ID', $dynamicColumnsToShowccr))
                                    <td>{{ $loop->iteration }}</td>
                                @endif

                                @if (!in_array('Rule', $dynamicColumnsToShowccr))
                                    <td class="Website-task">{{ $rule_list->name }}</td>
                                @endif

                                @if (!in_array('Description', $dynamicColumnsToShowccr))
                                    <td class="Website-task">
                                        {{ $rule_list->description }}

                                        @if(!empty($rule_list->description))
                                            <button type="button" data-id="" class="copy-btn btn btn-copy-description btn-sm" data-value="{{$rule_list->description}}">
                                                <i class="fa fa-clone" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                @endif

                                @if (!in_array('Coupon Code', $dynamicColumnsToShowccr))
                                    <td class="Website-task">
                                        {{ $rule_list->coupon_code }}

                                        @if(!empty($rule_list->coupon_code))
                                            <button type="button" data-id="" class="copy-btn btn btn-copy-coupon_code btn-sm" data-value="{{$rule_list->coupon_code}}">
                                                <i class="fa fa-clone" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                @endif

                                @if (!in_array('Websites', $dynamicColumnsToShowccr))
                                    <td class="Website-task">{{ $rule_list->website_name }} {{-- $rule_list->website_ids --}}</td>
                                @endif

                                @if (!in_array('Start', $dynamicColumnsToShowccr))
                                    <td>{{ $rule_list->from_date }}</td>
                                @endif

                                @if (!in_array('End', $dynamicColumnsToShowccr))
                                    <td>{{ $rule_list->to_date }}</td>
                                @endif

                                @if (!in_array('Created By', $dynamicColumnsToShowccr))
                                    <td class="Website-task">@if($rule_list->users)
                                        {{$rule_list->users->name}}
                                        @endif
                                    </td>
                                @endif

                                @if (!in_array('Status', $dynamicColumnsToShowccr))
                                    <td>{{ $rule_list->is_active == 1 ? "Active" : "InActive" }}</td>
                                @endif

                                @if (!in_array('Remarks', $dynamicColumnsToShowccr))
                                    <td>
                                        <div class=" mb-1 p-0 d-flex">
                                            <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" value="" id="remark_{{ $rule_list->id }}" data-catid="`+row['devoops_category_id']+`" data-subcatid="`+row['id']+`">
                                            <div style="margin-top: 0px;" class="d-flex p-0">
                                                <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $rule_list->id }})"><img src="/images/filled-sent.png"></button>
                                                <button type="button" data-id="{{ $rule_list->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Remarks Histories"><i class="fa fa-info-circle"></i></button>
                                            </div>
                                        </div>
                                    </td>
                                @endif

                                @if (!in_array('Action', $dynamicColumnsToShowccr))
                                    <td>
                                        <button type="button" class="btn btn-image p-1" title="Edit Rules" data-id="{{ $rule_list->id }}" data-coupon-type="{{ $rule_list->coupon_type }}" onClick="displayCouponCodeModal(this);"><i class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-image view_error p-1" title="View Update Logs" data-toggle="modal" data-id="{{ $rule_list->id }}"><i class="fa fa-eye"></i></button>
                                        @if($rule_list->coupon_code != '')
                                        <button type="button" class="btn btn-image set-rule p-1" title="Send Coupon Code" data-toggle="modal" data-target="#sendCouponModal" data-id="{{ $rule_list->id }}"><i class="fa fa-envelope"></i></button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @else
                            <tr style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                <td>{{ $loop->iteration }}</td>
                                <td class="Website-task">{{ $rule_list->name }}</td>
                                <td class="Website-task">
                                    {{ $rule_list->description }}

                                    @if(!empty($rule_list->description))
                                        <button type="button" data-id="" class="copy-btn btn btn-copy-description btn-sm" data-value="{{$rule_list->description}}">
                                            <i class="fa fa-clone" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </td>
                                <td class="Website-task">
                                    {{ $rule_list->coupon_code }}

                                    @if(!empty($rule_list->coupon_code))
                                        <button type="button" data-id="" class="copy-btn btn btn-copy-coupon_code btn-sm" data-value="{{$rule_list->coupon_code}}">
                                            <i class="fa fa-clone" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </td>
                                <td class="Website-task">{{ $rule_list->website_name }} {{-- $rule_list->website_ids --}}</td>
                                <td>{{ $rule_list->from_date }}</td>
                                <td>{{ $rule_list->to_date }}</td>
                                <td class="Website-task">@if($rule_list->users)
                                    {{$rule_list->users->name}}
                                    @endif
                                </td>
                                <td>{{ $rule_list->is_active == 1 ? "Active" : "InActive" }}</td>
                                <td>
                                    <div class=" mb-1 p-0 d-flex">
                                        <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" value="" id="remark_{{ $rule_list->id }}" data-catid="`+row['devoops_category_id']+`" data-subcatid="`+row['id']+`">
                                        <div style="margin-top: 0px;" class="d-flex p-0">
                                            <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $rule_list->id }})"><img src="/images/filled-sent.png"></button>
                                            <button type="button" data-id="{{ $rule_list->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Remarks Histories"><i class="fa fa-info-circle"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-image p-1" title="Edit Rules" data-id="{{ $rule_list->id }}" data-coupon-type="{{ $rule_list->coupon_type }}" onClick="displayCouponCodeModal(this);"><i class="fa fa-edit"></i></button>
                                    <button type="button" class="btn btn-image view_error p-1" title="View Update Logs" data-toggle="modal" data-id="{{ $rule_list->id }}"><i class="fa fa-eye"></i></button>
                                    @if($rule_list->coupon_code != '')
                                    <button type="button" class="btn btn-image set-rule p-1" title="Send Coupon Code" data-toggle="modal" data-target="#sendCouponModal" data-id="{{ $rule_list->id }}"><i class="fa fa-envelope"></i></button>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<hr>

<div class="row" style="display:none;">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" style="width: 99%" id="coupon_table">
            <thead>
                <tr>
                    <th width="15%">Code</th>
                    <th width="20%">Created</th>
                    <th>Expiration Date</th>
                    <th>Uses</th>
                    <th>Times Used</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div id="sendCouponModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Coupon Code</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('coupons.send') }}" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="send_rule_id" id="send_rule_id">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <strong>Customer</strong>
                        </div>
                        <div class="form-group col-md-9">
                            <select class="form-control select-multiple" id="customer" name="customer" style="width: 100%" required>
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary send-mail">Send</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="view_error" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="max-width: 700px !important;">
        <!-- Modal content-->
        <div class="modal-content modal-lg">
            <form action="{{ url('logging/assign') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">View Logs</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Index</th>
                                <th>Time</th>
                                <th>Coupon Code</th>
                                <th>Log</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody class="content">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close-setting" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="ccr-remarks-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remarks Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Remarks</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="ccr-remarks-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">

    $('.select-multiple').select2({width: '100%'});
   $('.select2').select2();

    /* beautify preserve:start */
    @if($errors->any())
    $('#couponModal').modal('show');
    @endif
    /* beautify preserve:end */
    $(document).ready(function() {

        $('#start').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });

        $('#coupon_rules_table').dataTable();
        $('#expiration').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        // $('#coupon_table').DataTable({
        //     "processing": true,
        //     "serverSide": true,
        //     "ajax": {
        //         url: '/checkout/coupons/load',
        //         type: 'GET'
        //     }
        // });
        $('.dataTables_length').addClass('bs-select');

        $('input#report-date').daterangepicker();

        $('input#report-date').on('apply.daterangepicker', function(ev, picker) {
            const couponId = $('#couponReportModal').attr('data-coupon-id');
            getReport(couponId);
        });

        $('#coupon_qty').attr("disabled", true);
        $('#coupon_length').attr("disabled", true);
        $('#format').attr("disabled", true);
        $('#prefix').attr("disabled", true);
        $('#suffix').attr("disabled", true);
        $('#dash').attr("disabled", true);


        // $("#accordion1").filter(":has(.ui-state-active)").accordion("activate", -1);
        // $(".ui-accordion-header").blur();
    });

    $('.hide_div').hide();

    $("#disable_coupon_code").change(function() {
        if (this.checked) {
            $('#coupon_code').attr("disabled", true);
        } else {
            $('#coupon_code').attr("disabled", false);
        }
    });

    $('#coupon_type').on('change', function() {
        let selected_val = $(this).val();

        if (selected_val == "NO_COUPON") {
            $('.hide_div').hide();
            $('#coupon_qty').attr("disabled", true);
            $('#coupon_length').attr("disabled", true);
            $('#format').attr("disabled", true);
            $('#prefix').attr("disabled", true);
            $('#suffix').attr("disabled", true);
            $('#dash').attr("disabled", true);
        } else {
            $('.hide_div').show();
            $('#coupon_qty').attr("disabled", false);
            $('#coupon_length').attr("disabled", false);
            $('#format').attr("disabled", false);
            $('#prefix').attr("disabled", false);
            $('#suffix').attr("disabled", false);
            $('#dash').attr("disabled", false);
        }
    })



    function copyCoupon(
        id,
        code,
        description,
        start,
        expiration,
        currency,
        discountFixed,
        discountPercentage,
        minimumOrderAmount,
        maximumUsage
    ) {
        /* beautify preserve:start */
        $('#coupon-form').attr('action', '{{ route('coupons.store') }}')
        /* beautify preserve:end */

        $('#coupon-form input[name="code"]').val(code);
        $('#coupon-form textarea[name="description"]').val(description);
        $('#coupon-form input[name="start"]').val(start);
        $('#coupon-form input[name="expiration"]').val(expiration);
        $('#coupon-form input[name="currency"]').val(currency);
        $('#coupon-form input[name="discount_fixed"]').val(discountFixed);
        $('#coupon-form input[name="discount_percentage"]').val(discountPercentage);
        $('#coupon-form input[name="minimum_order_amount"]').val(minimumOrderAmount);
        $('#coupon-form input[name="maximum_usage"]').val(maximumUsage);

        $('#couponModal').modal('show');
    }

    function deleteCoupon(id) {
        const shouldDelete = confirm('Do you want to delete coupon?');
        if (shouldDelete) {
            $.ajax({
                    method: "DELETE",
                    url: '/checkout/coupons/' + id,
                    data: {
                        _token: $('#coupon-form input[name="_token"]').val(),
                    }
                })
                .done(function(response) {
                    const responseJson = JSON.parse(response);
                    showReponseAlert(responseJson.message);
                    $('#coupon_table').DataTable().ajax.reload();
                })
                .fail(function(response) {
                    console.log(response);
                    showReponseAlert(response.responseJSON.message);
                });
        }
    }

    function editCoupon(id,
        code,
        description,
        start,
        expiration,
        currency,
        discountFixed,
        discountPercentage,
        minimumOrderAmount,
        maximumUsage,
        initialAmount,
        email
    ) {
        $('#coupon-form').attr('action', '/checkout/coupons/' + id);

        $('#coupon-form input[name="code"]').val(code);
        $('#coupon-form textarea[name="description"]').val(description);
        $('#coupon-form input[name="start"]').val(start);
        $('#coupon-form input[name="expiration"]').val(expiration);
        $('#coupon-form input[name="currency"]').val(currency);
        $('#coupon-form input[name="discount_fixed"]').val(discountFixed);
        $('#coupon-form input[name="discount_percentage"]').val(discountPercentage);
        $('#coupon-form input[name="minimum_order_amount"]').val(minimumOrderAmount);
        $('#coupon-form input[name="maximum_usage"]').val(maximumUsage);
        $('#coupon-form input[name="initial_amount"]').val(initialAmount);
        $('#coupon-form input[name="email"]').val(email);

        $('#couponModal').modal('show');
    }

    function createCoupon() {
        /* beautify preserve:start */
        $('#coupon-form').attr('action', '{{ route('coupons.store') }}')
        /* beautify preserve:end */
        $('#coupon-form input').not('input[name="_token"]').val('');
        $('#coupon-form textarea').val('');
        $('#couponModal').modal('show');
    }

    function executeCouponOperation() {
        const formActionUrl = $('#coupon-form').attr('action');

        $.ajax({
                method: "POST",
                url: formActionUrl,
                data: {
                    _token: $('#coupon-form input[name="_token"]').val(),
                    code: $('#coupon-form input[name="code"]').val(),
                    description: $('#coupon-form textarea[name="description"]').val(),
                    start: $('#coupon-form input[name="start"]').val(),
                    expiration: $('#coupon-form input[name="expiration"]').val(),
                    currency: $('#coupon-form input[name="currency"]').val(),
                    discount_fixed: $('#coupon-form input[name="discount_fixed"]').val(),
                    discount_percentage: $('#coupon-form input[name="discount_percentage"]').val(),
                    minimum_order_amount: $('#coupon-form input[name="minimum_order_amount"]').val(),
                    maximum_usage: $('#coupon-form input[name="maximum_usage"]').val(),
                    maximum_usage: $('#coupon-form input[name="maximum_usage"]').val(),
                    maximum_usage: $('#coupon-form input[name="maximum_usage"]').val(),
                    initialAmount: $('#coupon-form input[name="initial_amount"]').val(),
                    email: $('#coupon-form input[name="email"]').val(),
                }
            })
            .done(function(msg) {
                const response = JSON.parse(msg);
                showReponseAlert(response.message);
                $('#couponModal').modal('hide');
                $('#coupon_table').DataTable().ajax.reload();
            })
            .fail(function(response) {
                console.log(response);
                showReponseAlert(response.responseJSON.message);
                $('#couponModal').modal('hide');
            });

        return false;
    }

    function showReponseAlert(alert) {
        const responseAlert = $('#response-alert').clone();
        $(responseAlert).show();
        $(responseAlert).find('>span').text(alert);
        $('#response-alert-container').empty().append(responseAlert);
    }

    function getReport(id) {
        const startDateMoment = $('input#report-date').data('daterangepicker').startDate;
        const endDateMoment = $('input#report-date').data('daterangepicker').endDate;

        const startString = startDateMoment.format('YYYY-MM-DD ') + '00:00:00';
        const endString = endDateMoment.format('YYYY-MM-DD ') + '23:59:59';

        $('input#report-date').hide();

        $('#report-body').empty();

        $('#report-progress').show();



        $('#couponReportModal').modal('show');

        let url = '';
        if (id) {
            $('#couponReportModal').attr('data-coupon-id', id);
            url = '/checkout/coupons/' + id + '/report?start=' + startString + '&end=' + endString;
        } else {
            $('#couponReportModal').removeAttr('data-coupon-id');
            url = '/checkout/coupons/report?start=' + startString + '&end=' + endString;
        }

        $.ajax({
                method: 'GET',
                url
            })
            .done(function(response) {
                const coupons = JSON.parse(response);
                $('#report-progress').hide();
                $('input#report-date').show();
                $('#report-body').empty();

                for (let i = 0; i < coupons.length; i++) {
                    addCouponRow(coupons[i].coupon_id);

                    const orders = coupons[i].orders;

                    if (orders.length <= 0) {
                        addNoOrderDataRow();
                    } else {
                        for (let i = 0; i < orders.length; i++) {
                            addOrderRow(orders[i].order_id, orders[i].order_date, orders[i].client_name, orders[i].balance_amount);
                        }
                    }
                }

                if (id && coupons.length <= 0) {
                    addCouponRow(id);
                    addNoOrderDataRow();
                } else if (coupons.length <= 0) {
                    addNoOrderDataRow();
                }

            })
            .fail(function(error) {
                console.log(error);
            });
    }

    function showReport(id) {

        const startDate = moment().subtract(30, 'days').toDate();
        const endDate = moment().toDate();

        const startString = moment(startDate).format('YYYY-MM-DD ') + '00:00:00';
        const endString = moment(endDate).format('YYYY-MM-DD ') + '23:59:59';

        $('input#report-date').data('daterangepicker').setStartDate(startDate);
        $('input#report-date').data('daterangepicker').setEndDate(endDate);

        getReport(id);

    }

    function showOverallReport() {
        const startDate = moment().subtract(30, 'days').toDate();
        const endDate = moment().toDate();

        const startString = moment(startDate).format('YYYY-MM-DD ') + '00:00:00';
        const endString = moment(endDate).format('YYYY-MM-DD ') + '23:59:59';

        $('input#report-date').data('daterangepicker').setStartDate(startDate);
        $('input#report-date').data('daterangepicker').setEndDate(endDate);

        getReport();
    }

    function addCouponRow(couponId) {

        const orderRow = $("#coupon-row").clone();
        $(orderRow).removeAttr('id');
        $(orderRow).find('td').html('<strong>Coupon Id:<strong>' + couponId);

        $('#report-body').append(orderRow);
    }

    function addOrderRow(orderId, orderDate, clientName, orderBalanceAmount) {
        const orderRow = $("#order-row").clone();
        $(orderRow).removeAttr('id');
        $(orderRow).find('td[data-identifier="order-id"]').text(orderId);
        $(orderRow).find('td[data-identifier="order-date"]').text(orderDate);
        $(orderRow).find('td[data-identifier="order-client-name"]').text(clientName);
        $(orderRow).find('td[data-identifier="order-balance-amount"]').text(orderBalanceAmount);

        $('#report-body').append(orderRow);
    }

    function addNoOrderDataRow() {
        const row = $("#no-order-row").clone();
        $(row).removeAttr('id');
        $('#report-body').append(row);
    }


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.save-button').on('click', function() {

        if ($('#coupon-form').valid()) {
            let formData = $('#coupon-form').serializeArray();

            var indexed_array = {};
            $.map(formData, function(n, i) {
                if (n['name'] == "website_ids") {
                    indexed_array[n['name']] = $('.websites').val();
                } else if (n['name'] == "customer_groups") {
                    indexed_array[n['name']] = $('.customers').val();
                } else {
                    if (n['value'] != "") {
                        indexed_array[n['name']] = n['value'];
                    }
                }
            });

            if ($("#disable_coupon_code").is(":checked")) {
                indexed_array["auto_generate"] = true;
            }

            $.ajax({
                url: "{{ route('couponcode.store') }}",
                type: "POST",
                data: indexed_array,
                beforeSend: function() {
                    $("#loading-image-preview").show();
                },
                success: function(response) {
                    $("#loading-image-preview").hide();
                    if (response.type == "error") {
                        toastr['error'](response.message, 'error');
                        return false;
                    } else if (response.type == "success") {
                        toastr['success'](response.message, 'success');
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    $("#loading-image-preview").hide();
                    var err = eval("(" + xhr.responseText + ")");
                    toastr['error'](err, 'error');
                }
            });
        }

    });


    $(document).on('change', '#disable_coupon_code_edit', function() {
        if (this.checked) {
            $('#coupon_code_edit').attr("disabled", true);
        } else {
            $('#coupon_code_edit').attr("disabled", false);
        }
    });

    $(document).on('change', '#coupon_type_edit', function() {
        let selected_val = $(this).val();

        if (selected_val == "NO_COUPON") {
            $('.hide_div_edit').hide();
            $(document).find('#coupon_qty_edit').attr("disabled", true);
            $(document).find('#coupon_length_edit').attr("disabled", true);
            $(document).find('#format_edit').attr("disabled", true);
            $(document).find('#prefix_edit').attr("disabled", true);
            $(document).find('#suffix_edit').attr("disabled", true);
            $(document).find('#dash_edit').attr("disabled", true);

            $(document).find('.generate-code').attr("disabled", true);

        } else {
            $('.hide_div_edit').show();
            $(document).find('#coupon_qty_edit').attr("disabled", false);
            $(document).find('#coupon_length_edit').attr("disabled", false);
            $(document).find('#format_edit').attr("disabled", false);
            $(document).find('#prefix_edit').attr("disabled", false);
            $(document).find('#suffix_edit').attr("disabled", false);
            $(document).find('#dash_edit').attr("disabled", false);

            $(document).find('.generate-code').attr("disabled", false);

        }
    })

    function displayCouponCodeModal(ele) {
        let rule_id = $(ele).attr('data-id');
        $('#rule_id').val(rule_id);

        let coupon_type = $(ele).attr('data-coupon-type');

        $.ajax({
            url: "{{ route('rule_details') }}",
            type: "POST",
            data: {
                rule_id: rule_id
            },
            beforeSend: function() {
                $("#loading-image-preview").show();
            },
            success: function(response) {
                $("#loading-image-preview").hide();
                if (response.status == "error") {
                    toastr['error'](response.message, 'error');
                    return false;
                }
                if (response.status == "success") {
                    $('.edit-modal-section').html("");
                    $('.edit-modal-section').append(response.data.html);
                    $(document).find('#coupon_table1').dataTable();
                    if (coupon_type == "NO_COUPON") {
                        $(document).find('.hide_div_edit').hide();
                    } else {
                        $(document).find('.hide_div_edit').show();
                    }


                    if (coupon_type == "NO_COUPON") {
                        $(document).find('#coupon_qty_edit').attr("disabled", true);
                        $(document).find('#coupon_length_edit').attr("disabled", true);
                        $(document).find('#format_edit').attr("disabled", true);
                        $(document).find('#prefix_edit').attr("disabled", true);
                        $(document).find('#suffix_edit').attr("disabled", true);
                        $(document).find('#dash_edit').attr("disabled", true);

                        $(document).find('.generate-code').attr("disabled", true);

                    } else {
                        $(document).find('#coupon_qty_edit').attr("disabled", false);
                        $(document).find('#coupon_length_edit').attr("disabled", false);
                        $(document).find('#format_edit').attr("disabled", false);
                        $(document).find('#prefix_edit').attr("disabled", false);
                        $(document).find('#suffix_edit').attr("disabled", false);
                        $(document).find('#dash_edit').attr("disabled", false);

                        $(document).find('.generate-code').attr("disabled", false);

                    }

                    if (coupon_type == "SPECIFIC_COUPON" && $(document).find('#disable_coupon_code_edit').val() == "on") {

                        $(document).find('#coupon_qty_edit').attr("disabled", false);
                        $(document).find('#coupon_length_edit').attr("disabled", false);
                        $(document).find('#format_edit').attr("disabled", false);
                        $(document).find('#prefix_edit').attr("disabled", false);
                        $(document).find('#suffix_edit').attr("disabled", false);
                        $(document).find('#dash_edit').attr("disabled", false);

                        $(document).find('.generate-code').attr("disabled", false);

                    }

                    $('#couponEditModal').modal("show");

                }
            },
            error: function(xhr, status, error) {
                $("#loading-image-preview").hide();
                var err = eval("(" + xhr.responseText + ")");
                toastr['error'](err, 'error');
            }
        });

    }


    $('.edit-button').on('click', function() {
        if ($('#coupon-edit-form').valid()) {
            let formData = $('#coupon-edit-form').serializeArray();
            // console.log('formData', formData); return;

            var indexed_array = {};
            $.map(formData, function(n, i) {
                if (n['name'] == "website_ids_edit") {
                    indexed_array[n['name']] = $('.websites_edit').val();
                } else if (n['name'] == "customer_groups_edit") {
                    indexed_array[n['name']] = $('.customers_edit').val();
                } else {
                    if (n['value'] != "") {
                        indexed_array[n['name']] = n['value'];
                    }
                }
            });

            if ($("#disable_coupon_code_edit").is(":checked")) {
                indexed_array["auto_generate_edit"] = true;
            }

            $.ajax({
                url: "{{ route('salesrules.update') }}",
                type: "POST",
                data: indexed_array,
                beforeSend: function() {
                    $("#loading-image-preview").show();
                },
                success: function(response) {
                    $("#loading-image-preview").hide();
                    if (response.type == "error") {
                        toastr['error'](response.message, 'error');
                        return false;
                    }
                    if (response.type == "success") {
                        alert("Rule updated successfully");
                        $('#couponEditModal').modal('hide');
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    $("#loading-image-preview").hide();
                    var err = eval("(" + xhr.responseText + ")");
                    toastr['error'](err, 'error');
                }
            });
        }

    });

    $(document).on('click', '.generate-code', function() {
        $(this).attr('disabled', true);
        $.ajax({
            url: "{{ route('generateCode') }}",
            type: "POST",
            data: {
                rule_id: $('#rule_id').val(),
                qty: $('#coupon_qty_edit').val(),
                length: $('#coupon_length_edit').val(),
                format: $('#format_edit').val(),
                prefix: $('#prefix_edit').val(),
                suffix: $('#suffix_edit').val(),
                dash: $('#dash_edit').val()
            },
            beforeSend: function() {
                $("#loading-image-preview").show();
            },
            success: function(response) {
                $("#loading-image-preview").hide();
                $(this).attr('disabled', false);
                if (response.type == "error") {
                    toastr['error'](response.message, 'error');
                    return false;
                }
                if (response.type == "success") {
                    alert("Code generated successfully");
                    $('#couponEditModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                $("#loading-image-preview").hide();
                var err = eval("(" + xhr.responseText + ")");
                toastr['error'](err, 'error');
            }
        });
    });

    function getWebsitesByStoreId(ele) {
        let store_id = $(ele).val();

        $.ajax({
            url: "{{ route('getWebsiteByStore') }}",
            type: "POST",
            data: {
                store_id: store_id,
            },
            beforeSend: function() {
                $("#loading-image-preview").show();
            },
            success: function(response) {
                $("#loading-image-preview").hide();
                if (response.type == "success") {
                    $('.websites').html("");
                    $('.websites').append(response.data);

                    $('.websites_edit').html("");
                    $('.websites_edit').append(response.data);
                }
            },
            error: function(xhr, status, error) {
                $("#loading-image-preview").hide();
                var err = eval("(" + xhr.responseText + ")");
                toastr['error'](err, 'error');
            }
        });
    }

    function deleteCoupon(ele) {
        let code_id = $(ele).attr('data-id');
        $.ajax({
            url: "{{ route('deleteCouponByCode') }}",
            type: "POST",
            data: {
                id: code_id,
            },
            beforeSend: function() {
                $("#loading-image-preview").show();
            },
            success: function(response) {
                if (response.type == "success") {
                    alert("Coupon successfully removed");
                    location.reload();
                } else {
                    alert("Someting went wrong");
                }
            },
            error: function(xhr, status, error) {
                $("#loading-image-preview").hide();
                var err = eval("(" + xhr.responseText + ")");
                toastr['error'](err, 'error');
            }
        });
    }
    $('.select-multiple').select2();

    $('.set-rule').click(function() {
        $('#send_rule_id').val($(this).data('id'));
    });

    $(document).on('click', '.view_error', function(event) {
        event.preventDefault();
        $.ajax({
            url: '{{ route("couponcoderule.log.ajax") }}',
            dataType: "json",
            data: {
                id: $(this).data('id')
            },
            beforeSend: function() {
                $("#loading-image-preview").show();
            },
        }).done(function(data) {
            $("#loading-image-preview").hide();
            var $html = '';
            if (data.data.length > 0) {
                $.each(data.data, function(i, item) {
                    $html += '<tr>';
                    $html += '<td>' + parseInt(i + 1) + '</td>';
                    $html += '<td>' + item.created_at + '</td>';
                    $html += '<td>' + item.coupon_code + '</td>';
                    $html += '<td>' + item.log_type + '</td>';
                    $html += '<td>' + item.message + '</td>';
                    $html += '</tr>';
                });
            }
            $('#view_error table tbody.content').html($html);
            $('#view_error').modal('show');
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            $("#loading-image-preview").hide();
        });

    });

    function wordWrap(str, maxWidth) {
        var newLineStr = "\n";
        done = false;
        res = '';
        while (str.length > maxWidth) {
            found = false;
            // Inserts new line at first whitespace of the line
            for (i = maxWidth - 1; i >= 0; i--) {
                if (testWhite(str.charAt(i))) {
                    res = res + [str.slice(0, i), newLineStr].join('');
                    str = str.slice(i + 1);
                    found = true;
                    break;
                }
            }
            // Inserts new line at maxWidth position, the word is too long to wrap
            if (!found) {
                res += [str.slice(0, maxWidth), newLineStr].join('');
                str = str.slice(maxWidth);
            }

        }

        return res + str;
    }

    function testWhite(x) {
        var white = new RegExp(/^\s$/);
        return white.test(x.charAt(0));
    };

    function Showactionbtn(id){
        $(".action-btn-tr-"+id).toggleClass('d-none')
    }

    function saveRemarks(coupon_code_rules_id){

        var remarks = $("#remark_"+coupon_code_rules_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
            return false;
        }

        $.ajax({
            url: "{{route('coupons.saveremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'coupon_code_rules_id' :coupon_code_rules_id,                
                'remarks' :remarks,
            },
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                toastr['success'](response.message, 'success');

                $("#remark_"+coupon_code_rules_id).val('');
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error'](response.responseJSON.message);
        });
    }

    $(document).on('click', '.remarks-history-show', function() {
        var coupon_code_rules_id = $(this).attr('data-id');

        $.ajax({
            url: "{{route('coupons.getremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'coupon_code_rules_id' :coupon_code_rules_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.remarks != null) ? v.remarks : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#ccr-remarks-histories-list").find(".ccr-remarks-histories-list-view").html(html);
                    $("#ccr-remarks-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on("click",".btn-copy-coupon_code",function() {
        var apiToken = $(this).data('value');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(apiToken).select();
        document.execCommand("copy");
        $temp.remove();
        alert("Copied!");
    });

    $(document).on("click",".btn-copy-description",function() {
        var apiToken = $(this).data('value');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(apiToken).select();
        document.execCommand("copy");
        $temp.remove();
        alert("Copied!");
    });
</script>
@endsection