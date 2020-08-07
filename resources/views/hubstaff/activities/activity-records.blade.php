
    <form>
    @csrf

    <input type="hidden" name="user_id" value="{{$user_id}}">
    <input type="hidden" name="date" value="{{$date}}">

    <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
          <th>Date & time</th>
          <th>Time tracked</th>
          <th colspan="2" class="text-center">Action &nbsp;<input type="checkbox" name="sample" class="selectall"/></th>
        </tr>
          @foreach ($activityrecords as $record)
            <tr>
            <td>{{ $record->starts_at }} </td>
              <td>{{ number_format($record->tracked / 60,2,".",",") }}</td>
              <td>
                <input type="checkbox" value="{{$record->id}}" name="activities[]" {{$record->status ? 'checked' : ''}}>
              </td>
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
        <textarea class="form-control" cols="30" rows="5" placeholder="Rejection note...">@if($hubActivitySummery){{$hubActivitySummery->rejection_note}}@endif</textarea>
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
