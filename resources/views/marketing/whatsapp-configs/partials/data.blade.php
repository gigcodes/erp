 @if($whatsAppConfigs->isEmpty())

 <tr>
  <td>
    No Result Found
  </td>
</tr>
@else

@foreach ($whatsAppConfigs as $whatsAppConfig)

<tr>
 
  <td>{{ $whatsAppConfig->username }}</td>
  <td>{{ Crypt::decrypt($whatsAppConfig->password) }}</td>
  <td>{{ $whatsAppConfig->number }}</td>
  <td>{{ $whatsAppConfig->provider }}</td>
  <td>{{ $whatsAppConfig->frequency }}</td>
  <td>@if($whatsAppConfig->is_customer_support == 1) Yes @else No @endif</td>
  <td>{{ $whatsAppConfig->send_start }}</td>
  <td>{{ $whatsAppConfig->send_end }}</td>
  <td>{{ $whatsAppConfig->device_name }}</td>
  <!-- <td>{{ $whatsAppConfig->simcard_number }}</td>
  <td>{{ $whatsAppConfig->simcard_owner }}</td>
  <td>{{ $whatsAppConfig->payment }}</td>
  <td>{{ $whatsAppConfig->recharge_date }}</td> -->
  <td>@if($whatsAppConfig->status == 1) Active @elseif($whatsAppConfig->status == 2) Blocked @else Inactive @endif</td>
  <td>{{ $whatsAppConfig->created_at->format('d-m-Y') }}</td>
  <td>
    <button onclick="changewhatsAppConfig({{ $whatsAppConfig->id }})" class="btn btn-secondary btn-sm">Edit</button>
    @if(Auth::user()->hasRole('Admin'))
    <button onclick="deleteConfig({{ $whatsAppConfig->id }})" class="btn btn-sm">Delete</button>
    @endif
  </td>
</tr>

@include('marketing.whatsapp-configs.partials.edit-modal')
@endforeach

@endif