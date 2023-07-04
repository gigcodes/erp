@extends('layouts.app')

@section('styles')
<style type="text/css">
	table#config-refactor-data-list {
		width: 100% !important;
		display: inline-block !important;
		overflow-x: scroll !important;
	}
</style>
@endsection
@section('content')
@php $types = \App\ConfigRefactorSection::$types; @endphp
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Config Refactor ({{ $configRefactors->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    {{-- <form action="{{ route('zabbix-webhook-data.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-lg-4">
                                <input class="form-control" type="date" name="event_start" value="{{ request()->get('event_start') }}">
                            </div>
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('zabbix-webhook-data.index') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form> --}}
                </div>
                <div class="col-4">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#configRefactorStatusCreate"> Create Status </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="config-refactor-data-list">
                        <tr>
                            <th>ID</th>
                            <th>Section Name</th>
                            <th>Section Type</th>
                            <th>User</th>
                            <th>Step1 Status</th>
                            <th>Step1 Remark</th>
                            <th>Step2 Status</th>
                            <th>Step2 Remark</th>
                            <th>Step3 Status</th>
                            <th>Step3 Remark</th>
                            <th>Step3.1 Status</th>
                            <th>Step3.1 Remark</th>
                            <th>Step3.2 Status</th>
                            <th>Step3.2 Remark</th>
                        </tr>
                        @foreach ($configRefactors as $key => $configRefactor)
                            <tr data-id="{{ $configRefactor->id }}">
                                <td>{{ $configRefactor->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($configRefactor->configRefactorSection->name) > 12 ? substr($configRefactor->configRefactorSection->name, 0, 12).'...' :  $configRefactor->configRefactorSection->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $configRefactor->configRefactorSection->name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($types[$configRefactor->configRefactorSection->type]) > 30 ? substr($types[$configRefactor->configRefactorSection->type], 0, 30).'...' :  $types[$configRefactor->configRefactorSection->type] }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $types[$configRefactor->configRefactorSection->type] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-user select2" data-id="{{$configRefactor->id}}" data-column="user_id" name="user_id">
                                        <option value="">Select...</option>
                                        @foreach($users as $id => $user)
                                            @if( $configRefactor->user_id == $id )
                                                <option value="{{$id}}" selected>{{ $user }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $user }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-users ml-2" data-id="{{$configRefactor->id}}" title="Load user histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_1_status" name="step_1_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_1_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_1_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_1_remark_{{$configRefactor->id}}" name="step_1_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_1_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-id="{{$configRefactor->id}}" data-column="step_1_remark" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_2_status" name="step_2_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_2_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_2_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_2_remark_{{$configRefactor->id}}" name="step_2_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_2_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-id="{{$configRefactor->id}}" data-column="step_2_remark" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_3_status" name="step_3_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_3_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_3_remark_{{$configRefactor->id}}" name="step_3_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_3_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_remark" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_3_1_status" name="step_3_1_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_3_1_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_1_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_3_1_remark_{{$configRefactor->id}}" name="step_3_1_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_3_1_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_1_remark" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_3_2_status" name="step_3_2_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_3_2_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_2_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_3_2_remark_{{$configRefactor->id}}" name="step_3_2_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_3_2_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-column="step_3_2_remark" data-id="{{$configRefactor->id}}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $configRefactors->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

{{-- configRefactorStatusCreate --}}
@include('config-refactor.partials.config-refactor-status-create-modal')
{{-- #remark-area-list --}}
@include('config-refactor.partials.remark_list')
{{-- #status-area-list --}}
@include('config-refactor.partials.status_list')
{{-- #user-area-list --}}
@include('config-refactor.partials.users_list')

<script type="text/javascript">
    $(document).ready(function(){
    })

    // Load users Histories
    $(document).on('click', '.load-refactor-users', function() {
        var id = $(this).attr('data-id');

        $.ajax({
            method: "GET",
            url: `{{ route('config-refactor.get_users_histories', [""]) }}/` + id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_user_name != null) ? v.old_user_name : ' - ' } </td>
                                    <td> ${(v.new_user_name != null) ? v.new_user_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#user-area-list").find(".user-action-list-view").html(html);
                    $("#user-area-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    // Load Status Histories
    $(document).on('click', '.load-refactor-status', function() {
        var id = $(this).attr('data-id');
        var column = $(this).attr('data-column');

        $.ajax({
            method: "GET",
            url: `{{ route('config-refactor.get_status_histories', ["", ""]) }}/` + id + '/' + column,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_status_name != null) ? v.old_status_name : ' - ' } </td>
                                    <td> ${(v.new_status_name != null) ? v.new_status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#status-area-list").find(".status-action-list-view").html(html);
                    $("#status-area-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    // Load Remark
    $(document).on('click', '.load-refactor-remark', function() {
        var id = $(this).attr('data-id');
        var column = $(this).attr('data-column');

        $.ajax({
            method: "GET",
            url: `{{ route('config-refactor.get_remarks', ["", ""]) }}/` + id + '/' + column,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.remarks } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#remark-area-list").find(".remark-action-list-view").html(html);
                    $("#remark-area-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    // Store Remarks
    function saveRemarks(row_id, column) {
        var remark = $("#" + column + "_" + row_id).val();
        $.ajax({
            url: `{{ route('config-refactor.store.remark') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                id: row_id,
                column: column,
                remark: remark,
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            if (response.status) {
                $("#" + column + "_" + row_id).val('');
                toastr["success"](response.message);
            } else {
                toastr["error"](response.message);
            }
            $("#loading-image").hide();
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            if (jqXHR.responseJSON.errors !== undefined) {
                $.each(jqXHR.responseJSON.errors, function(key, value) {
                    // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                    toastr["warning"](value);
                });
            } else {
                toastr["error"]("Oops,something went wrong");
            }
            $("#loading-image").hide();
        });
    }

    // on status change
    $(document).on('change', '.change-config-refactor-status', function() {
        let id = $(this).attr('data-id');
        let column = $(this).attr('data-column');
        let status = $(this).val();

        $.ajax({
            url: "{{route('config-refactor.change.status')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                'id': id,
                'column': column,
                'status': status
            },
            success: function(response) {
                toastr["success"](response.message, "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")
            }
        });
    });

    // on user change
    $(document).on('change', '.change-config-refactor-user', function() {
        let id = $(this).attr('data-id');
        let user_id = $(this).val();

        $.ajax({
            url: "{{route('config-refactor.change.user')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                'id': id,
                'user_id': user_id
            },
            success: function(response) {
                toastr["success"](response.message, "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")
            }
        });
    });
</script>
@endsection