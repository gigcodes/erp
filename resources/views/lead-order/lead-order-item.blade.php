<div class="table-responsive-lg">
    <table class="table table-bordered" style="margin-top: 25px">
    <tr>
        <th style="width: 2%">Lead or Order</th>
        <th style="width: 15%">Customer Name</th>
        <th style="width: 8%">Date</th>
        <th style="width: 10%">Products ID</th>
        <th style="width: 15%">Product Name</th>
        <th style="width: 15%">Brand</th>
        <th style="width: 15%">Product Price</th>
        <th style="width: 15%">Discount</th>
        <th style="width: 15%">Final Price</th>
        <th style="width: 15%">GMU</th>
    </tr>
    @foreach ($leadOrder_array as $key => $leadOrder) 
        <tr class="">
            <td>{{ isset($leadOrder['id'])? $leadOrder['id'] : '' }}</td>
            <td>{{ isset($leadOrder['customer_name'])? $leadOrder['customer_name'] : '' }}</td>
            <td>{{ isset($leadOrder['order_date'])? Carbon\Carbon::parse($leadOrder['order_date'])->format('d-m-Y') : '' }}</td>
            <td>{{ isset($leadOrder['product_id'])? $leadOrder['product_id'] : ''}}</td>
            <td>{{ isset($leadOrder['name'])? $leadOrder['name'] : ''}}</td>
            <td>{{ isset($leadOrder['brand_name'])? $leadOrder['brand_name'] : ''}}</td>
            <td>{{ isset($leadOrder['price_inr'])? $leadOrder['price_inr'] : ''}}</td>
            <td>{{ isset($leadOrder['price_inr_discounted'])? $leadOrder['price_inr_discounted'] : ''}}</td>
            <td>
            @php 
             
             if($leadOrder['price_inr'])  {
              if($leadOrder['price_inr_discounted']) {
                $discount = $leadOrder['price_inr']*($leadOrder['price_inr_discounted']/100);
                $final_price = $leadOrder['price_inr'] - $discount;
              } 
              else {
               $final_price = $leadOrder['price_inr'];
              } 
            }
            else {
              $final_price = 0;
            }
            
            $gmu = 0;
            if($final_price) {
              $gmu = $final_price/1.22;
            }            
            @endphp
            {{ isset($final_price)? $final_price : ''}}</td>
            <td>{{number_format($gmu,2)}}</td>
        </tr>
    @endforeach
</table>
</div>
{{-- {!! $leads->links() !!} --}}
