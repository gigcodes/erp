@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scrap Links</h2>
        </div>

        <div class="table-responsive mt-3 col-lg-12 margin-tb">
            <table class="table table-bordered table-striped sort-priority-scrapper">
                <thead>
                    <tr>
                        <th width="5%">Id</th>
                        <th width="10%">Website</th>
                        <th>Link</th>
                        <th width="10%">Status</th>
                        <th width="10%">Created at</th>
                    </tr>
                </thead>
                <tbody class="conent">
                    @foreach ($scrap_links as $links)
                        <tr>
                            <td>{{ $links->id }}</td>
                            <td>{{ $links->website }}</td>
                            <td>{{ $links->links }}</td>
                            <td>{{ $links->status }}</td>
                            <td>{{ $links->created_at }}</td>
                        </tr>
                    @endforeach
               </tbody>

            </table>
            {{$scrap_links->links()}}
        </div>
    </div>
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection
@section('scripts')
@endsection