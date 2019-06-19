<div id="vendorCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('vendor.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Store a Vendor</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Category:</strong>
            <select class="form-control" name="category_id">
              <option value="">Select a Category</option>

              @foreach ($vendor_categories as $category)
                <option value="{{ $category->id }}" {{ $category->id == old('category_id') ? 'selected' : '' }}>{{ $category->title }}</option>
              @endforeach
            </select>

            @if ($errors->has('category_id'))
              <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
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
            <strong>Website:</strong>
            <input type="text" name="website" class="form-control" value="{{ old('website') }}">

            @if ($errors->has('website'))
              <div class="alert alert-danger">{{$errors->first('website')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Login:</strong>
            <input type="text" name="login" class="form-control" value="{{ old('login') }}">

            @if ($errors->has('login'))
              <div class="alert alert-danger">{{$errors->first('login')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Password:</strong>
            <input type="password" name="password" class="form-control" value="{{ old('password') }}">

            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
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
            <strong>Account Name:</strong>
            <input type="text" name="account_name" class="form-control" value="{{ old('account_name') }}">

            @if ($errors->has('account_name'))
              <div class="alert alert-danger">{{$errors->first('account_name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>IBAN:</strong>
            <input type="text" name="account_iban" class="form-control" value="{{ old('account_iban') }}">

            @if ($errors->has('account_iban'))
              <div class="alert alert-danger">{{$errors->first('account_iban')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>SWIFT:</strong>
            <input type="text" name="account_swift" class="form-control" value="{{ old('account_swift') }}">

            @if ($errors->has('account_swift'))
              <div class="alert alert-danger">{{$errors->first('account_swift')}}</div>
            @endif
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

<div id="vendorEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Vendor</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Category:</strong>
            <select class="form-control" name="category_id" id="vendor_category">
              <option value="">Select a Category</option>

              @foreach ($vendor_categories as $category)
                <option value="{{ $category->id }}" {{ $category->id == old('category_id') ? 'selected' : '' }}>{{ $category->title }}</option>
              @endforeach
            </select>

            @if ($errors->has('category_id'))
              <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required id="vendor_name">

            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Address:</strong>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}" id="vendor_address">

            @if ($errors->has('address'))
              <div class="alert alert-danger">{{$errors->first('address')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Phone:</strong>
            <input type="number" name="phone" class="form-control" value="{{ old('phone') }}" id="vendor_phone">

            @if ($errors->has('phone'))
              <div class="alert alert-danger">{{$errors->first('phone')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Email:</strong>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="vendor_email">

            @if ($errors->has('email'))
              <div class="alert alert-danger">{{$errors->first('email')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Social Handle:</strong>
            <input type="text" name="social_handle" class="form-control" value="{{ old('social_handle') }}" id="vendor_social_handle">

            @if ($errors->has('social_handle'))
              <div class="alert alert-danger">{{$errors->first('social_handle')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Website:</strong>
            <input type="text" name="website" class="form-control" value="{{ old('website') }}" id="vendor_website">

            @if ($errors->has('website'))
              <div class="alert alert-danger">{{$errors->first('website')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Login:</strong>
            <input type="text" name="login" class="form-control" value="{{ old('login') }}" id="vendor_login">

            @if ($errors->has('login'))
              <div class="alert alert-danger">{{$errors->first('login')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Password:</strong>
            <input type="password" name="password" class="form-control" value="{{ old('password') }}" id="vendor_password">

            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>GST:</strong>
            <input type="text" name="gst" class="form-control" value="{{ old('gst') }}" id="vendor_gst">

            @if ($errors->has('gst'))
              <div class="alert alert-danger">{{$errors->first('gst')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Account Name:</strong>
            <input type="text" name="account_name" class="form-control" value="{{ old('account_name') }}" id="vendor_account_name">

            @if ($errors->has('account_name'))
              <div class="alert alert-danger">{{$errors->first('account_name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>IBAN:</strong>
            <input type="text" name="account_iban" class="form-control" value="{{ old('account_iban') }}" id="vendor_account_iban">

            @if ($errors->has('account_iban'))
              <div class="alert alert-danger">{{$errors->first('account_iban')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>SWIFT:</strong>
            <input type="text" name="account_swift" class="form-control" value="{{ old('account_swift') }}" id="vendor_account_swift">

            @if ($errors->has('account_swift'))
              <div class="alert alert-danger">{{$errors->first('account_swift')}}</div>
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
