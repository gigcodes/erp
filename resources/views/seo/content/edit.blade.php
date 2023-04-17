@extends('layouts.app')

@section('content')
@php
    $auth = auth()->user();
@endphp
<div class="container-fluid">
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="text-center">Edit SEO Content</h3>
            <hr>
        </div>
        <div class="card-body">
            <form action="{{ route('seo.content.update', $seoProcess->id)}}" method="POST" id="seoForm" autocomplete="off"> @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Select Website </label>
                            <select name="website_id" class="form-control" {{ $auth->hasRole(['Admin']) ? '' : 'readonly'}}>
                                <option value="">-- Select --</option>
                                @foreach ($storeWebsites as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $seoProcess->website_id ? 'selected' : '' }}>{{ $item->website }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <section class="keywordSec">
                        <div class="col-8 mb-1">
                            <div class="row">
                                <label class="col form-label">Keywords</label>
                                <div class="col-2">
                                    <button type="button" class="badge btn addKeywordBtn">Add Keyword</button>
                                </div>
                            </div>
                        </div>

                        <table class="table kwRowSec">
                            @foreach ($seoProcess->keywords as $ky => $keyword)
                            <tr class="kwRow col-md-12" id="kwRow{{$ky+1}}">
                                <td>
                                    <input type="hidden" name="keywordId" class="keywordId" value="{{ $keyword->id }}">
                                    <input type="hidden" name="seo_status[]" class="seoStatusInp" value="{{ json_encode($keyword->seoRemarks->toArray() )}}">
                                    <input type="hidden" name="publish_status[]" class="publishStatusInp" value="{{ json_encode($keyword->publishRemarks->toArray() )}}">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Keyword</label>
                                                <input type="text" name="keyword[]" class="form-control" value="{{ $keyword->keyword }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Word count</label>
                                                <input type="number" name="word_count[]" class="form-control" value="{{ $keyword->word_count }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Suggestion</label>
                                                <input type="text" name="suggestion[]" class="form-control" value="{{ $keyword->content }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Status</label>
                                                <select name="kw_status[]" class="form-control">
                                                    <option value="approved" {{ $keyword->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="reject" {{ $keyword->status == 'reject' ? 'selected' : '' }}>Reject</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="mt-1 btn btn-primary btn-sm seoStatusBtn">Seo team status</button>
                                    <button type="button" class="mt-1 btn btn-primary btn-sm publishStatusBtn">Publish team status</button>
                                </td>
                                <td>
                                    <button type="button" class="mt-1 btn btn-danger btn-sm kwRmBtn">Remove</button>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </section>
                </div>

                @if($auth->hasRole(['Admin', 'Seo Head']))
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $seoProcess->user_id ? 'selected' : ''}}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="user_id" value="{{$seoProcess->user_id}}">
                @endif

                <div class="row mt-3">
                    @php
                    $priceClass = "";
                    $isPriceEdit = false;
                    if($seoProcess->is_price_approved) {
                        $priceClass = "bg-success text-light font-weight-bold";
                    } else {
                        $priceClass = "bg-warning text-dark font-weight-bold";
                    }

                    if($auth->hasRole(['user']) && !$seoProcess->is_price_approved ) {
                        $isPriceEdit = true;
                    } else if($auth->hasRole(['Admin'])) {
                        $isPriceEdit = true;
                    }
                    @endphp
                    <div class="col-md-4">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-control {{$priceClass}}" value="{{ $seoProcess->price }}" {{ ($isPriceEdit) ? '' : 'readonly' }}>
                    </div>

                    @if($auth->hasRole(['Admin']))
                        <div class="col-md-2">
                            <div class="form-check form-check-inline mt-4">
                                <input class="form-check-input" type="checkbox" {{ $seoProcess->is_price_approved ? 'checked' : '' }} name="is_price_approved" id="priceApprove" value="1">
                                <label class="form-check-label" for="priceApprove">Approve</label>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Document link</label>
                        <input type="text" name="google_doc_link" class="form-control" value="{{ $seoProcess->google_doc_link }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Seo Status</label>
                        <select name="seo_process_status_id" class="form-control">
                            <option value="">-- Select --</option>
                            @foreach ($seoProcessStatus as $item)
                            @if($item->type == 'seo_approval')
                            <option value="{{ $item->id }}" {{ $item->id == $seoProcess->seo_process_status_id ? 'selected' : ''}}>{{ $item->label }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Live status link</label>
                        <input type="text" name="live_status_link" class="form-control" value="{{ $seoProcess->live_status_link }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Publish date</label>
                        <input type="datetime-local" name="published_at" class="form-control" value="{{ $seoProcess->published_at }}">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="planned" {{ $seoProcess->status == 'planned'}}>Planned</option>
                            <option value="admin_approve" {{ $seoProcess->status == 'admin_approve'}}>Admin approve</option>
                        </select>
                    </div>
                </div>

                @if($auth->hasRole(['Admin']))    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary btn-sm historyBtn" data-type="user">User History</button>
                            <button type="button" class="btn btn-primary btn-sm ml-2 historyBtn" data-type="price">Price History</button>
                        </div>
                    </div>
                @endif

                <hr>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <a href="{{ route('seo.content.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Seo team status modal -->
<div class="modal fade" id="kwSeoModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seo team status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btnSave">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Publish team status modal -->
<div class="modal fade" id="kwPublishModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Publish team status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btnSave">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Price & User history modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true" data-rowid="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        let kwRowIdCount = 1;
        $(document).on('click', '.addKeywordBtn', function() {
            let $kwRow = $('.kwRow:last').clone();
            $kwRow.attr('id', `kwRow-${kwRowIdCount+1}`)
            $('input', $kwRow).val('');
            $('select option', $kwRow).removeAttr('selected');
            $kwRow.appendTo($('.kwRowSec'));
            kwRowIdCount++;
        });

        $(document).on('click', '.kwRow .kwRmBtn', function() {
            let $kwSec = $(document).find('.kwRowSec');
            if ($kwSec.find('.kwRow').length != 1) {
                let $per = $(this).closest('.kwRow');
                $per.remove();
            }
        });

        $(function() {
            $(document).on('click', ".kwRowSec .kwRow .seoStatusBtn", function() {
                let $kwRow = $(this).closest('.kwRow');
                $.ajax({
                    type: "GET"
                    , url: ""
                    , data: {
                        statusType: "SEO_STATUS"
                        , keywordId: $($kwRow).find('.keywordId').val()
                    }
                    , dataType: "json"
                    , success: function(response) {
                        let $seoModal = $(document).find('#kwSeoModal');
                        $($seoModal).find('.modal-body').html(response.data);
                        $($seoModal).attr('data-rowid', `#${$kwRow.attr('id')}`)
                        $($seoModal).modal('show');
                    }
                });
            })

            $(document).on('click', "#kwSeoModal .btnSave", function() {
                let $inputs = $('#kwSeoModal .statusSec');
                let kwRowId = $('#kwSeoModal').attr('data-rowid');
                let $kwRow = $(document).find(kwRowId);
                let statusArr = [];
                $inputs.each(function(index, element) {
                    statusArr.push({
                        seo_process_status_id: $(element).find('input').attr('data-id')
                        , remarks: $(element).find('input').val()
                    , });
                });
                $($kwRow).find('.seoStatusInp').val(JSON.stringify(statusArr));
                $('#kwSeoModal').modal('hide');
            });

            $(document).on('hide.bs.modal', "#kwSeoModal", function() {
                $('input', '#kwSeoModal').val('');
                $('#kwSeoModal').attr('data-rowid', '');
            });
        })

        $(function() {
            $(document).on('click', ".kwRowSec .kwRow .publishStatusBtn", function() {
                let $kwRow = $(this).closest('.kwRow');
                $.ajax({
                    type: "GET"
                    , url: ""
                    , data: {
                        statusType: "PUBLISH_STATUS"
                        , keywordId: $($kwRow).find('.keywordId').val()
                    }
                    , dataType: "json"
                    , success: function(response) {
                        let $publishModal = $(document).find('#kwPublishModal');
                        $($publishModal).find('.modal-body').html(response.data);
                        $($publishModal).attr('data-rowid', `#${$kwRow.attr('id')}`)
                        $($publishModal).modal('show');
                    }
                });
            })
        })

        $(document).on('click', "#kwPublishModal .btnSave", function() {
            let $inputs = $('#kwPublishModal .statusSec');
            let kwRowId = $('#kwPublishModal').attr('data-rowid');
            let $kwRow = $(document).find(kwRowId);
            let statusArr = [];
            $inputs.each(function(index, element) {
                statusArr.push({
                    seo_process_status_id: $(element).find('input').attr('data-id')
                    , remarks: $(element).find('input').val()
                , });
            });
            $($kwRow).find('.publishStatusInp').val(JSON.stringify(statusArr));
            $('#kwPublishModal').modal('hide');
        });

        $(document).on('hide.bs.modal', "#kwPublishModal", function() {
            $('input', '#kwPublishModal').val('');
            $('#kwPublishModal').attr('data-rowid', '');
        });

        $('#seoForm').validate({
            rules: {
                'website_id': {
                    required: true
                }
                , 'user_id': {
                    required: true
                , }
                , 'price': {
                    required: true
                    , number: true
                }
                , 'google_doc_link': {
                    required: true
                , }
                , 'live_status_link': {
                    required: true
                , }
                , 'seo_process_status_id': {
                    required: true
                , }
                , 'published_at': {
                    required: true
                , }
            , }
            , messages: {
                'website_id': {
                    required: "Please select website."
                }
                , 'user_id': {
                    required: "Please select user."
                , }
                , 'price': {
                    required: "Please enter a price."
                    , number: true
                }
                , 'google_doc_link': {
                    required: "Please enter a document link."
                , }
                , 'live_status_link': {
                    required: "Please enter a live status link."
                , }
                , 'seo_process_status_id': {
                    required: "Please select seo status."
                , }
                , 'published_at': {
                    required: "Please select publish date."
                , }
            , }
        });

        // Price & User Histroy Modal
        $(document).on('click', ".historyBtn", function() {
            let type = $(this).attr('data-type');
            $.ajax({
                type: "GET",
                url: "",
                data: {
                    type:"GET_HISTORY",
                    seoProcessId:`{{ $seoProcess->id }}`,
                    seoType:type,
                },
                dataType: "json",
                success: function (response) {
                    let $historyModal = $(document).find('#historyModal');
                    $($historyModal).find('.modal-body').html(response.data);
                    $($historyModal).modal('show');
                    if(type == 'user') {
                        $($historyModal).find('.modal-title').text('User history')
                    } else if(type == 'price') {
                        $($historyModal).find('.modal-title').text('Price history')
                    }
                }
            });
        })
    });

</script>
@endsection
