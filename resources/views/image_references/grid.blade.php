@extends('layouts.app')


@section('content')
 <div class="row">
        <div class="col-md-12">
            <h1 class="text-center">Crop Refernce Grid ({{ count($products) }})</h1>
            <div class="pull-right">
                 <button onclick="addTask()" class="btn btn-secondary">Add Issue</button>
                 
            </div>
        </div>
         
       

        {!! $products->links() !!}
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table">
                    <tr>
                        <th>ID <input type="checkbox" name="" id="globalCheckbox"></th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Brand</th>
                        <th>Original Image</th>
                        <th>Cropped Image</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Issue</th>
                    </tr>
                    @foreach($products as $product)
                        <tr>

                            <td><input type="checkbox" name="issue" value="{{ $product->id }}" class="checkBox">
                                {{ $product->id }}</td>
                            <td>@if($product->newMedia) {{ $product->productCategory($product->newMedia->id) }} @endif</td> 
                            <td>@if($product->newMedia) {{ $product->productSupplier($product->newMedia->id) }} @endif</td> 
                            <td>@if($product->newMedia) {{ $product->productBrand($product->newMedia->id) }} @endif</td>    
                            <td> <img src="{{ $product->media ? $product->media->getUrl() : '' }}" alt="" height="150" width="150" onmouseover="bigImg('{{ $product->media ? $product->media->getUrl() : '' }}')"></td>
                            <td> <img src="{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}" alt="" height="150" width="150" onmouseover="bigImg('{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}')"></td>
                            <td>{{ (int)str_replace('0:00:','',$product->speed) }} sec</td>
                            <td>{{ $product->updated_at->format('d-m-Y : H:i:s') }}</td>
                            <td>@if($product->newMedia) {{ $product->productStatus($product->newMedia->id) }} @endif</td>
                            <td>{{ $product->getProductIssueStatus($product->id) }}</td>
                           
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        
    </div>
 @include('partials.modals.task-module')
 @include('partials.modals.large-image-modal')
   
@endsection

@section('scripts')
<script type="text/javascript">
        
    function bigImg(img){
        $('#image_crop').attr('src',img);
        $('#largeImageModal').modal('show');
    }

    function addTask() {
       var id = [];
            $.each($("input[name='issue']:checked"), function(){
                id.push($(this).val());
            });
        if(id.length == 0){
            alert('Please Select Image');
        }else{
            $('#taskModal').modal('show');
            $('#task_subject').val('Image ID '+id);
        }   
        
    }

    $('#globalCheckbox').click(function(){
            if($(this).prop("checked")) {
                $(".checkBox").prop("checked", true);
            } else {
                $(".checkBox").prop("checked", false);
            }                
        });

</script>

@endsection