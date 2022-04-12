@extends('layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $(document).ready(function() {
        $('#org-users-table').DataTable({
            "ordering": true,
            "info": false
        });
    });
</script>
<style>
    #org-users-table_filter {
        text-align: right;
    }
</style>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Github Deploy Branch Migration Error</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 margin-tb">
    <table id="org-users-table" class="table table-bordered">
        <thead>
            <tr>
                <th width="10%">Id</th>
                <th width="20%">Branch Name</th>
                <th width="60%">Error</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gitDbError as $dbError)
            <tr>
                <td>{{$dbError['id']}}</td>
                <td class="expand-row" style="overflow-wrap: anywhere;">
                    <span class="td-mini-container">
                        {{ strlen( $dbError['branch_name'] ) > 30 ? substr( $dbError['branch_name'] , 0, 30).'...' :  $dbError['branch_name'] }}
                    </span>
                    <span class="td-full-container hidden">
                        {{$dbError['branch_name']}}
                    </span>
                </td>
                <td class="expand-row" style="overflow-wrap: anywhere;">
                    <span class="td-mini-container">
                        {{ strlen( $dbError['error'] ) > 150 ? substr( $dbError['error'] , 0, 150).'...' :  $dbError['error'] }}
                    </span>
                    <span class="td-full-container hidden">
                        {{$dbError['error']}}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection
@section('scripts')

<script>
   //Expand Row
    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
</script>
@endsection