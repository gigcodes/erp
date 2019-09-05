
<div id="assetsCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('assets-manager.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Store a Assets Manager</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Asset Type:</strong>
            <select class="form-control" name="asset_type" id="asset_type">
                <option value="">Select</option>
                <option value="Hard" {{ Input::old('asset_type') == 'Hard'? 'selected' : '' }}>Hard</option>
                <option value="Soft" {{ Input::old('asset_type') == 'Soft'? 'selected' : '' }}>Soft</option>
            </select>
            @if ($errors->has('asset_type'))
              <div class="alert alert-danger">{{$errors->first('asset_type')}}</div>
            @endif
          </div>


          <div class="form-group">
            <strong>Category:</strong>
            <select class="form-control" name="category_id" id="category_id">
                <option value="">Select</option>
                @foreach($category as $cat)
                  <option value="{{$cat->id}}" {{ $cat->id == old('category_id') ? 'selected' : '' }}>{{$cat->cat_name}}</option>
                @endforeach
                <option value="-1" {{ old('category_id') == '-1'? 'selected' : '' }}>Other</option>
            </select>
            @if ($errors->has('category_id'))
              <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
            @endif
          </div>

          <div class="form-group othercat" style="display: none;" >
            <input type="text" name="other" class="form-control" value="{{ old('other') }}">            
          </div>

          <div class="form-group">
            <strong>Purchase Type:</strong>
            <select class="form-control" name="purchase_type" id="purchase_type">
                <option value="">Select</option>
                <option value="Owned" {{ old('purchase_type') == 'Owned'? 'selected' : '' }}>Owned</option>
                <option value="Rented" {{ old('purchase_type') == 'Rented'? 'selected' : '' }}>Rented</option>
                <option value="Subscription" {{ old('purchase_type') == 'Subscription'? 'selected' : '' }}>Subscription</option>
            </select>
            @if ($errors->has('purchase_type'))
              <div class="alert alert-danger">{{$errors->first('purchase_type')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Payment Cycle:</strong>
            <select class="form-control" name="payment_cycle" id="payment_cycle">
                <option value="">Select</option>
                <option value="Daily" {{ old('payment_cycle') == 'Daily'? 'selected' : '' }}>Daily</option>
                <option value="Weekly" {{ old('payment_cycle') == 'Weekly'? 'selected' : '' }}>Weekly</option>
                <option value="Bi-Weekly" {{ old('payment_cycle') == 'Bi-Weekly'? 'selected' : '' }}>Bi-Weekly</option>
                <option value="Monthly" {{ old('payment_cycle') == 'Monthly'? 'selected' : '' }}>Monthly</option>
                <option value="Yearly" {{ old('payment_cycle') == 'Yearly'? 'selected' : '' }}>Yearly</option>
                <option value="One time" {{ old('payment_cycle') == 'One time'? 'selected' : '' }}>One time</option>
            </select>
            @if ($errors->has('payment_cycle'))
              <div class="alert alert-danger">{{$errors->first('payment_cycle')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Amount:</strong>
            <input type="number" name="amount" class="form-control" value="{{ old('amount') }}">

            @if ($errors->has('amount'))
              <div class="alert alert-danger">{{$errors->first('amount')}}</div>
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

<div id="assetsEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Assets Manager</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" id="asset_name" required>

            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Asset Type:</strong>
            <select class="form-control" name="asset_type" id="asset_asset_type">
                <option value="">Select</option>
                <option value="Hard" {{ Input::old('asset_type') == 'Hard'? 'selected' : '' }}>Hard</option>
                <option value="Soft" {{ Input::old('asset_type') == 'Soft'? 'selected' : '' }}>Soft</option>
            </select>
            @if ($errors->has('asset_type'))
              <div class="alert alert-danger">{{$errors->first('asset_type')}}</div>
            @endif
          </div>


          <div class="form-group">
            <strong>Category:</strong>
            <select class="form-control" name="category_id" id="category_id2">
                <option value="">Select</option>
                @foreach($category as $cat)
                  <option value="{{$cat->id}}" {{ $cat->id == old('category_id') ? 'selected' : '' }}>{{$cat->cat_name}}</option>
                @endforeach
                <option value="-1" {{ old('category_id') == '-1'? 'selected' : '' }}>Other</option>
            </select>
            @if ($errors->has('category_id'))
              <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
            @endif
          </div>
          <div class="form-group othercatedit" style="display: none;" >
            <input type="text" name="other" class="form-control" value="{{ old('other') }}">            
          </div>
          <div class="form-group">
            <strong>Purchase Type:</strong>
            <select class="form-control" name="purchase_type" id="asset_purchase_type">
                <option value="">Select</option>
                <option value="Owned" {{ old('purchase_type') == 'Owned'? 'selected' : '' }}>Owned</option>
                <option value="Rented" {{ old('purchase_type') == 'Rented'? 'selected' : '' }}>Rented</option>
                <option value="Subscription" {{ old('purchase_type') == 'Subscription'? 'selected' : '' }}>Subscription</option>
            </select>
            @if ($errors->has('purchase_type'))
              <div class="alert alert-danger">{{$errors->first('purchase_type')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Payment Cycle:</strong>
            <select class="form-control" name="payment_cycle" id="asset_payment_cycle">
                <option value="">Select</option>
                <option value="Daily" {{ old('payment_cycle') == 'Daily'? 'selected' : '' }}>Daily</option>
                <option value="Weekly" {{ old('payment_cycle') == 'Weekly'? 'selected' : '' }}>Weekly</option>
                <option value="Bi-Weekly" {{ old('payment_cycle') == 'Bi-Weekly'? 'selected' : '' }}>Bi-Weekly</option>
                <option value="Monthly" {{ old('payment_cycle') == 'Monthly'? 'selected' : '' }}>Monthly</option>
                <option value="Yearly" {{ old('payment_cycle') == 'Yearly'? 'selected' : '' }}>Yearly</option>
                <option value="One time" {{ old('payment_cycle') == 'One time'? 'selected' : '' }}>One time</option>
            </select>
            @if ($errors->has('payment_cycle'))
              <div class="alert alert-danger">{{$errors->first('payment_cycle')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Amount:</strong>
            <input type="number" name="amount" id="asset_amount" class="form-control" value="{{ old('amount') }}">

            @if ($errors->has('amount'))
              <div class="alert alert-danger">{{$errors->first('amount')}}</div>
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

