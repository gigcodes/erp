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
                <a class="btn btn-secondary" href="{{ route('purchase.grid') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div id="purchaseList">
      @include('purchase.purchase-item')
    </div>

@endsection

@section('scripts')
  <script type="text/javascript">
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
  </script>
@endsection
