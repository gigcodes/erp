@extends('layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $(document).ready(function() {
        $('#repository-table').DataTable({
            "paging": true,
            "ordering": true,
            "info": false
        });
    });
</script>
<style>
    #repository-table_filter {
        text-align: right;
    }
</style>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Github Repositories (<span id="repository_row_html_id">{{ count($repositories) }}</span>)</h2>
    </div>
</div>

<div class="container">
    @if(strlen($organizationId) == 0)
        <form action="" method="GET" id="filterRepositoryForm">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Organization</label>
                    <select class="form-control" id="organization" name="organization">
                        @foreach($githubOrganizations as $githubOrganization)
                            <option value="{{ $githubOrganization->id }}" {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '') }}>{{ $githubOrganization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default" style="margin-top: 25px;"><i class="fa fa-filter"></i> </button>
                </div>
            </div>
        </form>
        <div class="clearfix"></div><br />
    @endif

    <table id="repository-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Name</th>
                <th>Last Update </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @include('github.include.repository-list')
        </tbody>
    </table>
</div>

<script>
    $("#filterRepositoryForm").submit(function(e){
        e.preventDefault();

        var organizationId = $("#organization").val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : '{{ url('github/repos') }}/'+organizationId,
            type : 'GET',
            success : function(result){
                $('#repository-table').DataTable().clear().destroy();

                $('#repository_row_html_id').html(result.count);;
                $('#repository-table tbody').empty().html(result.tbody);

                $('#repository-table').DataTable();
            }
        });
    });
</script>
@endsection