<tr>
    <td>{{$item["name"]}}</td>
    <td>{{$item["image_count"]}}</td>
    <td>{{$item["text_count"]}}</td>
    <td>
        @if($item['example_image'])
            <img style="width: 100px" src="{{ asset($item['example_image']) }}">
        @endif
    </td>
</tr>
