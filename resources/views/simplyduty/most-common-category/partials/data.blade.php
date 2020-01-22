 @if($categories->isEmpty())

            <tr>
                <td>
                    No Result Found
                </td>
            </tr>
@else

@foreach ($categories as $id => $category)
    @foreach($category as $cat)
    <tr>
        <td><input type="checkbox" class="form-control checkBoxClass" value="" name="composition"></td>
        <td>{{ $id }}</td>
        <td>{{ $cat }}</td>
    </tr>
    @endforeach   
@endforeach
@endif