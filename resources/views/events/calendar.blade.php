@extends('layouts.app')


@section('content')
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/core/main.css') }}">
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/daygrid/main.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/timegrid/main.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/list/main.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('css/user-calendar.css') }}" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.css"> 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/clockpicker@0.0.7/dist/bootstrap-clockpicker.min.css">

<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Events</h2>
    </div>
</div>

@include('partials.flash_messages')
{{-- <p class="text-secondary">Calendar link:</p>
<div class="border border-light p-2 my-3">
    {{ URL::to('calendar/public/'.$link) }}  
    <button class="btn btn-secondary"  data-toggle="modal" data-target="#calanderCommonEmailModal">
        Send Email
    </button>
</div> --}}

<div id="calendar"></div>
<div id="calanderCommonEmailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('common.send.clanaderLinkEmail') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id">
                {{-- <input type="hidden" name="object"> --}}
                <input type="hidden" name="datatype" value="multi_user">
                <input type="hidden" name="action" class="action" value="{{route('common.getmailtemplate')}}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <strong>User type</strong>
                        <select class="form-control" name="object" required id="calander_email_object">
                            <option selected disabled value="">Select</option>
                            <option value="vendor">vendor</option>
                            <option value="user">user</option>
                            <option value="supplier">supplier</option>
                            <option value="customer">customer</option>
                            {{-- <option value="order">order</option> --}}
                            <option value="charity">charity</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Send To</strong>
                        <select name="send_to[]" id="send_to" multiple>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>From Mail</strong>
                        <select class="form-control" name="from_mail">
                          <?php $emailAddressArr = \App\EmailAddress::all(); ?>
                          @foreach ($emailAddressArr as $emailAddress)
                            <option value="{{ $emailAddress->from_address }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                          @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Mail Templates</strong>
                        <select class="form-control getTemplateData" name="mail_template" required>
                          <?php $mail_templates = \App\MailinglistTemplate::whereNotNull('static_template')->get(); ?>
                           <option value="">Select a template</option>
                          @foreach ($mail_templates as $mail_template)
                            <option value="{{ $mail_template->id }}">{{ $mail_template->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Subject *</strong>
                        <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required>
                    </div>

                    <div class="form-group">
                        <strong>Message *</strong>
                        {{-- <textarea name="message" class="form-control" rows="8" cols="80" required>{{ old('message') ?? URL::to('calendar/public/'.$link) }}</textarea> --}}
                    </div>

                    <div class="form-group">
                        <strong>Files</strong>
                        <input type="file" name="file[]" value="" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>
<style>
    .fc-button{
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .fc-button-primary:not(:disabled).fc-button-active ,.fc-button:hover{
        background-color: #5a6268;
        border-color: #5a6268;
        color: #333;
    }
    .fc-event, .fc-event-dot {
        background-color: #6c757d;
        color: white!important;
        border: 1px solid #6c757d;
    }
    .duration .select2-container, .date-range-type .select2-container  {
        display: block;
    }
    .fc-unthemed td.fc-today {
        background: #f1f1f1;
    }

    .event-type-label {
        display: contents
    }
    .fc-disabled-day {
        visibility:hidden;
    }
    .day-row {
        display: none;
    }
</style>
@include('partials.modals.user-event-modal')
@include('events.event-category-create')

<script type="text/javascript" src="{{ URL::asset('libs/fullcalendar/core/main.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('libs/fullcalendar/daygrid/main.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('libs/fullcalendar/timegrid/main.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('libs/fullcalendar/list/main.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('libs/fullcalendar/interaction/main.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/clockpicker@0.0.7/dist/bootstrap-clockpicker.min.js"></script>

<script>
    let calendar;
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
            customButtons: {
                createEvent: {
                    text: 'Create Event',
                    click: function() {
                        $("#create-event-modal").modal("show");
                    }
                },
                publicEvent: {
                    text: 'Event Lists',
                    click: function() {
                        window.open('{{ route('event.public') }}', '_blank');
                    }
                },
                createCategory: {
                    text: 'Create Category',
                    click: function() {
                        $("#event-create-category-modal").modal("show");
                    }
                },
            },
            header: {
            left: 'prev,next today',
            center: 'title',
            right: 'createCategory createEvent publicEvent dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            defaultView: 'dayGridMonth',
            allDaySlot: false,
            editable: false,
            showNonCurrentDates: false,
            eventLimit: true, // allow "more" link when too many events
            eventLimitText: "More", //sets the text for more events
            eventSources: [{
                url: '/event/getSchedules',
                method: 'GET',
                failure: function() {
                    alert('there was an error while fetching events!');
                }
            }],
            eventRender: function(info) {
                if (info.event.extendedProps.event_type == 'PU' || info.event.extendedProps.event_type == 'PR'){
                    var i = document.createElement('i');
                    i.className = 'fa fa-remove delete-event';
                    i.id = 'event-id-'+info.event.extendedProps.event_id;
                    i.title = 'Delete Event'
                    i.onclick = function() {
                        removeEvent(info.event);
                    }
                    info.el.append(i);
                }

                if(info.event.extendedProps.event_type == 'PR') {
                    var recurringIcon = document.createElement('i');
                    recurringIcon.className = 'fa fa-stop-circle stop-recurring-event';
                    recurringIcon.id = 'event-id-'+info.event.extendedProps.event_id;
                    recurringIcon.title = 'Stop Recurring Event'
                    recurringIcon.onclick = function() {
                        stopRecurringEvent(info.event);
                    }
                    info.el.append(recurringIcon);
                }
            },
            eventClick: function(info) {
                console.log(info);
                console.log('jsEvent', info.jsEvent);
                console.log('el', info.el);
                addOverlay(info.el);
            },
        });
        calendar.render();
    });

    function closeCreateNewEventOverlay() {
        document.getElementById('create-overlay').style = "pointer-events: none";
        document.getElementById('new-event').style.visibility = 'hidden';
        document.getElementById('new-event-start-time').value = '';
        document.getElementById('new-event-title').value = '';
    }

    function formatDate(date) {
        let dateFormat = '';
        dateFormat += date.getFullYear() + '-';
        let month = date.getMonth() + 1;
        if (month < 10) {
            dateFormat += '0' + month + '-';
        } else {
            dateFormat += month + '-';
        }
        if (date.getDate() < 10) {
            dateFormat += '0' + date.getDate() + '-';
        } else {
            dateFormat += date.getDate() + ' ';
        }
        if (date.getHours() < 10) {
            dateFormat += '0' + date.getHours() + ':';
        } else {
            dateFormat += date.getHours() + ':';
        }
        if (date.getMinutes() < 10) {
            dateFormat += '0' + date.getMinutes() + ':';
        } else {
            dateFormat += date.getMinutes() + ':';
        }
        if (date.getSeconds() < 10) {
            dateFormat += '0' + date.getSeconds();
        } else {
            dateFormat += date.getSeconds();
        }
        return dateFormat;
    }

    function createNewEvent() {
        let start = new Date(document.getElementById('new-event-start-time').value);
        start = formatDate(start);
        const title = document.getElementById('new-event-title').value;
        console.log({
            title,
            start
        });
        const event = calendar.addEvent({
            title,
            start
        });
        console.log(event);
        closeCreateNewEventOverlay();
        const xhttp = new XMLHttpRequest();
        const formData = new FormData();
        const token = '{{ csrf_token() }}';
        const data = {
            title,
            start,
            _token: token
        };
        for (name in data) {
            formData.append(name, data[name]);
        }
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        xhttp.open("POST", "/calendar/events");
        xhttp.setRequestHeader('X-CSRF-TOKEN', token)
        xhttp.send(formData);
    }

    function removeEvent(event) {
        if (confirm('Are you sure you want to delete this event?')) {
            event.remove();
            const xhttp = new XMLHttpRequest();
            const token = '{{ csrf_token() }}';
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                }
            };
            if (event.extendedProps.event_type == 'PR') {
                xhttp.open("DELETE", "/event/" + event.extendedProps.event_id, true);
            }
            if (event.extendedProps.event_type == 'PU') {
                xhttp.open("DELETE", "/event/delete-schedule/" + event.extendedProps.event_schedule_id, true);
            }
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.setRequestHeader('X-CSRF-TOKEN', token)
            xhttp.send();
            location.reload();
        }
    }

    function stopRecurringEvent(event) {
        if (confirm('Are you sure you want to stop recurring this event?')) {
            const xhttp = new XMLHttpRequest();
            const token = '{{ csrf_token() }}';
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                }
            };
            if (event.extendedProps.event_type == 'PR') {
                xhttp.open("PUT", "/event/stop-recurring/" + event.extendedProps.event_id, true);
            }
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.setRequestHeader('X-CSRF-TOKEN', token)
            xhttp.send();
            location.reload();
        }
    }

    $("#send_to").select2({
        multiple: true,
        width: "100%"
    });
    $(document).ready(function () {
        $('.select2').select2();
        $('input.timepicker').timepicker({}); 
        $('.event-dates').datetimepicker({
            format: 'YYYY-MM-DD'
        });


        $(document).on("submit", "#create-event-submit-form", function(e) {
            e.preventDefault();
            var $form = $(this).closest("form");
            $.ajax({
                type: "POST",
                url: $form.attr("action"),
                data: $form.serialize(),
                dataType: "json",
                success: function(data) {
                    if (data.code == 200) {
                        $form[0].reset();
                        $("#create-event-modal").modal("hide");
                        toastr['success'](data.message, 'Message');
                        location.reload();
                    } else {
                        toastr['error'](data.message, 'Message');
                    }
                },
                error: function(xhr, status, error) {
                    var errors = xhr.responseJSON;
                    $.each(errors, function(key, val) {
                        $("#create-event-submit-form " + "#" + key + "_error").text(val[0]);
                    });
                }
            });
        });
    });
</script>
@endsection