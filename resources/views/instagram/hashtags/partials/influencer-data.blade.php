@foreach($influencers as $influencer)
<tr>
    <td><a href="{{ $influencer->url }}" target="_blank">{{ $influencer->name }}</a></td>
    <td>{{ $influencer->posts }}</td>
    <td>{{ $influencer->phone }}</td>
    <td>{{ $influencer->website }}</td>
    <td>{{ $influencer->twitter }}</td>
    <td>{{ $influencer->facebook }}</td>
    <td>{{ $influencer->country }}</td>
    <td>{{ $influencer->email }}</td>
    <td>{{ $influencer->followers }}</td>
    <td>{{ $influencer->following }}</td>
    <td class="expand-row table-hover-cell"><span class="td-mini-container">{{ strlen($influencer->description) > 80 ? substr($influencer->description, 0, 80) : $influencer->description }}</span>
        <span class="td-full-container hidden">
            {{ $influencer->description }}
        </span></td>
    <td>{{ $influencer->keyword }}</td>    
</tr> 
@endforeach


