@extends('layouts.app')


@section('favicon' , 'productstats.png')


@section('title', 'Product Status Log')


@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Total Product found ({{$products->count()}})</h2>
        </div>
    </div>
    <form action="{{ action('ProductController@productScrapLog') }}" method="get">
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="form-group">
                <input type="text" name="select_date" class="form-control datepicker" id="select_date" placeholder="Enter Date">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-control" name="product_id" id="product_id">
                    <option value="">Products</option>
                    @foreach($products as $product)
                        <option {{ $request->get('product_id')==$product->id ? 'selected' : '' }} value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                <select class="form-control" name="status" id="status">
                    <option value="">Status</option>
                    @foreach($status as $val)
                        <option {{ $request->get('status')==$val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="col-md-1">
                <button class="btn btn-image btn-default">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->appends($request->except('page'))->links() }}.
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Date</th>
                    <th>Product ID</th>
                    <th>scrape</th>
                    <th>auto crop</th>
                    <th>final approval</th>
                    <th>is being cropped</th>
                    <th>import</th>
                    <th>unable to scrape image</th>
                    <th>crop skipped</th>
                    <th>is being enhanced</th>
                    <th>is being sequenced</th>
                    <th>import</th>
                    <th>scrape</th>
                </tr>
                @foreach($products as $product)
                    <tr>
                        <td>{{date('Y-m-d')}}</td>
                        <td>
                            <a href="{{ action('ProductController@show', $product->id) }}">{{$product->id}}</a>
                        </td>
                        <td>
                            {{isset($product->alllog_status[12][0]["created_at"]) ? $product->alllog_status[12][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[4][0]["created_at"]) ? $product->alllog_status[4][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[9][0]["created_at"]) ? $product->alllog_status[9][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[15][0]["created_at"]) ? $product->alllog_status[15][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[20][0]["created_at"]) ? $product->alllog_status[20][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[33][0]["created_at"]) ? $product->alllog_status[33][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[35][0]["created_at"]) ? $product->alllog_status[35][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[36][0]["created_at"]) ? $product->alllog_status[36][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[38][0]["created_at"]) ? $product->alllog_status[38][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[39][0]["created_at"]) ? $product->alllog_status[39][0]["created_at"] : "NA"}}
                        </td>
                        <td>
                            {{isset($product->alllog_status[40][0]["created_at"]) ? $product->alllog_status[40][0]["created_at"] : "NA"}}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->appends($request->except('page'))->links() }}.
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
    </script>
@endsection