 @if($segments->isEmpty())

            <tr>
                <td colspan="5">
                    No Result Found
                </td>
            </tr>
@else

@foreach ($segments as $segment)
    <tr>
        <td>{{ $segment->id }}</td>
        <td><span id="segment_{{$segment->id}}">{{ $segment->segment }}</span></td>
        <td><span id="price_{{$segment->id}}">{{ $segment->price }}</span></td>
        <td>
       
        <form id="frmdel" action="{{ url('duty/segment/delete') }}" method="POST" >
        @csrf
        <input type="hidden" value="{{ $segment->id }}" name="segment_id">
        <button type="button" class="btn btn-secondary" onclick="showaddedit('{{$segment->id}}')">Edit</button>
        <button type="button" class="btn btn-secondary" onclick="$('#frmdel').submit();">Delete</button>
       
    </form>

        </td>
        
    </tr>   
@endforeach
@endif