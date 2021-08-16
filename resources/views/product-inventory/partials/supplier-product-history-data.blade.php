@foreach ($allHistory as $key=> $row ) 
    <tr>
        <td>{{$row['supplier_name']}}</td>
        <td>{{$row['last_scrapped_on']}}</td>
        <td>{{$row['products']}}</td>
        <td><a href="javascript:;" data-supplier-id="{{ $row['supplier_id'] }}" class="brand-result-page">{{$row['brands']}}</a></td>
        <?php foreach($columnData as $e) { ?>
            <td> <?php echo isset($row['dates'][$e]) ? $row['dates'][$e] : 0; ?> </td>
        <?php } ?> 
        <td class="showSummary"><a target="_blank" href="{{route('supplier.product.summary',$row['supplier_id'])}}">Details</td>
    </tr>
@endforeach