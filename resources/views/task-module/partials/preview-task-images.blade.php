@foreach($records as $key => $record)
<tr>
    <td>{{$key + 1}}</td>
    <td>
        <p>{{$record['url']}}</p>
        <!-- <img style="max-height:100px;" src="{{$record['url']}}" alt=""> -->
    </td>
    <td>
    <a class="btn-secondary" href="{{$record['url']}}" target="__blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
    
    
    </td>
</tr>
@endforeach