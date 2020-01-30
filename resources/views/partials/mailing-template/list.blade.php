@foreach($mailings as $value)
    <tr>
        <td>{{$value["name"]}}</td>
        <td>{{$value["image_count"]}}</td>
        <td>{{$value["text_count"]}}</td>
        <td>
            @if($value['example_image'])
                <img style="width: 100px" src="{{ asset($value['example_image']) }}">
            @endif
        </td>
    </tr>
    @endforeach