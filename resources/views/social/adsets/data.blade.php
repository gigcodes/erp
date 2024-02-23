@foreach ($adsets as $adset)
    <tr>
        <td>{{ \Carbon\Carbon::parse($adset->created_at)->format('d-m-Y') }}</td>
        <td>{{ $adset->account->name }}</td>
        <td>
            {{--          {{ isset($campaingns[$adset->campaign_id])?$campaingns[$adset->campaign_id]:'' }}--}}
        </td>
        <td>@if(isset($adset->account->storeWebsite))
                {{ $adset->account->storeWebsite->title }}
            @endif</td>
        <td>{{ $adset->name }}</td>

        <td>{{ $adset->billing_event }}</td>

        <td>{{ $adset->daily_budget }}</td>

        <td>{{ $adset->status  }}</td>
        <td>{{ $adset->live_status  }}</td>
        <td><a href="javascript:;" data-id="{{ $adset->id }}" class="account-history"><i class="fa fa-history"
                                                                                         title="History"></i></a></td>
    </tr>
@endforeach
{{$adsets->appends(request()->except("page"))->links()}}
