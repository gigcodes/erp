@extends('layouts.app')

@section('title', 'Magento Product Api call')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <style type="text/css">

  #loading-image {
    position: fixed;
    top: 50%;
    left: 50%;
    margin: -50px 0px 0px -50px;
  }
  input {
    width: 100px;
  }

</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Magento Product API Call</h2>
      <div class="pull-right">
        <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
      </div>

    </div>
  </div>

  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body p-0">
        <div class="table-responsive">
          <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
              <th style="width:10%">Website</th>
              <th style="width:10%">Product SKU</th>
              <th style="width:9%">Product Name</th>
              <th style="width:10%">Category assigned</th>
              <th style="width:7%">Size Pushed</th>
              <th style="width:11%">Brand Pushed</th>
              <th style="width:8%">Size Chart Pushed</th>
              <th style="width:11%">Dimensions Pushed</th>
              <th style="width:12%">Composition Pushed</th>
              <th style="width:10%">Images Pushed</th>
              <th style="width:7%">Sync Status</th>
              <th style="width:8%">English</th>
              <th style="width:8%">Arabic</ th>
              <th style="width:8%">German</th>
              <th style="width:8%">Spanish</th>
              <th style="width:8%">French</th>
              <th style="width:8%">Italian</th>
              <th style="width:10%">Japanese</th>
              <th style="width:8%">Korean</th>
              <th style="width:8%">Russian</th>
              <th style="width:8%">Chinese</th>
            </thead>
            <tbody>
              <tr>
                <td>

                </td>
              </tr>
            </tbody>
          </table>
          <div class="text-center">
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
  <script type="text/javascript">
  if (localStorage.getItem("luxury-product-data-asin") !== null) {
    var data = JSON.parse(localStorage.getItem('luxury-product-data-asin'));
    $.ajax({
      method: "POST",
      url: "/logging/magento-product-skus-ajax/",
      data: {
        "_token": "{{ csrf_token() }}",
        productSkus:JSON.stringify(data)
      }
    })
    .done(function(msg) {
      console.log("Data Saved: ", msg);
    });
  }
</script>
@if (Session::has('errors'))
  <script>
  toastr["error"]("{{ $errors->first() }}", "Message")
</script>
@endif
@if (Session::has('success'))
  <script>
  toastr["success"]("{{Session::get('success')}}", "Message")
</script>
@endif
@endsection
