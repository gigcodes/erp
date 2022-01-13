 @if($socialConfigs->isEmpty())

 <tr>
  <td>
    No Result Found
  </td>
</tr>
@else

@foreach ($socialConfigs as $socialConfig)

<tr>
  <td>@if(isset($socialConfig->storeWebsite)) {{ $socialConfig->storeWebsite->title }} @endif</td>
  <td>{{ $socialConfig->platform }}</td>
  <td>{{ $socialConfig->name }}</td>
  <td>{{ $socialConfig->email }}</td>
  <td>@if($socialConfig->status == 1) Active @elseif($socialConfig->status == 2) Blocked @elseif($socialConfig->status == 3)  Scan Barcode @else Inactive @endif</td>
  <td>{{ $socialConfig->created_at->format('d-m-Y') }}</td>
  <td>
    <button onclick="changesocialConfig({{ $socialConfig->id }})" class="btn btn-secondary btn-sm">Edit</button>
    @if(Auth::user()->hasRole('Admin'))
    <button onclick="deleteConfig({{ $socialConfig->id }})" class="btn btn-sm">Delete</button>
    @endif
  </td>
</tr>

@include('social.configs.partials.edit-modal')
@endforeach

@endif