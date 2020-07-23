@foreach ($product_translations as $key => $product)
                <tr>
                    <td>{{ $product->product_id }}</td>
                    
                    <td>{{ $product->title }}</td>
                    <td>
                        {{$product->description}}
                    </td>
                    <td>
                    <a class="btn btn-image view-btn" data-toggle="modal" data-target="#translationModal" data-id="{{$product->id}}"><img src="/images/view.png"/></a>
                    </td>
                </tr>
            @endforeach