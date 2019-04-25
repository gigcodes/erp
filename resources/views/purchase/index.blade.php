@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Purchase List</h2>
            <div class="pull-left">

                <form action="/purchases/" method="GET" id="searchForm">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <input name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search">
                            </div>
                            <div class="col-md-4">
                                <button hidden type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#sendExportModal">Export</button>
              <a class="btn btn-secondary" href="{{ route('purchase.grid') }}">+</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
    @include('purchase.partials.modal-purchase')

    <div id="purchaseList">
      @include('purchase.purchase-item')
    </div>

@endsection

@section('scripts')
  <script type="text/javascript">
    var purchases_array = [];
    var agents_array = {!! json_encode($agents_array) !!};

    $(document).on('click', '.pagination a', function(e) {
      e.preventDefault();
      var url = $(this).attr('href');

      getPurchases(url);
    });

    $(document).on('click', '.ajax-sort-link', function(e) {
      e.preventDefault();
      var url = $(this).attr('href');

      getPurchases(url);
    });

    function getPurchases(url) {
      $.ajax({
        url: url
      }).done(function(data) {
        console.log(data);
        $('#purchaseList').html(data.html);
      }).fail(function() {
        alert('Error loading more purchases');
      });
    }

    $('#searchForm').on('submit', function(e) {
      e.preventDefault();

      var url = "{{ route('purchase.index') }}";
      var formData = $('#searchForm').serialize();

      $.ajax({
        url: url,
        data: formData
      }).done(function(data) {
        $('#purchaseList').html(data.html);
      }).fail(function() {
        alert('Error searching for purchases');
      });
    });

    $('#purchaseExportButton').on('click', function(e) {
      e.preventDefault();

      if (purchases_array.length > 0) {
        $('#selected_purchases').val(JSON.stringify(purchases_array));

        if ($('#purchaseExportForm')[0].checkValidity()) {
          $('#purchaseExportForm').submit();
          $('#sendExportModal').find('.close').click();
        } else {
          $('#purchaseExportForm')[0].reportValidity();
        }

      } else {
        alert('Please select atleast 1 purchase');
      }
    });

    $(document).on('click', '.export-checkbox', function() {
      var checked = $(this).prop('checked');
      var id = $(this).data('id');

      if (checked) {
        purchases_array.push(id);
      } else {
        purchases_array.splice(purchases_array.indexOf(id), 1);
      }

      console.log(purchases_array);
    });

    $(document).on('change', '#export_supplier', function() {
      var supplier_id = $(this).val();

      agents = agents_array[supplier_id];

      $('#export_agent').empty();

      $('#export_agent').append($('<option>', {
        value: '',
        text: 'Select Agent'
      }));

      Object.keys(agents).forEach(function(agent) {
        $('#export_agent').append($('<option>', {
          value: agent,
          text: agents_array[supplier_id][agent]
        }));
      });
    });
  </script>
@endsection
