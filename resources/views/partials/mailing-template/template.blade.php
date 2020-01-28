<tr>
    <td>{{$item->id}}</td>
    <td>{{$item->audience->name}}</td>
    <td>{{$item->template->name}}</td>
    {{--          <td>{{$value["subject"]}}</td>--}}
    <td>{{$item->scheduled_date}}</td>
    <td>
        <i title="Preview" id="preview" class="fa fa-eye preview" aria-hidden="true"></i>
        <i title="Duplicate" id="duplicate"  class="fa fa-clone duplicate" aria-hidden="true"></i>
    </td>
</tr>
