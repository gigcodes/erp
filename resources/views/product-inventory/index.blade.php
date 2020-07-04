@extends('layouts.app')
@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@php
	$roletype = "Inventory";
@endphp

@section('favicon' , 'inventory.png')
@section('title', 'Products Grid - ERP Sololuxury')

@section('content')
	<div class="row">
        <div class="col-lg-12 margin-tb">
        	<h2 class="page-heading">Product Inventory ({{ $products->total() }})</h2>
        </div>
        @if(auth()->user()->isAdmin())
          @if( $roletype != 'Selection' && $roletype != 'Sale' )
          	  	<div class="col-lg-12 margin-tb">	
	              	<div class="pt-2 pb-3">
	              		<a href="{{ route('pending',$roletype) }}"><strong>Pending: </strong> {{ \App\Product::getPendingProductsCount($roletype) }}</a>
	              	</div>
	              	@if ($roletype == 'Inventory')
	                	<form class="form-inline mb-3" action="{{ route('productinventory.import') }}" method="POST" enctype="multipart/form-data">
	                  		@csrf
	                  		<div class="form-group">
	                    		<input type="file" name="file" class="form-control-file" required>
	                  		</div>
	                  		<button type="submit" class="btn btn-secondary ml-3">Import Inventory</button>
	                	</form>
	              @endif
        		</div>
          @endif
        @endif
        <div class="col-lg-12 margin-tb">
        	<form action="?" method="GET" class="form-inline align-items-start">
        		<div class="form-group mr-3 mb-3">
        			<input name="term" type="text" class="form-control" id="product-search" value="{{ request('term','') }}" placeholder="sku,brand,category,status,stage">
        		</div>
        		<div class="form-group mr-3 mb-3">
                  {!! $category_selection !!}
                </div>
                <div class="form-group mr-3 mb-3">
                  @php $brands = \App\Brand::getAll(); @endphp
                  {!! Form::select('brand[]',$brands, request("brand",[]), ['data-placeholder' => 'Select a Brand','class' => 'form-control select-multiple2', 'multiple' => true]) !!}
                </div>
                <div class="form-group mr-3 mb-3">
                  @php $colors = new \App\Colors(); @endphp
                  {!! Form::select('color[]',$colors->all(), request("color",[]), ['data-placeholder' => 'Select a Color','class' => 'form-control select-multiple2', 'multiple' => true,'style' => "width:250px;"]) !!}
                </div>
                <div class="form-group mr-3 mb-3">
                	{!! Form::select('supplier[]',$suppliersDropList, request("supplier",[]), ['data-placeholder' => 'Select a Supplier','class' => 'form-control select-multiple2', 'multiple' => true]) !!}
                </div>
                <div class="form-group mr-3 mb-3">
                	{!! Form::select('type[]',$typeList, request("type",[]), ['data-placeholder' => 'Select a Type','class' => 'form-control select-multiple2', 'multiple' => true]) !!}
                </div>
                <div class="form-group mr-3 mb-3">
                  <input name="size" type="text" class="form-control" value="{{ request('size',null) }}" placeholder="Size">
                </div>
                <div class="form-group mr-3 mb-3">
                  <strong class="mr-3">Price</strong>
                    <input type="text" name="price_min" class="form-control" placeholder="min. price" value="{{ isset($_GET['price_min']) ? $_GET['price_min'] : '' }}">
                    <input type="text" name="price_max" class="form-control" placeholder="max. price" value="{{ isset($_GET['price_max']) ? $_GET['price_max'] : '' }}">
                </div>
                <div class="form-group mr-3 mb-3">
                  <div class='input-group date' id='filter-date'>
                      <input type='text' class="form-control" name="date" value="{{ request('date','') }}" placeholder="Date" />
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>
                </div>
                <div class="form-group mr-3 mb-3">
                   <input {{ (request('is_on_sale')) ? 'checked' : '' }} type="checkbox" name="is_on_sale" id="is_on_sale"><label for="is_on_sale">Sale</label>
                </div>
                <div class="form-group mr-3 mb-3">
                   <input {{ (request('without_category')) ? 'checked' : '' }} ? 'checked' : '' }} type="checkbox" name="without_category" id="without_category"><label for="without_category">Without Category?</label>
                </div>
                @if (isset($customer_id) && $customer_id != null)
                  <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                @endif
                <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>	
        	</form>	
        </div>
        <div class="col-lg-12 margin-tb">
	        <div class="productGrid " id="productGrid">
        		<div class="infinite-scroll">
	               @include("product-inventory.partials.grid")
            	   <div class="row">
            	   		{!! $products->appends(Request::except('page'))->links() !!}
            	   </div>	
            	</div>  
	        </div>
        </div>
  </div>
@endsection
@include('partials.modals.category')
@include('partials.modals.color')
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script>
    	
    	$(document).ready(function() {
       		$(".select-multiple").multiselect();
       		$(".select-multiple2").select2();
    	});

        $(document).on('mouseover', 'select.update-product', function() { 
            $(this).select2().select2('open');
        });

        $(document).on('mouseover', 'select.update-color', function() { 
            $(this).select2().select2('open');
        });

        $(document).on("click",".push-in-shopify",function() {
            var product_id  = $(this).data("id");
            $.ajax({
                url: '/product-inventory/'+product_id+'/push-in-shopify',
                type: 'GET',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(result){
                    if(result.code == 200) {
                        toastr['success']('Sent to store successfully', 'success');
                    }else{
                        toastr['error'](result.message, 'error');
                    }
                    $("#loading-image").hide();
                }, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
            });     
        });

        $(function () {
            $('.infinite-scroll').jscroll({
                autoTrigger: true,
                loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                padding: 2500,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.infinite-scroll',
                callback: function () {
                	$('ul.pagination').not(":last").remove();
                   	//$(".select-multiple").multiselect();
       				//$(".select-multiple2").select2();
                }
            });
        });

        $(document).on('change', '.update-product', function () {    
            product_id 	= $(this).attr('data-id');
            category 	= $(this).find('option:selected').text();
            category_id = $(this).val();
            //Getting Scrapped Category
            $.ajax({
                url: '/products/'+product_id+'/originalCategory',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                  $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    $('#categoryUpdate').modal('show');
                    if(result[0] == 'success'){
                        $('#old_category').text(result[1]);
                        $('#changed_category').text(category);
                        $('#product_id').val(product_id);
                        $('#category_id').val(category_id);
                        if(typeof result[2] != "undefined") {
                            $("#no_of_product_will_affect").html(result[2]);
                        }
                    }else{
                        $('#old_category').text('No Scraped Product Present');
                        $('#changed_category').text(category);
                        $('#product_id').val(product_id);
                        $('#category_id').val(category_id);
                        $("#no_of_product_will_affect").html(0);
                    }
                },
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                    $('#categoryUpdate').modal('show');
                    $('#old_category').text('No Scraped Product Present');
                    $('#changed_category').text(category);
                    $('#product_id').val(product_id);
                    $('#category_id').val(category_id);
                    $("#no_of_product_will_affect").html(0);
                }
            });
        });


      function changeSelected(){
            product_id 	= $('#product_id').val();
            category 	= $('#category_id').val();
            $.ajax({
                url: '/products/'+product_id+'/updateCategory',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    category : category
                },
                beforeSend: function () {
                      $('#categoryUpdate').modal('hide');  
                      $("#loading-image").show();
                      //$("#loading-image").hide();
                },
                success: function(result){
                    toastr['success']('Request Sent successfully', 'success');
                    $("#loading-image").hide();
                }, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
            });
        }

        function changeAll(){
            product_id = $('#product_id').val();
            category = $('#category_id').val();
            $.ajax({
                url: '/products/'+product_id+'/changeCategorySupplier',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    category : category
                },
                beforeSend: function () {
                              $('#categoryUpdate').modal('hide');  
                              $("#loading-image").show();
                          },
                success: function(result){
                    toastr['success']('Request Sent successfully', 'success');
                     $("#loading-image").hide();
            	}, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
         	});
        }


        function changeSelectedColor(){
            product_id = $('#product_id').val();
            color = $('#color_id').val();
            $.ajax({
                url: '/products/'+product_id+'/updateColor',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    color : color
                },
                beforeSend: function () {
	                $("#loading-image").show();
	                $("#loading-image").hide();
	            },
                success: function(result){
                    $('#colorUpdate').modal('hide');
                    toastr['success']('Request Sent successfully', 'success');
                     $("#loading-image").hide();
                }, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
            });
        
        }


        function changeAllColors(){
            product_id = $('#product_id').val();
            color = $('#color_id').val();
            $.ajax({
                url: '/products/'+product_id+'/changeColorSupplier',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    color : color
                },
                beforeSend: function () {
                   $('#colorUpdate').modal('hide');  
                   $("#loading-image").show();
                },
                success: function(result){
                   toastr['success']('Request Sent successfully', 'success');
                    $("#loading-image").hide();
             	}, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
         	});
        } 


     $(document).on('change', '.update-color', function () {    
            product_id = $(this).attr('data-id');
            color = $(this).find('option:selected').text();
            color_id = $(this).val();
            //Getting Scrapped Category
            $.ajax({
                url: '/products/'+product_id+'/originalColor',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                  $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    $('#colorUpdate').modal('show');
                    if(result[0] == 'success'){
                        $('#old_color').text(result[1]);
                        $('#changed_color').text(color);
                        $('#product_id').val(product_id);
                        $('#color_id').val(color_id);
                        if(typeof result[2] != "undefined") {
                            $("#no_of_product_will_affect_color").html(result[2]);
                        }
                    }else{
                        $('#old_color').text('No Scraped Product Present');
                        $('#changed_color').text(color);
                        $('#product_id').val(product_id);
                        $('#color_id').val(color_id);
                        $("#no_of_product_will_affect_color").html(0);
                    }
                },
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                    $('#colorUpdate').modal('show');
                    $('#old_color').text('No Scraped Product Present');
                    $('#changed_color').text(color);
                    $('#product_id').val(product_id);
                    $('#color_id').val(color_id);
                    $("#no_of_product_will_affect_color").html(0);
                }
            });
        });
  </script>
@endsection    