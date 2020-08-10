
@foreach($taskMilestones as $key => $taskMilestone)
<tr>
    <td>{{$key + 1}}</td>
    <td>
        {{substr($taskMilestone->developertask->task, 0, 100)}}
        <br>
        <p style="margin:0px;">total milestone : <strong>{{$taskMilestone->developertask->no_of_milestone}}</strong> </p>
        <p style="margin:0px;">Completed : <strong>{{$taskMilestone->developertask->milestone_completed}}</strong> </p>
    </td>  
    <td>
        {{$taskMilestone->ono_of_content}}
    </td>  
    <td>
        Publisher
    </td>
    <td>
        @if(!$taskMilestone->status)
        <input type="checkbox" name="store_social_content_milestone_id[]" value="{{$taskMilestone->id}}">
        @else 
        Approved
        @endif
    </td>
</tr>
@endforeach