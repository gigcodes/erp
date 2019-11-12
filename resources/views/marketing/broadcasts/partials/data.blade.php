 @if($customers->isEmpty())

 <tr>
  <td>
    No Result Found
  </td>
</tr>
@else

@foreach ($customers as $customer)

<tr>
  <td>{{ $customer->id }}</td>
  <td>{{ $customer->name }}</td>
  <td>
  	 <label class="switch">
  	 	 <input type="checkbox" class="checkbox" value="{{ $customer->id }}">
  	 	 <span class="slider round"></span>
  	 </label>
  </td>
  <td></td>
  <td>
  	 <label class="switch">
      @if(isset($customer->manual) && $customer->manual->active == 1)
       <input type="checkbox" class="checkboxs" checked value="{{ $customer->id }}">
       @else
        <input type="checkbox" class="checkboxs" value="{{ $customer->id }}">
       @endif
       <span class="slider round"></span>
  	 </label>
  </td>
  <td>@if(isset($customer->broadcastLatest)) {{ $customer->broadcastLatest->group_id }}  @if( $customer->broadcastLatest->is_delivered == 1) <span class="dot"></span> @endif @endif</td>
  <td>
    <select class="form-control whatsapp" data-id="{{ $customer->id }}">
      <option>Select Number</option>
      @foreach($numbers as $number)
      <option value="{{ $number->number }}" @if($number->number == $customer->whatsapp_number) selected @endif>{{ $number->number }}</option>
      @endforeach
    </select>
    <br>
    <input type="text" value="{{ $customer->whatsapp_number }}" disabled class="form-control">
    
  </td>
   <td><button type="button" class="btn btn-image make-remarks d-inline" data-toggle="modal" data-target="#makeRemarksModal" data-id="{{ $customer->id }}"><img src="/images/remark.png" /></button></td>
</tr>


@endforeach

@endif
