</br>
<button type="button" class="btn {{ ${$columnname . 'Var'} }} btn-sm update-scrapper-status" title="Status" data-task_id="{{$taskss_id}}" data-column_name="title">
    <i class="fa {{ ${$columnname . 'Icon'} }}" aria-hidden="true"></i>
</button>
<button type="button" class="btn btn-default btn-sm update-scrapper-remarks" title="Remarks" data-task_id="{{ $taskss_id }}" data-column_name="title">
    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
</button>

@if(!empty(${$columnname . 'Record'}))
    <button type="button" class="btn btn-default btn-sm view-scrapper-remarks" title="Remarks" data-task_id="{{ $taskss_id }}" data-column_name="title" data-remarks="{{${$columnname . 'Record'}['remarks']}}">
        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
    </button>
@endif

