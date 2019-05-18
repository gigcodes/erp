@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Sitejabber Accounts</h2>
        </div>
        <div class="col-md-12 mb-5">
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Name</th>
                        <th>E-Mail Address</th>
                        <th>Password</th>
                        <th>Created On</th>
                        <th>Reviews Posted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $key=>$sj)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $sj->first_name ?? 'N/A' }} {{ $sj->first_name ?? 'N/A' }}</td>
                            <td>{{ $sj->email }}</td>
                            <td>{{ $sj->password }}</td>
                            <td>{{ $sj->created_at->diffForHumans() }}</td>
                            <td>
                                @if ($sj->reviews()->count())
                                    <div class="card">
                                        <div class="card-body">
                                            @foreach($sj->reviews as $answer)
                                                <li>
                                                    {{ $answer->review }}
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
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
@endsection