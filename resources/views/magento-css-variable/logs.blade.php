@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Magento CSS Variable Logs ({{ $magentoCssVariableJobLogs->total() }})</h2>
        @if($errors->any())
        <div class="row m-2">
        {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
        </div>
        @endif
        @if (session('success'))
        <div class="col-12">
        <div class="alert alert-success">{{session('success')}}</div>
        </div>
        @endif
        @if (session('error'))
        <div class="col-12">
        <div class="alert alert-danger">{{session('error')}}</div>
        </div>
        @endif
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    
                </div>
                <div class="col-12" style="margin-top: 10px;">
                    <div class="pull-right" style="display: flex">
                        <a class="btn btn-secondary" href="{{ route('magento-css-variable.index') }}">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="magento-css-variable-logs-list">
                        <tr>
                            <th width="3%">ID</th>
                            <th width="10%">Project</th>
                            <th width="10%">Command</th>
                            <th width="10%">Message</th>
                            <th width="10%">Status</th>
                            <th width="10%">CSV Path</th>
                        </tr>
                        @foreach ($magentoCssVariableJobLogs as $key => $magentoCssVariableJobLog)
                            <tr data-id="{{ $magentoCssVariableJobLog->id }}">
                                <td>{{ $magentoCssVariableJobLog->id }}</td>
                                <td>{{ optional($magentoCssVariableJobLog->magentoCssVariable)->project?->name }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoCssVariableJobLog->command) > 30 ? substr($magentoCssVariableJobLog->command, 0, 30).'...' :  $magentoCssVariableJobLog->command }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariableJobLog->command }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoCssVariableJobLog->message) > 30 ? substr($magentoCssVariableJobLog->message, 0, 30).'...' :  $magentoCssVariableJobLog->message }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariableJobLog->message }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $magentoCssVariableJobLog->status }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoCssVariableJobLog->csv_file_path) > 30 ? substr($magentoCssVariableJobLog->csv_file_path, 0, 30).'...' :  $magentoCssVariableJobLog->csv_file_path }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariableJobLog->csv_file_path }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $magentoCssVariableJobLogs->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
</script>
@endsection