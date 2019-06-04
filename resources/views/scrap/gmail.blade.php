@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Gmail Data</h2>
        </div>
        <div class="col-md-12">
            <table class="table table-striped">
                <tr>
                    <th>SN</th>
                    <th>Page Url</th>
                    <th>Sender</th>
                    <th>Date Sent</th>
                    <th>Date Sent</th>
                    <th>Images</th>
                    <th>tags</th>
                </tr>
                @foreach($data as $key=>$datum)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $datum->post_url }}</td>
                        <td>
                            @foreach($datum->images as $image)
                                <a href="{{ $image }}">
                                    <img src="{{ $image }}" alt="" style="width: 150px;">
                                </a>
                            @endforeach
                        </td>
                        <td>
                            @foreach($datum->tags as $tag)
                                <li>{{ $tag }}</li>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>

    </script>
@endsection
