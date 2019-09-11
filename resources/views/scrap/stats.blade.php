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
                            <th>Remark</th>
                            <th>Functions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($scrapeData as $data)
                        <tr<?= $data->running == 0 ? ' style="background-color: red; color: white;"' : '' ?>>
                            @php 
                                $remark = \App\ScrapRemark::select('remark')->where('scraper_name',$data->website)->orderBy('created_at','desc')->first();
                            @endphp
                            <td class="p-2">{{ $data->website }}</td>
                            <td class="p-2">{{ date('d-m-Y H:i:s', strtotime($data->last_scrape_date)) }}</td>
                            <td class="p-2 text-right">{{ $data->total - $data->errors }}</td>
                            <td class="p-2 text-right">{{ $data->total }}</td>
                            <td class="p-2 text-right">{{ $data->errors }}</td>
                            <td class="p-2 text-right">{{ $data->warnings }}</td>
                            <td class="p-2">@if($remark != ''){{ $remark->remark }}
                            @endif</td>
                            <td><button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $data->website }}"><img src="/images/remark.png" /></button></td>
                            
                        </tr>
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
     $(document).on('click', '.make-remark', function(e) {
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
            name:name
          },
      }).done(response => {
          var html='';

          $.each(response, function( index, value ) {
            html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            html+"<hr>";
          });
          $("#makeRemarkModal").find('#remark-list').html(html);
      });
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark').find('textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('scrap.addRemark') }}',
          data: {
            id:id,
            remark:remark
          },
      }).done(response => {
          $('#add-remark').find('textarea[name="remark"]').val('');

          var html =' <p> '+ remark +' <br> <small>By You updated on '+ moment().format('DD-M H:mm') +' </small></p>';

          $("#makeRemarkModal").find('#remark-list').append(html);
      }).fail(function(response) {
        console.log(response);

        alert('Could not fetch remarks');
      });
    });
</script>
@endsection