@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Virtualmin Domains ({{ $domains->total() }})</h2>
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
                        <a href="{{ route('virtualmin.domains.sync') }}" class="btn btn-primary">Sync Domains</a>
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
                    <table class="table table-bordered" style="table-layout: fixed;" id="zabbix-webhook-data-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Name</th>
                            <th width="20%">Status</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($domains as $key => $domain)
                            <tr data-id="{{ $domain->id }}">
                                <td>{{ $domain->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($domain->name) > 30 ? substr($domain->name, 0, 30).'...' :  $domain->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $domain->name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($domain->status) > 30 ? substr($domain->status, 0, 30).'...' :  $domain->status }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $domain->status }}
                                    </span>
                                </td>
                                <td>Actions</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $domains->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

{{-- zabbixStatusCreate --}}
{{-- @include('zabbix-webhook-data.partials.zabbix-status-create-modal') --}}
{{-- #remark-area-list --}}
{{-- @include('zabbix-webhook-data.partials.remark_list') --}}
{{-- #assignee-history-list --}}
{{-- @include('zabbix-webhook-data.partials.assignee-history-list') --}}

{{-- @include('partials.modals.zabbix-task-create-window') --}}

{{-- @include('zabbix-webhook-data.partials.zabbix-status-listing') --}}

<script type="text/javascript">
    $(document).ready(function(){
        
    })

    function Showactionbtn(id) {
        $(".action-btn-tr-" + id).toggleClass('d-none')
    }

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    // Load Remark
    $(document).on('click', '.load-module-remark', function() {
        var id = $(this).attr('data-id');
        $.ajax({
            method: "GET",
            url: `{{ route('zabbix-webhook-data.get_remarks', '') }}/` + id,
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

    // Show task assignee histories
    $(document).on('click', '.show-task-assignee-history', function() {
        var zabbix_task_id = $(this).attr('data-zabbix_task_id');

        $.ajax({
            method: "GET",
            url: `{{ route('zabbix-task.get-assignee-histories', '') }}/` + zabbix_task_id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.new_assignee !== undefined) ? v.new_assignee.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#assignee-history-list").find(".assignee-history-list-view").html(html);
                    $("#assignee-history-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    // Store Reark
    function saveRemarks(row_id) {
        var remark = $("#remarks_" + row_id).val();
        $.ajax({
            url: `{{ route('zabbix-webhook-data.store.remark') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                remarks: remark,
                zabbix_webhook_data_id: row_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            if (response.status) {
                $("#remarks_" + row_id).val('');
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
    $(document).on('change', '.change-zabbix-status', function() {
        let id = $(this).attr('data-id');
        let status = $(this).val();
        $.ajax({
            url: "{{route('zabbix-webhook-data.change.status')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                'zabbix_webhook_data': id,
                'status': status
            },
            success: function(response) {
                toastr["success"](response.message, "Message")
                $(`#zabbix-webhook-data-list tr[data-id="${id}"]`).css('background-color', response.colourCode);
           },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")
            }
        });
    });

    $(document).on("click", ".save-zabbix-task-window", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                $(this).text('Loading...');
            },
            success: function(response) {
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#zabbix-task-create").modal("hide");
                    location.reload();
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });
</script>
@endsection