@extends('layouts.app')
@section('favicon' , '')

@section('title', 'Schedule event')

@section('styles')

@endsection

@section('content')
    <div class="col-md-12">
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Schedule event</h2>
        </div>
        <div class="col-md-4 col-6">
            <div class="from-group">
                <label for="">Select Event date:</label>
                <input type="text" name="" class="form-control" id="event-date" autocomplete="off">
            </div>
        </div>
    </div>

    @include('partials.flash_messages')


    <div class="modal fade" id="guest-schedule-event" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {{-- <div class="page-header" style="width: 69%">
                    <h4>Schedule Event</h4>
                </div> --}}
                <div class="modal-header">
                    <h4 class="modal-title">Schedule Event</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="" method="POST" action="{{route('guest.create-schedule')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="schedule-date" class="schedule-date">
                        <div class="guest-schedule-event-body">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                                    aria-label="Close">Close
                            </button>
                            <button type="submit" class="float-right custom-button btn">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let availableDays = {{json_encode($availableDays ?? [])}};
        $(document).ready(function () {
            $("#event-date").datepicker({
                minDate: new Date("{{$event->start_date}}"),
                maxDate: new Date("{{$event->end_date}}"),
                dateFormat: 'yy-mm-dd',
                onSelect: function(date) {
                    let scheduleDate = date;
                    $('#guest-schedule-event').modal('hide');
                    $("#guest-schedule-event .schedule-date").val(date);
                    date = new Date(date);
                    $.ajax({
                        type: "get",
                        url: "{{route('guest.schedule-event-slot')}}",
                        data: {
                            event_id: "{{$event->id}}",
                            day: date.getDay(),
                            scheduleDate
                        },
                        success: function (response) {
                            $('.guest-schedule-event-body').html(response);
                            $('#guest-schedule-event').modal('show');
                        },
                        error: function(error) {
                            alert("Something went wrong.");
                        }
                    });
                },
                beforeShowDay: function (date) {
                    if (availableDays.includes(date.getDay())) {
                        return [true, ''];
                    } else {
                        return [false, ''];
                    }
                },
            });
        });
    </script>
@endsection
