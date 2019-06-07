<ul>
    @foreach($childs as $child)
        <li>
            {{ $child->title }} ({{\App\Product::where('category', $child->id)->count()}})
            @if(count($child->childs))
                @include('category.manageChild',['childs' => $child->childs])
            @endif
        </li>
    @endforeach
</ul>