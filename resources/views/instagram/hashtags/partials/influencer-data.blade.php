@foreach($influencers as $influencer)
<tr>
    <td>{{ $influencer->name }}</td>
    <td>{{ $influencer->posts }}</td>
    <td><a href="{{ $influencer->url }}" target="_blank">{{ $influencer->url }}</a></td>
    <td>{{ $influencer->followers }}</td>
    <td>{{ $influencer->following }}</td>
    <td class="expand-row table-hover-cell"><span class="td-mini-container">{{ strlen($influencer->description) > 80 ? substr($influencer->description, 0, 80) : $influencer->description }}</span>
        <span class="td-full-container hidden">
            {{ $influencer->description }}
        </span></td>
</tr> 
@endforeach
