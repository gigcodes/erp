@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Magento CSS Variables ({{ $magentoCssVariables->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('magento-css-variable.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-3 pd-sm">
                                <?php 
                                    if(request('search_project_id')){   $search_project_id = request('search_project_id'); }
                                    else{ $search_project_id = ''; }
                                ?>
                                <select name="search_project_id" id="search_project_id" class="form-control select2">
                                    <option value="" @if($search_project_id=='') selected @endif>-- Select a project --</option>
                                    @forelse($projects as $id => $name)
                                    <option value="{{ $id }}" @if($search_project_id==$id) selected @endif>{!! $name !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-3 pd-sm">
                                <?php 
                                    if(request('search_file_path')){   $search_file_path = request('search_file_path'); }
                                    else{ $search_file_path = ''; }
                                ?>
                                <select name="search_file_path" id="search_file_path" class="form-control select2">
                                    <option value="" @if($search_file_path=='') selected @endif>-- Select a file path --</option>
                                    @forelse($file_paths as $file_path)
                                    <option value="{{ $file_path }}" @if($search_file_path==$file_path) selected @endif>{!! $file_path !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-3 pd-sm">
                                <?php 
                                    if(request('search_variable')){   $search_variable = request('search_variable'); }
                                    else{ $search_variable = ''; }
                                ?>
                                <select name="search_variable" id="search_variable" class="form-control select2">
                                    <option value="" @if($search_variable=='') selected @endif>-- Select a variable --</option>
                                    @forelse($variables as $variable)
                                    <option value="{{ $variable }}" @if($search_variable==$variable) selected @endif>{!! $variable !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-1 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('magento-css-variable.index') }}" class="btn btn-image" id="">
                                    <img src="/images/resend2.png" style="cursor: nwse-resize;">
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-12" style="margin-top: 10px;">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#magento-css-variable-create"> Create </button>
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
                    <table class="table table-bordered" style="table-layout: fixed;" id="magento-css-variable-list">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Project Name</th>
                            <th width="10%">File name</th>
                            <th width="10%">File Path</th>
                            <th width="10%">Variable</th>
                            <th width="10%">Value</th>
                            <th width="10%">Created By</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($magentoCssVariables as $key => $magentoCssVariable)
                            <tr data-id="{{ $magentoCssVariable->id }}">
                                <td>{{ $magentoCssVariable->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoCssVariable->project?->name) > 30 ? substr($magentoCssVariable->project?->name, 0, 30).'...' :  $magentoCssVariable->project?->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariable->project?->name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoCssVariable->filename) > 30 ? substr($magentoCssVariable->filename, 0, 30).'...' :  $magentoCssVariable->filename }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariable->filename }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoCssVariable->file_path) > 30 ? substr($magentoCssVariable->file_path, 0, 30).'...' :  $magentoCssVariable->file_path }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariable->file_path }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $magentoCssVariable->variable }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $magentoCssVariable->value }}
                                    <button type="button" class="btn btn-xs btn-image load-value-histories ml-2 pull-right" data-id="{{$magentoCssVariable->id}}" title="Load value histories"> 
                                        <i class="fa fa-info-circle"></i>
                                    </button>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $magentoCssVariable->user?->name }}
                                </td>
                                <td>
                                    <button type="button" data-id="{{ $magentoCssVariable->id }}" class="btn btn-xs btn-edit-magento-css-variable">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    {!! Form::open(['method' => 'DELETE', 'class' => 'delete-form', 'route' => ['magento-css-variable.destroy', $magentoCssVariable->id], 'style'=>'display:inline']) !!}
                                    <button type="submit" class="btn btn-xs delete-button" onclick="return confirmDelete(event)">
                                        <i class="fa fa-trash" style="color: #808080;"></i>
                                    </button>
                                    {!! Form::close() !!}
                                    <button type="button" title="Update Value" data-id="{{ $magentoCssVariable->id }}" class="btn btn-xs btn-update-magento-css-value" style="padding: 0px 5px !important;">
                                        <i class="fa fa-upload" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $magentoCssVariables->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

{{-- magento-css-variable-create --}}
@include('magento-css-variable.partials.create-modal')
{{-- magento-css-variable-edit --}}
@include('magento-css-variable.partials.edit-modal')
{{-- magento-css-value-edit --}}
@include('magento-css-variable.partials.value-edit-modal')
{{-- #value-histories-modal --}}
@include('magento-css-variable.partials.value-histories-modal')

<script type="text/javascript">
    $('.select2').select2();

    function confirmDelete(event) {
        event.preventDefault();
        var confirmDelete = confirm("Are you sure you want to delete this item?");
        if (confirmDelete) {
            event.target.closest('.delete-form').submit();
        }
        return false;
    }

    $(document).ready(function(){
        $(".btn-edit-magento-css-variable").on('click', function(e) {
            var ajaxUrl = "{{ route('magento-css-variable.edit', ['magento_css_variable' => ':id']) }}";
            ajaxUrl = ajaxUrl.replace(':id', $(this).data("id"));

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: ajaxUrl,
            }).done(function(response) {
                $("#magento-css-variable-edit-form #id").val(response.data.id);
                $("#magento-css-variable-edit-form #project_id").val(response.data.project_id).trigger('change');
                $("#magento-css-variable-edit-form #filename").val(response.data.filename);
                $("#magento-css-variable-edit-form #file_path").val(response.data.file_path);
                $("#magento-css-variable-edit-form #variable").val(response.data.variable);
                $("#magento-css-variable-edit-form #value").val(response.data.value);
                $("#magento-css-variable-edit").modal("show");
            }).fail(function(response) {});
        });

        $(".btn-update-magento-css-value").on('click', function(e) {
            var ajaxUrl = "{{ route('magento-css-variable.edit', ['magento_css_variable' => ':id']) }}";
            ajaxUrl = ajaxUrl.replace(':id', $(this).data("id"));

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: ajaxUrl,
            }).done(function(response) {
                $("#magento-css-value-edit-form #id").val(response.data.id);
                $("#magento-css-value-edit-form #file_path").val(response.data.file_path);
                $("#magento-css-value-edit-form #variable").val(response.data.variable);
                $("#magento-css-value-edit-form #value").val(response.data.value);
                $("#magento-css-value-edit").modal("show");
            }).fail(function(response) {});
        });

        // Load value Histories
        $(document).on('click', '.load-value-histories', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-css-variable.value-histories', [""]) }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${(v.old_value != null) ? v.old_value : ' - ' } </td>
                                        <td> ${(v.new_value != null) ? v.new_value : ' - ' } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#value-histories-list").find(".value-histories-list-view").html(html);
                        $("#value-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
    });

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
    $(document).on("click", ".btn-update-value", function(e) {
        e.preventDefault();
        if (confirm('Are sure you want to update value?')) {
            $.ajax({
                type: 'POST',
                url: '/magento-css-variable/update-value',
                beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $(this).data('id'),
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image-preview").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                }else{
                    toastr['error'](response.message, 'error');
                }
                
            }).fail(function (response) {
                $("#loading-image-preview").hide();
                toastr['error']("Sorry, something went wrong", 'error');
            });
        }
	
	});
</script>
@endsection