@if($histories)
@foreach($histories as $record)
    <tr>
        <td>{{$record->created_at}}</td>
        <td>{{$record->attribute_name}}</td>
        @if($record->attribute_name == 'category')
            <td>{{$record->old_category->title}}</td>
            <td>{{$record->new_category->title}}</td>
        @elseif($record->attribute_name == 'size')
            @if(isset($record->old_size))
            <td>{{$record->old_size->name}}</td>
            @else
            <td>{{$record->old_value}}</td>
            @endif
            <td>{{$record->new_size->name}}</td>
        @else
            <td>{{$record->old_value}}</td>
            <td>{{$record->new_value}}</td>
        @endif
        <td>{{$record->user->name}}</td>
    </tr>
@endforeach
@endif
