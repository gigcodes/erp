@extends('layouts.app')
@section('favicon', 'task.png')

@section('title', $title)

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }

        td {
            word-wrap: break-word;
        }

    </style>

    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ $title }} <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col col-md-6">
                    <div class="row ml-3">
                        <!-- Purpose : Rename from Today Common Errors Report to Export Today Common Errors Report - DEVTASK-20123  -->
                        <a href="{{ route('magento_product_today_common_err') }}" class="btn btn-sm btn-warning mr-2">
                            Export Today Common Errors Report
                        </a>

                        <!-- START - Purpose : Add button - DEVTASK-20123  -->
                        <a href="#" class="btn btn-sm btn-warning view_today_common_errors_report">
                            Today Common Errors Report
                        </a>
                        <!-- END - DEVTASK-20123  -->
                    </div>
                </div>
                <div class="col">
                    <div class="h" style="margin-bottom:10px;">
                        <form class="form-inline message-search-handler" method="post">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        {{-- <label for="keyword">Keyword:</label> --}}
                                        <?php echo Form::text('keyword', request('keyword'), ['class' => 'form-control', 'placeholder' => 'Enter keyword']); ?>
                                    </div>
                                    <div class="form-group">
                                        {{-- <label for="keyword">Date:</label> --}}
                                        <div class="col-md-2 pd-sm">
                                            <input placeholder="Date" type="text" class="form-control estimate-date_picker"
                                                id="estimate_date_picker" name="log_date">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{-- <label for="keyword">Website:</label> --}}

                                        <select class="form-control" name="website" id="website">
                                            <option value="all" selected>All</option>
                                            @foreach ($websites as $website)
                                                <option value="{{ $website->id }}">{{ $website->title }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        {{-- <label for="button">&nbsp;</label> --}}
                                        <button style="display: inline-block;width: 10%"
                                            class="btn btn-sm btn-image btn-secondary btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-content ">
                <!-- Pending task div start -->
                <div class="tab-pane active" id="1">
                    <div class="row" style="margin:10px;">
                        <div class="col-12">
                            <div class="margin-tb" id="page-view-result">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
              50% 50% no-repeat;display:none;">
        </div>

        <div class="common-modal modal" role="dialog">
            <div class="modal-dialog" role="document" style="width: 1000px; max-width: 1000px;">
            </div>
        </div>

        <!-- START - Purpose : modal for liting error - DEVTASK-20123  -->
        <!-- Modal -->
        <div class="modal fade" id="today_common_error_report_modal" tabindex="-1" role="dialog" aria-labelledby=""
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-lg">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Today Common Errors Report</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%"
                            style="table-layout:fixed;">
                            <thead>
                                <tr>
                                    <th class="th-sm" style="width:20%">Count</th>
                                    <th class="th-sm" style="width:80%">Message</th>
                                </tr>
                            </thead>
                            <tbody class="table_data">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="log_history_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Status History</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="row">
                        <div class="col-md-12" id="log_history_div">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Old Status</th>
                                        <th>New Status</th>
                                        <th>Updated by</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        @include("magento-product-error.templates.list-template")

        <script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('/js/jquery-ui.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/magento-product-error.js') }}"></script>

        <script type="text/javascript">
            page.init({
                bodyView: $("#common-page-layout"),
                baseUrl: "<?php echo url('/'); ?>"
            });

            //START - Purpose : Get data - DEVTASK-20123
            $(document).on('click', '.view_today_common_errors_report', function(e) {

                $.ajax({
                    type: 'GET',
                    url: "{{ route('magento_product_today_common_err_report') }}",
                    dataType: "json",
                    success: function(response) {

                        if (response.code == 200) {
                            var html_content = '';
                            $.each(response.data, function(key, value) {

                                html_content += '<tr>';
                                html_content += '<td>' + value.count + '</td>';
                                html_content += '<td>' + value.message + '</td>';
                                html_content += '</tr>';
                            });

                            $('.table_data').html(html_content);

                            $('#today_common_error_report_modal').modal('show');
                        }

                    },
                    error: function() {
                        toastr['error']('Could not change module!');
                    }
                });
            });
            //END - DEVTASK-20123

            $('#estimate_date_picker').datepicker({
                dateformat: 'yyyy-mm-dd'
            });

            $(document).on('click', '.show-logs-history', function() {
                $('#log_history_modal table tbody').html('');
                $.ajax({
                    url: '/log_history/list/' + $(this).data('id'),
                    method: 'post',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data != 'error') {
                            $.each(data, function(i, item) {

                                $('#log_history_modal table tbody').append(
                                    '<tr>\
                                            <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                                <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                                                <td>' + item['new_value'] + '</td>\
                                                <td>' + item['name'] + '</td>\
                                            </tr>'
                                );
                            });
                        }
                    }
                })
                $('#log_history_modal').modal('show');
            })
            $(document).on('change', '#error_status', function() {

                $.ajax({
                    url: '/log_status/change/' + $(this).data('log_id'),
                    method: 'post',
                    data: {
                        type: $(this).val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {

                    }
                })


            })
        </script>

    @endsection
