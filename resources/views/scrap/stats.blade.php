@extends('layouts.app')

@section('title', 'Supplier Scrapping  Info')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')

    <div class="row mb-5">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Supplier Scrapping Info</h2>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row no-gutters mt-3">
        <div class="col-xs-12 col-md-12" id="plannerColumn">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm">
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