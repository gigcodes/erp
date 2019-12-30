@foreach ($logScrappers as $logScrapper)
    <tr>
        <td style="width: 20% !important;">@if($logScrapper->brandLink($logScrapper->sku,$logScrapper->brand))<a href="{{ $logScrapper->brandLink($logScrapper->sku,$logScrapper->brand) }}" target="_blank"> @endif{{ $logScrapper->brand }}</a></td>
        <td style="width: 20% !important;">@if(isset($logScrapper->category)) {{ $logScrapper->dataUnserialize($logScrapper->category) }} @endif</td>
        <td style="width: 20% !important;">{{ $logScrapper->website }}</td>
        <td>{{ $logScrapper->getFailedCount($logScrapper->website,$logScrapper->brand) }}</td>
        <td>@if($logScrapper->taskType($logScrapper->website,$logScrapper->dataUnserialize($logScrapper->category),$logScrapper->brand) == false) 
                <button onclick="addTask('{{ $logScrapper->website }}' , '{{ $logScrapper->dataUnserialize($logScrapper->category) }}','{{ $logScrapper->sku }}','{{ $logScrapper->brand }}')" class="btn btn-secondary">Add Issue</button>
            @else
               {!! $logScrapper->taskType($logScrapper->website,$logScrapper->dataUnserialize($logScrapper->category),$logScrapper->brand) !!}
            @endif
            </td>
    </tr>
@endforeach
