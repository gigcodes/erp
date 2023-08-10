@extends('layouts.app')


@section('favicon' , 'productstats.png')


@section('title', 'Product Description')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<style type="text/css">
  .modal-lg{
            max-width: 1500px !important; 
  }
        </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            {{-- @dd('hi'); --}}
            <h2 class="page-heading">Total Product found ({{$products_count}})</h2>
        </div>
    </div>
<div class="row">
    <div class="col-md-12 pl-5 pr-5">
    <form action="{{ action([\App\Http\Controllers\ProductController::class, 'productDescription']) }}" method="get">
        <div class="row mb-5">
			<div class="col-md-2 pd-sm">
                <h5>Search Suppliers </h5>	
                <select class="form-control globalSelect2" multiple="true" id="supplier" name="supplier[]" placeholder="Select suppliers">
                    <option value="">Select Suppliers</option>
                    @foreach($supplier as $supplier)
                    <option value="{{ $supplier->id }}" @if(in_array($supplier->id, $request->input('supplier', []))) selected @endif>{{ $supplier->supplier }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 pd-sm">
                <h5>Search Colors </h5>	
            <?php echo Form::select("colors[]", [], null, [
                "class" => "form-control globalSelect2",
                "style" => "width: 100%;",
                'data-ajax' => route('select2.productsColors'),
                'data-placeholder' => 'Select colors',
                'multiple' => 'multiple',
            ]); ?>
            </div>
            <div class="col-md-2 pd-sm">
                <h5>Search Size system </h5>	
                <?php echo Form::select("sizeSystem[]", [], null, [
                    "class" => "form-control globalSelect2",
                    "style" => "width: 100%;",
                    'data-ajax' => route('select2.productsSizesystem'),
                    'data-placeholder' => 'Select Sizesystem',
                    'multiple' => 'multiple',
                ]); ?>
            </div>
            {{-- <div class="col-md-2 pd-sm"><br><br>
                <?php //echo Form::select("sizeSystem",['' => ''],null,["class" => "form-control globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.productsSizesystem'), 'data-placeholder' => 'Select Sizesystem']); ?>
            </div> --}}
			<div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_id" class="form-control" id="product_id" placeholder="Enter Product ID" value="{{isset($request->product_id) ? $request->product_id : ''}}">
            </div>
			<div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="sku" class="form-control" id="sku" placeholder="Enter SKU" value="{{isset($request->sku) ? $request->sku : ''}}">
            </div>
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_title" class="form-control" id="product_title" placeholder="Enter Title" value="{{isset($request->product_title) ? $request->product_title : ''}}">
            </div>
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_description" class="form-control" id="product_description" placeholder="Enter Description" value="{{isset($request->product_description) ? $request->product_description : ''}}">
            </div>
            {{-- <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_color" class="form-control" id="product_color" placeholder="Enter Color" value="{{isset($request->product_color) ? $request->product_color : ''}}">
            </div> --}}
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_size" class="form-control" id="product_size" placeholder="Enter Size" value="{{isset($request->product_size) ? $request->product_size : ''}}">
            </div>
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_composition" class="form-control" id="product_composition" placeholder="Enter Composition" value="{{isset($request->product_composition) ? $request->product_composition : ''}}">
            </div>
            {{-- <div class="col-md-2 pd-sm"><br>
                <input type="text" name="product_size_system" class="form-control" id="product_size_system" placeholder="Enter Size system" value="{{isset($request->product_size_system) ? $request->product_size_system : ''}}">
            </div> --}}
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_price" class="form-control" id="product_price" placeholder="Enter Product price" value="{{isset($request->product_price) ? $request->product_price : ''}}">
            </div>
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_discount" class="form-control" id="product_discount" placeholder="Enter Product Discounts" value="{{isset($request->product_discount) ? $request->product_discount : ''}}">
            </div>
			<div class="col-md-2 pd-sm"><br><br>
                <button class="btn btn-image mt-0">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{route('products.description')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </div>
    </form>
</div>
</div>
    
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->appends($request->except('page'))->links() }}.
        </div>
    </div>
    <div class="col-md-12 ">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" id="quick-reply-list" style="table-layout: fixed;">
                <tr>
                    <th width="6%">Product ID</th>
                    <th width="5%">SKU</th>
                    <th width="4%">Supplier</th>
                    <th width="5%">Title</th>
                    <th width="6%">Description</th>
                    <th width="6%">Color</th>
                    <th width="8%">Size</th>
                    <th width="5%">Category</th>
                    <th width="6%">Composition</th>
                    <th width="4%">Price</th>
                    <th width="6%">Size System</th>
                    <th width="5%">Discount</th>
                    <th width="6%">Dimensions</th>
                    <th width="5%">Functions</th>
                </tr>
                @foreach($products as $product)
                    <tr>
                        <td>
                            <a target="__blank" href="{{$product->supplier_link}}" style="color:black;">{{$product->product_id}}</a>
                        </td>
                        <td class="Website-task visible-app" title="{{isset($product->product->sku) ? $product->product->sku : "-"}}">
                            {{isset($product->product->sku) ? $product->product->sku : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->supplier->supplier) ? $product->supplier->supplier : "-"}}">
                            {{isset($product->supplier->supplier) ? $product->supplier->supplier : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->title) ? $product->title : "-"}}">
                            {{isset($product->title) ? $product->title : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->description) ? $product->description : "-"}}">
                            {{isset($product->description) ? $product->description : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->color) ? $product->color : "-"}}">
                            {{isset($product->color) ? $product->color : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->size) ? $product->size : "-"}}">
                            {{isset($product->size) ? $product->size : "-"}}
                        </td>
                        <td class="Website-task"title="{{isset($product->product->categories) ? $product->product->categories->title : "-"}}">
                            {{isset($product->product->categories) ? $product->product->categories->title : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->composition) ? $product->composition : "-"}}">
                            {{isset($product->composition) ? $product->composition : "-"}}
                        </td>
                        <td>
                            {{isset($product->price) ? $product->price : "-"}}
                        </td>
                        <td>
                            {{isset($product->size_system) ? $product->size_system : "-"}}
                        </td>
                        <td>
                            {{isset($product->price_discounted) ? $product->price_discounted : "-"}}
                        </td>
                        <td>
                            {{isset($product->product) ? $product->product->lmeasurement.",".$product->product->hmeasurement.",".$product->product->dmeasurement : "-"}}
                        </td>
                        <td>
                            <button style="padding: 1px" data-id="{{ $product->scraper_id }}" type="button" class="btn btn-image d-inline get-tasks-remote" title="Task list">
                                <i class="fa fa-tasks"></i>
                           </button>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->appends($request->except('page'))->links() }}.
        </div>
    </div>
    <div id="show-content-model-table" class="modal fade scrp-task-list" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                   
                </div>
            </div>
        </div>
  </div>
@endsection


@section('scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    @include('partials.script_developer_task')
    <script>
        $("#select_date").datepicker({
	  	format: 'yyyy-mm-dd'
	});

 </script>
@endsection