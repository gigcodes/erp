<div id="timingEditModal" class="modal fade col-md-12 col-sm-12" role="dialog">
  <div class="modal-dialog modal-xl modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('twilio.create.timings') }}" method="POST">
        @csrf
        <input type="hidden" id="twilioSidVal" name="twilioSid">

        <div class="modal-header">
          <h4 class="modal-title">Add Timings</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <table class="table">
              <thead>
                <th>Day</th>
                <th>Morning Start</th>
                <th>Morning End</th>
                <th>Evening Start</th>
                <th>Evening End</th>
                <th>Enable</th>
              </thead>
              <tbody>
              <tr>
                <td>Monday</td>
                <td><input type="text" name="monday_morning_start" id="monday_morning_start" class="form-control" value="" ></td>
                <td><input type="text" name="monday_morning_end" id="monday_morning_end" class="form-control" value="" ></td>
                <td><input type="text" name="monday_evening_start" id="monday_evening_start" class="form-control" value="" ></td>
                <td><input type="text" name="monday_evening_end" id="monday_evening_end" class="form-control" value="" ></td>
                <td>
                  <input type="checkbox" class="form-check-input" name="monday_check" value="1">
                </td>
              </tr>
              <tr>
                <td>Tuesday</td>
                <td><input type="text" name="tuesday_morning_start" id="tuesday_morning_start" class="form-control" value="" ></td>
                <td><input type="text" name="tuesday_morning_end" id="tuesday_morning_end" class="form-control" value="" ></td>
                <td><input type="text" name="tuesday_evening_start" id="tuesday_evening_start" class="form-control" value="" ></td>
                <td><input type="text" name="tuesday_evening_end" id="tuesday_evening_end" class="form-control" value="" ></td>
                <td>
                  <input type="checkbox" class="form-check-input" name="tuesday_check" value="2">
                  
                </td>
              </tr>
              <tr>
                <td>Wednesday</td>
                <td><input type="text" name="wednesday_morning_start" id="wednesday_morning_start" class="form-control" value="" ></td>
                <td><input type="text" name="wednesday_morning_end" id="wednesday_morning_end" class="form-control" value="" ></td>
                <td><input type="text" name="wednesday_evening_start" id="wednesday_evening_start" class="form-control" value="" ></td>
                <td><input type="text" name="wednesday_evening_end" id="wednesday_evening_end" class="form-control" value="" ></td>
                <td> <input type="checkbox" class="form-check-input" name="wednesday_check" value="3">
                  </td>
              </tr>
              <tr>
                <td>Thursday</td>
                <td><input type="text" name="thursday_morning_start" id="thursday_morning_start" class="form-control" value="" ></td>
                <td><input type="text" name="thursday_morning_end" id="thursday_morning_end" class="form-control" value="" ></td>
                <td><input type="text" name="thursday_evening_start" id="thursday_evening_start" class="form-control" value="" ></td>
                <td><input type="text" name="thursday_evening_end" id="thursday_evening_end" class="form-control" value="" ></td>
                <td><input type="checkbox" class="form-check-input" name="thursday_check" value="4">
                  </td>
              </tr>
              <tr>
                <td>Friday</td>
                <td><input type="text" name="friday_morning_start" id="friday_morning_start" class="form-control" value="" ></td>
                <td><input type="text" name="friday_morning_end" id="friday_morning_end" class="form-control" value="" ></td>
                <td><input type="text" name="friday_evening_start" id="friday_evening_start" class="form-control" value="" ></td>
                <td><input type="text" name="friday_evening_end" id="friday_evening_end" class="form-control" value="" ></td>
                <td><input type="checkbox" class="form-check-input" name="friday_check" value="5">
                  </td>
              </tr>
              <tr>
                <td>Saturday</td>
                <td><input type="text" name="saturday_morning_start" id="saturday_morning_start" class="form-control" value="" ></td>
                <td><input type="text" name="saturday_morning_end" id="saturday_morning_end" class="form-control" value="" ></td>
                <td><input type="text" name="saturday_evening_start" id="saturday_evening_start" class="form-control" value="" ></td>
                <td><input type="text" name="saturday_evening_end" id="saturday_evening_end" class="form-control" value="" ></td>
                <td><input type="checkbox" class="form-check-input" name="saturday_check" value="6">
                  </td>
              </tr>
              <tr>
                <td>Sunday</td>
                <td><input type="text" name="sunday_morning_start" id="sunday_morning_start" class="form-control" value="" ></td>
                <td><input type="text" name="sunday_morning_end" id="sunday_morning_end" class="form-control" value="" ></td>
                <td><input type="text" name="sunday_evening_start" id="sunday_evening_start" class="form-control" value="" ></td>
                <td><input type="text" name="sunday_evening_end" id="sunday_evening_end" class="form-control" value="" ></td>
                <td><input type="checkbox" class="form-check-input" name="sunday_check" value="0">
                  </td>
              </tr>
              </tbody>
              
            </table>
          </div>         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Submit</button>
        </div>
      </form>
    </div>

  </div>
</div>
