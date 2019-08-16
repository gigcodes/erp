@extends('layouts.app')

@section('content')
    @php
        // Get all data
        $results = \Illuminate\Support\Facades\DB::select('SELECT categories.title, brands.name, MIN(price*1) AS minimumPrice, MAX(price*1) AS maximumPrice FROM products JOIN categories ON products.category=categories.id JOIN brands ON products.brand=brands.id GROUP BY products.category, products.brand ORDER BY brands.name, categories.title');
    @endphp
    <table class="table table-striped">
        <tr>
            <th>Brand</th>
            <th>Category</th>
            <th>Minimum Price</th>
            <th>Maximum Price</th>
        </tr>
        @foreach ( $results as $result )
            <tr>
                <td>{{ $result->name }}</td>
                <td>{{ $result->title }}</td>
                <td>{{ $result->minimumPrice }}</td>
                <td>{{ $result->maximumPrice }}</td>
            </tr>
        @endforeach
    </table>
@endsection
