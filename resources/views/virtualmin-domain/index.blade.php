@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Virtualmin Domains ({{ $domains->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    <form action="{{ route('virtualmin.domains') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>                            
                            <div class="col-md-3 pd-sm">                                
                                <select name="status" id="status" class="form-control select2">
                                    <option value="">-- Select a status --</option> 
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('virtualmin.domains') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
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
                    <table class="table table-bordered" style="table-layout: fixed;" id="virtualmin-domains-list">
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
                                       {{ strlen($domain->is_enabled_text) > 30 ? substr($domain->is_enabled_text, 0, 30).'...' :  $domain->is_enabled_text }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $domain->is_enabled_text }}
                                    </span>
                                </td>
                                <td>
                                    @if ($domain->is_enabled === 0)
                                        <a href="{{ route('virtualmin.domains.enable', ['id' => $domain->id]) }}" class="btn btn-xs" title="Enable">
                                            <i class="fa fa-check" style="color: #808080;"></i>
                                        </a>
                                    @endif
                                    @if ($domain->is_enabled === 1)
                                    <a href="{{ route('virtualmin.domains.disable', ['id' => $domain->id]) }}" class="btn btn-xs" title="Disable">
                                        <i class="fa fa-ban" style="color: #808080;"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('virtualmin.domains.delete', ['id' => $domain->id]) }}" class="btn btn-xs" title="Delete">
                                        <i class="fa fa-trash" style="color: #808080;"></i>
                                    </a>
                                    <button type="button" class="btn btn-xs domain-history"
                                            data-id="{{ $domain->id }}" title="Domain History" onclick="listdomainhistory()">
                                            <i class="fa fa-info-circle" style="color: #808080;"></i>
                                        </button>
                                        
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $domains->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

<div id="loading-image-preview" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
</div>
{{-- zabbixStatusCreate --}}
{{-- @include('zabbix-webhook-data.partials.zabbix-status-create-modal') --}}
{{-- #remark-area-list --}}
{{-- @include('zabbix-webhook-data.partials.remark_list') --}}
{{-- #assignee-history-list --}}
{{-- @include('zabbix-webhook-data.partials.assignee-history-list') --}}

{{-- @include('partials.modals.zabbix-task-create-window') --}}

{{-- @include('zabbix-webhook-data.partials.zabbix-status-listing') --}}


<div id="domain-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Virtualmin Domains History</b></h4>
                </div>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="domain-history-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                                
    function listdomainhistory(pageNumber = 1) {
        var button = document.querySelector('.btn.btn-xs.domain-history'); // Corrected class name
        var id = button.getAttribute('data-id');

            $.ajax({
                url: '{{ route('virtualmin.domains.history') }}',
                dataType: "json",
                data: {
                    id: id,
                    page:pageNumber,
                },
                beforeSend: function() {
                $("#loading-image-preview").show();
            }
            }).done(function(response) {
                $('#domain-history-modal-html').empty().html(response.html);
                $('#domain-history-modal').modal('show');
                renderdomainPagination(response.data);
                $("#loading-image-preview").hide();
            }).fail(function(response) {
                $('.loading-image-preview').show();
                console.log(response);
            });
    }

        function renderdomainPagination(response) {
            var paginationContainer = $(".pagination-container-domain");
            var currentPage = response.current_page;
            var totalPages = response.last_page;
            var html = "";
            var maxVisiblePages = 10;

            if (totalPages > 1) {
                html += "<ul class='pagination'>";
                if (currentPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(" + (currentPage - 1) + ")'>Previous</a></li>";
                }
                var startPage = 1;
                var endPage = totalPages;

                if (totalPages > maxVisiblePages) {
                if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                    endPage = maxVisiblePages;
                } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                    startPage = totalPages - maxVisiblePages + 1;
                } else {
                    startPage = currentPage - Math.floor(maxVisiblePages / 2);
                    endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
                }

                if (startPage > 1) {
                    html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(1)'>1</a></li>";
                    if (startPage > 2) {
                    html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                    }
                }
                }

                for (var i = startPage; i <= endPage; i++) {
                html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(" + i + ")'>" + i + "</a></li>";
                }
                html += "</ul>";
            }
            paginationContainer.html(html);
         }

        function changedomainPage(pageNumber) {
            listdomainhistory(pageNumber);
        }
          

</script>
@endsection