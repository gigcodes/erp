<!-- Modal -->
<div id="create-event-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Event</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="create-event-submit-form" action="<?php echo route('event.store') ?>" method="post">
                    {{ csrf_field() }}    
                    <div class="form-group">
                        <label for="event-name">Name</label>
                        <input id="event-name" name="name" class="form-control" type="text">
                        <span id="name_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="event-description">Description</label>
                        <input id="event-description" name="description" class="form-control" type="text">
                        <span id="description_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="event-date">Date Range</label>
                        <input id="event-date" name="daterange" type="text" class="form-control" value="" placeholder="Select Date Range">
                        <span id="daterange_error" class="text-danger"></span>
                    </div>
                    <div class="form-group duration">
                        <label for="event-duration">Duration</label>
                        <select name="duration_in_min" id="event-duration" class="form-control select2">
                            <option value="">-- Select Duration --</option>
                            <option value="15">15min</option>
                            <option value="30">30min</option>
                            <option value="45">45min</option>
                            <option value="60">60min</option>
                        </select>
                        <span id="duration_in_min_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[1][day]"></td>
                                    <td>MON</td>
                                    <td><input name="event_availability[1][start_at]" class="form-control timepicker" type="text"></td>
                                    <td><input name="event_availability[1][end_at]" class="form-control timepicker" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[2][day]"></td>
                                    <td>TUE</td>
                                    <td><input name="event_availability[2][start_at]" class="form-control timepicker" type="text"></td>
                                    <td><input name="event_availability[2][end_at]" class="form-control timepicker" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[3][day]"></td>
                                    <td>WED</td>
                                    <td><input name="event_availability[3][start_at]" class="form-control timepicker" type="text"></td>
                                    <td><input name="event_availability[3][end_at]" class="form-control timepicker" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[4][day]"></td>
                                    <td>THU</td>
                                    <td><input name="event_availability[4][start_at]" class="form-control timepicker" type="text"></td>
                                    <td><input name="event_availability[4][end_at]" class="form-control timepicker" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[5][day]"></td>
                                    <td>FRI</td>
                                    <td><input name="event_availability[5][start_at]" class="form-control timepicker" type="text"></td>
                                    <td><input name="event_availability[5][end_at]" class="form-control timepicker" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[6][day]"></td>
                                    <td>SAT</td>
                                    <td><input name="event_availability[6][start_at]" class="form-control timepicker" type="text"></td>
                                    <td><input name="event_availability[6][end_at]" class="form-control timepicker" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[7][day]"></td>
                                    <td>SUN</td>
                                    <td><input name="event_availability[7][start_at]" class="form-control timepicker" type="text"></td>
                                    <td><input name="event_availability[7][end_at]" class="form-control timepicker" type="text"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group pull-right">
                        <input id="event-submit" class="btn btn-secondary" type="submit">
                    </div>
               </form> 
           </div>
        </div>
    </div>
</div>