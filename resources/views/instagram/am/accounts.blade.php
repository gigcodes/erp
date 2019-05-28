@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Comments On #sololuxury</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>I.D</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Email/Phone</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $key=>$account)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $account->first_name }}</td>
                            <td>{{ $account->last_name }}</td>
                            <td>{{ $account->password }}</td>
                            <td>{{ $account->email }}</td>
                            <td>{{ $account->created_at }}</td>
                            <td>
{{--                                <button class="btn btn-danger btn-sm">--}}
{{--                                    <i class="fa fa-trash"></i>--}}
{{--                                </button>--}}
                                <a class="btn btn-success" href="{{ action('AccountController@test', $account->id) }}">
                                    <i class="fa fa-check"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <style>
        thead input {
            width: 100%;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#table thead tr').clone(true).appendTo( '#table thead' );
            $('#table thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
            var table = $('#table').dataTable({
                orderCellsTop: true,
                fixedHeader: true
            });
        });
    </script>
    @if (Session::has('message'))
        <script>
            toastr["success"]("{{ Session::get('message') }}", "Message")
        </script>
    @endif
@endsection