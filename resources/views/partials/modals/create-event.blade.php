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
                        <div class="form-check-inline">
                            <label class="form-check-label event-type-label" for="public">
                              <input type="radio" class="form-check-input" id="public" name="event_type" value="PU" checked>Public
                            </label>
                          </div>
                          <div class="form-check-inline">
                            <label class="form-check-label event-type-label" for="private">
                              <input type="radio" class="form-check-input" id="private" name="event_type" value="PR">Private
                            </label>
                          </div>
                    </div>
                    <div class="form-group duration">
                        <label for="event-name">Event Category</label>
                        <select name="event_category_id" id="event_category_id" class="form-control select2">
                            <option value="">-- Select category --</option>
                            @php  $eventcatgories =  \App\Models\EventCategory::get(); @endphp
                            @foreach ($eventcatgories as $cat)
                                <option value="{{$cat->id}}">{{$cat->category}}</option>
                            @endforeach
                        </select>
                        <span id="event_category_id_error" class="text-danger"></span>
                    </div>
                    <div class="form-group duration">
                        <label for="event-name">Event User</label>
                        <select name="user_id" id="user_id" class="form-control select2">
                            <option value="">-- Select User --</option>
                            @php  $users =  \App\User::get(); @endphp
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                        <span id="user_id_error" class="text-danger"></span>
                    </div>
                    <div class="form-group duration">
                        <label for="event-name">Event Vendor
                            <button type="button" class="btn custom-button float-right mr-3 add-vendor">Add Vendor</button>
                        </label>
                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                            <option value="">-- Select Vendor --</option>
                            @php  $vendors =  \App\Vendor::get(); @endphp
                            @foreach ($vendors as $vendor)
                                <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                            @endforeach
                        </select>
                    </div>
                      <div class="form-group duration" id="vendor-inputs" style="display: none;">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor-name">Vendor category</label>
                                    <select class="form-control" name="vendor_category_id"  placeholder="Category:">
                                        @php  $categories =  \App\VendorCategory::get(); @endphp
                                        <option value="">Select a Category</option>
                                        @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                                        @endforeach
                                      </select>
                                      <span id="vendor_category_id_error" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor-name">Vendor Name</label>
                                    <input type="text" name="vendor_name" id="vendor_name" class="form-control" placeholder="Enter Vendor Name">
                                    <span id="vendor_name_error" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor-email">Email</label>
                                    <input type="email" name="vendor_email" id="vendor_email" class="form-control" placeholder="Enter Email Name">
                                    <span id="vendor_email_error" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor-name">Phone Number</label>
                                    <input type="number" name="vendor_phone" id="vendor_phone" class="form-control" placeholder="Enter Phone Number">
                                    <span id="vendor_phone_error" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email-from-address">Email From Address</label>
                        <input type="email" id="email_from_address" name="from_address" class="form-control" value="info@mio-moda.com">
                        <span id="email_from_address_error" class="text-danger"></span>

                    </div>
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
                    <div class="form-group date-range-type">
                        <label for="date-range-type">Date Range Type</label>
                        <select name="date_range_type" id="date-range-type" class="form-control select2">
                            <option value="within">Within a date range</option>
                            <option value="indefinitely">Indefinitely into the future</option>
                        </select>
                        <span id="date_range_type_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="event-start-date">Start Date</label>
                        <input type="date" name="start_date" id="event-start-date" value=""  class="form-control input-sm" placeholder="Select Start Date">
                        <span id="start_date_error" class="text-danger"></span>
                    </div>
                    <div class="form-group" id="end-date-div">
                        <label for="event-end-date">End Date</label>
                        <input type="date" name="end_date" id="event-end-date" value=""  class="form-control input-sm" placeholder="Select End Date">
                        <span id="end_date_error" class="text-danger"></span>
                    </div>
                    
                    <div class="form-group">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[1][day]"></td>
                                    <td>MON</td>
                                    <td><input name="event_availability[1][start_at]" class="form-control timepicker" placeholder="Start Time" type="text"></td>
                                    <td><input name="event_availability[1][end_at]" class="form-control timepicker" placeholder="End Time" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[2][day]"></td>
                                    <td>TUE</td>
                                    <td><input name="event_availability[2][start_at]" class="form-control timepicker" placeholder="Start Time" type="text"></td>
                                    <td><input name="event_availability[2][end_at]" class="form-control timepicker" placeholder="End Time" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[3][day]"></td>
                                    <td>WED</td>
                                    <td><input name="event_availability[3][start_at]" class="form-control timepicker" placeholder="Start Time" type="text"></td>
                                    <td><input name="event_availability[3][end_at]" class="form-control timepicker" placeholder="End Time" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[4][day]"></td>
                                    <td>THU</td>
                                    <td><input name="event_availability[4][start_at]" class="form-control timepicker" placeholder="Start Time" type="text"></td>
                                    <td><input name="event_availability[4][end_at]" class="form-control timepicker" placeholder="End Time" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[5][day]"></td>
                                    <td>FRI</td>
                                    <td><input name="event_availability[5][start_at]" class="form-control timepicker" placeholder="Start Time" type="text"></td>
                                    <td><input name="event_availability[5][end_at]" class="form-control timepicker" placeholder="End Time" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[6][day]"></td>
                                    <td>SAT</td>
                                    <td><input name="event_availability[6][start_at]" class="form-control timepicker" placeholder="Start Time" type="text"></td>
                                    <td><input name="event_availability[6][end_at]" class="form-control timepicker" placeholder="End Time" type="text"></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="event_availability[7][day]"></td>
                                    <td>SUN</td>
                                    <td><input name="event_availability[7][start_at]" class="form-control timepicker" placeholder="Start Time" type="text"></td>
                                    <td><input name="event_availability[7][end_at]" class="form-control timepicker" placeholder="End Time" type="text"></td>
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

<script>
    $(document).ready(function() {
        $('.select2').select2();

        // Add Vendor button click event
        $('.add-vendor').click(function() {
            $('#vendor-inputs').toggle();
            $("#vendor_id").css({
                style: 'display: none;',
            });
        });
    });
</script>