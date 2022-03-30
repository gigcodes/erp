

<tr>
    <td>entered_in_product_push</td>
    <td>SKU</td>
    @foreach($conditions as $condition)
        <td>{{$condition->condition}}</td>
    @endforeach
</tr>

<tr>
    <td> @if(in_array('entered_in_product_push', $pushJourney)) YES @else NO @endif</td>
    <td>{{$productSku}}</td>
    @foreach($conditions as $condition)
        {{-- <td>@if(in_array($condition, $pushJourney)) YES @else NO @endif</td> --}}
        <td>{{$condition->$useStatus}}</td>
    @endforeach
</tr>