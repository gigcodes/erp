@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Accounts</h2>
        </div>
    </div>
    <div class="row">
        <div class="p-5" style="background: #dddddd">
            <form action="{{ action('InstagramController@store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_name">Full name</label>
                            <input class="form-control" type="text" id="first_name" name="first_name" placeholder="Full name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_name">Instagram Username</label>
                            <input class="form-control" type="text" id="last_name" name="last_name" placeholder="Username">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" name="password" id="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="checkbox" id="broadcast" name="broadcast">
                            <label for="broadcast">Direct Messaging</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="checkbox" id="manual_comments" name="manual_comments">
                            <label for="manual_comments">Manual Comments</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="checkbox" id="bulk_comments" name="bulk_comments">
                            <label for="bulk_comments">Bulk Comments</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="email">Phone/Email</label>
                        <input class="form-control" type="text" name="email" id="email" placeholder="Email/phone">
                    </div>
                    <div class="col-md-4">
                        <label for="country">Country</label>
                        <select class="form-control" name="country" id="country">
                            <option value="">All</option>
                            @foreach($countries as $country)
                                <option value="{{$country->region}}">{{$country->region}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 text-right">
                        <button class="btn btn-primary">Add Account</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12 mt-5">
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>I.D</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Email/Phone</th>
                        <th>Messages</th>
                        <th>Manual Commenting</th>
                        <th>Bulk Commenting Commenting</th>
                        <th>Created At</th>
                        <th style="width: 100%">Action</th>
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
                            <td>{{ $account->broadcast ? 'YES' : 'NO' }}</td>
                            <td>{{ $account->manual_comment ? 'YES' : 'NO' }}</td>
                            <td>{{ $account->bulk_comment ? 'YES' : 'NO' }}</td>
                            <td>{{ $account->created_at }}</td>
                            <td style="width: 100px;">
{{--                                <button class="btn btn-danger btn-sm">--}}
{{--                                    <i class="fa fa-trash"></i>--}}
{{--                                </button>--}}
                                <div style="width: 100%">
                                    <a class="btn btn-image" href="{{ action('AccountController@test', $account->id) }}">
                                        <i class="fa fa-check"></i>
                                    </a>
                                    <a href="{{ action('InstagramController@edit', $account->id) }}" class="btn btn-image">
                                        <img src="{{ asset('images/edit.png') }}" alt="Edit User" title="Edit Product">
                                    </a>
                                    <a href="{{ action('InstagramController@deleteAccount', $account->id) }}" class="btn btn-image">
                                        <img src="{{ asset('images/delete.png') }}" alt="Delete User" title="Delete Product">
                                    </a>
                                </div>
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