@foreach($keywords as $keyword)
<tr>
    <td>{{$keyword->id}}</td>
    <td>{{$keyword->keyword}}</td>
    <td>{{$keyword->created_at}}</td>
    <td>
    <div class="d-flex justify-content-between">
        {!! Form::open(['method' => 'DELETE','route' => ['ad-group-keyword.deleteKeyword', $campaignId, $keyword['google_adgroup_id'], $keyword['google_keyword_id']],'style'=>'display:inline']) !!}
        <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
        {!! Form::close() !!}
    </div>
    </td>
</tr>
@endforeach 