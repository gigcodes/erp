<!-- Modal -->
<div id="quick-user-event-notification-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quick User Event Notification</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="notification-submit-form" action="<?php echo route('calendar.event.create') ?>" method="post">
                    {{ csrf_field() }}    
                    <div class="form-group">
                        <label for="notification-date">Date</label>
                        <input id="notification-date" name="date" class="form-control" type="text">
                        <span id="date_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="notification-time">Time</label>
                        <input id="notification-time" name="time" class="form-control" type="text">
                        <span id="time_error" class="text-danger"></span>
                    </div>    
                    <div class="form-group">
                        <label for="notification-subject">Subject</label>
                        <input id="notification-subject" name="subject" class="form-control" type="text">
                        <span id="subject_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="notification-description">Description</label>
                        <input id="notification-description" name="description" class="form-control" type="text">
                        <span id="description_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="notification-participants">Participants(vendor)</label>
                        <?php echo Form::select("vendors[]",\App\Vendor::all()->pluck("name","id")->toArray(),null,[
                            "id" => "vendors" , "class" => "form-control selectx-vendor", "multiple" => true , "style" => "width:100%"
                        ]); ?>
                    </div>
                    <div class="form-group">
                        <input id="notification-submit" class="btn btn-secondary" type="submit">
                    </div>
               </form> 
           </div>
        </div>
    </div>
</div>