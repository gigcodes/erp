@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Dubbizle Posts</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>URL</th>
                        <th>Keywords</th>
                        <th>Requirement</th>
                        <th>Body</th>
                        <th>Post</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $key=>$post)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td style="word-break: break-word !important; word-wrap: break-word !important;">
                                <a href="{{ $post->url }}">Visit</a>
                            </td>
                            <td style="word-break: break-all !important; word-wrap: break-word !important;">{{ $post->keywords }}</td>
                            <td style="word-break: break-all !important; word-wrap: break-word !important;">{{ $post->requirements }}</td>
                            <td style="word-break: break-all !important; word-wrap: break-word !important;">{{ $post->body }}</td>
                            <td>{{ $post->post_date }}</td>
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