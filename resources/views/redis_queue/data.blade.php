@if($queues->isEmpty())

    <tr>
        <td colspan="4" class="text-center">
            No Result Found!
        </td>
    </tr>
@else

@foreach ($queues as $queue)

    <tr id="trId-{{ $queue->id }}">
        <td>{{ $queue->id ?? '' }}</td>
        <td>{{ $queue->name ?? '' }}</td>
        <td>
            @if($queue->type == 'WEBPUSHQUEUE')
                {{ 'Web Push Queue' }}
            @elseif($queue->type == 'MAINQUEUE')
                {{ 'Main Queue' }}
            @else
                {{ 'NA' }}
            @endif
        </td>
        <td>
            <a class="btn" onclick="editQueue({{ $queue->id }})">
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </a>
            <a onclick="deleteQueue({{ $queue->id }})" class="btn" title="Delete queue"><i
                        class="fa fa-trash" aria-hidden="true"></i></a>
        </td>
    </tr>


@endforeach

@endif