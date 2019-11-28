@extends('layouts.app')

@section('title', 'Supplier Scrapping  Info')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
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
                        <th>No</th>
                        <th>Supplier</th>
                        <th>Server</th>
                        <th>Last Scraped</th>
                        <th>Successful</th>
                        <th>Total</th>
                        <th>Errors</th>
                        <th>Warnings</th>
                        <th>Total URLs</th>
                        <th>Existing URLs</th>
                        <th>New URLs</th>
                        <th>Functions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @php $arMatchedScrapers = []; $i=0; @endphp
                    @foreach ($activeSuppliers as $supplier)
                        @php $data = null; @endphp
                        @foreach($scrapeData as $tmpData)
                            @if ( !empty($tmpData->website) && $tmpData->website == $supplier->scraper_name )
                                @php $data = $tmpData; $arMatchedScrapers[] = $supplier->scraper_name @endphp
                            @endif
                        @endforeach
                        @php
                            // Set percentage
                            if ( isset($data->errors) && isset($data->total) ) {
                                $percentage = ($data->errors * 100) / $data->total;
                            } else {
                                $percentage = 0;
                            }

                            // Show correct background color
                            if ( (!empty($data) && $data->running == 0) || $data == null ) {
                                echo '<tr style="background-color: red; color: white;">';
                            } elseif ( $percentage > 25 ) {
                                echo '<tr style="background-color: orange; color: white;">';
                            } else {
                                echo '<tr>';
                            }

                            $remark = \App\ScrapRemark::select('remark')->where('scraper_name',$supplier->scraper_name)->orderBy('created_at','desc')->first();
                        @endphp
                        <td>{{ ++$i }}</td>
                        <td class="p-2"><a href="/supplier/{{$supplier->id}}">{{ ucwords(strtolower($supplier->supplier)) }}</a>
                            @if(substr(strtolower($supplier->supplier), 0, 6)  == 'excel_')
                                &nbsp;<i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            @endif
                        </td>
                        <td class="p-2">{{ !empty($data) ? $data->ip_address : '' }}</td>
                        <td class="p-2">{{ !empty($data) ? date('d-m-Y H:i:s', strtotime($data->last_scrape_date)) : '' }}</td>
                        <td class="p-2 text-right">{{ !empty($data) ? $data->total - $data->errors : '' }}</td>
                        <td class="p-2 text-right">{{ !empty($data) ? $data->total : '' }}</td>
                        <td class="p-2 text-right">{{ !empty($data) ? $data->errors : '' }}</td>
                        <td class="p-2 text-right">{{ !empty($data) ? $data->scraper_new_urls : '' }}</td>
                        <td class="p-2 text-right">{{ !empty($data) ? $data->scraper_existing_urls : '' }}</td>
                        <td class="p-2 text-right">{{ !empty($data) ? $data->scraper_total_urls : '' }}</td>
                        <td class="p-2 text-right">{{ !empty($data) ? $data->warnings : '' }}</td>
                        <td>
                            <button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $supplier->scraper_name }}"><img src="/images/remark.png"/></button>
                        </td>
                        </tr>
                    @endforeach
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Supplier</th>
                        <th>Server</th>
                        <th>Last Scraped</th>
                        <th>Inventory</th>
                        <th>Total</th>
                        <th>Errors</th>
                        <th>Warnings</th>
                        <th>Total Url's</th>
                        <th>Existing URLs</th>
                        <th>New URLs</th>
                        <th>Functions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $i=0; @endphp
                    @foreach ($scrapeData as $data )
                        @if ( !in_array($data->website, $arMatchedScrapers) )
                            <tr <?php  $percentage = ($data->errors * 100) / $data->total; echo (!empty($percentage) && $percentage >= 25) ? 'style="background-color: orange; color: white;"' : '' ?>>
                                @php
                                    $remark = \App\ScrapRemark::select('remark')->where('scraper_name',$data->website)->orderBy('created_at','desc')->first();
                                @endphp
                                <td>{{ ++$i }}</td>
                                <td class="p-2">{{ $data->website }}</td>
                                <td class="p-2">{{ $data->ip_address }}</td>
                                <td class="p-2">{{ !empty($data) ? date('d-m-Y H:i:s', strtotime($data->last_scrape_date)) : '' }}</td>
                                <td class="p-2 text-right">{{ !empty($data) ? $data->total - $data->errors : '' }}</td>
                                <td class="p-2 text-right">{{ !empty($data) ? $data->total : '' }}</td>
                                <td class="p-2 text-right">{{ !empty($data) ? $data->errors : '' }}</td>
                                <td class="p-2 text-right">{{ !empty($data) ? $data->warnings : '' }}</td>
                                <td class="p-2 text-right">{{ !empty($data) ? $data->scraper_new_urls : '' }}</td>
                                <td class="p-2 text-right">{{ !empty($data) ? $data->scraper_existing_urls : '' }}</td>
                                <td class="p-2 text-right">{{ !empty($data) ? $data->scraper_total_urls : '' }}</td>
                                
                                <td>
                                    <button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $supplier->scraper_name }}"><img src="/images/remark.png"/></button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                @include('partials.modals.remarks')
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript">
        $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();

            var name = $(this).data('name');

            $('#add-remark input[name="id"]').val(name);

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.getremark') }}',
                data: {
                    name: name
                },
            }).done(response => {
                var html = '';

                $.each(response, function (index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></p>';
                    html + "<hr>";
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $('#addRemarkButton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.addRemark') }}',
                data: {
                    id: id,
                    remark: remark
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');

                var html = ' <p> ' + remark + ' <br> <small>By You updated on ' + moment().format('DD-M H:mm') + ' </small></p>';

                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                console.log(response);

                alert('Could not fetch remarks');
            });
        });
    </script>
@endsection