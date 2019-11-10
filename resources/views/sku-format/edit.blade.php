@foreach ($skus as $sku)
  <div id="skuEditModal{{ $sku->id }}" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('sku.update') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Edit SKU Format</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" class="form-control" value="{{ $sku->name }}">

                            @if ($errors->has('name'))
                                <div class="alert alert-danger">{{$errors->first('name')}}</div>
                            @endif
                        </div>
                        <input type="hidden" name="id" value="{{ $sku->id }}">
                        <div class="form-group">
                            <strong>Sku Format:</strong>
                            <input type="text" name="sku_format" class="form-control" value="{{ $sku->sku_format }}">

                            @if ($errors->has('sku_format'))
                                <div class="alert alert-danger">{{$errors->first('sku_format')}}</div>
                            @endif
                        </div>
                        <div class="form-group users">
                            <select class="form-control" name="category_id">
                                @foreach($categories as $category)
                                    <option class="form-control" value="{{ $category->id }}" @if($category->id == $sku->category_id) selected @endif>{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group users">
                            <select class="form-control" name="brand_id">
                                @foreach($brands as $brand)
                                    <option class="form-control" value="{{ $brand->id }}" @if($brand->id == $sku->brand_id) selected @endif>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Edit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>  
@endforeach





