@if(!empty($inventory_data->items()))
  @foreach ($inventory_data as $row => $data)
    <tr>
    <td><input type="checkbox" class="selected-product-ids" name="selected_product_ids[]" value="{{ $data['id'] }}"> {{ $data['id'] }}</td>
    <td>
      <span id="sku_long_string_{{$data['id']}}" style="display: none">{{ $data['sku'] }}</span>
      <span id="sku_small_string_{{$data['id']}}"><?php echo \Illuminate\Support\Str::substr($data['sku'],-10) ?> @if(strlen($data['sku'])>10) ...<a href="javascript:;" data-id="{{$data['id']}}" class="show_sku_long">More</a> @endif

    </td>
    <td>
    <span id="prod_long_string_{{$data['id']}}" style="display: none">{{ $data['product_name'] }}</span>
      <span id="prod_small_string_{{$data['id']}}"><?php echo \Illuminate\Support\Str::substr($data['product_name'],-10) ?> @if(strlen($data['product_name'])>10) ...<a href="javascript:;" data-id="{{$data['id']}}" class="show_prod_long">More</a> @endif


    </td>
    <td>{{ $data['category_name'] }}</td>
    <td>{{ $data['brand_name'] }}</td>
    <td>{{ $data['supplier'] }}</td>
    <td>{{ $data['size_system'] }}</td>
    <td>{{ $data['size'] }}</td>
    <td>{{ $data['size_eu'] }}</td>
    <td>
      @foreach(\App\Helpers\StatusHelper::getStatus() as $key => $status)
        @if($key==$data['status_id'])
          {{ $status }}
        @endif
      @endforeach
     </td>
    <td>{{ isset($data['history_date']) ? $data['history_date'] : $data['created_at'] }}</td>
    <td>
      <a  title="show medias" class="btn btn-image show-medias-modal des-pd" data-id="{{ $data['id'] }}" aria-expanded="false"><i class="fa fa-picture-o" aria-hidden="true"></i></a>
      <a  title="show status history" class="btn btn-image show-status-history-modal des-pd"><i class="fa fa-clock-o" aria-hidden="true"></i></a>
      <a  title="show Inventory history" data-id="{{ $data['id'] }}" class="btn btn-image show-inventory-history-modal des-pd"><i class="fa fa-history" aria-hidden="true"></i></a>
    </td>
    <td class="medias-data" data='@if(isset($data['medias']))@json($data['medias'])@endif' style="display:none"></td>
    <td class="status-history" data='@if(isset($data['status_history']))@json($data['status_history'])@endif' style="display:none"></td>
    <!-- <td class="inventory-history" data='@if(isset($data['inventory_history']))@json($data['inventory_history'])@endif' style="display:none"></td> -->
  </tr>
  @endforeach
@else
  <tr><td colspan="9"><h2>No Records</h2></td></tr>
@endif
