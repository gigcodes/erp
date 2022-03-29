

<tr>
    <td>entered_in_product_push</td>
    <td>SKU</td>
    @foreach($conditions as $condition)
        <td>{{$condition}}</td>
    @endforeach
</tr>

<tr>
    <td> @if(in_array('entered_in_product_push', $pushJourney)) YES @else NO @endif</td>
    <td>{{$productSku}}</td>
    <?php 
        if($useStatus == 'status')
        {}
        else{}
    ?>
    
    @foreach($conditions as $condition)
    
        {{-- <td>@if(in_array($condition, $pushJourney)) YES @else NO @endif</td> --}}
        <td></td>
    @endforeach
</tr>