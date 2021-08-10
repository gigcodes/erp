@extends('layouts.app')

@section('title', 'Log List Magento')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <style type="text/css">

  #loading-image {
    position: fixed;
    top: 50%;
    left: 50%;
    margin: -50px 0px 0px -50px;
  }
  input {
    width: 100px;
  }
  .btn-primary, .btn-primary:hover{
    background: #fff;
    color:#757575;
    border: 1px solid #ddd;
  }
  .select2-container{
    width: 100% !important;
  }
  .select2-container--default .select2-selection--multiple{
    border: 1px solid #ddd !important;
  }
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px;
  }
 label{
   margin-top:5px;
   margin-bottom:4px;
   margin-left:3px;
 }
.export_btn {
	margin: 27px 0 0 0;
}
@media(max-width:767px){
	div#Export_popup .col-xs-12{padding:0;}
	.export_btn{margin:10px 0 0 0;}
}
</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row m-0">
    <div class="col-lg-12 margin-tb p-0">
      <h2 class="page-heading">Log List Magento ({{ $total_count }})
      <div class="pull-right">
        <button type="button" class="btn btn-image pr-0" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
        <a href="/logging/magento-product-api-call" target="__blank">
          <button type="button" class="btn btn-image"><img src="/images/details.png" /></button>
        </a>

        <button class="btn btn-primary ml-3" id="submit-show-report">
          Show Error Report
        </button>

        <button class="btn btn-primary" id="retry-failed-job">
          Retry failed job
        </button>
		
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Export_popup">Export</button>
        
      </div>
      </h2>
  </div>
  </div>

  <div class="col-md-12 pl-3 pr-3">
    <div class="mb-3">

      <div class="panel-body p-0">
        <form action="{{ route('list.magento.logging') }}" method="GET" class="handle-search">
          <div class="row m-0">
          <div class="col-md-2 pl-0">
                <label for="select_date">Date</label>
                <input type="text" name="select_date" class="form-control datepicker" id="select_date" placeholder="Enter Date" value="{{isset($request->select_date) ? $request->select_date : ''}}">
             
            </div>
            <div class="col-md-2 pl-0">
              <label for="product_id">Product ID</label>
              <input type="text" class="form-control" id="product_id" name="product_id" value="{{ old('queue') }}">
            </div>
            <div class="col-md-2 pl-0">
              <label for="sku">SKU</label>
              <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku')}}">
            </div>
            <div class="col-md-2 pl-0">
              <label for="sku">Brand</label>
              <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand')}}">
            </div>
            @php
              $category_suggestion = \App\Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])->selected(request('category',null))->renderAsDropdown();
            @endphp
            <div class="col-md-2 pl-0">
                <label for="sku">Category</label>
                    {!! $category_suggestion !!}
                </div>

            <div class="col-md-2 pl-0 pr-0">
              <label for="sku">Status</label>
              <select class="form-control" name="status">
                <option value=''>All</option>
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
              </select>
            </div>

              <div class="col-md-2 pl-0">
                  <label for="sku">Sync Status</label>
                  <select class="form-control" name="sync_status">
                      <option value=''>All</option>
                      <option value="success" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'success' ? 'selected' : '' }}>Success</option>
                      <option value="error" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'error' ? 'selected' : '' }}>Error</option>
                      <option value="waiting" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'waiting' ? 'selected' : '' }}>Waiting</option>
                      <option value="started_push" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'started_push' ? 'selected' : '' }}>Sync Status</option>
                      <option value="size_chart_needed" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'size_chart_needed' ? 'selected' : '' }}>Size chart needed</option>
                      <option value="image_not_found" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'image_not_found' ? 'selected' : '' }}>Image not found</option>
                  </select>
              </div>
              <div class="col-md-1 pl-0">
                  <label for="queue">Queue List</label>
                  <?php echo Form::select("queue",[null => "--Select--"] + \App\Helpers::getQueueName(true),request('queue'),["class" => "form-control"]); ?>
              </div>

          <div class="col-md-1 pl-0">
            <label for="size_info">Size info</label>
            <select class="form-control" name="size_info">
              <option value=''>All</option>
              <option value="yes" {{ isset($filters['size_info']) && $filters['size_info'] == 'yes' ? 'selected' : '' }}>Yes</option>
              <option value="no" {{ isset($filters['size_info']) && $filters['size_info'] == 'no' ? 'selected' : '' }}>No</option>
            </select>
          </div>

          <div class="col-md-2 pl-0">
            <label for="select_date">Date</label>
            <input type="text" name="job_start_date" class="form-control datepicker" id="job_start_date" placeholder="Enter Job Start Date" value="{{isset($request->job_start_date) ? $request->job_start_date : ''}}">

          </div>
          <div class="col-md-2 pl-0">
            <label for="sku">Users</label>
            <select class="form-control" name="user">
              <option value=''>All</option>
              @foreach($users as $user)
                <option value="{{$user->id}}" {{ isset($filters['user']) && $filters['user'] == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
              @endforeach
              <option >
            </select>
          </div>

          <div class="col-md-2 pl-0">
            <label for="sku">Crop Image Date</label>
              <input type="hidden" class="range_start_filter" value="<?php echo request()->get('crop_start_date') ; ?>" name="crop_start_date" />
              <input type="hidden" class="range_end_filter" value="<?php echo request()->get('crop_end_date'); ?>" name="crop_end_date" />
              <div id="filter_date_range_" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
                  <!-- <i class="fa fa-calendar"></i>&nbsp;
                  <span  id="date_current_show"></span><i class="fa fa-caret-down"></i> -->
                  <i class="fa fa-calendar"></i>&nbsp;
                  <span class="d-none" id="date_current_show"></span> <p style="display:contents;" id="date_value_show"> {{request()->get('crop_start_date') .' '.request()->get('crop_end_date')}}</p><i class="fa fa-caret-down"></i>
              </div>
          </div>

          <div class="col-md-2 pl-0" style="display: flex;align-items: flex-end">
            <button class="btn btn-primary" id="submit">
              <span class="fa fa-filter"></span> Filter Results
            </button>
            <button class="btn btn-primary" id="send-product-for-live-checking"><span class="fa fa-send"></span>&nbsp;Send Live Product</button>
          </div>

                </div>

            </form>
          </div>
        </form>
  </div>




     <div class="row m-0">
        <div class="table-responsive">
          <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout: fixed">
            <thead>
              <th style="width:5%">Product ID</th>
              <th style="width:5%">SKU</th>
              <th style="width:7%">Brand</th>
              <th style="width:6%">Category</th>
              <th style="width:5%">Price</th>
              <th style="width:6%">Message</th>
              <th style="width:6%">Date/  Time</th>
              <th style="width:5%">Website</th>
              <th style="width:6%">Status</th>
              <th style="width:4%">Lang. Id</th>
              <th style="width:8%">Sync Status</th>
              <th style="width:4%">Job Start</th>
              <th style="width:4%">Job End</th>
              <th style="width:5%;padding-left: 0">Success</th>
              <th style="width:5%">Failure</th>
              <th style="width:3%;padding-left: 0">User</th>
              <th style="width:5%;">Time</th>
              <th style="width:4%;padding-left: 5px">Size</th>
              <th style="width:7%;padding-left: 2px">Queue</th>
              <th style="width:4%">Try</th>
              <th style="width:8%">Action</th>
            </thead>
            <tbody class="infinite-scroll-pending-inner">
				@include("logging.partials.magento_product_data")                
            </tbody>
          </table>


        </div>
        
     </div>

  <div id="ErrorLogModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">magento push error log</h4>
        </div>
        <div class="modal-body">
          <table class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
              <th style="width:7%">Product ID</th>
              <th style="width:6%">Date</th>
              <th style="width:11%">Website</th>
              <th style="width:20%">Message</th>
              <th style="width:25%">Request data</th>
              <th style="width:25%">Response Data</th>
              <th style="width:6%">Status</th>
            </thead>
            <tbody class="error-log-data">

            </tbody>
          </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  
  <div id="Export_popup" class="modal fade" role="dialog">
    <div class="modal-dialog " style="padding: 0px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Export</h4>
        </div>
        <div class="modal-body">
		  {{Form::open(array('url'=>'/logging/list-magento/export', 'method'=>'get'))}}
				<div class="col-md-8 col-xs-12 pl-0">
					<label for="sku">Select Date</label>
					<input type="hidden" class="start_date" name="start_date" />
					<input type="hidden" class="end_date" name="end_date" />
					<div id="filter_date_range_new" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
						<!-- <i class="fa fa-calendar"></i>&nbsp;
						<span  id="date_current_show"></span><i class="fa fa-caret-down"></i> -->
						<i class="fa fa-calendar"></i>&nbsp;
						<span class="d-none" id="date_current_show_new"></span> <p style="display:contents;" id="date_value_show_new"> </p><i class="fa fa-caret-down"></i>
					</div>
				</div>
				<div class="col-md-4 pl-0 col-xs-12 export_btn">
					<button type="submit" class="btn btn-primary">Export</button>
				</div>
			</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <div id="update_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Update Product</h4>
        </div>
        <form role="form" action="{{route('product.update.magento')}}" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="update_product_id" id="update_product_id" value="">
            <div class="form-group col-md-8">
              <label for="title">Name</label>
              <input name="name" type="text" class="form-control" id="update_name" value="" required>
            </div>
            <div class="form-group col-md-4">
              <img src="" id="single_product_image" class="quick-image-container img-responive" style="width: 70px;" alt="">
            </div>
            <div class="form-group col-md-12">
              <label for="title">Size</label>
              <input name="size" type="text" class="form-control" id="update_size" value="">
            </div>
            <div class="form-group col-md-12">
              <label for="title">Short Description</label>
              <textarea name="short_description" class="form-control" id="update_short_description"></textarea>
            </div>
            <div class="form-group col-md-4">
              <label for="title">Price</label>
              <input name="price" type="text" class="form-control" id="update_price" value="" required>
            </div>
            <div class="form-group col-md-4">
              <label for="title">Price Special</label>
              <input name="price_eur_special" type="text" class="form-control" id="update_price_eur_special" value="" required>
            </div>
            <div class="form-group col-md-4">
              <label for="title">Price Discounted</label>
              <input name="price_eur_discounted" type="text" class="form-control" id="update_price_eur_discounted" value="" required>
            </div>
            <div class="form-group col-md-4">
              <label for="title">Price INR</label>
              <input name="price_inr" type="text" class="form-control" id="update_price_inr" value="" required>
            </div>
            <div class="form-group col-md-4">
              <label for="title">Price Special</label>
              <input name="price_inr_special" type="text" class="form-control" id="update_price_inr_special" value="" required>
            </div>
            <div class="form-group col-md-4">
              <label for="title">Price Discounted</label>
              <input name="price_inr_discounted" type="text" class="form-control" id="update_price_inr_discounted" value="" required>
            </div>
            <div class="form-group col-md-12">
              <label for="title">Measurement Type</label>
              <input name="measurement_size_type" type="text" class="form-control" id="update_measurement_size_type" value="">
            </div>
            <div class="form-group col-md-4">
              <label for="title">L Measurement</label>
              <input name="lmeasurement" type="text" class="form-control" id="update_lmeasurement" value="">
            </div>
            <div class="form-group col-md-4">
              <label for="title">H Measurement</label>
              <input name="hmeasurement" type="text" class="form-control" id="update_hmeasurement" value="">
            </div>
            <div class="form-group col-md-4">
              <label for="title">D Measurement</label>
              <input name="dmeasurement" type="text" class="form-control" id="update_dmeasurement" value="">
            </div>
            <div class="form-group col-md-12">
              <label for="title">Composition</label>
              <input name="composition" type="text" class="form-control" id="update_composition" value="" required>
            </div>
            <div class="form-group col-md-6">
              <label for="title">Made In</label>
              <input name="made_in" type="text" class="form-control" id="update_made_in" value="">
            </div>
            <div class="form-group col-md-6">
              <label for="title">Brand</label>
              <select name="brand" class="form-control" id="update_brand">
                <option value=""></option>
                @foreach($brands as $brand)
                  <option value="{{$brand->id}}">{{$brand->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="title">Category</label>
              <select name="category" class="form-control" id="update_category">
                <option value=""></option>
                @foreach($categories as $cat)
                  <option value="{{$cat->id}}">{{$cat->title}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="title">Supplier</label>
              <input name="supplier" type="text" class="form-control" id="update_supplier">
            </div>
            <div class="form-group col-md-12">
              <label for="title">Supplier Link</label>
              <input name="supplier_link" type="text" class="form-control" id="update_supplier_link">
            </div>
            <div class="form-group col-md-12">
              <label for="title">Product Link</label>
              <input name="product_link" type="text" class="form-control" id="update_product_link">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary pull-left">Update Product</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <div id="show-error-count" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Error with count</h4>
        </div>
          <div class="modal-body">
            
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          </div>
      </div>
    </div>
  </div>
  <div id="show-product-information" class="modal fade" role="dialog" style="margin: 150px;">
    <div class="modal-dialog modal-lg" style="margin: 0px;">
      <div class="modal-content" style="width: 1500px">
        <div class="modal-header">
          <h4 class="modal-title">Product information</h4>
        </div>
          <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                  <th width="10%">SKU</th>
                  <th width="15%">Description</th>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Composition</th>
                  <th>Material</th>
                  <th>Manufracturer.</th>
                  <th>Brand</th>
                  <th>Sizes</th>
                  <th>Dimensions</th>
                  <th width="5%">Stock</th>
                  <th width="5%">Min day</th>
                  <th width="5%">Max day</th>
                </thead>
                <tbody class="product-information-data">

                </tbody>
              </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          </div>
      </div>
    </div>
  </div>

  <div id="retry-failed-job-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Retry failed job request</h4>
            </div>
            <form class="retry-failed-job-modal-form">
              {!! csrf_field() !!}
              <div class="modal-body">
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <strong>Start Date&nbsp;:&nbsp;</strong>
                              <input type="text" name="start_date" value="" class="form-control start-date-picker">
                          </div>
                        </div>  
                        <div class="col">
                          <div class="form-group">
                              <strong>End Date&nbsp;:&nbsp;</strong>
                              <input type="text" name="end_date" value="" class="form-control end-date-picker">
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <strong>Store website&nbsp;:&nbsp;</strong>
                              <?php echo Form::select("store_website_id",[null => "- Select -"] + \App\StoreWebsite::where("website_source","magento")->pluck('title','id')->toArray(),null,["class" => "form-control select2"]); ?>
                          </div>
                        </div>  
                        <div class="col">
                          <div class="form-group">
                              <strong>Product id&nbsp;:&nbsp;</strong>
                              <input type="text" name="keyword" value="" class="form-control">
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary pull-left btn-secondary retry-failed-job-modal-btn">Retry</button>
              </div>
            </form>
        </div>
    </div>
  </div>

  <div id="send-live-product-check-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Send product to check website</h4>
            </div>
            <form class="retry-failed-job-modal-form">
              {!! csrf_field() !!}
              <div class="modal-body">
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <strong>Start Date&nbsp;:&nbsp;</strong>
                              <input type="text" name="start_date" value="" class="form-control start-date-picker">
                          </div>
                        </div>  
                        <div class="col">
                          <div class="form-group">
                              <strong>End Date&nbsp;:&nbsp;</strong>
                              <input type="text" name="end_date" value="" class="form-control end-date-picker">
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <strong>Store website&nbsp;:&nbsp;</strong>
                              <?php echo Form::select("store_website_id",[null => "- Select -"] + \App\StoreWebsite::where("website_source","magento")->pluck('title','id')->toArray(),null,["class" => "form-control select2"]); ?>
                          </div>
                        </div>  
                        <div class="col">
                          <div class="form-group">
                              <strong>Product id&nbsp;:&nbsp;</strong>
                              <input type="text" name="keyword" value="" class="form-control">
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary pull-left btn-secondary send-live-product-check-btn">Send</button>
              </div>
            </form>
        </div>
    </div>
  </div>

  <div id="print-live-product-screenshot-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Live Screenshot</h4>
            </div>
           <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                  <th>SKU</th>
                  <th>Website</th>
                  <th>Status</th>
                  <th>Image</th>
                  <th>Created at</th>
                </thead>
                <tbody class="screenshot-modal-information-data">

                </tbody>
              </table>
           </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
  </div>

  

@endsection

@section('scripts')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $("#select_date").datepicker({
      	  	format: 'yyyy-mm-dd'
      	});

        $("#job_start_date").datepicker({
          format: 'yyyy-mm-dd'
        });

        $(".start-date-picker").datepicker({
            format: 'yyyy-mm-dd'
        });

        $(".end-date-picker").datepicker({
            format: 'yyyy-mm-dd'
        });

        
    </script>
  <script type="text/javascript">
  var product = []
  if (localStorage.getItem("luxury-product-data-asin") !== null) {
     var data = JSON.parse(localStorage.getItem('luxury-product-data-asin'));
     product = data
     $.each(product,function(i,e){
      console.log(e)
      $(".selectProductCheckbox_class[value='"+e.sku+"']").attr("checked",true);
     });
  }

  $('#magento_list_tbl_895 tbody').on('click', 'tr', function () {
    var ref = $(this);
    ref.find('td:eq(15)').children('.selectProductCheckbox_class').change(function(){
      if($(this).is(':checked')){
        var val = $(this).val()
        //if(!product.includes($(this).val())){  
          if(product.length > 0){
            $.each(product,function(i,e){
            if(e.sku == val){
              product.splice(i,1)
            }
          })
          }
          
          var item = {"sku":$(this).val(),"websiteid":$(this).attr("websiteid")}
            product.push(item)
            
        //  }
          localStorage.setItem('luxury-product-data-asin', JSON.stringify(product));
      } else {
        //var index = product.indexOf($(this).val())
        var val = $(this).val()
        $.each(product,function(i,e){
          if(e.sku == val){
            product.splice(i,1)
          }
        })
        // if(index !=-1){
        //   product.splice(index,1)
        // }
        localStorage.setItem('luxury-product-data-asin', JSON.stringify(product));
      }
    })
    console.log(product)
  })
  $(document).on("click", ".show_error_logs", function() {
    var id = $(this).data('id');
    var store_website_id = $(this).data('website');
    $.ajax({
      method: "GET",
      url: "/logging/show-error-log-by-id/" + id,
      data: {
        "_token": "{{ csrf_token() }}"
      },
      dataType: 'html'
    })
    .done(function(result) {
      $('#ErrorLogModal').modal('show');
      $('.error-log-data').html(result);
    });

  });
  $(document).on("click", ".update_modal", function() {
    var data = $(this).data('id');

    var detail = $(this).data('id');
    //alert(JSON.stringify(detail));
    $("#single_product_image").attr("src", detail['image_url']);
    $("#update_product_id").val(detail['product_id']);
    $("#update_name").val(detail['name']);
    $("#update_short_description").val(detail['short_description']);
    $("#update_size").val(detail['size']);
    $("#update_price").val(detail['price']);
    $("#update_price_eur_special").val(detail['price_eur_special']);
    $("#update_price_eur_discounted").val(detail['price_eur_discounted']);

    $("#update_price_inr").val(detail['price_inr']);
    $("#update_price_inr_special").val(detail['price_inr_special']);
    $("#update_price_inr_discounted").val(detail['price_inr_discounted']);

    $("#update_measurement_size_type").val(detail['measurement_size_type']);
    $("#update_lmeasurement").val(detail['lmeasurement']);
    $("#update_hmeasurement").val(detail['hmeasurement']);
    $("#update_dmeasurement").val(detail['dmeasurement']);

    $("#update_composition").val(detail['composition']);
    $("#update_made_in").val(detail['made_in']);
    $("#update_brand").val(detail['brand']);
    $("#update_category").val(detail['category']);
    $("#update_supplier").val(detail['supplier']);
    $("#update_supplier_link").val(detail['supplier_link']);
    $("#update_product_link").val(detail['product_link']);
  });

  $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });

  function changeMagentoStatus(logId, newStatus) {
    if (!newStatus) {
      return;
    }
    $.ajax({
      method: "POST",
      url: "/logging/list-magento/" + logId,
      data: {
        "_token": "{{ csrf_token() }}",
        status: newStatus
      }
    })
    .done(function(msg) {
      console.log("Data Saved: ", msg);
    });
  }

  $(document).on("click","#submit-show-report",function(e){
    e.preventDefault();
    $.ajax({
      method: "GET",
      url: "/logging/list-magento/error-reporting"
    })
    .done(function(response) {
        $("#show-error-count").find(".modal-body").html(response);
        $("#show-error-count").modal("show");
    });
  });

  $(document).on("click",".show-product-information",function (e) {
    e.preventDefault();
    var id  = $(this).data("id");
    $.ajax({
      method: "GET",
      url: "/logging/list-magento/product-information",
      data : {
         product_id : id
      }
    })
    .done(function(response) {
        $(".product-information-data").html(response);
        $("#show-product-information").modal("show");
    });
  });

  $(document).on("click","#retry-failed-job",function(e) {
     e.preventDefault();
     $("#retry-failed-job-modal").modal("show");
  });

  $(document).on("click",".retry-failed-job-modal-btn",function(e) {
    e.preventDefault();
      var form = $(this).closest('form');
      $.ajax({
        method: "GET",
        url: "/logging/list-magento/retry-failed-job",
        data : form.serialize(),
        dataType:"json",
        beforeSend : function( ) {
           $("#loading-image").show();
        }
      })
      .done(function(response) {
          $("#loading-image").hide();
          if(response.code == 200) {
            $("#retry-failed-job-modal").modal("hide");
            toastr["success"](response.message);
          }else{
            toastr["error"](response.message);
          }
      });
  });

  $(".select-multiple").select2({tags:true});

  $(document).on("click","#send-product-for-live-checking",function(e) {
      e.preventDefault();
      $("#send-live-product-check-modal").modal("show");
  });

  $(document).on("click",".send-live-product-check-btn",function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      $.ajax({
        method: "GET",
        url: "/logging/list-magento/send-live-product-check",
        data : form.serialize(),
        dataType:"json",
        beforeSend : function( ) {
           $("#loading-image").show();
        }
      })
      .done(function(response) {
          $("#loading-image").hide();
          if(response.code == 200) {
            $("#send-live-product-check-modal").modal("hide");
            toastr["success"](response.message);
          }else{
            toastr["error"](response.message);
          }
      });
  });


  $(document).on("click",".btn-product-screenshot",function(e) {
      var $this = $(this);
      $.ajax({
        method: "GET",
        url: "/logging/list-magento/get-live-product-screenshot",
        data : {
          id : $this.data("id") 
        },
        beforeSend : function(response) {
           $("#loading-image").show();
           
        }
      })
      .done(function(response) {
          $("#loading-image").hide();
          $(".screenshot-modal-information-data").html(response);
          $("#print-live-product-screenshot-modal").modal("show");
      });
  });


  let r_s = "";
  let r_e = "";

  let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(0, 'days');
  let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

  jQuery('input[name="range_start"]').val();
  jQuery('input[name="range_end"]').val();

  function cb(start, end) {
      $('#filter_date_range_ span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  }

  $('#filter_date_range_').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        //parentEl: '#filter_date_range_',
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
  }, cb);

  cb(start, end);


  $('#filter_date_range_').on('apply.daterangepicker', function(ev, picker) {
      let startDate=   jQuery('input[name="crop_start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
      let endDate =    jQuery('input[name="crop_end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
      $("#date_current_show").removeClass("d-none");
      $("#date_value_show").css("display", "none");
  });


  jQuery('input[name="start_date"]').val();
  jQuery('input[name="end_date"]').val();

  function cb1(start, end) {
      $('#filter_date_range_new span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  }

  $('#filter_date_range_new').daterangepicker({
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
  }, cb1);

  cb1(start, end);


  $('#filter_date_range_new').on('apply.daterangepicker', function(ev, picker) {
      let startDate=   jQuery('input[name="start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
      let endDate =    jQuery('input[name="end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
      $("#date_current_show_new").removeClass("d-none");
      $("#date_value_show_new").css("display", "none");
  });



  $(document).on('click', '.upload-single', function () {
      $this = $(this);
      var id = $(this).data('id');
      var thiss = $(this);
      $(this).addClass('fa-spinner').removeClass('fa-upload')
      url = "{{ url('products') }}/" + id + '/listMagento';
      $.ajax({
          type: 'POST',
          url: url,
          data: {
              _token: "{{ csrf_token() }}",
          },
          beforeSend: function () {
              // $(thiss).text('Loading...');
              // $(thiss).html('<i class="fa fa-spinner" aria-hidden="true"></i>');
          }
      }).done(function (response) {
          thiss.removeClass('fa-spinner').addClass('fa-upload')
          toastr['success']('Request Send successfully', 'Success')
          $('#product' + id).hide();
      }).fail(function (response) {
          console.log(response);
          thiss.removeClass('fa-spinner').addClass('fa-upload')
          toastr['error']('Internal server error', 'Failure')
          $('#product' + id).hide();
          //alert('Could not update product on magento');
      })
  });
  /** infinite loader **/
	var isLoading = false;
	var page = 1;
	$(document).ready(function () {
		$(window).scroll(function() {
			if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
				loadMore();
			}
		});

		function loadMore() {
			if (isLoading)
				return;
			isLoading = true;
			var $loader = $('.infinite-scroll-products-loader');
			page = page + 1;
			$.ajax({
				url: "/logging/list-magento?type=product_log_list&page="+page,
				type: 'GET',
				data: $('.handle-search').serialize(),
				beforeSend: function() {
					$loader.show();
				},
				success: function (data) {
					//console.log(data);
					$loader.hide();				
					$('.infinite-scroll-pending-inner').append(data.tbody);
					isLoading = false;
					if(data.tbody == "") {
						isLoading = true;
					}
				},
				error: function () {
					$loader.hide();
					isLoading = false;
				}
			});
		}
	});
	//End load more functionality
</script>
@if (Session::has('errors'))
  <script>
  toastr["error"]("{{ $errors->first() }}", "Message")
</script>
@endif
@if (Session::has('success'))
  <script>
  toastr["success"]("{{Session::get('success')}}", "Message")
</script>
@endif

@endsection
