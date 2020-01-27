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

    <tr>
        <input type="hidden" id="childCategory" value="{{ $name }}">
        <td><input type="checkbox" class="form-control checkBoxClass" value="{{ $product['composition'] }} {{ $childCategory }} {{ $parentCategory->title }}" name="composition"></td>
        <td>
        {{ $name }}</td>
        <td>{{ $product['total'] }}</td>
        <td>{{ $product['composition'] }}</td>
    </tr>   
@endforeach
@endif