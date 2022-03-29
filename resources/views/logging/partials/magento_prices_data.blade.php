@foreach($prices as $log)
<?php 

$pData = $p::find($log->product_id);

$dutyPrice = $p->getDuty( $pData->product_country_code );
$category_segment = $pData->category_segment != null ? $pData->category_segment : $pData->brand_segment;
//dd($p->product_country_code);
//$price = $p->getPrice( $log->store_websites_id, 'IN',null, true,$dutyPrice, null, null, null, isset($p->suppliers_info) ?  $p->suppliers_info[0]->price : 0, $category_segment);
?>
<tr>
    <td>
    @if($log->product_id)
        {{$log->product_id}}
    @endif
    </td>
    <td>
        {{$log->default_price}}
    </td>
    <td>
        {{$log->discounted_percentage}}
    </td>
    <td>
        {{$log->segment_discount}}
    </td>
    <td>
        {{-- {{(float)$price['segment_discount_per']}} --}}
    </td>
    <td>
        {{$log->duty_price}}
    </td>
    <td>
        {{-- {{$product->getDuty( $price->product_country_code)."%"}} --}}
    </td>
    <td>
        {{$log->override_price}}
    </td>
    <td>
    @if($log->status)
        {{$log->status}}
    @endif
    </td>
    <td>
    @if($log->web_store_id)
        {{$log->web_store_name}}
    @endif
    </td>
    <td>
    @if($log->store_website_id)
        {{$log->store_website->title}}
    @endif
    </td>
   
</tr>
@endforeach
