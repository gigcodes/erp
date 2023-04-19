@extends('layouts.app')

@section('content')
<style>
    .card-remove{
        position:absolute;
        top:0px;
        right:0px;
    }
</style>
@php
$auth = auth()->user();
@endphp
<h2 class="page-heading">Add {{ $moduleName }}</h2>
<div class="container-fluid">
    <div class="mt-3">
        <div class="">
            <form action="{{ route('seo.content.store')}}" method="POST" id="seoForm" autocomplete="off"> @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Select Website </label>
                            <select name="website_id" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($storeWebsites as $item)
                                <option value="{{ $item->id }}">{{ $item->website }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <section class="keywordSec">
                        <div class="col-12 mb-1">
                            <div class="row">
                                <label class="col form-label">Keywords <button type="button" class="badge btn addKeywordBtn"><i class="fa fa-plus" aria-hidden="true"></i>
                                </button></label>
                            </div>
                        </div>

                        <table class="table kwRowSec">
                            <tr class="kwRow" id="kwRow-1">
                                <td>
                                    <input type="hidden" name="seo_status[]" class="seoStatusInp" value="">
                                    <input type="hidden" name="publish_status[]" class="publishStatusInp" value="">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Keyword</label>
                                                <input type="text" name="keyword[]" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Word count</label>
                                                <input type="number" name="word_count[]" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Suggestion</label>
                                                <input type="text" name="suggestion[]" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Status</label>
                                                <select name="kw_status[]" class="form-control">
                                                    <option value="approved">Approved</option>
                                                    <option value="reject">Reject</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-remove">
                                            <button type="button" class="mt-1 btn btn-sm kwRmBtn">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div class="pb-3 px-5">
                                            <button type="button" class="mt-1 btn btn-secondary btn-sm seoStatusBtn">Seo team status</button>
                                            <button type="button" class="mt-1 btn btn-secondary btn-sm publishStatusBtn">Publish team status</button>
                                        </div>
                                    </div>
                                    
                                </td>
                            </tr>
                        </table>
                    </section>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-control">
                            <option value="">-- Select --</option>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-control">
                    </div>
                    @if($auth->hasRole([config('site.role.admin')]))
                    <div class="col-md-2">
                        <div class="form-check form-check-inline mt-4">
                            <input class="form-check-input" type="checkbox" name="is_price_approved" id="priceApprove" value="1">
                            <label class="form-check-label" for="priceApprove">Approve</label>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Document link</label>
                        <input type="text" name="google_doc_link" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Seo Status</label>
                        <select name="seo_process_status_id" class="form-control">
                            <option value="">-- Select --</option>
                            @foreach ($seoProcessStatus as $item)
                            @if($item->type == 'seo_approval')
                            <option value="{{ $item->id }}">{{ $item->label }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Live status link</label>
                        <input type="text" name="live_status_link" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Publish date</label>
                        <input type="datetime-local" name="published_at" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="planned">Planned</option>
                            <option value="admin_approve">Admin approve</option>
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row mt-3 mb-5">
                    <div class="col-md-12">
                        <a href="{{ route('seo.content.index') }}" class="btn btn-notification">Cancel</a>
                        <button type="submit" class="btn btn-secondary">Submit</button>
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
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary btnSave">Save changes</button>
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
                @foreach ($seoProcessStatus as $item)
                @if($item->type == 'publish')
                <div class="row mt-2">
                    <div class="col-md-8 statusSec">
                        <label class="form-label">{{ $item->label }}</label>
                        <input type="text" class="form-control" data-id="{{ $item->id }}">
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary btnSave">Save changes</button>
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
                        type: "TEAM_STATUS"
                        , statusType: "SEO_STATUS"
                    , }
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
                        type: "TEAM_STATUS"
                        , statusType: "PUBLISH_STATUS"
                    , }
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
    });

</script>
@endsection
