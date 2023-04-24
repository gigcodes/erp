@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
@php
$auth = auth()->user();
@endphp
<h2 class="page-heading">{{ $moduleName }}</h2>

<div class="container-fluid">
    <div class="d-flex justify-content-end">
        <a href="{{ route('seo.company.create') }}" class="btn btn-secondary">Add new</a>
    </div>
    <div class="mt-3">
        <div class=" mt-4">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control typeSelect">
                        <option value=""> -- SELECT --</option>
                        @foreach ($companyTypes as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <table class="table table-bordered " id="seoTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Website</th>
                        <th>DA</th>
                        <th>PA</th>
                        <th>SS</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true" data-rowid="">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SEO Company History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mt-3" id="historyTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Website</th>
                            <th>Type</th>
                            <th>DA</th>
                            <th>PA</th>
                            <th>SS</th>
                            <th>Updated User</th>
                            <th>Date</th>
                            <th>Live Link</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script>
    let $loader = $(document).find('.loaderSection');
    async function getSeoProcess({
        page
    }) {
        return $.ajax({
            type: "GET"
            , url: ""
            , async: true
            , beforeSend: function(msg) {
                $loader.show();
            }
            , data: {
                page: page
            }
            , dataType: "json"
            , success: function(response) {
                $loader.hide();
                return response;
            }
        });
    }

    $(document).ready(function() {

        // Datatable 
        const $datatableData = $('#seoTable').DataTable({
            serverSide:true,
            processing:true,
            ajax:{
                url:'',
                data:{
                    companyTypeId:() => $(document).find(".typeSelect option:selected").val(),
                }
            },
            columns:[
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                {data:'website.website', name:'website.website'},
                {data:'da', name:'da'},
                {data:'pa', name:'pa'},
                {data:'ss', name:'ss'},
                {data:'user.name', name:'user.name'},
                {data:'created_at', name:'created_at'},
                {data:'actions', name:'actions'},
            ]
        });

        $("select[name=type]").on("change", function(e) {
           $datatableData.clear().draw();
        });

        let $dataTable = null;
        $(document).on('click', '.historyBtn', function() {
            let companyId = $(this).attr('data-id');
            let $historyModal = $(document).find('#historyModal');
            $($historyModal).modal('show')
            
            $dataTable = $($historyModal).find('#historyTable').DataTable({
                serverSide:true,
                ajax:{
                    url:'',
                    data:{
                        companyId:companyId,
                        type:"COMPANY_HISTORY",
                    },
                },
                columns:[
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data:'website.website', name:'website.website'},
                    {data:'company_name', name:'company_name'},
                    {data:'da', name:'da'},
                    {data:'pa', name:'pa'},
                    {data:'ss', name:'ss'},
                    {data:'user.name', name:'user.name'},
                    {data:'created_at', name:'created_at'},
                    {data:'live_link', name:'live_link'},
                ]
            })
        })

        $(document).on('hide.bs.modal', '#historyModal', function() {
            $dataTable.destroy();
            $dataTable = null;
        })

    });

</script>
@endsection
