
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
    <div class="form-group">
        <textarea class="form-control" name="rejection_note" id="rejection_note" cols="30" rows="5" placeholder="Rejection note...">@if($hubActivitySummery){{$hubActivitySummery->rejection_note}}@endif</textarea>
    </div>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-danger submit-record">Submit</button>
    </div>
</form>

<script type="text/javascript">
    $('#date_of_payment').datetimepicker({
      format: 'YYYY-MM-DD'
    });
</script>
