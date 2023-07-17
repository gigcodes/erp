@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Magento Css Variables ({{ $magentoCssVariables->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    <form action="{{ route('magento-css-variable.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-3 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-4 pd-sm pl-0 mt-2">
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
                <div class="col-4">
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
                                       {{ strlen($magentoCssVariable->project->name) > 30 ? substr($magentoCssVariable->project->name, 0, 30).'...' :  $magentoCssVariable->project->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariable->project->name }}
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
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $magentoCssVariable->user->name }}
                                </td>
                                <td>
                                    <button type="button" data-id="{{ $magentoCssVariable->id }}" class="btn btn-xs btn-edit-magento-css-variable">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" title="Update Value" data-id="{{ $magentoCssVariable->id }}" class="btn btn-xs btn-update-value" style="padding: 0px 5px !important;">
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
{{-- @include('magento-css-variable.partials.edit-modal') --}}

<script type="text/javascript">
    $('.select2').select2();

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