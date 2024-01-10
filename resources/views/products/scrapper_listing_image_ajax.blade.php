<div class="row p-0 m-0">
    @foreach ($products as $key => $product)
        <div data-interval="false" id="carousel_{{ $product->id }}" class="carousel slide" data-ride="carousel">
           <div class="carousel-inner maincarousel">
                <div class="item" style="display: block;">
                    <!-- {{ $product->id }}  -->
                    <img src="{{asset( 'scrappersImages/'.$product->img_name)}}" style="height: 150px; width: 150px;display: block;margin-left: auto;margin-right: auto;"> 
                </div>
            </div>
        </div>
    @endforeach    
</div>
<?php echo $products->appends(request()->except("page"))->links(); ?>