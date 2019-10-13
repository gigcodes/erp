<div id="productGroup" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Product</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('quicksell.save.group') }}" method="POST">


                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <strong>Quick Sell Product:</strong>
                        @php
                        $products = \App\Product::where('quick_product',1)->get();
                        @endphp
                        <select class="form-control" name="products[]" multiple>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>



<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Quick Product</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" id="updateForm">
                @csrf

                <div class="modal-body text-left">
                    <div class="form-group">
                        <input type="file" name="images[]" multiple />
                        @if ($errors->has('images'))
                            <div class="alert alert-danger">{{$errors->first('images')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        @php $supplier_list = (new \App\ReadOnly\SupplierList)->all();
                        @endphp
                        <select class="form-control" name="supplier" id="supplier_select">
                            <option value="">Select Supplier</option>
                            @foreach ($supplier_list as $index => $value)
                                <option value="{{ $index }}" {{ $index == old('supplier') ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('supplier'))
                            <div class="alert alert-danger">{{$errors->first('supplier')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Price:</strong>
                        <input type="number" name="price" class="form-control" id="price_field" />
                        @if ($errors->has('price'))
                            <div class="alert alert-danger">{{$errors->first('price')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Special Price (INR):</strong>

                        <input type="number" class="form-control" name="price_special" value="" id="price_special_field">

                        @if ($errors->has('price_special'))
                            <div class="alert alert-danger">{{$errors->first('price_special')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Size:</strong>
                        <input type="text" name="size" class="form-control" id="size_field" />
                        @if ($errors->has('size'))
                            <div class="alert alert-danger">{{$errors->first('size')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Brand:</strong>
                        <select name="brand" class="form-control" id="brand_field">
                            <option value="">Select Brand</option>
                            @foreach ($brands as $id => $brand)
                                <option value="{{ $id }}">{{ $brand }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('brand'))
                            <div class="alert alert-danger">{{$errors->first('brand')}}</div>
                        @endif
                    </div>

                    @if (Auth::user()->hasRole('Admin'))
                        <div class="form-group">
                            <strong>Location:</strong>
                            <select name="location" class="form-control" id="location_field">
                                <option value="">Select a Location</option>
                                @foreach ($locations as $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('location'))
                                <div class="alert alert-danger">{{$errors->first('location')}}</div>
                            @endif
                        </div>
                    @endif

                    <div class="form-group">
                        <strong>Category:</strong>
                        {!! $category_selection !!}
                        @if ($errors->has('category'))
                            <div class="alert alert-danger">{{$errors->first('category')}}</div>
                        @endif
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Update</button>
                </div>
            </form>
        </div>

    </div>
</div>
