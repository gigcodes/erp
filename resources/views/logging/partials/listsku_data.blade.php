@foreach ($logScrappers as $logScrapper)
    <tr>
        <td>{{ $logScrapper->id }}</td>
        <td>{{ $logScrapper->sku }}</td>
        <td>{{ $logScrapper->skuFormat($logScrapper->sku,$logScrapper->brand) }}</td>
        <td>{{ $logScrapper->brand }}</td>
        <td>@if(isset($logScrapper->category)) {{ unserialize($logScrapper->category) }} @endif</td>
        <td>{{ $logScrapper->website }}</td>
        <td>{{ $logScrapper->skuError( $logScrapper->validation_result) }}</td>
        <td>{{ $logScrapper->created_at->format('d-M-Y H:i:s') }}</td>
    </tr>
@endforeach
