<table>
    <thead>
        <tr>
            <th>Supplier</th>
            <th>Title</th>
            <th>Description</th>
            <th>Missing</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData as $value)
        <tr>
            <td> {{$value->supplier}}</td>
            <td> {{$value->name}}</td>
            <td> {{$value->short_description}}</td>
            <td> 
                @if(empty($value->category))
                    {{'Category is Missing'}} <br/>
                @endif
                @if(empty($value->color))
                    {{'Color is Missing'}} <br/>
                @endif
                @if(empty($value->composition))
                    {{'Composition is Missing'}}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>