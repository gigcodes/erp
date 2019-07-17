@extends('layouts.app')

@section('content')
    <table class="table table-striped table-bordered">
        <tr>
            <th>Image</th>
            <th>Color</th>
            <th>ERP Color</th>
        </tr>
        @foreach($pictures as $picture)
            <td><img src="{{ $picture->image_url }}" alt="" style="width: 250px;"></td>
            <td style="background-color: #{{$picture->picked_color}}"></td>
            <td>{{ $picture->color }}</td>
        @endforeach
    </table>
@endsection