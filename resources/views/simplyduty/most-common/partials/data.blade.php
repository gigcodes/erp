 @if($products->isEmpty())

            <tr>
                <td>
                    No Result Found
                </td>
            </tr>
@else

@foreach ($products as $product)
    @php
        $categoryTree = \App\Http\Controllers\CategoryController::getCategoryTree($product['category']);
            if(is_array($categoryTree)){
                $childCategory = implode(' > ',$categoryTree);
            }

            $parentCategory = \App\Category::find($product['category']);
            $name = $childCategory.' > '.$parentCategory->title;
        @endphp

    <tr id="category{{ $product['category'] }}">
        <td><input type="checkbox" class="form-control checkBoxClass" name="composition" data-name="{{ $product['composition'] }} {{ $name }}" data-category="{{ $product['category'] }}"></td>
        <td>{{ $name }}</td>
        <td>{{ $product['total'] }}</td>
        <td>{{ $product['composition'] }}</td>
    </tr>   
@endforeach
@endif