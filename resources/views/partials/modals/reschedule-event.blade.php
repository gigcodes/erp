<!-- Modal -->
<div id="reschedule-event-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reschedule Event</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="reschedule-event-submit-form" action="<?php echo route('event.reschedule') ?>" method="post">
                    {{ csrf_field() }}    
                    
                    <div class="form-group duration">
                        <input name="id" type="hidden" class="form-control event-id" id="event-id" value="">

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
                        <label for="event-start-date">Start Date</label>
                        <input id="event-start-date" name="start_date" type="text" class="form-control event-dates" value="" placeholder="Select Start Date">
                        <span id="start_date_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="event-start-date">Is Recurring ?</label>
                        <input type="checkbox" name="is_recurring" class="form-control" id="is_recurring">
                    </div>
                    <div class="form-group hide" id="recurring-end-div">
                        <label for="recurring-end">Recurring Ends</label>
                        <select name="recurring_end" class="form-control">
                            <option value="">Select option</option>
                            <option value="never">Never</option>
                            <option value="on">On</option>
                        </select>
                        <span id="recurring_end_error" class="text-danger"></span>
                    </div> 
                    <div class="form-group" id="end-date-div">
                        <label for="event-end-date">End Date</label>
                        <input id="event-end-date" name="end_date" type="text" class="form-control event-dates" value="" placeholder="Select End Date">
                        <span id="end_date_error" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="event-mail-body">Mail Body</label>
                        <input id="event-mail-body" name="mail_body" type="text" class="form-control" value="" placeholder="Enter reschedule mail body content">
                        <span id="mail_body_error" class="text-danger"></span>
                    </div>
                    
                    <div class="form-group pull-right">
                        <input id="event-submit" class="btn btn-secondary" type="submit">
                    </div>
               </form> 
           </div>
        </div>
    </div>
</div>