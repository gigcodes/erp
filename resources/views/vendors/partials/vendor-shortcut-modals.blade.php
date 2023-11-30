<div id="vendorShortcutCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ url('vendors/storeshortcut') }}" method="POST" id="createVendorForm">
        @csrf

        <input type="hidden" id="vendor_organization_id" name="organization_id">
        <div class="modal-header">
          <h4 class="modal-title">Store a Vendor</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="modal-body">
              <div class="col-md-6">
                <div class="form-group">
                    {{ Form::select("category_id", \App\VendorCategory::pluck('title','id')->toArray(), request('category_id'), ["class" => "form-control", "placeholder" => "Category"]) }}
                  @if ($errors->has('category_id'))
                  <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <select class="form-control" name="type" placeholder="Type:">
                    <option value="">Select a Type</option>
                    <option value="Freelancer">Freelancer</option>
                    <option value="Agency">Agency</option>
                  </select>
                  @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('type')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="name" class="form-control" placeholder="Name:" value="{{ old('name') }}" required>
                  @if ($errors->has('name'))
                  <div class="alert alert-danger">{{$errors->first('name')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="number" name="phone" class="form-control" placeholder="Phone:" value="{{ old('phone') }}">
                  @if ($errors->has('phone'))
                  <div class="alert alert-danger">{{$errors->first('phone')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="email" name="email" class="form-control" placeholder="Email:" value="{{ old('email') }}"> @if ($errors->has('email'))
                  <div class="alert alert-danger">{{$errors->first('email')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="email" name="gmail" class="form-control" placeholder="Gmail:" value="{{ old('gmail') }}"> @if ($errors->has('gmail'))
                  <div class="alert alert-danger">{{$errors->first('gmail')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="website" class="form-control" placeholder="Website:" value="{{ old('website') }}">
                  @if ($errors->has('website'))
                  <div class="alert alert-danger">{{$errors->first('website')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="url" class="form-control" placeholder="URL:" value="{{ old('url') }}">
                  @if ($errors->has('url'))
                  <div class="alert alert-danger">{{$errors->first('url')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-12">
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-secondary">Add</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>