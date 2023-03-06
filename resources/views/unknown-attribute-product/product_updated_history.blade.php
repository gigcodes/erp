@if($histories)
@foreach($histories as $record)
    <tr>
        <td>{{$record->created_at}}</td>
        <td>{{$record->attribute_name}}</td>
        @if($record->attribute_name == 'category')
            <td>
                @php
                $old_category = $record->old_value;
                if(isset($record->old_category) && !empty($record->old_category)){
                    $old_category = $record->old_category->title;
                }
                echo $old_category;
                @endphp
            </td>
            <td>{{$record->new_category->title}}</td>
        @else
            <td>{{$record->old_value}}</td>
            <td>{{$record->new_value}}</td>
        @endif
        <td>{{$record->user->name}}</td>
    </tr>
@endforeach
@endif
