<table class="table table-bordered table-responsive">
    <thead>
        <tr>
            <th># Website</th>
            <th>Url</th>
            <th>Count</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($log))
            @foreach($log as $l)
                <tr>
                    <td>{{$l->website}}</td>
                    <td>{{$l->url}}</td>
                    <td>{{$l->total_error}}</td>
                </tr>
            @endforeach
        @endif
    </tbody> 
</table>    