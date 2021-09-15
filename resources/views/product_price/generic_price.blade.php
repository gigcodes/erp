@extends('layouts.app')

@section('title','Product pricing')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<style>.hidden {
    display:none;
}
.btn-secondary, .btn-secondary:hover, .btn-secondary:focus{
        background: #fff;
        color: #757575;
        border: 1px solid #ddd;
        outline: none;
        box-shadow: none;
    }
  .shortTable{
    cursor: pointer;
  }
</style>
<div class = "row m-0">
    <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">Product pricing</h2>
    </div>
</div>
<div class = "row m-0">
    <div class="pl-3 pr-3 margin-tb">
        <div class="pull-left cls_filter_box">
            <form class="form-inline filter_form" action="" method="GET">
                <div class="form-group mr-3">
                    <select class="form-control globalSelect2" data-placeholder="Select Category" name="category_id" id="categoryForGenericPrices">
                    <option value="">Select Websites</option>
                    @php
                    $selectcate ='';
                    if(isset($_GET['id'])){
                      $selectcate =$_GET['id'];
                    }
                    @endphp
                        @if ($categories)
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}" @if($selectcate == $category['id']) selected @endif  >{{ $category['title'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group mr-3">
                    <a onClick="showgenerice()" class="btn btn-secondary">Show Generic Prices</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row m-0">
    <div class="col-lg-12"> 
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="table-responsive">
                   <table class="table table-bordered table-striped" id="product-price" style="table-layout: fixed">
                       <thead>
                       <tr>
                           <th style="width: 7%">Category
                            <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="category" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer"data-input="category" data-order="desc" aria-hidden="true"></i>
                           </th>
                           <th style="width: 7%">website 
                            <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="website" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="website" data-order="desc" aria-hidden="true"></i>
                           </th>
                           <th style="width: 7%">Brand segment
                             <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="bsegment" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="bsegment" data-order="desc" aria-hidden="true"></i>
                           </th>
                           <th style="width: 4%;word-break: break-all">Product</th>
                           <th style="width: 5%">Country segment
                            <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="csegment" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="csegment" data-order="desc" aria-hidden="true"></i>
                           </th>
                           <th style="width: 2%">Price</th>
                           @foreach($category_segments as $category_segment)
                              <th width="3%"> Category Segment {{ $category_segment->name }}</th>
                           @endforeach
                           <th style="width: 5%">Add Duty </th>
                           <th style="width: 5%">Add Profit </th>
                           <th style="width: 3%">less_IVA </th>
                           <th style="width: 3%">Final Price</th>
                           <th style="width: 3%">Update</th>
                       </tr>
                       </thead>
                       <tbody>
                       @php $i=1; @endphp
                       @foreach ($product_list as $product) 
                           <tr  data-id="{{$i}}" data-country_code="{{$product['country']['country_code']}}" class="tr_{{$i++}}">
                               <td class="expand-row" style="word-break: break-all">
                                   <span class="td-mini-container">
                                      {{ strlen( $product['categoryName']) > 9 ? substr( $product['categoryName'], 0, 8).'...' :  $product['categoryName'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $product['categoryName'] }}
                                   </span>
                                </td>

                                <td class="expand-row" style="word-break: break-all">
                                   <span class="td-mini-container">
                                      {{ strlen( $product['product_website']) > 9 ? substr( $product['product_website'], 0, 8).'...' :  $product['product_website'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $product['product_website'] }}
                                   </span>
                                </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $product['brandSegment']) > 15 ? substr( $product['brandSegment'], 0, 15).'...' :  $product['brandSegment'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $product['brandSegment'] }}
                                            </span>
                               </td>
                               <td>{{ $product['product'] }}</td>
                             
                               <td class="expand-row" style="word-break: break-all">

                                   <span class="td-mini-container">
                                        {{ strlen( $product['country']['dutySegment']) > 9 ? substr( $product['country']['dutySegment'], 0, 9).'...' :  $product['country']['dutySegment'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $product['country']['dutySegment'] }}
                                   </span>
                               </td>
                            
                               <td>{{ $product['product_price'] }}</td>
                                @php
                                    $j=1;
                                @endphp
                                  @foreach($category_segments as $category_segment)
                                    <td>
                                        @php
                                            $category_segment_discount = \DB::table('category_segment_discounts')->where('brand_id', $product['brandId'])->where('category_segment_id', $category_segment->id)->first();
                                        @endphp

                                        @if($category_segment_discount)
                                            <input type="text" class="form-control seg_discount1 segment{{$j}}" data-row="{{$i}}" data-name="{{'seg_discount'.$i}}" value="{{ $category_segment_discount->amount }}" = data-ref="{{ $category_segment->id }}" onKeyUp="checkFinalPriceBeforeUpdate(this)" data-less_IVA="{{ $product['less_IVA'] }}" data-cate_segment_discount="{{ $product['cate_segment_discount'] }}" data-cate_segment_discount_type="{{ $product['cate_segment_discount_type'] }}" data-product_price="{{ $product['product_price'] }}" data-default_duty="{{ $product['country']['default_duty'] }}"></th>
                                        @else
                                            <input type="text" class="form-control seg_discount segment{{$j}}" data-row="{{$i}}"  data-name="{{'seg_discount'.$i}}" value="" data-ref="{{ $category_segment->id }}" onKeyUp="checkFinalPriceBeforeUpdate(this)" data-less_IVA="{{ $product['less_IVA'] }}" data-cate_segment_discount="{{ $product['cate_segment_discount'] }}" data-cate_segment_discount_type="{{ $product['cate_segment_discount_type'] }}" data-product_price="{{ $product['product_price'] }}" data-default_duty="{{ $product['country']['default_duty'] }}"></th>
                                        @endif
                                    </td>
                                    @php
                                    $j++;
                                    @endphp
                                  @endforeach 
                                  
                               <td>
                                   <div class="form-group">
                                       <div class="input-group">
                                           <input style="width: 75%;border-radius: 4px;" data-row="{{$i}}" data-name="{{'add_duty'}}" placeholder="add duty" data-ref="{{str_replace(' ', '_', $product['country']['country_name'])}}" value="{{ str_replace('%', '', $product['country']['default_duty']) }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $product['country']['country_name'])}}" name="add_duty" onKeyUp="checkFinalPriceBeforeUpdate2(this)" data-less_IVA="{{ $product['less_IVA'] }}" data-cate_segment_discount="{{ $product['cate_segment_discount'] }}" data-cate_segment_discount_type="{{ $product['cate_segment_discount_type'] }}" data-product_price="{{ $product['product_price'] }}" data-default_duty="{{ $product['country']['default_duty'] }}">
                                           <div class="ml-2" style="float: left;">%</div>
                                       </div>
                                   </div>
                               </td>
                               <td>
                                  <div style="align-items: center">
                                      <span style="min-width:50px;">{{ $product['add_profit'] }}</span>
                                      <div class="ml-2" style="float: right;">%</div>
                                      <div style="float: right;width:50%;">
                                          <input style="padding: 6px" placeholder="add profit" data-ref="web_{{ $product['store_websites_id']}}" value="{{ $product['add_profit_per'] }}" type="text" class="form-control add_profit web_{{ $product['store_websites_id']}}" name="add_profit">
                                      </div>
                                  </div>
                               </td>
                               <td>{{ $product['less_IVA'] }}</td>
                               <td id="final_price_row{{$i}}">{{ $product['final_price'] }}</td>
                               <td><button class="btn btn-secondary UpdateProduct" data-brandId ="{{$product['brandId']}}" data-countryId ="{{$product['country']['id']}}">Update</button></td>
                           </tr> 
                       @endforeach
                       </tbody>
                   </table>
                   <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
              </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
function showgenerice() {
    var catId = $('#categoryForGenericPrices').val();
    if(catId==''){
      alert('Select Category First');
    }else{
        var url = "{{url('/')}}/product-generic-pricing?id="+catId;
         window.location.replace(url);
    }
}

    var isLoading = false;
    var page = 1;
    $(document).ready(function () {
        
        $(window).scroll(function() {
            if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
              loadMore();
            }
        });

        let data = $('.filter_form').serialize();

        function loadMore() {
            if (isLoading)
                return;
            isLoading = true;
            var $loader = $('.infinite-scroll-products-loader');
            page = page + 1;

            var url  = new URL(window.location.href);
            var search_params = url.searchParams;
          // add "topic" parameter
            search_params.set('page', page);
            search_params.set('count',{{$i}});
            url.search = search_params.toString();
            var new_url = url.toString();

            $.ajax({
                url: new_url + '&' + data,
                type: 'GET',
                data: $('.filter_form').serialize(),
                beforeSend: function() {
                    $loader.show();
                },
                success: function (data) {
                    $loader.hide();
                    $('tbody').append($.trim(data['html']));
                    isLoading = false;
                },
                error: function () {
                    $loader.hide();
                    isLoading = false;
                }
            });
        }  

    $(document).on('click', '.shortTable',function(){
      var $loader = $('#loading-image-preview');
      $loader.show();
      var order = $(this).data('order');
      var input = $(this).data('input');

      var url  = new URL(window.location.href);
      var search_params = url.searchParams;

      search_params.set('order', order);
      search_params.set('input', input);

      url.search = search_params.toString();
      var new_url = url.toString();

      window.history.pushState("", "Title", new_url);
      $.ajax({
        url: new_url,
        type: 'GET',
        data: {},
        // beforeSend: function() {
        //   $loader.show();
        // },
        success: function (data) {
            $loader.hide();
            $('tbody').html($.trim(data['html']));
            isLoading = false;
        },
        error: function () {
            $loader.hide();
            isLoading = false;
        } 
      });

    });
});

    function checkFinalPriceBeforeUpdate2($that){
        var less_iva = $($that).data('less_iva').replace('%', "");;
        var product_price = $($that).data('product_price');
        var final_price =product_price;

        var cate_segment_discount = $($that).data('cate_segment_discount');
        var cate_segment_discount_type = $($that).data('cate_segment_discount_type');
        var default_duty = $($that).data('default_duty');
        var name = $($that).data('name');

        cate_segment_discount = $($that).val();
        var row = $($that).data('row');

        // if(cate_segment_discount !='' && cate_segment_discount != null){
        //     if(cate_segment_discount_type == 'percentage'){
        //         var catDisc = (product_price * cate_segment_discount)/100;
        //         final_price = final_price - catDisc;

        //     }else{
        //         final_price = final_price - cate_segment_discount;
        //     }
        // }

        // if(less_iva!=0){
        //     var lessIva = (final_price * less_iva )/100;
        //     final_price = final_price - lessIva;
        // }
        if(default_duty !=''){
            var dutyDisc = (final_price * default_duty)/100;
            final_price = final_price + dutyDisc;
        }
        $('#final_price_row'+row).text(final_price.toFixed(2));
    }

    function checkFinalPriceBeforeUpdate($that){
        var less_iva = $($that).data('less_iva').replace('%', "");;
        var product_price = $($that).data('product_price');
        var final_price =product_price;

        var cate_segment_discount = $($that).data('cate_segment_discount');
        var cate_segment_discount_type = $($that).data('cate_segment_discount_type');
        var default_duty = $($that).data('default_duty');
        var name = $($that).data('name');

        cate_segment_discount = $($that).val();
        var row = $($that).data('row');

        if(cate_segment_discount !='' && cate_segment_discount != null){
            if(cate_segment_discount_type == 'percentage'){
                var catDisc = (product_price * cate_segment_discount)/100;
                final_price = final_price - catDisc;

            }else{
                final_price = final_price - cate_segment_discount;
            }
        }

        if(less_iva!=0){
            var lessIva = (final_price * less_iva )/100;
            final_price = final_price - lessIva;
        }
        if(default_duty !=''){
            var dutyDisc = (final_price * default_duty)/100;
            final_price = final_price + dutyDisc;
        }
        $('#final_price_row'+row).text(final_price.toFixed(2));
    }


function updateDutyPrice(countryId, dutyPrice) {     
     $.ajax({
        url: "{{route('updateDutyPrice')}}",
        type: 'post',
        data: {
            _token: '{{csrf_token()}}',
            countryId: countryId,
            dutyPrice: dutyPrice.value
        },
        beforeSend: function () {
            $("#loading-image").show();
        }
    }).done(function(response) {
        $("#loading-image").hide();
        if(response.status == false){
             toastr["error"]("Something went wrong, Please try again.");
        }else{
            toastr["success"]("Product updated successfully!", "Message");
        }
    });
}

function updateSegmentPrice(segmentId, brandId, price) {
        var data = {
                _token: '{{csrf_token()}}',
                segmentId: segmentId,
                brandId: brandId,
                price: price.value
            };  
         $.ajax({
            //updateSegmentPrice  real route
            url: "{{route('updateSegmentPrice')}}",
            type: 'post',
            data: data,
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            if(response.status == false){
                toastr["error"]("Something went wrong, Please try again.");
            }else{
                toastr["success"]("Product updated successfully!", "Message");
            }
        });
    }

$(document).on('click', '.expand-row', function () {
  var selection = window.getSelection();
  if (selection.toString().length === 0) {
    $(this).find('.td-mini-container').toggleClass('hidden');
    $(this).find('.td-full-container').toggleClass('hidden');
  }
});

$(document).on('keyup', '.add_profit', function () {
        if (event.keyCode != 13) {
            return;
        }
        let add_profit = $(this).val().replace('%', '');
        let ref_name = $(this).data('ref');
        let rows = $('.'+ref_name).closest('tr');
        let product_array = [];
        for(let i=0; i< rows.length; i++){
            product_array[i] = {
                'row_id' : $(rows[i]).attr('data-id'),
                'storewebsitesid' : $(rows[i]).attr('data-storewebsitesid'),
                'product_id' : $(rows[i]).closest('tr').find('.product_id').text(),
                'add_duty' : $(rows[i]).closest('tr').find('.add_duty').val().replace('%', ''),
                'product_id' : $(rows[i]).find('.product_id').text(),
                'add_profit' : $(rows[i]).closest('tr').find('.add_profit').val().replace('%', ''),
                'country_code' : $(rows[i]).attr('data-country_code'), 
            };
        }

        $.ajax({
            url: "{{route('product.pricing.update.add_profit')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                product_array: JSON.stringify(product_array),
                add_profit: add_profit,
                row_id: $(this).closest('tr').attr('data-id'),
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            if(response.status == false){
                toastr["error"](response.message + " is not exist!", "Message");
            }else{
                response.data.forEach(function(item, index) {
                    if(item.status){
                        let row = $(`.tr_${item.row_id}`); 
                        $(row).find('td:nth-child(12) span').html(item.add_profit);
                        $(row).find('.add_profit').val(add_profit);
                        $(row).find('td:nth-child(13)').html(item.price);
                    }
                }); 
                toastr["success"]("profit updated successfully!", "Message");
            }
        });

    }); 

$(document).on('click', '.UpdateProduct', function () {
    var data ={
         _token: '{{csrf_token()}}',
        default_duty: $('.add_duty').val(),
        segmentId1:   $('.segment1').data('ref'),
        segmentprice1 :   $('.segment1').val(),
        segmentId2:   $('.segment2').data('ref'),
        segmentprice2:   $('.segment2').val(),
        brandId:      $(this).attr('data-brandId'),
        countryId:    $(this).attr('data-countryId'),
    }

    $.ajax({
        //updateSegmentPrice  real route
        url: "{{route('product_update')}}",
        type: 'post',
        data: data,
        beforeSend: function () {
            $("#loading-image").show();
        }
    }).done(function(response) {
        $("#loading-image").hide();
        if(response.status == false){
            toastr["error"]("Something went wrong, Please try again.");
        }else{
            toastr["success"]("Product updated successfully!", "Message");
        }
    });
});

</script>

@endsection