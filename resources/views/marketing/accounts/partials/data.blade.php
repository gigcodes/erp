 @if($accounts->isEmpty())

 <tr>
  <td>
    No Result Found
  </td>
</tr>
@else

@foreach ($accounts as $account)

<tr>
 
  <td>{{ $account->last_name }}</td>
  <td>@if(isset($account->storeWebsite)) {{ $account->storeWebsite->title }} @endif</td>
  
  <td>{{ $account->password }}</td>
  <td>{{ $account->number }}</td>
  <td>{{ $account->email }}</td>
  <td>{{ $account->platform }}</td>
  <td>{{ $account->provider }}</td>
  <td>{{ $account->frequency }}</td>
  <td>@if($account->is_customer_support == 1) Yes @else No @endif</td>
  <td>
    
    
    <button onclick="editAccount({{ $account->id }})" class="btn btn-secondary btn-sm">Edit</button>
    @if(Auth::user()->hasRole('Admin'))
    <button onclick="deleteConfig({{ $account->id }})" class="btn btn-sm">Delete</button>
    @endif
  </td>
</tr>

@include('marketing.accounts.partials.edit-modal')
@endforeach

@endif