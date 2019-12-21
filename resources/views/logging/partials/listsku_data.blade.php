@foreach ($logScrappers as $logScrapper)
    <tr>
        <td style="width: 2% !important;">{{ $logScrapper->id }}</td>
        <td style="width: 5% !important;">{{ $logScrapper->sku }}</td>
        <td style="width: 5% !important;">{{ $logScrapper->skuFormat($logScrapper->sku,$logScrapper->brand) }}</td>
         <td style="width: 5% !important;">{{ $logScrapper->skuFormatExample($logScrapper->sku,$logScrapper->brand) }}</td>
        <td style="width: 20% !important;"> {{ $logScrapper->brand }}</td>
        <td style="width: 20% !important;">@if(isset($logScrapper->category)) {{ $logScrapper->unserialize($logScrapper->category) }} @endif</td>
        <td style="width: 20% !important;">{{ $logScrapper->website }}</td>
        <td>{{ $logScrapper->skuError( $logScrapper->validation_result) }}</td>
        <td>{{ $logScrapper->created_at->format('d-M-Y H:i:s') }}</td>
        <td><button onclick="addTask('{{ $logScrapper->website }}' , '{{ $logScrapper->unserialize($logScrapper->category) }}')" class="btn btn-secondary">Add Task</button></td>
    </tr>
@endforeach
