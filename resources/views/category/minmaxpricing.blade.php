@extends('layouts.app')

@section('content')
    @php
        // Get all categories
        $categories = \App\Category::orderBy('parent_id', 'asc')->all();

        // Get all brands
        $brands = \App\Brand::all();
    @endphp
    <table class="table table-hover-cell">
        <tr>
            <th>Category</th>
            <th>Brand</th>
            <th>Minimum Price</th>
            <th>Maximum Price</th>
        </tr>
        @foreach ( $categories as $category )
            @foreach ( $brands as $brand )
                <tr>
                    <td>{{ $category->title }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>{{ \App\Product::where('category', $category->id)->where('brand', $brand->id)->min('price') }}</td>
                    <td>{{ \App\Product::where('category', $category->id)->where('brand', $brand->id)->max('price') }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>
@endsection
