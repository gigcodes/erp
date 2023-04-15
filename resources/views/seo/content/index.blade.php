@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
@php
    $auth = auth()->user();
@endphp
<div class="container-fluid">
    <div class="card mt-3">
        <div class="card-header">
            <div class="d-flex">
                <h3 class="text-center col">SEO Content</h3>
                <div class="mt-5 mr-5">
                    @if($auth->hasRole(['Admin', 'User']))
                        <a href="{{route('seo.content.create')}}" class="btn btn-primary">Add new</a>
                    @endif
                </div>
            </div>
            <hr>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="seoProcessTbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Website</th>
                        <th>User</th>
                        <th>Price</th>
                        <th>Publish Date</th>
                        <th>Seo Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script>

    let $loader = $(document).find('.loaderSection');
    async function getSeoProcess({ page }) {
        return $.ajax({
            type: "GET",
            url: "",
            async:true,
            beforeSend: function(msg){
                $loader.show();
            },
            data: {
                page:page
            },
            dataType: "json",
            success: function (response) {
                $loader.hide();
                return response;
            }
        });
    }

    function getHtmlContent(data) {
        let html = ``;
        data.forEach(ele => {
            let editRoute = `{{ route('seo.content.edit', ":id") }}`;
            editRoute = editRoute.replace(':id', ele.id)
            html += `<tr>`;
                html += `<td>${ele?.website.website}</td>`
                html += `<td>${ele?.user.name}</td>`
                html += `<td>${ele.price}</td>`
                html += `<td>${ele.published_at}</td>`
                html += `<td>${ele.status}</td>`
                html += `<td><a href='${editRoute}' class='btn btn-warning btn-sm'>Edit</a></td>`
            html += `</tr>`;
        });

        return html;
    }

    $(document).ready(function () {
        
        // Datatable 
        const $datatable = $('#seoProcessTbl').DataTable({
            serverSide:true,
            processing:true,
            ajax:{
                url:'',
            },
            columns:[
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                {data:'website.website', name:'website.website'},
                {data:'user.name', name:'user.name'},
                {data:'price', name:'price'},
                {data:'published_at', name:'published_at'},
                {data:'status', name:'status'},
                {data:'actions', name:'actions'},
            ]
        });

        async function fetchSeoProcess({page}) {
            let resp = await getSeoProcess({
                page:page
            });
            let html = getHtmlContent(resp.data?.data);
            $(document).find('#seoProcessTbl').append(html)
        }
    });
</script>
@endsection