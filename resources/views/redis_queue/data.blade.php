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
        <td class="text-center">
            {{--            <a class="btn p-2" onclick="queueRun({{ $queue->id }}, 'status')" title="Queue status">--}}
            {{--                <i class="fa fa-line-chart" aria-hidden="true"></i>--}}
            {{--            </a>--}}
            <a onclick="queueRun({{ $queue->id }}, 'start')" class="btn p-2" title="Queue start">
                <i class="fa fa-play" aria-hidden="true"></i>
            </a>
            {{--            <a onclick="queueRun({{ $queue->id }}, 'pause')" class="btn p-2" title="Queue pause">--}}
            {{--                <i class="fa fa-pause" aria-hidden="true"></i>--}}
            {{--            </a>--}}
            <a onclick="queueRun({{ $queue->id }}, 'restart')" class="btn p-2" title="Queue resume">
                <i class="fa fa-repeat" aria-hidden="true"></i>
            </a>
            {{--            <a onclick="queueRun({{ $queue->id }}, 'terminate')" class="btn p-2" title="Queue terminate">--}}
            {{--                <i class="fa fa-times" aria-hidden="true"></i>--}}
            {{--            </a>--}}
            <a onclick="queueCommandLogs({{ $queue->id }})" class="btn p-2" title="View command execution log"><i
                        class="fa fa-eye" aria-hidden="true"></i>
            </a>
        </td>
        <td class="text-center">

            <a class="btn p-2" onclick="editQueue({{ $queue->id }})" title="Edit queue">
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </a>
            <a onclick="deleteQueue({{ $queue->id }})" class="btn p-2" title="Delete queue"><i
                        class="fa fa-trash" aria-hidden="true"></i></a>
        </td>
    </tr>


@endforeach

@endif