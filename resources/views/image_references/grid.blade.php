@extends('layouts.app')


@section('content')
 <div class="row">
        <div class="col-md-12">
            <h1 class="text-center">Crop Refernce Grid ({{ count($products) }})</h1>
        </div>


        {!! $products->links() !!}
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table">
                    <tr>
                        <th>ID</th>
                        <th>Original Image</th>
                        <th>Cropped Image</th>
                        <th>Time</th>
                        <th>Date</th>
                    </tr>
                    @foreach($products as $product)
                        <tr>

                            <td><input type="checkbox" name="">
                                {{ $product->id }}</td>
                            <td> <img src="{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}" alt="" height="150" width="150" onmouseover="bigImg('{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}')"></td>
                            <td> <img src="{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}" alt="" height="150" width="150" onmouseover="bigImg('{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}')"></td>
                            <td>{{ $product->speed }}</td>
                            <td>{{ $product->updated_at->format('d-m-Y : H:i:s') }}</td>
                           
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        
    </div>
   
@endsection

@section('scripts')
<script type="text/javascript">
        
    function bigImg(img){
        
    }

</script>

@endsection