@foreach ($websiteList as  $list)
    <tr>
    <td>{{ \Carbon\Carbon::parse($list['created_at'])->format('d-m-y') }} </td>
    <td>{{$list['id']}}</td>
    <td>{{$list['name']}}</td>

    <td>
        <button title="Open Images" type="button" class="btn preview-attached-img-btn btn-image no-pd" data-suggestedproductid="{{$list['id']}}">
             <img src="/images/forward.png" style="cursor: default;">
        </button>
        {{-- <button title="Send Images" type="button" class="btn btn-image sendImageMessage no-pd" data-id="{{$list->id}}" data-suggestedproductid="{{$list->id}}"><img src="/images/filled-sent.png" /></button> --}}
    </td>
    </tr>
    <tr class="expand-{{$list['id']}} hidden">
        <td colspan="7" id="attach-image-list-{{$list['id']}}">
            @if ($list['scrapper_image'])
            @include('scrapper-phyhon.list-image-products')
                
            @endif
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="4">
        {{ $websiteListRow->appends(request()->except("page"))->links() }}
    </td>
</tr>