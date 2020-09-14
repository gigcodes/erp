@if(!empty($inventory_data->items()))
  @foreach ($inventory_data as $row => $data)
    <tr>
    <td>{{ $data['id'] }}</td>
    <td>{{ $data['sku'] }}</td>
    <td>{{ $data['product_name'] }}</td>
    <td>{{ $data['category'] }}</td>
    <td>{{ $data['brand_name'] }}</td>
    <td>{{ $data['supplier'] }}</td>
    <td>{{ $data['created_at'] }}</td>
    <td>
      <a  title="show medias" class="btn btn-image show-medias-modal" aria-expanded="false"><i class="fa fa-picture-o" aria-hidden="true"></i></a>
      <a  title="show status history" class="btn btn-image show-status-history-modal"><i class="fa fa-clock-o" aria-hidden="true"></i></a>
    </td>
    <td class="medias-data" data='@if(isset($data['medias']))@json($data['medias'])@endif' style="display:none"></td>
    <td class="status-history" data='@if(isset($data['status_history']))@json($data['status_history'])@endif' style="display:none"></td>
  </tr>
  @endforeach
@else
  <tr><td colspan="8"><h2>No Records</h2></td></tr>
@endif
