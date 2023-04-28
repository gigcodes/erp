@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
@php
$auth = auth()->user();
@endphp
<h2 class="page-heading">{{ $moduleName }}</h2>

<div class="container-fluid">
    <div class="d-flex justify-content-end">
        <div class="">
            @if($auth->hasRole(['Admin', 'User', 'Seo Head']))
            <a href="javascript:;" class="btn btn-secondary addNewBtn">Add new</a>
            @endif
        </div>
    </div>
    <div class="mt-3">
        <div class="row">
            <div class="col-md-2">
                <label for="">Select website</label>
                <select name="website_id" class="form-control websiteFilter">
                    <option value="">-- SELECT --</option>
                    @foreach ($websites as $item)
                        <option value="{{ $item->id }}">{{ $item->website }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="">Select price status</label>
                <select name="website_id" class="form-control priceStatusFilter">
                    <option value="">-- SELECT --</option>
                    <option value="1">Approved</option>
                    <option value="2">Unapproved</option>
                </select>
            </div>
            @if($auth->hasRole(['Admin']))
                <div class="col-md-2">
                    <label for="">Select user</label>
                    <select name="website_id" class="form-control userFilter">
                        <option value="">-- SELECT --</option>
                        @foreach ($users as $item)
                            @if(!$item->hasRole(['user']))
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-2">
                <label for="">Status</label>
                <select name="website_id" class="form-control statusFilter">
                    <option value="">-- SELECT --</option>
                    <option value="planned">Planned</option>
                    <option value="admin_approve">Admin Approved</option>
                </select>
            </div>


            <div class="col-md-2 mt-5">
                <label for="">&nbsp;</label>
                <button class="btn btn-image search ui-autocomplete-input searchBtn">
                    <img src="{{ url('images/search.png')}}" alt="Search" style="cursor: default;">
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive-lg" style="overflow-x:auto;">
                <table class="table table-bordered" id="seoProcessTbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Website</th>
                            <th>Keywords</th>
                            <th>Word count</th>
                            <th>Suggestion</th>
                            <th>SEO Checklist</th>
                            <th>Publish Checklist</th>
                            <th>Document Link</th>
                            <th>Live Status Link</th>
                            <th>SEO Status</th>
                            <th>User</th>
                            <th>Price</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Add Form Modal -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary saveFormBtn">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary saveFormBtn">Submit</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        // Datatable 
        const $datatable = $('#seoProcessTbl').DataTable({
            serverSide: true,
            lengthMenu: [ [50, 100, 150, -1], [50, 100, 150, "All"] ],
            searching:false,
            responsive:true
            , ajax: {
                url: '', 
                data:{
                    filter:{
                        website_id: () => $(document).find('.websiteFilter').val(),
                        price_status:() => $(document).find('.priceStatusFilter').val(),
                        user_id:() => $(document).find('.userFilter').val(),
                        status:() => $(document).find('.statusFilter').val(),
                    }
                }
            , }
            , columns: [{
                    data: 'DT_RowIndex'
                    , 'orderable': false
                    , 'searchable': false
                }
                , {
                    data: 'website_id'
                    , name: 'website_id'
                }
                , {
                    data: 'keywords'
                    , name: 'keywords'
                },
                {
                    data: 'word_count'
                    , name: 'word_count'
                },
                {
                    data: 'suggestion'
                    , name: 'suggestion'
                },
                {
                    data:'seoChecklist',
                    name:'seoChecklist'
                },
                {
                    data:'publishChecklist',
                    name:'publishChecklist'
                },
                {
                    data:'documentLink',
                    name:'documentLink'
                },
                {
                    data:'liveStatusLink',
                    name:'liveStatusLink'
                },
                {
                    data:'seoStatus',
                    name:'seoStatus'
                }
                , {
                    data: 'user_id'
                    , name: 'user_id'
                }
                , {
                    data: 'price'
                    , name: 'price'
                }
                , {
                    data: 'published_at'
                    , name: 'published_at'
                }
                , {
                    data: 'status'
                    , name: 'status'
                }
                , {
                    data: 'actions'
                    , name: 'actions'
                }
            , ]
        });

        $(function() {
            $(document).on('click', '.addNewBtn', function() {
                let $formModal = $(document).find('#formModal');
                $($formModal).modal('show');
                $.ajax({
                    type: "GET",
                    url: "{{ route('seo.content.create') }}",
                    data: {
                        formType:"CREATE_FORM"
                    },
                    dataType: "json",
                    success: function (response) {
                        $($formModal).find('.modal-body').html(response.data)
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let url = $(this).attr('data-url');
                let $formModal = $(document).find('#formModal');
                $($formModal).modal('show');
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                    },
                    dataType: "json",
                    success: function (response) {
                        $($formModal).find('.modal-body').html(response.data)
                    }
                });
            });

            $(document).on('click', '.searchBtn', function() {
                $datatable.clear().draw();
            })
        })
    });

</script>

@include('seo.content.script')
@endsection
