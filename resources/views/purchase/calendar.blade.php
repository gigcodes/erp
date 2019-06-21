@extends('layouts.app')

@section('title', 'Purchase Calendar')

@section('styles')
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

<div class="row">
  <div class="col-12 margin-tb mb-3">
    <h2 class="page-heading">Purchase Calendar</h2>

    {{-- <form action="{{ route('image.grid.approved') }}" method="GET" class="form-inline align-items-start">
      <div class="form-group mr-3 mb-3">
        {!! $category_selection !!}
      </div>

      <div class="form-group mr-3">
        <select class="form-control select-multiple" name="brand[]" multiple>
          <optgroup label="Brands">
            @foreach ($brands as $key => $name)
              <option value="{{ $key }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </optgroup>
        </select>
      </div>

      <div class="form-group mr-3">
        <strong class="mr-3">Price</strong>
        <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="10000000" data-slider-step="10" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '10000000' }}]"/>
      </div>

      <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
    </form> --}}

    <div class="pull-right">
      {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#sendAllModal">Create Broadcast</button>
      <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#uploadImagesModal">Upload Images</button> --}}
    </div>

    {{-- @include('customers.partials.modal-upload-images')
    @include('customers.partials.modal-send-to-all') --}}

  </div>
</div>

@include('partials.flash_messages')

<div class="row">
  <div class="col-12">
    <div id="calendar"></div>
  </div>
</div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
       $('#calendar').fullCalendar({
         editable: true,
         header: {
           right: "month,agendaWeek,agendaDay, today prev,next",
         },
          events: [
            @foreach ($purchase_data as $purchase)
              {
                title: "{{ $purchase['customer_city'] }} - Customer ID {{ $purchase['customer_id'] }}",
                start: "{{ $purchase['shipment_date']}}",
                order_product_id: "{{ $purchase['order_product_id'] }}"
              },
            @endforeach
          ],
          eventDrop: function(event, delta, revertFunc) {
            $.ajax({
              type: "POST",
              url: "{{ url('purchase') }}/" + event.order_product_id + "/updateDelivery",
              data: {
                _token: "{{ csrf_token() }}",
                shipment_date: event.start.format('Y-MM-DD H:mm')
              }
            }).done(function(response) {

            }).fail(function(response) {
              alert('Could not update delivery date!');
              console.log(response);
            });
          }
          // eventClick: function(calEvent, jsEvent, view) {
          //   $('#image_container').empty();
          //
          //   var download_images = [];
          //   var image = '<div class="row">';
          //   calEvent.image_names.forEach(function(img) {
          //     image += '<div class="col-md-4"><img src="' + img.name + '" class="img-responsive" /></div>';
          //     download_images.push(img.id);
          //   });
          //   image += '</div>';
          //
          //   $('#image_container').append($(image));
          //   $('#download_images_field').val(JSON.stringify(download_images));
          //
          //   // jQuery.noConflict();
          //   $('#calendarModal').modal('toggle');
          // },
          // eventRender: function(event, eventElement) {
          //   if (event.image_names) {
          //     event.image_names.forEach(function(image) {
          //       eventElement.find("div.fc-content").prepend("<img src='" + image.name +"' width='50' height='50'>");
          //     });
          //   }
          // },
          // eventDrop: function(event, delta, revertFunc) {
          //   $.ajax({
          //     type: "POST",
          //     url: "{{ route('image.grid.update.schedule') }}",
          //     data: {
          //       _token: "{{ csrf_token() }}",
          //       images: event.image_names,
          //       date: event.start.format('Y-MM-DD H:mm')
          //     }
          //   }).done(function(response) {
          //
          //   }).fail(function(response) {
          //     alert('Could not update schedule');
          //     console.log(response);
          //   });
          // }
        });
    });
  </script>
@endsection
