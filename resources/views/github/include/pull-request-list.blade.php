@foreach($pullRequests as $pullRequest)
<?php $class =  !empty($pullRequest['conflict_exist']) ? "table-danger" : ""; ?>
<tr class="{!! $class !!}">
    <td class="Website-task">{{$pullRequest['repository']['name']}}
    <td class="Website-task">{{$pullRequest['id']}}</td>
    <td class="Website-task">{{$pullRequest['title']}}</td>
    <td class="Website-task">{{$pullRequest['source']}}</td>
    <td class="Website-task">{{$pullRequest['username']}}</td>
    <td class="Website-task">{{date('Y-m-d H:i:s', strtotime($pullRequest['updated_at']))}}</td>
    <td >
        <a class="btn btn-sm btn-secondary" href="{{ url('/github/repos/'.$pullRequest['repository']['id'].'/deploy?branch='.urlencode($pullRequest['source'])) }}">Deploy</a>
        @if($pullRequest['repository']['name'] == "erp")
            <a style="margin-top: 5px;" class="btn btn-sm btn-secondary" href="{{ url('/github/repos/'.$pullRequest['repository']['id'].'/deploy?branch='.urlencode($pullRequest['source'])) }}&composer=true">Deploy + Composer</a>
        @endif
    </td>
    <td style="width:10%;">
        {{-- <div>
            <a class="btn btn-sm btn-secondary" href="{{url('/github/repos/'.$pullRequest['repository']['id'].'/branch/merge?source=master&destination='.urlencode($pullRequest['source']))}}">
                Merge from master
            </a>
        </div> --}}
        <div style="margin-top: 5px;">
            <button class="btn btn-sm btn-secondary" style="margin-top: 5px;" onclick="confirmMergeToMaster('{{$pullRequest["source"]}}','{{url('/github/repos/'.$pullRequest['repository']['id'].'/branch/merge?destination=master&source='.urlencode($pullRequest['source']).'&task_id='.urlencode($pullRequest['id']))}}')">
                Merge into master
            </button>
            <button class="btn btn-sm btn-secondary" style="margin-top: 5px;" onclick="confirmClosePR({!! $pullRequest['repository']['id'] !!}, {!! $pullRequest['id'] !!})">
                Close PR
            </button>
        </div>
    </td>
</tr>
@endforeach