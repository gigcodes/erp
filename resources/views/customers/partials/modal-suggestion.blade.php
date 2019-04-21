<div id="suggestionModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('customer.send.suggestion') }}" method="POST">
        @csrf
        <input type="hidden" name="customer_id" value="{{ $customer->id }}">

        <div class="modal-header">
          <h4 class="modal-title">Send Suggestion</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Brands:</strong>
            <select class="form-control select-multiple" name="brand[]" multiple>
              @foreach ($brands as $brand)
               <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
             @endforeach
           </select>

            @if ($errors->has('brand'))
              <div class="alert alert-danger">{{$errors->first('brand')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Categories:</strong>
            {!! $category_suggestion !!}

            @if ($errors->has('category'))
              <div class="alert alert-danger">{{$errors->first('category')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Sizes:</strong>
            <select class="form-control select-multiple" name="size[]" id="size_selection" multiple>
              {{-- @foreach ($brands as $brand)
               <option value="{{ $brand->id }}">{{ $brand->name }}</option>
             @endforeach --}}
           </select>

            @if ($errors->has('size'))
              <div class="alert alert-danger">{{$errors->first('size')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Suppliers:</strong>
            <select class="form-control select-multiple" name="supplier[]" multiple>
              @foreach ($suppliers as $supplier)
               <option value="{{ $supplier->id }}">{{ $supplier->supplier }}</option>
             @endforeach
           </select>

            @if ($errors->has('supplier'))
              <div class="alert alert-danger">{{$errors->first('supplier')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Number of Images:</strong>
            <input type="number" class="form-control" name="number" min="0" value="5" required>

            @if ($errors->has('number'))
              <div class="alert alert-danger">{{$errors->first('number')}}</div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Send Suggestion</button>
        </div>
      </form>
    </div>

  </div>
</div>
