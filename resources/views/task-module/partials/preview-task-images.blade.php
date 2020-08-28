@foreach($records as $key => $record)
<tr>
    <td>{{$key + 1}}</td>
    <td>
    @if($record['isImage'])
    <img class="zoom-img" style="max-height:150px;" src="{{$record['url']}}" alt="">
    @else 
        <p>{{$record['url']}}</p>
     @endif   
    </td>
    <td>
    <a class="btn-secondary" href="{{$record['url']}}" target="__blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
    </td>
</tr>
@endforeach