@foreach($googleadsaccount as $googleadsac)
<tr>
    <td>{{$loop->iteration}}</td>
    <td>{{$googleadsac->chanel_name}}</td>
    <td>{{$googleadsac->store_websites}}</td>
    <td>{{$googleadsac->status}}</td>
    <td>{{$googleadsac->created_at}}</td>
    <td>
        {{--  <a href="/google-campaigns/ads-account/update/{{$googleadsac->id}}" class="btn-image"><img src="/images/edit.png"></a>  --}}
        <button type="button" onclick="editaccount('{{$googleadsac->id}}')" class="btn-image" data-toggle="modal" data-target="#EditModal"><img src="{{asset('/images/edit.png')}}"></button>
        {{--  @if(Auth::user()->hasRole('Admin'))
        {!! Form::open(['method' => 'DELETE','route' => ['googleadsaccount.deleteGoogleAdsAccount', $googleadsac->id],'style'=>'display:inline']) !!}
            <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
        {!! Form::close() !!}
        @endif  --}}
        {{--  <a href="/google-campaigns?account_id={{$googleadsac->id}}" class="btn btn-sm">create campaign</a>  --}}
    </td>
</tr>
@endforeach