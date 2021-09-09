<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<style>.hidden {
	display:none;
}
</style>
<div class = "row m-0">
    <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">Product pricing</h2>
    </div>
</div>

<div class="row m-0">
    <div class="col-lg-12"> 
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="table-responsive">
                   <table class="table table-bordered table-striped" id="product-price" style="table-layout: fixed">
                       <thead>
                       <tr>
                           <th style="width: 7%">Category</th>
                           <th style="width: 6%">Brand</th>
                           <th style="width: 4%;word-break: break-all">Product</th>
                           <th style="width: 5%">Country</th>
                           <th style="width: 5%">Price</th>
                           @foreach($category_segments as $category_segment)
							  <th width="3%">{{ $category_segment->name }}</th>
						   @endforeach
                           <th style="width: 5%">Add Duty </th>
                           <th style="width: 5%">less_IVA </th>
                           <th style="width: 5%">Final Price</th>
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
                                                {{ strlen( $product['brandName']) > 15 ? substr( $product['brandName'], 0, 15).'...' :  $product['brandName'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $product['brandName'] }}
                                            </span>
                               </td>
                               <td>{{ $product['product'] }}</td>
                               <td class="expand-row" style="word-break: break-all">

                                   <span class="td-mini-container">
                                        {{ strlen( $product['country']['country_name']) > 9 ? substr( $product['country']['country_name'], 0, 9).'...' :  $product['country']['country_name'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $product['country']['country_name'] }}
                                   </span>
                               </td>

                               <td>{{ $product['product_price'] }}</td>

                                  @foreach($category_segments as $category_segment)
                                    <td>
                                        @php
                                            $category_segment_discount = \DB::table('category_segment_discounts')->where('brand_id', $product['brandId'])->where('category_segment_id', $category_segment->id)->first();
                                        @endphp

                                        @if($category_segment_discount)
                                            <input type="text" class="form-control seg_discount1" value="{{ $category_segment_discount->amount }}" = data-ref="{{ $category_segment->id }}" onKeyUp="updateSegmentPrice({{$category_segment->id}}, {{$product['brandId']}}, this)"></th>
                                        @else
                                            <input type="text" class="form-control seg_discount1" value="" data-ref="{{ $category_segment->id }}" onKeyUp="updateSegmentPrice({{$category_segment->id}}, {{$product['brandId']}}, this)"></th>
                                        @endif
                                    </td>
                                  @endforeach 
                              
                               <td>
                                   <div class="form-group">
                                       <div class="input-group">
                                           <input style="width: 75%;border-radius: 4px;" placeholder="add duty" data-ref="{{str_replace(' ', '_', $product['country']['country_name'])}}" value="{{ str_replace('%', '', $product['country']['default_duty']) }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $product['country']['country_name'])}}" name="add_duty" onKeyUp="updateDutyPrice({{$product['country']['id']}}, this)">
                                           <div class="ml-2" style="float: left;">%</div>
                                       </div>
                                   </div>
                               </td>
                               <td>{{ $product['less_IVA'] }}</td>
                               <td>{{ $product['final_price'] }}</td>
                           </tr> 
                       @endforeach
                       </tbody>
                   </table>
                   <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
              </div>
        </div>
    </div>
</div>
