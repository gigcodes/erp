@extends('layouts.app')

@section('title', 'Log List Magento')

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
             <h2 class="page-heading">Log List Magento ({{ $total_count }})</h2>
                <div class="pull-right">
                    <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
                    <a href="/logging/magento-product-api-call" target="__blank">
                    <button type="button" class="btn btn-image"><img src="/images/details.png" /></button>
                    </a>
                </div>
         </div>

        <div class="col-md-12">
            <button type="button" class="btn btn-xs btn-secondary read_csv_file" data-toggle="modal" data-target="#product-push-infomation-modal">
                Read csv    
            </button>
        </div>


  <div class="col-md-12">
    <div class="panel panel-default">

      <div class="panel-body p-0">

        <div class="table-responsive">
          <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
              <th style="width:7%">Product ID</th>
              <th style="width:10%">SKU</th>
              <th style="width:9%">Brand</th>
              <th style="width:8%">Category</th>
              <th style="width:7%">Price</th>
              <th style="width:11%">Message</th>
              <th style="width:8%">Date/Time</th>
              <th style="width:9%">Website</th>
              <th style="width:8%">Status</th>
              <th style="width:8%">Language Id</th>
              <th style="width:7%">Sync Status</th>
              <th style="width:5%">Success</th>
              <th style="width:5%">Failure</th>
              <th style="width:6%">User</th>
              <th style="width:6%">Time</th>
              <th style="width:8%">Action</th>
            </thead>
            <tbody>
              @foreach($logListMagentos as $item)
                <tr>

                  <td>
                    <a class="show-product-information" data-id="{{ $item->product_id }}" href="/products/{{ $item->product_id }}" target="__blank">{{ $item->product_id }}</a>
                  </td>
                  <td class="expand-row-msg" data-name="sku" data-id="{{$item->id}}">
                    <span class="show-short-sku-{{$item->id}}">{{ str_limit($item->sku, 5 ,'...')}}</span>
                    <span style="word-break:break-all;" class="show-full-sku-{{$item->id}} hidden"><a href="{{ $item->website_url }}/default/catalogsearch/result/?q={{ $item->sku }}" target="__blank">{{$item->sku}}</a></span>
                  </td>
                  <td class="expand-row-msg" data-name="brand_name" data-id="{{$item->id}}">
                    <span class="show-short-brand_name-{{$item->id}}">{{ str_limit($item->brand_name, 10, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-brand_name-{{$item->id}} hidden">{{$item->brand_name}}</span>
                  </td>
                  <td class="expand-row-msg" data-name="category_title" data-id="{{$item->id}}">
                    <span class="show-short-category_title-{{$item->id}}">{{ str_limit($item->category_home, 10, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-category_title-{{$item->id}} hidden">{{$item->category_home}}</span>
                  </td>
                  <td> {{$item->price}} </td>
                  <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                    <span class="show-short-message-{{$item->id}}">{{ str_limit($item->message, 20, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$item->message}}</span>
                  </td>
                  <td>
                    @if(isset($item->log_created_at))
                      {{ date('M d, Y',strtotime($item->log_created_at))}}
                    @endif
                  </td>
                  <td class="expand-row-msg" data-name="website_title" data-id="{{$item->id}}">
                    <span class="show-short-website_title-{{$item->id}}">{{ str_limit($item->website_title, 10, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-website_title-{{$item->id}} hidden">{{$item->website_title}}</span>
                  </td>
                  <td>
                    {{ (isset($item->stock) && $item->stock > 0) ? 'Available' : 'Out of Stock' }}
                  </td>
                  <td> {{(!empty($item->languages)) ? implode(", ",json_decode($item->languages)) : ''}} </td>
                  <td> {{$item->sync_status}} </td>
                  <td>{{$item->total_success}} </td>
                  <td> {{$item->total_error}}</td>
                  <td>{{$item->log_user_name}}</td>
                  <td>{{Carbon\Carbon::parse($item->log_created_at)->format('H:i')}}</td>
                  <td style="display:flex;justify-content: space-between;align-items: center;">
                    <button data-toggle="modal" data-target="#update_modal" class="btn btn-xs btn-secondary update_modal" data-id="{{ $item}}"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-xs btn-secondary show_error_logs" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}"><i class="fa fa-eye"></i></button>
                    <input style="width:20px;height:20px" type="checkbox" class="form-control selectProductCheckbox_class" value="{{ $item->sku }}{{ $item->color }}" websiteid="{{$item->store_website_id}}" name="selectProductCheckbox"/>
                  </td>
                </tr>
              @endforeach()
            </tbody>
          </table>

          <div class="text-center">
            {!! $logListMagentos->appends($filters)->links() !!}
          </div>
        </div>
      </div>
    </div>
  </div>


  
  <div class="modal fade" id="product-push-infomation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Read Csv</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

            <form action="{{ route('update.magento.product-push-information') }}" method="POST" id="store-product-push-information">
                @csrf
                <label for="cars">Choose a website:</label>
                <div class="form-group">
                    <select class="form-control" name="websites" id="websites">
                    <option value="sololuxury.com">sololuxury.com</option>
                    <option value="suvandnat.com">suvandnat.com</option>
                    <option value="Avoir-chic.com">Avoir-chic.com</option>
                    <option value="veralusso.com">veralusso.com</option>
                    <option value="Brands-labels.com">Brands-labels.com</option>
                    </select>

                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">File path</label>
                        <input type="text" class="form-control" id="website-path" value="{{ 'sololuxury.com' . '/var/exportcsv/product.csv'  }}">
                      </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn  btn-secondary">Update data</button>
                </div>
            </form>

        </div>

      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $("#select_date").datepicker({
	  	format: 'yyyy-mm-dd'
	});
    </script>
  <script type="text/javascript">
  var product = []
  if (localStorage.getItem("luxury-product-data-asin") !== null) {
     var data = JSON.parse(localStorage.getItem('luxury-product-data-asin'));
     product = data
     $.each(product,function(i,e){
      console.log(e)
      $(".selectProductCheckbox_class[value='"+e.sku+"']").attr("checked",true);
     });
  }

  $('#magento_list_tbl_895 tbody').on('click', 'tr', function () {
    var ref = $(this);
    ref.find('td:eq(15)').children('.selectProductCheckbox_class').change(function(){
      if($(this).is(':checked')){
        var val = $(this).val()
        //if(!product.includes($(this).val())){  
          if(product.length > 0){
            $.each(product,function(i,e){
            if(e.sku == val){
              product.splice(i,1)
            }
          })
          }
          
          var item = {"sku":$(this).val(),"websiteid":$(this).attr("websiteid")}
            product.push(item)
            
        //  }
          localStorage.setItem('luxury-product-data-asin', JSON.stringify(product));
      } else {
        //var index = product.indexOf($(this).val())
        var val = $(this).val()
        $.each(product,function(i,e){
          if(e.sku == val){
            product.splice(i,1)
          }
        })
        // if(index !=-1){
        //   product.splice(index,1)
        // }
        localStorage.setItem('luxury-product-data-asin', JSON.stringify(product));
      }
    })
    console.log(product)
  })
  $(document).on("click", ".show_error_logs", function() {
    var id = $(this).data('id');
    var store_website_id = $(this).data('website');
    $.ajax({
      method: "GET",
      url: "/logging/show-error-log-by-id/" + id,
      data: {
        "_token": "{{ csrf_token() }}"
      },
      dataType: 'html'
    })
    .done(function(result) {
      $('#ErrorLogModal').modal('show');
      $('.error-log-data').html(result);
    });

  });
  $(document).on("click", ".update_modal", function() {
    var data = $(this).data('id');

    var detail = $(this).data('id');
    //alert(JSON.stringify(detail));
    $("#single_product_image").attr("src", detail['image_url']);
    $("#update_product_id").val(detail['product_id']);
    $("#update_name").val(detail['name']);
    $("#update_short_description").val(detail['short_description']);
    $("#update_size").val(detail['size']);
    $("#update_price").val(detail['price']);
    $("#update_price_eur_special").val(detail['price_eur_special']);
    $("#update_price_eur_discounted").val(detail['price_eur_discounted']);

    $("#update_price_inr").val(detail['price_inr']);
    $("#update_price_inr_special").val(detail['price_inr_special']);
    $("#update_price_inr_discounted").val(detail['price_inr_discounted']);

    $("#update_measurement_size_type").val(detail['measurement_size_type']);
    $("#update_lmeasurement").val(detail['lmeasurement']);
    $("#update_hmeasurement").val(detail['hmeasurement']);
    $("#update_dmeasurement").val(detail['dmeasurement']);

    $("#update_composition").val(detail['composition']);
    $("#update_made_in").val(detail['made_in']);
    $("#update_brand").val(detail['brand']);
    $("#update_category").val(detail['category']);
    $("#update_supplier").val(detail['supplier']);
    $("#update_supplier_link").val(detail['supplier_link']);
    $("#update_product_link").val(detail['product_link']);
  });

  $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });

  function changeMagentoStatus(logId, newStatus) {
    if (!newStatus) {
      return;
    }
    $.ajax({
      method: "POST",
      url: "/logging/list-magento/" + logId,
      data: {
        "_token": "{{ csrf_token() }}",
        status: newStatus
      }
    })
    .done(function(msg) {
      console.log("Data Saved: ", msg);
    });
  }

  $(document).on("click","#submit-show-report",function(e){
    e.preventDefault();
    $.ajax({
      method: "GET",
      url: "/logging/list-magento/error-reporting"
    })
    .done(function(response) {
        $("#show-error-count").find(".modal-body").html(response);
        $("#show-error-count").modal("show");
    });
  });

  $(document).on("click",".show-product-information",function (e) {
    e.preventDefault();
    var id  = $(this).data("id");
    $.ajax({
      method: "GET",
      url: "/logging/list-magento/product-information",
      data : {
         product_id : id
      }
    })
    .done(function(response) {
        $(".product-information-data").html(response);
        $("#show-product-information").modal("show");
    });
  });

$(document).on('submit','#store-product-push-information',function(e){
e.preventDefault()


      $.ajax({
      method: "POST",
      url: "{{ route('update.magento.product-push-information') }}",
      data: $(this).serialize()
    })
    .done(function(msg) {
      console.log("Data Saved: ", msg);
    });

})

$(document).on('change','#websites',function(){
    $('#website-path').val($(this).val()+'/var/exportcsv/product.csv')
})


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
