@extends('layouts.app')

@section('title', 'Supplier Scrapping  Info')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')

    <div class="row mb-5">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Supplier Scrapping  Info </h2>

            <div class="pull-left">
                <form class="form-inline" action="{{ route('mastercontrol.index') }}" method="GET">

                    <div class="form-group ml-3">
                        <input type='text' class="form-control" name="search" placeholder="Search" required />
                    </div>

                    <div class="form-group ml-3">
                        <select name="status" class="form-control">
                            <option>Select Type</option>
                            <option>Sucess</option>
                            <option>Failed</option>
                        </select>
                    </div>

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

            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row no-gutters mt-3">
        <div class="col-xs-12 col-md-12" id="plannerColumn">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Last Scraped</th>
                            <th>Inventory</th>
                            <th>Total</th>
                            <th>Errors</th>
                            <th>Warnings</th>
                            <th>Developer</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($scrapeData as $data)
                        <tr<?= $data->running == 0 ? ' style="background-color: red; color: white;"' : '' ?>>
                            <td class="p-2">{{ $data->website }}</td>
                            <td class="p-2">{{ date('d-m-Y H:i:s', strtotime($data->last_scrape_date)) }}</td>
                            <td class="p-2 text-right">{{ $data->total - $data->errors }}</td>
                            <td class="p-2 text-right">{{ $data->total }}</td>
                            <td class="p-2 text-right">{{ $data->errors }}</td>
                            <td class="p-2 text-right">{{ $data->warnings }}</td>
                            <td class="p-2">Unknown</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection