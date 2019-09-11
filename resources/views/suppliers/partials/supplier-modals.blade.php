<div id="supplierCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('supplier.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Store a Supplier</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">

          
          <div class="form-group">
              <strong>Category:</strong>
              <select name="supplier_category_id" class="form-control">
                <option value="">Select Category</option>
                @foreach($suppliercategory as $category)
                  <option value="{{$category->id}}" {{ $category->id == old('supplier_category_id') ? 'selected' : '' }}>{{$category->name}}</option>
                @endforeach
              </select>
          </div>        

          <div class="form-group">
            <strong>Supplier:</strong>
            <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}" required>

            @if ($errors->has('supplier'))
              <div class="alert alert-danger">{{$errors->first('supplier')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Address:</strong>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}">

            @if ($errors->has('address'))
              <div class="alert alert-danger">{{$errors->first('address')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Phone:</strong>
            <input type="number" name="phone" class="form-control" value="{{ old('phone') }}">

            @if ($errors->has('phone'))
              <div class="alert alert-danger">{{$errors->first('phone')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Email:</strong>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">

            @if ($errors->has('email'))
              <div class="alert alert-danger">{{$errors->first('email')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Social Handle:</strong>
            <input type="text" name="social_handle" class="form-control" value="{{ old('social_handle') }}">

            @if ($errors->has('social_handle'))
              <div class="alert alert-danger">{{$errors->first('social_handle')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Scraper Name:</strong>
            <input type="text" name="scraper_name" class="form-control" value="{{ old('scraper_name') }}" required>

            @if ($errors->has('scraper_name'))
              <div class="alert alert-danger">{{$errors->first('scraper_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Inventory Lifetime:</strong>
            <input type="number" name="inventory_lifetime" class="form-control" value="{{ old('inventory_lifetime') }}" required>

            @if ($errors->has('inventory_lifetime'))
              <div class="alert alert-danger">{{$errors->first('inventory_lifetime')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>GST:</strong>
            <input type="text" name="gst" class="form-control" value="{{ old('gst') }}">

            @if ($errors->has('gst'))
              <div class="alert alert-danger">{{$errors->first('gst')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Status:</strong>
            <select name="supplier_status_id" class="form-control">
              <option value="">Select Status</option>
              @foreach($supplierstatus as $status)
                <option value="{{$status->id}}" {{ $status->id == old('supplier_status_id') ? 'selected' : '' }}>{{$status->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Add</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="supplierEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Supplier</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <strong>Category:</strong>
            <select name="supplier_category_id" id="supplier_category_id" class="form-control">
              <option value="">Select Category</option>
              @foreach($suppliercategory as $category)
                <option value="{{$category->id}}" {{ $category->id == old('supplier_category_id') ? 'selected' : '' }}>{{$category->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <strong>Supplier:</strong>
            <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}" required id="supplier_supplier">

            @if ($errors->has('supplier'))
              <div class="alert alert-danger">{{$errors->first('supplier')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Address:</strong>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}" id="supplier_address">

            @if ($errors->has('address'))
              <div class="alert alert-danger">{{$errors->first('address')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Phone:</strong>
            <input type="number" name="phone" class="form-control" value="{{ old('phone') }}" id="supplier_phone">

            @if ($errors->has('phone'))
              <div class="alert alert-danger">{{$errors->first('phone')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Email:</strong>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="supplier_email">

            @if ($errors->has('email'))
              <div class="alert alert-danger">{{$errors->first('email')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Social Handle:</strong>
            <input type="text" name="social_handle" class="form-control" value="{{ old('social_handle') }}" id="supplier_social_handle">

            @if ($errors->has('social_handle'))
              <div class="alert alert-danger">{{$errors->first('social_handle')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Scraper Name:</strong>
            <input type="text" name="scraper_name" id="supplier_scraper_name" class="form-control" value="{{ old('scraper_name') }}" required>

            @if ($errors->has('scraper_name'))
              <div class="alert alert-danger">{{$errors->first('scraper_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Inventory Lifetime:</strong>
            <input type="number" name="inventory_lifetime" id="supplier_inventory_lifetime" class="form-control" value="{{ old('inventory_lifetime') }}" required>

            @if ($errors->has('inventory_lifetime'))
              <div class="alert alert-danger">{{$errors->first('inventory_lifetime')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>GST:</strong>
            <input type="text" name="gst" class="form-control" value="{{ old('gst') }}" id="supplier_gst">

            @if ($errors->has('gst'))
              <div class="alert alert-danger">{{$errors->first('gst')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Status:</strong>
            <select name="supplier_status_id" id="supplier_status_id" class="form-control">
              <option value="">Select Status</option>
              @foreach($supplierstatus as $status)
                <option value="{{$status->id}}" {{ $status->id == old('supplier_status_id') ? 'selected' : '' }}>{{$status->name}}</option>
              @endforeach
            </select>
          </div>
          <!-- <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
              <option {{ old('status') == 0 ? 'selected' : '' }} value="0">Inactive</option>
              <option {{ old('status') == 1 ? 'selected' : '' }} value="1">Active</option>
            </select>
          </div> -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Update</button>
        </div>
      </form>
    </div>

  </div>
</div>
