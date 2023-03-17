@extends('layouts.app')


@section('favicon', 'productstats.png')


@section('title', 'Product Status Log')


@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Total Product found ({{ $products_count }})</h2>
        </div>
    </div>
    <form action="{{ action([\App\Http\Controllers\ProductController::class, 'productScrapLog']) }}" method="get">
        <div class="row mb-5">
            <div class="col-md-2">
                <div class="form-group">
                    <input type="text" name="select_date" class="form-control datepicker" id="select_date"
                        placeholder="Enter Date" value="{{ isset($request->select_date) ? $request->select_date : '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <input type="text" name="product_id" class="form-control" id="product_id" placeholder="Enter Product ID"
                    value="{{ isset($request->product_id) ? $request->product_id : '' }}">
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <select class="form-control" name="status" id="status">
                        <option value="">Status</option>
                        @foreach ($status as $k => $val)
                            <option {{ $request->get('status') == $k ? 'selected' : '' }} value="{{ $k }}">
                                {{ ucwords($val) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <input type="text" name="sku" class="form-control" id="sku" placeholder="Enter Sku"
                    value="{{ isset($request->sku) ? $request->sku : '' }}">
            </div>
            <div class="col-md-2">
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
                    <th>Scrape</th>
                    <th>Auto crop</th>
                    <th>Final approval</th>
                    <th>Is being cropped</th>
                    <th>Is being scraped</th>
                    <th>Pending products without category</th>
                    <th>Request For external Scraper</th>
                    <th>Send external Scraper</th>
                    <th>Finished external Scraper</th>
                    <th>Unknown Color</th>
                    <th>Unknown Size</th>
                    <th>Unknown Composition</th>
                    <th>Unknown Measurement</th>
                </tr>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ isset($request->select_date) ? $request->select_date : date('Y-m-d') }}</td>
                        <td>
                            <a
                                href="{{ action([\App\Http\Controllers\ProductController::class, 'show'], $product->id) }}">{{ $product->id }}</a>
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[2]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[2]['created_at']))
                                {{ $product->alllog_status[2]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[2]) && $product->alllog_status[2]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[2]) && $product->alllog_status[2]['pending_status']==0)
                                {{ $product->alllog_status[2]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[4]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[4]['created_at']))
                                {{ $product->alllog_status[4]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[4]) && $product->alllog_status[4]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[4]) && $product->alllog_status[4]['pending_status']==0)
                                {{ $product->alllog_status[4]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[9]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[9]['created_at']))
                                {{ $product->alllog_status[9]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[9]) && $product->alllog_status[9]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[9]) && $product->alllog_status[9]['pending_status']==0)
                                {{ $product->alllog_status[9]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[15]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[15]['created_at']))
                                {{ $product->alllog_status[15]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[15]) && $product->alllog_status[15]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[15]) && $product->alllog_status[15]['pending_status']==0)
                                {{ $product->alllog_status[15]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[20]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[20]['created_at']))
                                {{ $product->alllog_status[20]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[20]) && $product->alllog_status[20]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[20]) && $product->alllog_status[20]['pending_status']==0)
                                {{ $product->alllog_status[20]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[33]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[33]['created_at']))
                                {{ $product->alllog_status[33]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[33]) && $product->alllog_status[33]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[33]) && $product->alllog_status[33]['pending_status']==0)
                                {{ $product->alllog_status[33]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[35]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[35]['created_at']))
                                {{ $product->alllog_status[35]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==0)
                                {{ $product->alllog_status[35]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[46]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[46]['created_at']))
                                {{ $product->alllog_status[46]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[46]) && $product->alllog_status[46]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[46]) && $product->alllog_status[46]['pending_status']==0)
                                {{ $product->alllog_status[46]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[47]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[47]['created_at']))
                                {{ $product->alllog_status[47]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[47]) && $product->alllog_status[47]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[47]) && $product->alllog_status[47]['pending_status']==0)
                                {{ $product->alllog_status[47]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            @if (isset($product->all_pending_log_status[35]) || isset($product->all_pending_log_status[37]))
                                {{ 'Pending' }}
                            @elseif (isset($product->sub_status_id) && $product->sub_status_id == 37 && $product->alllog_status[35]['created_at'])
                                {{ $product->alllog_status[35]['created_at'] }}
                            @elseif (isset($product->alllog_status[37]['created_at']))
                                {{ $product->alllog_status[37]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{--@if (isset($product->alllog_status[38]) && $product->alllog_status[38]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[38]) && $product->alllog_status[38]['pending_status']==0)
                                {{ $product->alllog_status[38]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}

                            @if (isset($product->alllog_status[38]) && $product->alllog_status[38]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[38]) && $product->alllog_status[38]['pending_status']==0)
                                {{ $product->alllog_status[38]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif

                        </td>
                        <td>
                            {{--@if (isset($product->all_pending_log_status[35]))
                                {{ 'Pending' }}
                            @elseif (isset($product->sub_status_id) && $product->sub_status_id == 39 && $product->alllog_status[35]['created_at'])
                                {{ $product->alllog_status[35]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif--}}
                            @if (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==0)
                                {{ $product->alllog_status[35]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td>
                        <td>
                            {{-- @if (isset($product->all_pending_log_status[40]))
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[40]['created_at']))
                                {{ $product->alllog_status[40]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif --}}
                            @if (isset($product->alllog_status[40]) && $product->alllog_status[40]['pending_status']==1)
                                {{ 'Pending' }}
                            @elseif (isset($product->alllog_status[40]) && $product->alllog_status[40]['pending_status']==0)
                                {{ $product->alllog_status[40]['created_at'] }}
                            @else
                                {{ 'NA' }}
                            @endif
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
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $("#select_date").datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
