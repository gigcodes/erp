@foreach ($product_translations as $key => $product)
    <tr>
        <td>{{ $product->product_id }}</td>

        <td>
            <button type="button" class="btn-link quick-edit-description__"
                    data-id="{{ $product->id }}" data-target="#product_image_{{ $product->id }}" data-toggle="modal">View
            </button>
        </td>

        <td>{{strtoupper($product->locale)}}</td>

        <td>{{$product->title}}</td>

        <td>{{$product->description}}</td>

        <td>{{ $product->site ?  $product->site->title : '-'}}</td>

        <td>{{$product->created_at->format('d M Y')}}</td>

        <td>
{{--            <a class="btn btn-image view-btn" data-toggle="modal" data-target="#translationModal" data-id="{{$product->id}}"><img src="/images/view.png"/></a>--}}
            <i style="cursor: pointer; " class="fa fa-eye" data-toggle="modal" data-target="#translationModal" data-id="{{$product->id}}" aria-hidden="true"></i>
            <i style="cursor: pointer; " class="fa fa-history" data-toggle="modal" data-target="#showHistory_{{ $product->id }}" data-id="{{$product->id}}" aria-hidden="true"></i>
            <i style="cursor: pointer; " class="fa fa-{{ $product->is_rejected == 1 ? '' : 'close' }} rejectProduct" data-toggle="modal" data-action="{{ route('product.translation.rejection') }}" data-value="{{$product->is_rejected}}" data-id="{{$product->id}}" aria-hidden="true"></i>
        </td>
    </tr>
@endforeach


