@extends('layouts.app')
@section('title', 'Meetings')
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">

    {{-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <style>
        #message-wrapper {
            height: 450px;
            overflow-y: scroll;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="page-heading"> Meetings</h2>
        </div>
        <div class="container">
        </div>
    </div>
    <div class="clearboth"></div>
    <div class="row" style="margin:10px;">
        <!-- <h4>List Of Upcoming Meetings</h4> -->
        <div class="col-lg-12 margin-tb">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="3%">ID</th>
                        <th width="10%">Meeting Id</th>
                        <th width="10%" class="category">Meeting Topic</th>
                        <th width="15%">Meeting Agenda</th>
                        <th width="5%">Join Meeting URL</th>
                        <th width="10%">Start Date Time</th>
                        <th width="5%">Meeting Duration</th>
                        <th width="5%">Timezone</th>
                        <th width="5%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($meetingData)
                        @foreach($meetingData as $meetings)
                            <tr>
                                <td class="p-2">{{ $meetings->id }}</td>
                                <td class="p-2">{{ $meetings->meeting_id }}</td>
                                <td class="p-2">{{ $meetings->meeting_topic }}</td>
                                <td class="p-2">{{ $meetings->meeting_agenda }}</td>
                                <td class="p-2"><a href="{{ $meetings->join_meeting_url }}" target="_blank">Link</a></td>
                                <td class="p-2">{{ Carbon\Carbon::parse($meetings->start_date_time)->format('M, d-Y H:i') }}</td>
                                <td class="p-2">{{ $meetings->meeting_duration }} mins</td>
                                <td class="p-2" width="20%">{{ $meetings->timezone }}</td>
                                <td>
                                    <button type="button" title="Fetch Recordings" class="btn" style="padding: 0px 1px;">
										<i class="fa fas fa-refresh fetch-zoom-meeting-recordings" data-meeting_id="{{ $meetings->meeting_id }}"></i>
									</button>
                                    <button type="button" title="Fetch Participants" class="btn" style="padding: 0px 1px;">
										<i class="fa fas fa-refresh fetch-zoom-meeting-participants" data-meeting_id="{{ $meetings->meeting_id }}"></i>
									</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            {!! $meetingData->appends(request()->except('page'))->links() !!}
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script> --}}

    <script type="text/javascript">
        $('.fetch-zoom-meeting-recordings').click(function() {
            var $this = $(this);
            var meetingId = $this.data('meeting_id');

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('meeting.fetch.recordings')}}",
                type: 'POST',
                data: {
                    meetingId : meetingId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                $("#loading-image-preview").hide();
                toastr["success"](response.message);
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
                toastr["error"](errObj.responseJSON.message);
            });
        });

        $('.fetch-zoom-meeting-participants').click(function() {
            var $this = $(this);
            var meetingId = $this.data('meeting_id');

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('meeting.fetch.participants')}}",
                type: 'POST',
                data: {
                    meetingId : meetingId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                $("#loading-image-preview").hide();
                toastr["success"](response.message);
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
                toastr["error"](errObj.responseJSON.message);
            });
        });
    </script>
@endsection