<table>
    <thead>
        <tr>
            <th>Supplier</th>
            <th>Missing Category</th>
            <th>Missing Color</th>
            <th>Missing Composition</th>
            <th>Missing Name</th>
            <th>Missing Short Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData as $value)
        <tr>
            <td>{{$value->supplier}}</td>
            <td>{{$value->missing_category}}</td>
            <td>{{$value->missing_color}}</td>
            <td>{{$value->missing_composition}}</td>
            <td>{{$value->missing_name}}</td>
            <td>{{$value->missing_short_description}}</td>
        </tr>
        @endforeach
    </tbody>
</table>