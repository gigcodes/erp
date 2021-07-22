@extends('layouts.app')

@section('title','Product pricing')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<style>
    .model-width{
        max-width: 1250px !important;
    }
</style>
<div class = "row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Product pricing</h2>
    </div>
</div>


@include('partials.flash_messages')
<div class = "row">
    <div class="col-md-10 margin-tb">
        <div class="pull-left cls_filter_box">
            <form class="form-inline" action="" method="GET">
                <div class="form-group ml-3 cls_filter_inputbox">
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
    <div class="col-md-2 margin-tb">
        <div class="pull-right mt-3">
            {{-- <button type="button" class="btn btn-secondary" btn="" btn-success="" btn-block="" btn-publish="" mt-0="" data-toggle="modal" data-target="#setSchedule" title="" data-id="1">Set cron time</button> --}}
           
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb">
        {{-- {{ $list->links() }} --}}
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <table class="table table-bordered table-striped" id="product-price">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>PRODUCT ID</th>
                            <th>Country</th>
                            <th>BRAND</th>
                            <th>SEGMENT</th>
                            <th>MAIN WEBSITE</th>
                            <th>EURO PRICE</th>
                            <th>SEG DISCOUNT</th>
                            <th>LESS IVA</th>
                            <th>ADD DUTY ( DEFAULT )</th>
                            <th>ADD PROFIT</th>
                            <th>FINAL PRICE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @forelse ($product_list as $key)
                            <tr data-storeWebsitesID="{{$key['storeWebsitesID']}}" data-id="{{$i}}" data-country_code="{{$key['country_code']}}" class="tr_{{$i++}}">
                                <td>{{ $key['sku'] }}</td>
                                <td class="product_id">{{ $key['id'] }}</td>
                                <td>{{ $key['country_name'] }}</td>
                                <td>{{ $key['brand'] }}</td>
                                <td>{{ $key['segment'] }}</td> 
                                <td>{{ $key['website'] }}</td>
                                <td>{{ $key['eur_price'] }}</td>
                                <td>
                                    <span style="width: 50%">{{ $key['seg_discount'] }}</span>
                                    <input style="width: 50%;display : inline-block; float:right" placeholder="segment discount" data-ref="{{$key['segment']}}" value="{{ $key['segment_discount_per'] }}%" type="text" class="form-control seg_discount {{$key['segment']}}" name="seg_discount">
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
                                    <span style="width: 50%">{{ $key['add_profit'] }}</span>
                                    <input style="width: 50%;display : inline-block; float:right" placeholder="add profit" data-ref="{{str_replace(' ', '_', $key['brand'])}}" value="{{ $key['add_profit_per'] }}" type="text" class="form-control add_profit {{str_replace(' ', '_', $key['brand'])}}" name="add_profit">
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

@endsection
    
@section('scripts')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>

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
            success: function (response) {
                response.data.forEach(function(item, index) {
                    let row = $(`.tr_${item.row_id}`);
                    $(rows).find('td:nth-child(8) span').html(item.seg_discount);
                    $(rows).find('.seg_discount').val(item.segment_discount_per + '%');
                    $(rows).find('td:nth-child(12)').html(item.price);
                }); 
                toastr["success"]("segment discount updated successfully!", "Message");
            }
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
            success: function (response) {
                response.data.forEach(function(item, index) {
                    let row = $(`.tr_${item.row_id}`);
                    $(row).find('.add_duty').val(add_duty);
                    $(row).find('td:nth-child(12)').html(item.price);
                }); 
                toastr["success"]("duty updated successfully!", "Message");
            }
        });

    }); 

    $(document).on('keyup', '.add_profit', function () {
        if (event.keyCode != 13) {
            return;
        }
        let add_profit = $(this).val();
        let thiss = $(this);
        product_array = {
                'row_id' : $(this).closest('tr').attr('data-id'),
                'storewebsitesid' : $(this).closest('tr').attr('data-storewebsitesid'),
                'product_id' : $(this).closest('tr').find('.product_id').text(),
                'country_code' : $(this).closest('tr').attr('data-country_code'),
                'add_duty' : $(this).closest('tr').find('.add_duty').val().replace('%', ''),
                'add_profit' : $(this).closest('tr').find('.add_profit').val().replace('%', ''),
            };
        $.ajax({
            url: "{{route('product.pricing.update.add_profit')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                product_array: product_array,
                add_profit: add_profit,
                row_id: $(this).closest('tr').attr('data-id'),
            },
            success: function (response) {
                let row = $(`.tr_${response.row_id}`);
                $(row).find('td:nth-child(11) span').html(response.data.add_profit);
                $(row).find('.add_profit').val(response.data.add_profit_per);
                $(row).find('td:nth-child(12)').html(response.data.price);
                toastr["success"]("profit updated successfully!", "Message");
            }
        });

    }); 
</script>

@endsection
