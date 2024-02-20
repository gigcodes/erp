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
                <form id="create-event-submit-form" action="{{route('event.store')}}" method="post">
                    @csrf
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
                        <select name="event_user_id" id="event_user_id" class="form-control select2">
                            <option value="">-- Select User --</option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                        <span id="event_user_id_error" class="text-danger"></span>
                    </div>
                    <div class="form-group duration">
                        <strong>Recurring type</strong>
                        <select class="form-control select2" name="recurring_type" id="recurring_type">
                            <option value="">-- Select Recurring Type  --</option>
                            <option value="Daily" {{ old('recurring_type') == 'Daily'? 'selected' : '' }}>Daily</option>
                            <option value="Weekly" {{ old('recurring_type') == 'Weekly'? 'selected' : '' }}>Weekly</option>
                            <option value="Bi-Weekly" {{ old('recurring_type') == 'Bi-Weekly'? 'selected' : '' }}>Bi-Weekly</option>
                            <option value="Monthly" {{ old('recurring_type') == 'Monthly'? 'selected' : '' }}>Monthly</option>
                            <option value="Yearly" {{ old('recurring_type') == 'Yearly'? 'selected' : '' }}>Yearly</option>
                            <option value="One time" {{ old('recurring_type') == 'One time'? 'selected' : '' }}>One time</option>
                        </select>
                      </div>
                    <div class="form-group duration">
                        <label for="event-name">Event Vendor
                            <button type="button" class="btn custom-button float-right mr-3 add-vendor">Add Vendor</button>
                        </label>
                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                            <option value="">-- Select Vendor --</option>
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
                        <input type="date" name="start_date" id="event-start-date" value="{{ date('Y-m-d') }}" class="form-control input-sm" placeholder="Select Start Date">
                        <span id="start_date_error" class="text-danger"></span>
                    </div>
                    <div class="form-group" id="end-date-div">
                        <label for="event-end-date">End Date</label>
                        <input type="date" name="end_date" id="event-end-date" value="{{ date('Y-m-d') }}" class="form-control input-sm" placeholder="Select End Date">
                        <span id="end_date_error" class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr class="day-row monday">
                                    <td><input type="checkbox" name="event_availability[1][day]"></td>
                                    <td>MON</td>
                                    <td>
                                        <div class="input-group clockpicker">
                                        <input name="event_availability[1][start_at]" class="form-control clockpicker" placeholder="Start Time" type="text">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                 </div>
                                    </td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[1][end_at]" class="form-control clockpicker" placeholder="End Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="day-row tuesday">
                                    <td><input type="checkbox" name="event_availability[2][day]"></td>
                                    <td>TUE</td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[2][start_at]" class="form-control clockpicker" placeholder="Start Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                         </div>
                                        </td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[2][end_at]" class="form-control clockpicker" placeholder="End Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                     </div>
                                    </td>
                                </tr>
                                <tr class="day-row wednesday">
                                    <td><input type="checkbox" name="event_availability[3][day]"></td>
                                    <td>WED</td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[3][start_at]" class="form-control clockpicker" placeholder="Start Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[3][end_at]" class="form-control clockpicker" placeholder="End Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                         </div>
                                    </td>
                                </tr>
                                <tr class="day-row thursday">
                                    <td><input type="checkbox" name="event_availability[4][day]"></td>
                                    <td>THU</td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[4][start_at]" class="form-control clockpicker" placeholder="Start Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                     </td>
                                    <td>
                                        <div class="input-group clockpicker">
                                        <input name="event_availability[4][end_at]" class="form-control clockpicker" placeholder="End Time" type="text">
                                        <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                    </td>
                                </tr>
                                <tr class="day-row friday">
                                    <td><input type="checkbox" name="event_availability[5][day]"></td>
                                    <td>FRI</td>
                                    <td>
                                        <div class="input-group clockpicker">
                                         <input name="event_availability[5][start_at]" class="form-control clockpicker" placeholder="Start Time" type="text">
                                         <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                    </td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[5][end_at]" class="form-control clockpicker" placeholder="End Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="day-row saturday">
                                    <td><input type="checkbox" name="event_availability[6][day]"></td>
                                    <td>SAT</td>
                                    <td>
                                        <div class="input-group clockpicker">
                                         <input name="event_availability[6][start_at]" class="form-control clockpicker" placeholder="Start Time" type="text">
                                         <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                        </td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[6][end_at]" class="form-control clockpicker" placeholder="End Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="day-row sunday">
                                    <td><input type="checkbox" name="event_availability[7][day]"></td>
                                    <td>SUN</td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input type="text" class="form-control clockpicker"  placeholder="Start Time" name="event_availability[7][start_at]">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                </td>
                                    <td>
                                        <div class="input-group clockpicker">
                                            <input name="event_availability[7][end_at]" class="form-control clockpicker" placeholder="End Time" type="text">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>

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

<script type="text/javascript">
$(document).ready(function() {
    var days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
    function updateDayRows(startDate, endDate) {
        $('.day-row').hide();
    var currentDate = new Date(startDate);
        while (currentDate <= endDate) {
            var currentDay = currentDate.getDay();
            $('.' + days[currentDay]).show();
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }

    function updateDayRowsWithEndDate(startDate, endDate) {
        $('.day-row').hide();
        var currentDay = startDate.getDay();
        var lastDay = endDate.getDay();

        for (var i = currentDay; i <= lastDay; i++) {
            $('.' + days[i]).show();
        }
    }

    function showAdditionalElements() {
        $('.day-row, .clockpicker').show();
    }

    $('#event-start-date, #event-end-date').on('change', function() {
        var startDate = new Date($('#event-start-date').val());
        var endDate = new Date($('#event-end-date').val());
        updateDayRows(startDate, endDate);
    });

    $('select[name="date_range_type"]').on('change', function() {
        if ($(this).val() == 'within') {
            $('#end-date-div').removeClass('hide');
            var startDate = new Date($('#event-start-date').val());
            var endDate = new Date($('#event-end-date').val());
            updateDayRowsWithEndDate(startDate, endDate);
        } else {
            $('#end-date-div').addClass('hide');
            var startDate = new Date($('#event-start-date').val());
            updateDayRows(startDate, startDate);
            showAdditionalElements(); // Hide additional elements
        }
    });

    // Initialize day rows based on current day
    var currentDay = new Date().getDay();
    $('.day-row').hide();
    $('.' + days[currentDay]).show();

    $('.clockpicker[name$="[start_at]"], #event-duration').on('change', function() {
        var startInput = $(this).closest('tr').find('.clockpicker[name$="[start_at]"]');
        var endInput = $(this).closest('tr').find('.clockpicker[name$="[end_at]"]');
        var selectedDuration = parseInt($('#event-duration').val()); // Get selected duration in minutes

        if (startInput.val() && !isNaN(selectedDuration)) {
            var start = moment(startInput.val(), 'HH:mm');
            var duration = moment.duration(selectedDuration, 'minutes');

            var end = start.clone().add(duration);

            endInput.val(end.format('HH:mm'));
        }
    });

    $('.clockpicker').clockpicker({
        autoclose: true,
        doneText: 'Done',
        placement: 'top' // Set the placement to 'top'

    });

});

    $(document).ready(function() {
        // Add Vendor button click event
        $('.add-vendor').click(function() {
            $('#vendor-inputs').toggle();
            $("#vendor_id").css({
                style: 'display: none;',
            });
        });
    });
</script>
