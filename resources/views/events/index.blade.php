@extends('layouts.app')

@section('title', 'Events')

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.css">  

    <style type="text/css">
        .duration .select2-container, .date-range-type .select2-container {
        display: block;
    }
    </style>
@endsection

@section('content')
    <br />
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="col-md-12 pl-3 pr-3">
        <div class="row m-0">
            <div class="col-lg-12 margin-tb p-0">
                <h2 class="page-heading">Public Events</h2>
                <div class="pull-right">
                    <a class="btn btn-secondary" href="{{route('event.index')}}">Back to calendar</a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="magento_list_tbl_895" class="table table-bordered table-hover">
                    <thead>
                        <th>Name</th>
                        <th>Public Link</th>
                        <th>Description</th>
                        <th>Duration (min)</th>
                        <th>Date Range Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </thead>
                    <tbody class="infinite-scroll-pending-inner">
                        @foreach ($events as $event)
                            <tr>
                                <td> {{ $event->name }} </td>
                                <td class="expand-row"> 
                                    <span class="td-mini-container">
                                        {{ strlen($event->link) > 10 ? substr($event->link, 0, 10).'...' : $event->link }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$event->link}}
                                    </span>
                                </td>
                                <td> {{ $event->description }} </td>
                                <td> {{ $event->duration_in_min }} </td>
                                <td> {{ $event->date_range_type_full_name }} </td>
                                <td> {{ $event->start_date }} </td>
                                <td> {{ $event->end_date }} </td>
                                <td> {{ $event->created_at }} </td>
                                <td>
                                    <i class="fa fa-calendar reschedule-event" data-id="{{ $event->id }}"></i>
                                    <i class="fa fa-trash fa-trash-bin-record" data-id="{{ $event->id }}"></i>
                                    @if ($event->date_range_type == "indefinitely")
                                    <i class="fa fa-stop-circle stop-recurring-event" data-id="{{ $event->id }}" title="Stop Recurring"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach()
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('partials.modals.reschedule-event')
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.js"></script> 
    <script>
        /** infinite loader **/
        var isLoading = false;
        var page = 1;
        $(document).ready(function() {
            $('.select2').select2();
            $('input.timepicker').timepicker({}); 
            $('.event-dates').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $(document).on('click', '.expand-row', function () {
                var selection = window.getSelection();
                if (selection.toString().length === 0) {
                    $(this).find('.td-mini-container').toggleClass('hidden');
                    $(this).find('.td-full-container').toggleClass('hidden');
                }
            });

            $(window).scroll(function() {
                if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                    loadMore();
                }
            });

            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                $.ajax({
                    url: "/event?page=" + page,
                    type: 'GET',
                    // data: $('.handle-search').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(data) {
                        //console.log(data);
                        $loader.hide();
                        $('.infinite-scroll-pending-inner').append(data.tbody);
                        isLoading = false;
                        if (data.tbody == "") {
                            isLoading = true;
                        }
                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }

            $('input[name="daterange"]').daterangepicker({
				opens: 'left'
			}, function(start, end, label) {
				console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
			});

            $('#reschedule-event-submit-form select[name="date_range_type"]').on('change', function() {
                if($(this).val() == 'within') {
                    $('#reschedule-event-submit-form #event-end-date').val("");
                    $('#reschedule-event-submit-form #end-date-div').removeClass('hide')
                } else {
                    $('#reschedule-event-submit-form #end-date-div').addClass('hide');
                } 
            });

            // Reschedule Event
            $('.reschedule-event').on('click', function() {
                $('#reschedule-event-submit-form #event-id').val($(this).data("id"));
                $("#reschedule-event-modal").modal("show");
            });

            // Stop Recurring Event
            $('.stop-recurring-event').on('click', function() {
                if (confirm('Are you sure you want to stop recurring for this event?')) {
                    var $this = $(this);

                    $.ajax({
                        url: '{{ route('event.stop-recurring', '') }}/' + $this.data("id"),
                        type: 'PUT',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: $this.data("id")
                        },
                        beforeSend: function() {
                            $("#loading-image-preview").show();
                        }
                    }).done( function(response) {
                        $("#loading-image-preview").hide();
                        if(response.code == 200) {
                            toastr["success"](response.message);
                            location.reload();
                        } else{
                            toastr["error"](response.message);
                        }
                    }).fail(function(errObj) {
                        $("#loading-image-preview").hide();
                    });
                }
            });

            $(document).on("submit", "#reschedule-event-submit-form", function(e) {
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
                            $("#reschedule-event-modal").modal("hide");
                            toastr['success'](data.message, 'Message');
                            location.reload();
                        } else {
                            toastr['error'](data.message, 'Message');
                        }
                    },
                    error: function(xhr, status, error) {
                        var errors = xhr.responseJSON;
                        $.each(errors, function(key, val) {
                            $("#reschedule-event-submit-form " + "#" + key + "_error").text(val[0]);
                        });
                    }
                });
            });

            $(document).on("click",".fa-trash-bin-record",function() {
                if (confirm('Are you sure you want to delete this?')) {
                    var $this = $(this);

                    $.ajax({
                        url: '{{ route('event.destroy', '') }}/' + $this.data("id"),
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: $this.data("id")
                        },
                        beforeSend: function() {
                            $("#loading-image-preview").show();
                        }
                    }).done( function(response) {
                        $("#loading-image-preview").hide();
                        if(response.code == 200) {
                            toastr["success"](response.message);
                            location.reload();
                        } else{
                            toastr["error"]('Record is unable to delete!');
                        }
                    }).fail(function(errObj) {
                        $("#loading-image-preview").hide();
                    });
                }
            });
        });
        //End load more functionality
    </script>
@endsection
