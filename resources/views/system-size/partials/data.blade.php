 @if($systemSizesManagers->isEmpty())

            <tr>
                <td colspan="5" style="text-align: center;">
                    No Result Found
                </td>
            </tr>
@else

@foreach ($systemSizesManagers as $systemSizesManager)
    <tr>
        <td>{{ $systemSizesManager->category}}</td>
        <td>{{ $systemSizesManager->country }}</td>
        <td>{{ $systemSizesManager->size }}</td>
        <td>{{ date('d-m-Y',strtotime($systemSizesManager->created_at)) }}</td>
        <td>{{ date('d-m-Y H:i:s', strtotime($systemSizesManager->updated_at))}}</td>
        <td>
            <button class="btn btn-primary editmanager" data-id="{{$systemSizesManager->id}}" data-size="{{$systemSizesManager->size}}"><i class="fa fa-edit"></i></button>
            <button class="btn btn-danger deletemanager" data-id="{{$systemSizesManager->id}}"><i class="fa fa-trash"></i></button>
        </td>
    </tr>   
@endforeach
@endif