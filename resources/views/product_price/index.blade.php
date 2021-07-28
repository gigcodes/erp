@extends('layouts.app')

@section('title','Product pricing')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<style>
    .model-width{
        max-width: 1250px !important;
    }
    .btn-secondary, .btn-secondary:hover, .btn-secondary:focus{
        background: #fff;
        color: #757575;
        border: 1px solid #ddd;
        outline: none;
        box-shadow: none;
    }
    table.dataTable thead th, table.dataTable thead td{
        border-bottom : 1px solid #ddd;
    }
    .table-bordered th, .table-bordered td {
        border-right: 1px solid #dee2e6;
        border-left: none !important;
        border-top: none !important;
    }
    table.dataTable thead th, table.dataTable thead td {
        padding: 3px 18px 3px 7px;
    }
    table.dataTable tbody th, table.dataTable tbody td {
        padding: 5px 5px;
    }
    #product-price_filter{
        /*position: absolute;*/
        /*top: -10px;*/
        /*right: 10px;*/
    }
    .form-group{
        margin-bottom:0 !important;
    }
</style>
<div class = "row m-0">
    <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">Product pricing</h2>
    </div>
</div>

{{--<div class="col-md-2 margin-tb">--}}
{{--    <div class="pull-right mt-3">--}}
{{--        --}}{{-- <button type="button" class="btn btn-secondary" btn="" btn-success="" btn-block="" btn-publish="" mt-0="" data-toggle="modal" data-target="#setSchedule" title="" data-id="1">Set cron time</button> --}}

{{--    </div>--}}
{{--</div>--}}
@include('partials.flash_messages')
<div class = "row m-0">
    <div class="pl-3 pr-3 margin-tb">
        <div class="pull-left cls_filter_box">
            <form class="form-inline" action="" method="GET">
                <div class="form-group ml-0 cls_filter_inputbox">
                    <input type="text" name="product" value="{{ request('product') }}" class="form-control" placeholder="Enter Product Or SKU">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <select name="country_code" class="form-control">
                        @php $country = request('country_code','') @endphp
                        <option value="">Select country code</option>
                        @foreach ($countryGroups as $key => $item)
                            <option value="{{ $key }}" {{ ( $country == $key ) ? 'selected' : '' }} >{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <?php echo Form::select("random",["" => "No","Yes" => "Yes"],request('random'),["class"=> "form-control"]); ?>
                </div>
                {{-- <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="keyword">
                </div> --}}
                <button type="submit" class="btn btn-secondary ml-3">Get record</button>
            </form> 
        </div>
    </div>  

</div>
<div class="row m-0">
    <div class="col-lg-12 margin-tb pl-3 pr-3">
        {{-- {{ $list->links() }} --}}
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class=" mt-3">

                   <table class="table table-bordered table-striped" id="product-price" style="table-layout: fixed;">
                       <thead>
                       <tr>
                           <th style="width: 12%">SKU</th>
                           <th style="width: 5%">Product ID</th>
                           <th style="width: 6%">Country</th>
                           <th style="width: 6%">Brand</th>
                           <th style="width: 4%;word-break: break-all">Segment</th>
                           <th style="width: 20%">Main Website</th>
                           <th style="width: 5%">EURO Price</th>
                           <th style="width: 10%">Seg Discount</th>
                           <th style="width: 5%">Less IVA</th>
                           <th style="width: 7%">Add Duty (Default)</th>
                           <th style="width: 12%">Add Profit</th>
                           <th style="width: 5%">Final Price</th>
                       </tr>
                       </thead>
                       <tbody>
                       @php $i=1; @endphp
                       @forelse ($product_list as $key)
                           <tr data-storeWebsitesID="{{$key['storeWebsitesID']}}" data-id="{{$i}}" data-country_code="{{$key['country_code']}}" class="tr_{{$i++}}">

                               <td class="expand-row" style="word-break: break-all">
{{--                                   {{ $key['sku'] }}--}}


                                   <span class="td-mini-container">
                                                {{ strlen( $key['sku']) > 15 ? substr( $key['sku'], 0, 15).'...' :  $key['sku'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                                {{  $key['sku'] }}
                                            </span>



                               </td>
                               <td class="product_id">{{ $key['id'] }}</td>
                               <td>{{ $key['country_name'] }}</td>
                               <td>{{ $key['brand'] }}</td>
                               <td>{{ $key['segment'] }}</td>
                               <td class="expand-row" style="word-break: break-all">

                                   <span class="td-mini-container">
                                                {{ strlen( $key['website']) > 30 ? substr( $key['website'], 0, 30).'...' :  $key['website'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                                {{  $key['website'] }}
                                            </span>
                               </td>
                               <td>{{ $key['eur_price'] }}</td>
                               <td>
                                   <div class="d-flex" style="align-items: center">
                                       <span style="min-width:26px;">{{ $key['seg_discount'] }}</span>
                                       <input style="padding: 6px" placeholder="segment discount" data-ref="{{$key['segment']}}" value="{{ $key['segment_discount_per'] }}%" type="text" class="form-control seg_discount {{$key['segment']}}" name="seg_discount">
                                   </div>
                               </td>
                               <td>{{ $key['iva'] }}</td>
                               <td>
                                   <div class="form-group">
                                       <div class="input-group">
                                           <input style="min-width: 30px;" placeholder="add duty" data-ref="{{str_replace(' ', '_', $key['country_name'])}}" value="{{ $key['add_duty'] }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $key['country_name'])}}" name="add_duty">
                                       </div>
                                   </div>
                               </td>
                               <td>
                                  <div class="d-flex" style="align-items: center">
                                      <span style="min-width:50px;">{{ $key['add_profit'] }}</span>
                                      <input style="padding: 6px" placeholder="add profit" data-ref="web_{{ $key['storeWebsitesID']}}" value="{{ $key['add_profit_per'] }}" type="text" class="form-control add_profit web_{{ $key['storeWebsitesID']}}" name="add_profit">
                                  </div>
                               </td>
                               <td>{{ $key['final_price'] }}</td>
                           </tr>
                       @empty
                           <tr>
                               <td colspan="11"> NO data found </td>
                           </tr>
                       @endforelse
                       </tbody>
                   </table>

            </div>
        </div>
        {{-- {{ $list->links() }} --}}
    </div>
</div>

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
            50% 50% no-repeat;display:none;">
</div>
@endsection
    
@section('scripts')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            // if ($(this).data('switch') == 0) {
            //   $(this).text($(this).data('details'));
            //   $(this).data('switch', 1);
            // } else {
            //   $(this).text($(this).data('subject'));
            //   $(this).data('switch', 0);
            // }
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
    $(document).ready( function () {
        $('#product-price').DataTable({
            "paging":   false,
            "ordering": true,
            "info":     false
        });
    } );

    $(document).on('keyup', '.seg_discount', function () {
        if (event.keyCode != 13) {
            return;
        }
        let seg_discount = $(this).val();
        let ref_name = $(this).data('ref');
        let rows = $('.'+ref_name).closest('tr');
        let product_array = [];
        for(let i=0; i< rows.length; i++){
            product_array[i] = {
                'row_id' : $(rows[i]).attr('data-id'),
                'storewebsitesid' : $(rows[i]).attr('data-storewebsitesid'),
                'product_id' : $(rows[i]).find('.product_id').text(),
                'country_code' : $(rows[i]).attr('data-country_code'),
                'add_duty' : $(rows[i]).find('.add_duty').val().replace('%', ''),
            };
        }
        $.ajax({
            url: "{{route('product.pricing.update.segment')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                product_array: JSON.stringify(product_array),
                seg_discount: seg_discount,
                row_id: $(this).closest('tr').attr('data-id'),
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) { 
            $("#loading-image").hide();
            response.data.forEach(function(item, index) {
                let row = $(`.tr_${item.row_id}`);
                $(row).find('td:nth-child(8) span').html(item.seg_discount);
                $(row).find('.seg_discount').val(seg_discount);
                $(row).find('td:nth-child(12)').html(item.price);
            }); 
            toastr["success"]("segment discount updated successfully!", "Message");
        });

    });

    $(document).on('keyup', '.add_duty', function () {
        if (event.keyCode != 13) {
            return;
        }
        let add_duty = $(this).val();
        let ref_name = $(this).data('ref');
        let rows = $('.'+ref_name).closest('tr');
        let product_array = [];
        for(let i=0; i< rows.length; i++){
            product_array[i] = {
                'row_id' : $(rows[i]).attr('data-id'),
                'storewebsitesid' : $(rows[i]).attr('data-storewebsitesid'),
                'add_duty' : $(this).closest('tr').find('.add_duty').val().replace('%', ''),
                'product_id' : $(rows[i]).find('.product_id').text(),
                'country_code' : $(rows[i]).attr('data-country_code'), 
                'seg_discount' : $(rows[i]).find('.seg_discount').val().replace('%', ''),
            };
        }
        $.ajax({
            url: "{{route('product.pricing.update.add_duty')}}",
            type: 'post', 
            data: {
                _token: '{{csrf_token()}}',
                product_array: JSON.stringify(product_array),
                add_duty: add_duty,
                row_id: $(this).closest('tr').attr('data-id'),
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) { 
            $("#loading-image").hide();
            response.data.forEach(function(item, index) {
                let row = $(`.tr_${item.row_id}`);
                $(row).find('.add_duty').val(add_duty);
                $(row).find('td:nth-child(12)').html(item.price);
            }); 
            toastr["success"]("duty updated successfully!", "Message");
        });

    }); 

    $(document).on('keyup', '.add_profit', function () {
        if (event.keyCode != 13) {
            return;
        }
        let add_profit = $(this).val();
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
                        $(row).find('td:nth-child(11) span').html(item.add_profit);
                        $(row).find('.add_profit').val(add_profit);
                        $(row).find('td:nth-child(12)').html(item.price);
                    }
                }); 
                toastr["success"]("profit updated successfully!", "Message");
            }
        });

    }); 
</script>

@endsection
