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
                    @csrf
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
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="notification-time">Repeat</label>
                            <select name="repeat" class="form-control">
                                <option value="">Select option</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="form-group col-6 hide" id="repeat_on">
                            <label for="notification-time">Repeat on</label>
                            <select name="repeat_on" class="form-control">
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-6 hide" id="ends_on">
                            <label for="notification-time">Ends</label>
                            <select name="ends_on" class="form-control">
                                <option value="">Select option</option>
                                <option value="never">Never</option>
                                <option value="on">On</option>
                            </select>
                        </div>
                        <div class="form-group col-6 hide" id="repeat_end_date">
                            <label for="repeat_end_date">Select date</label>
                            <input id="repeat_end" name="repeat_end_date" class="form-control" type="text">
                        </div>
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
                        <span id="vendor_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="notification-participants">Provider(user)</label>
                        <?php
                        $users = cache()->remember('User::all::pluck::name,id,toarray', 60 * 60 * 4, function (){
                            return \App\User::all()->pluck("name","id")->toArray();
                        });
                        echo Form::select("users[]",$users,null,[
                            "id" => "users" , "class" => "form-control selectx-users", "multiple" => true , "style" => "width:100%"
                        ]); ?>
                        <span id="user_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="timezone">Participants Time zone</label>
                        <select name="timezone" id="timezone" class="form-control">
                            <option value="">Select option</option>
                            @foreach (timezone_identifiers_list() as $zone)
                                <option value="{{$zone}}">{{$zone}}</option>
                            @endforeach
                        </select>
                        <span id="timezone_error" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="type">Select Type</label>
                        <select name="type" class="form-control">
                            <option value="event">For Event</option>
                            <option value="learning">For Learning</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type">Cost</label>
                        <input id="cost" name="cost" class="form-control" type="text">
                    </div>
                    <div class="form-group">
                        <label for="type">Currency</label>
                        @php
                              $currency= Illuminate\Support\Facades\Cache::remember('Currency::all', 60 * 60 * 24 * 7, function(){
                                return \App\Currency::all();
                                });
                        @endphp
                        <select name="currency" class="form-control">
                             @foreach($currency as $c)
                            <option value="{{$c->code}}">{{$c->name}}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group">
                        <input id="notification-submit" class="btn btn-secondary" type="submit">
                    </div>
               </form>
           </div>
        </div>
    </div>
</div>
