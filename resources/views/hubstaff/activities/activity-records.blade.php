
    <form>
    @csrf

    <input type="hidden" name="user_id" value="{{$user_id}}">
    <input type="hidden" name="date" value="{{$date}}">

    <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
    <div>
        <table class="table table-bordered" style="table-layout:fixed;">
        <tr>
          <th style="width:40%">Date & time</th>
          <th style="width:10%">Time tracked</th>
          <th style="width:10%">Time Approved</th>
          <th style="width:30%">Task</th>
          <th style="width:10%" class="text-center">Action</th>
        </tr>
          @foreach ($activityrecords as $record)
            <tr>
            <td>{{ $record->OnDate }} {{$record->onHour}}:00:00 </td>
              <td>{{ number_format($record->total_tracked / 60,2,".",",") }}</td>
              <td>{{ number_format($record->totalApproved / 60,2,".",",") }}</td>
              <td></td>
              <td>
              &nbsp;<input type="checkbox" name="sample" {{$record->sample ? 'checked' : ''}}  data-id="{{ $record->OnDate }}{{$record->onHour}}" class="selectall"/>
                <a data-toggle="collapse" href="#collapse_{{ $record->OnDate }}{{$record->onHour}}"><img style="height:15px;" src="/images/forward.png"></a>
              </td>
            </tr>
            <tr style="width:100%;" id="collapse_{{ $record->OnDate }}{{$record->onHour}}" class="panel-collapse collapse">
            <td colspan="5" style="padding:0px;">
              <table style="table-layout:fixed;" class="table table-bordered">
              @foreach ($record->activities as $a)
                <tr>
                <td style="width:40%">{{ $a->starts_at}}</td>
                  <td style="width:10%">{{ number_format($a->tracked / 60,2,".",",") }}@if($a->is_manual) (Manual time) @endif</td>
                  <td style="width:10%">{{ number_format($a->totalApproved / 60,2,".",",") }}</td>
                  <td style="width:30%">{{ $a->taskSubject}}</td>
                  <td style="width:10%">
                    <input type="checkbox" class="{{ $record->OnDate }}{{$record->onHour}}" value="{{$a->id}}" name="activities[]" {{$a->status ? 'checked' : ''}}>
                  </td>
                </tr>
              @endforeach
              </table>
            </td>
            </tr>
          @endforeach
      </table>
    </div>
    <input type="hidden" id="hidden-forword-to" name="forworded_person">
    @if($isAdmin)
    <!-- <div class="form-group">
        <label for="forword_to">Forword to user</label>
        <select name="forword_to_user" id="" data-person="user" class="form-control select-forword-to">
          <option value="">Select</option>
          @foreach($users as $user)
          <option value="{{$user->id}}">{{$user->name}}</option>
          @endforeach
        </select>
    </div> -->
    @if(count($teamLeaders) > 0)
      <div class="form-group">
          <label for="forword_to">Forword to team leader</label>
          <select name="forword_to_team_leader" id="" data-person="team_lead" class="form-control select-forword-to">
            <option value="">Select</option>
            @foreach($teamLeaders as $ld)
            <option value="{{$ld->id}}">{{$ld->name}}</option>
            @endforeach
          </select>
      </div>
      @endif
    @endif
    @if($isTeamLeader)
      <div class="form-group">
          <label for="forword_to">Forword to admin</label>
          <select name="forword_to_admin" id="" data-person="admin" class="form-control select-forword-to">
            <option value="">Select</option>
            @foreach($admins as $admin)
            <option value="{{$admin->id}}">{{$admin->name}}</option>
            @endforeach
          </select>
      </div>
    @endif
    @if($taskOwner)
      <div class="form-group">
          <label for="forword_to">Forword to admin</label>
          <select name="forword_to_admin" id="" data-person="admin" class="form-control select-forword-to">
            <option value="">Select</option>
            @foreach($admins as $admin)
            <option value="{{$admin->id}}">{{$admin->name}}</option>
            @endforeach
          </select>
      </div>
      @if(count($teamLeaders) > 0)
      <div class="form-group">
          <label for="forword_to">Forword to team leader</label>
          <select name="forword_to_team_leader" id="" data-person="team_lead" class="form-control select-forword-to">
            <option value="">Select</option>
            @foreach($teamLeaders as $ld)
            <option value="{{$ld->id}}">{{$ld->name}}</option>
            @endforeach
          </select>
      </div>
      @endif
    @endif
    @if($hubActivitySummery)
    <div class="form-group">
        <label for="">Previous remarks</label>
        <textarea class="form-control" cols="30" rows="5" name="previous_remarks" placeholder="Rejection note...">@if($hubActivitySummery){{$hubActivitySummery->rejection_note}}@endif</textarea>
    </div>
    @endif
    <div class="form-group">
        <label for="">New remarks</label>
        <textarea class="form-control" name="rejection_note" id="rejection_note" cols="30" rows="5" placeholder="Rejection note..."></textarea>
    </div>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    @if($isAdmin)
    <button type="submit" class="btn btn-danger final-submit-record">Approve</button>
    @if(count($teamLeaders) > 0)
    <button type="submit" class="btn btn-danger submit-record">Forword</button>
    @endif
    @else
    <button type="submit" class="btn btn-danger submit-record">Forword</button> 
    @endif
    
    </div>
</form>

<script type="text/javascript">
    $('#date_of_payment').datetimepicker({
      format: 'YYYY-MM-DD'
    });
</script>
