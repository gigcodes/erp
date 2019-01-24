@extends('layouts.app')

@section('content')
    @if(Session::has('message'))
        <div class="row mt-5">
            <div class="col-md-6 col-md-offset-3">
                <div class="alert alert-info">
                    {{ Session::get('message') }}
                </div>
            </div>
        </div>
    @endif
    <div class="mt-5">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Create A Schedule</a></li>
            <li><a data-toggle="tab" href="#menu1">Scheduled Posts</a></li>
            <li><a data-toggle="tab" href="#menu2">Calendar</a></li>
        </ul>

        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <form action="{{ action('InstagramController@postSchedules') }}" method="post">
                    @csrf
                    <div class="row mt-5">
                        <div class="col-md-12 text-center mb-4">
                            @if(count($imagesWithoutSchedules))
                                <span role="button" class="btn btn-lg btn-info start-schedule">Start Scheduling</span>
                                <button role="button" class="btn btn-lg btn-success schedule-to-be-shown" style="display: none"><i class="fa fa-send"></i> Save Schedule Details</button>
                            @else
                                <h2 class="text-center">No images to schedule at the moment!</h2>
                            @endif
                        </div>
                        <div class="col-md-6 col-md-offset-3 schedule-to-be-shown-slide" style="display: none">
                            <div class="mt-4 form-group">
                                <input type="checkbox" name="facebook" id="facebook">
                                <label for="facebook">Facebook</label>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="instagram" id="instagram" disabled>
                                <label class="text-muted" for="instagram">Instagram</label>
                            </div>
                            <div class="form-group">
                                <label for="date">Schedule Date & Time</label>
                                <input type="text" class="form-control datepicker" name="date" value="{{ date('Y-m-d') }}">
                                <div class="row">
                                    <div class="container">
                                        <input type="number" value="0" placeholder="Enter Hour" min="0" max="23" class="form-control mt-1 col-md-6" name="hour">
                                        <input type="number" value="0" min="0" max="59" class="form-control mt-1 col-md-6" name="minute">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="message">Caption</label>
                                <textarea name="caption" id="caption" cols="5" class="form-control"></textarea>
                            </div>
                        </div>
                        @foreach($imagesWithoutSchedules as $key => $item)
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-image">
                                        <img alt="Instagram Image" style="width: 100%;" src="{!! $item->filename ? asset('uploads/social-media') . '/' . $item->filename : 'http://lorempixel.com/555/300/black' !!}">
                                    </div><!-- card image -->
                                    <div class="card-action" style="display: none">
                                        <div class="checkbox">
                                            <label class="pl-0">
                                                <input type="checkbox" value="{{$item->id}}" name="images[]">
                                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                                Select This Image
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="description-{{$key}}">About this image</label>
                                            <textarea name="description[{{$item->id}}]" id="description-{{$key}}" class="form-control"></textarea>
                                        </div>
                                    </div><!-- card actions -->
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <div id="menu1" class="tab-pane fade">
                <div class="row mt-5">
                    <div class="col-md-12 text-center mb-4">
                        @if (count($imagesWithSchedules))
                            <div class="row">
                                @foreach($imagesWithSchedules as $key=>$schedule)
                                    <div class="col-md-12">
                                        <div class="jumbotron">
                                            <h2 class="mt-0">Schedule #{{$key+1}} ({{ $schedule->scheduled_for->diffForHumans() }})</h2>
                                            <p>{{ $schedule->description }}</p>
                                            <div class="row">
                                                @foreach($schedule->images->get() as $image)
                                                    <div class="col-md-4">
                                                        <div class="card">
                                                            <div class="card-image">
                                                                <img alt="Instagram Image" style="width: 100%;" src="{!! $image->filename ? asset('uploads/social-media') . '/' . $image->filename : 'http://lorempixel.com/555/300/black' !!}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-right">
                                                    <div class="text-center mt-5">
                                                        <button class="btn btn-success btn-lg post-now" data-pid="{{$schedule->id}}" data-schedule-id="{{$schedule->id}}">
                                                            Post Now <i class="fa fa-send"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-lg post-delete" data-pid="{{$schedule->id}}" data-schedule-id="{{$schedule->id}}">
                                                            <i class="fa fa-trash"></i> Delete Schedule
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <h2 class="text-center">There are no scheduled posts at the moment!</h2>
                        @endif
                    </div>
                </div>
            </div>
            <div id="menu2" class="tab-pane fade">
                <div class="row mt-5">
                    <div class="col-md-12">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="calendarModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="row" id="image_container"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>
        var scheduling = false;
        $(document).ready(function() {
            $('.start-schedule').click(function() {
                scheduling = !scheduling;
                if (scheduling) {
                    $('.card-action, .schedule-to-be-shown-slide').slideDown();
                    $('.schedule-to-be-shown').show('fast');
                    $(this).html('Stop Scheduling');
                } else {
                    $('.card-action, .schedule-to-be-shown-slide').slideUp();
                    $('.schedule-to-be-shown').hide('fast');
                    $(this).html('Start Scheduling');
                }
            });

            $('#calendar').fullCalendar({
                header: {
                    right: "month,agendaWeek,agendaDay, today prev,next",
                },
                events: '{{ action('InstagramController@getScheduledEvents') }}',
                eventClick: function(calEvent, jsEvent, view) {
                    $('#image_container').empty();

                    let download_images = [];
                    let image = '';
                    calEvent.image_names.forEach(function(img) {
                        image += '<div class="col-md-6"><div class="card"><div class="card-image"><a><img src="' + img.name + '" style="width:100%" /></a></div></div></div>';
                        download_images.push(img.id);
                    });

                    $('#image_container').append($(image));

                    jQuery.noConflict();
                    $('#calendarModal').modal('toggle');
                },
                eventRender: function(event, eventElement) {
                    if (event.image_names) {
                        let imgHTML = '<div>';
                        event.image_names.forEach(function(image) {
                            imgHTML += ("<img src='" + image.name +"' width='50' height='50''>");
                        });
                        eventElement.find("div.fc-content").append(imgHTML+'</div>');
                    }
                }
            });

            $('.post-now').click(function() {
                let scheduleId = $(this).attr('data-schedule-id');
                let self = this;
                $.ajax({
                    url: '{{ action('InstagramController@postMediaNow', '') }}'+'/'+scheduleId,
                    type: 'get',
                    success: function(response) {
                        if (response.status == 'success') {
                            alert("Successfully posted! We will reload the page for recent data.");
                            location.reload();
                        }
                    },
                    beforeSend: function() {
                        $(self).html('Posting... <i class="fa fa-spinner"></i>');
                    }
                });
            });

            $('.post-delete').click(function() {
                let scheduleId = $(this).attr('data-schedule-id');
                let self = this;
                $.ajax({
                    url: '{{ action('InstagramController@cancelSchedule', '') }}'+'/'+scheduleId,
                    type: 'get',
                    success: function(response) {
                        if (response.status == 'success') {
                            alert("Successfully deleted! We will reload the page for recent data.");
                            location.reload();
                        }
                    },
                    beforeSend: function() {
                        $(self).html('<i class="fa fa-spinner"></i> Deleting... ');
                    }
                });
            });
        });
    </script>
@endsection