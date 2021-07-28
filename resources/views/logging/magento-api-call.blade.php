@extends('layouts.app')

@section('title', 'Magento Product Api call')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css" rel="stylesheet"/>
  <script src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
  <style type="text/css">

  #loading-image {
    position: fixed;
    top: 50%;
    left: 50%;
    margin: -50px 0px 0px -50px;
  }
  input {
    width: 130px;
  }
  thead tr th{
    width: 220px !important;
  }
  thead tr th:nth-child(5),thead tr th:nth-child(6),thead tr th:nth-child(7),thead tr th:nth-child(8),thead tr th:nth-child(9),thead tr th:nth-child(11),thead tr th:nth-child(12),thead tr th:nth-child(13),thead tr th:nth-child(14)
   ,thead tr th:nth-child(15),thead tr th:nth-child(16),thead tr th:nth-child(17),thead tr th:nth-child(18),thead tr th:nth-child(19),thead tr th:nth-child(20){
    width: 80px !important;
  }

  #magento_list_tbl_895_wrapper{
    padding : 10px;
  }
</style>
@endsection

@section('content')

  <div class="row m-0">
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Magento Product API Call</h2>
    </div>
  </div>
  <div class="row m-0" style="margin-bottom: 10px; margin: 10px">
    <div class="col-lg-12 margin-tb">
       <input type="text" placeholder="Enter the limit of product" name="product_limit" class="form-control col-md-4 product-limit-text">
       <input type="text" placeholder="Search Here" name="product_name" class="form-control col-md-4 product-name-text ml-3">
       <button class="btn btn-secondary check-latest-product ml-2">Check latest product</button>
       <div class="pull-right">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Delete
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="left: auto !important;right: 0 !important;">
            <li class="dropdown-item delete_api_search_history" value="60">Last Hours</li>
            <li class="dropdown-item delete_api_search_history" value="1">Last 24 Hours</li>
            <li class="dropdown-item delete_api_search_history" value="7">Last 7 Days</li>
            <li class="dropdown-item delete_api_search_history" value="30">Last 4 Weeks</li>
            <li class="dropdown-item delete_api_search_history" value="100">All Time</li>
          </div>
        </div>
       </div>
    </div>
    
  </div>
  <div class="row m-0 pt-3">
    <div class="col-md-12">
      {{-- <div class="panel panel-default"> --}}
        {{-- <div class="panel-body p-0"> --}}
          <div class="table-responsive">
            <table id="magento_list_tbl_895" class="table table-bordered table-hover">
              <thead>
                <th>No</th>
                <th>Website</th>
                <th>Product SKU</th>
                <th>Product Name</th>
                <th>Category assigned</th>
                <th>Size Pushed</th>
                <th>Brand Pushed</th>
                <th>Size Chart Pushed</th>
                <th>Dimensions Pushed</th>
                <th>Composition Pushed</th>
                <th>Images Pushed</th>
                <th>English</th>
                <th>Arabic</ th>
                <th>German</th>
                <th>Spanish</th>
                <th>French</th>
                <th>Italian</th>
                <th>Japanese</th>
                <th>Korean</th>
                <th>Russian</th>
                <th>Chinese</th>
                <th>Status</th>
                <th>Action</th>
              </thead>
              <tbody class="magento_api_search_data">
                @foreach ($data as $key => $val)
                    <tr data-id="{{ $val->id }}">
                      <td>{{ ++$key }}</td>
                      <td>{{ $val->website_id }}</td>
                      <td>{{ $val->sku }}</td>
                      <td>{{ $val->website }}</td>
                      <td>{{ $val->category_names }}</td>
                      <td>{{ $val->size }}</td>
                      <td>{{ $val->brands }}</td>
                      <td>{{ $val->size_chart_url }}</td>
                      <td>{{ $val->dimensions }}</td>
                      <td>{{ $val->composition }}</td>
                      <td>
                        @if ($val->images)
                          <img src="{{ $val->images}}" style="height:100px;">
                        @endif
                      </td>
                      <td>{{ $val->english }}</td>
                      <td>{{ $val->arabic }}</td>
                      <td>{{ $val->german }}</td>
                      <td>{{ $val->spanish }}</td>
                      <td>{{ $val->french }}</td>
                      <td>{{ $val->italian }}</td>
                      <td>{{ $val->japanese }}</td>
                      <td>{{ $val->korean }}</td>
                      <td>{{ $val->russian }}</td>
                      <td>{{ $val->chinese }}</td>
                      <td>{{ $val->status }}</td>
                      <td><button class="btn btn-image delete_api_search_history" data-id="{{ $val->id }}"><i class="fa fa-trash"></i></button></td>
                    </tr>
                @endforeach
              </tbody>
            </table>
            {{-- <div class="text-center">
            </div> --}}
          </div>
          <tr>{{ $data->links() }}</tr>
        {{-- </div> --}}
      {{-- </div> --}}
      @if ($data->count() === 0)
        <div class="text-center"><span class=""><h2 style="color:gray;">No Data Found </h2></span></div>
      @endif
    </div>
  </div>
  <div class="ajax-loader" style="display:none;margin-left:50%;">
    <div class="inner_loader">
    <img src="{{ asset('loading.gif') }}">
    </div>
  </div>
    @endsection
  @section('scripts')
    <script type="text/javascript">
      $(document).on('click','.delete_api_search_history',function(){
        var days = $(this).val();
        var id = $(this).data('id');
        var $this = $(this);
        $.ajax({
          method: "POST",
          url: "{{ route('delete.magento.api-search-history') }}",
          data: {
            "_token": "{{ csrf_token() }}",
            id:id,
            days:days,
          },
          success: function(response){
            if (response.status == true) {
              $this.closest('tr').remove();
              toastr.success('Data Deleted Successfully')
            }
            if (response.code == 200) {
              toastr.success(response.message)
              setTimeout(function(){
                location.reload();
              }, 1000);

            }
          }
        });
      });
    // if (localStorage.getItem("luxury-product-data-asin") !== null) {
    //   var data = JSON.parse(localStorage.getItem('luxury-product-data-asin'));
    //   var example = $("#magento_list_tbl_895").DataTable({
    //     dom: 'flBrtip',
    //     stateSave: true,
    //     paging: true,
    //     processing: true,
    //     serverSide: true,
    //     bJQueryUI: true,
    //     ordering: false,
    //     lengthMenu: [[10, 25, 50,100,200, -1], [10, 25, 50,100,200, "All"]],
    //     ajax:{
    //       method: "POST",
    //       url: "/logging/magento-product-skus-ajax/",
    //       data: {
    //         "_token": "{{ csrf_token() }}",
    //         productSkus:JSON.stringify(data)
    //       }
    //     },  fixedColumns: true,
    //     language: {
    //       searchPlaceholder: "Search..."
    //     },columns:
    //     [
    //       {
    //         mRender: function (data, type, row)
    //         {
    //           return row.websites.join(', ')
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.sku
    //         }
    //       }, {
    //         mRender: function (data, type, row)
    //         {
    //           return row.product_name
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.category_names.join(', ')
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.size
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.brands ?  row.brands  :'Not Provided'
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //         return row.size_chart_url ? row.size_chart_url : "No"
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.dimensions ? row.dimensions : 'Not Provided'
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.composition ? row.composition : 'Not Provided'
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           if(row.media_gallery_entries.length > 0 ){
    //             return row.media_gallery_entries[0].file
    //           }else{
    //             return 'Not Provided'
    //           }
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.english
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.arabic
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.german
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.spanish
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.french
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.italian
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.japanese
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.korean
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.russian
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.chinese
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.success ? "Success" : "Product not found in Website."
    //         }
    //       }
    //     ]
    //   });
    // }

     $(document).on("click",".check-latest-product",function() {
        let limit = $(".product-limit-text").val();
        let website_name = $(".product-name-text").val();
        if(limit == '') {
           alert("Please select limit");
           return false;
        }

        $.ajax({
          method: "GET",
          url: "/logging/get-latest-product-for-push",
          data: {
            "limit": limit,
            "website_name": website_name
          },
          // dataType: 'json'
        })
        .done(function(result) {
          $('.pagination').hide();
          $('.magento_api_search_data').html(result);
          // if(result.code == 200){ 
          //   localStorage.setItem('luxury-product-data-asin', JSON.stringify(result.products));
          // }
          // window.location.href = `?limit=${limit}&website_name=${website_name}`;
        });

    });

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
