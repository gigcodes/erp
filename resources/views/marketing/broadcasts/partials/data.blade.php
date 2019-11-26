 @if($customers->isEmpty())

 <tr>
  <td>
    No Result Found
  </td>
</tr>
@else

@foreach ($customers as $customer)

<tr id="row{{ $customer->id }}">
  <td class="show_select"><input type="checkbox" name="select" class="form-control checkbox_select" value="{{ $customer->id }}"></td>
  <td><a href="/customers/{{ $customer->id }}/post-show" target="_blank">{{ $customer->id }}</a></td>
  <td>{{ $customer->name }}</td>
  <td>{{ $customer->phone }}</td>
  <td>
  	 <label class="switch" style="margin: 0px">
      @if($customer->do_not_disturb == 1)
       <input type="checkbox" class="checkbox" checked value="{{ $customer->id }}">
       @else
        <input type="checkbox" class="checkbox" value="{{ $customer->id }}">
       @endif
       <span class="slider round"></span>
  	 </label>
  </td>
 <!--  <td></td> -->
  <td>
  	 <label class="switch" style="margin: 0px">
      @if(isset($customer->customerMarketingPlatformActive) && $customer->customerMarketingPlatformActive->active == 1)
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
      <option value="{{ $number->number }}" @if($number->number == $customer->broadcast_number) selected @endif>{{ $number->number }}</option>
      @endforeach
    </select>
  </td>
   <td><button type="button" class="btn btn-image make-remarks d-inline" data-toggle="modal" data-target="#makeRemarksModal" data-id="{{ $customer->id }}"><img src="/images/remark.png" /></button></td>
</tr>


@endforeach

@endif
