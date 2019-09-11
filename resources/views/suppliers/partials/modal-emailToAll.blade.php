<div id="emailToAllModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send Email to Multiple Suppliers</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="{{ route('supplier.email.send.bulk') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-body">
          <div class="form-group">
            <strong>Category</strong>
            <select name="supplier_category_id" id="supplier_category_id2" class="form-control">
              <option value="">Select Category</option>
              @foreach($suppliercategory as $category)
                <option value="{{$category->id}}">{{$category->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group"> 
            <strong>Status</strong>             
            <select name="supplier_status_id" id="supplier_status_id2" class="form-control">
              <option value="">Select Status</option>
              @foreach($supplierstatus as $status)
                <option value="{{$status->id}}">{{$status->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            Filter By:&nbsp;&nbsp;
            <a href="javascript:void(0)" onclick="filtersupplier('');">All</a>&nbsp;&nbsp;
            @foreach (range('A', 'Z') as $char) 
                <a href="javascript:void(0)" onclick="filtersupplier('{{$char}}');">{{$char}}</a>&nbsp;&nbsp;
            @endforeach
            @for($i=0; $i<10; $i++)
            <a href="javascript:void(0)" onclick="filtersupplier('{{$i}}');">{{$i}}</a>&nbsp;&nbsp;
            @endfor
          </div>
          <div class="form-group" style="display: none;" id="suppliers-selection">
            <strong>Suppliers</strong> &nbsp;&nbsp;<a href="javascript:void(0);" id="select_all">Select All</a> &nbsp;&nbsp;<a href="javascript:void(0);" id="select_no">Unselect All</a> 
            <select class="form-control select-multiple" id="suppliers" name="suppliers[]" multiple>                  
             </select>
          </div>

            <div class="form-group text-right">
                <a class="add-cc mr-3" href="#">Cc</a>
                <a class="add-bcc" href="#">Bcc</a>
            </div>

            <div id="cc-label" class="form-group" style="display:none;">
                <strong class="mr-3">Cc</strong>
                <a href="#" class="add-cc">+</a>
            </div>

            <div id="cc-list" class="form-group">

            </div>

            <div id="bcc-label" class="form-group" style="display:none;">
                <strong class="mr-3">Bcc</strong>
                <a href="#" class="add-bcc">+</a>
            </div>

            <div id="bcc-list" class="form-group">

            </div>

          <div class="form-group">
            <input type="checkbox" name="not_received" id="notReceived">
            <label for="notReceived">Send to all who haven't received an email</label>
          </div>

          <div class="form-group">
            <input type="checkbox" name="received" id="received">
            <label for="received">Send to all who haven't replied to an email</label>
          </div>

          <div class="form-group">
            <strong>Subject *</strong>
            <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required>
          </div>

          <div class="form-group">
            <strong>Message *</strong>
            <textarea name="message" class="form-control" rows="8" cols="80" required>{{ old('message') }}</textarea>
          </div>

          <div class="form-group">
            <strong>Files</strong>
            <input type="file" name="file[]" value="" multiple>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Send</button>
        </div>
      </form>
    </div>

  </div>
</div>
@section('scripts')
<script type="text/javascript">
 $(document).ready(function() {
      $('#supplier_category_id2').on('change', function(){
        var supplier_category_id = $('#supplier_category_id2').val();
        var supplier_status_id = $('#supplier_status_id2').val();      
        getSuppliers(supplier_category_id, supplier_status_id,'');  
      });
      $('#supplier_status_id2').on('change', function(){
        var supplier_category_id = $('#supplier_category_id2').val();
        var supplier_status_id = $('#supplier_status_id2').val();
        getSuppliers(supplier_category_id, supplier_status_id,'');
      }); 
  });
  function filtersupplier(filter)
  {
    var supplier_category_id = $('#supplier_category_id2').val();
    var supplier_status_id = $('#supplier_status_id2').val();
    getSuppliers(supplier_category_id, supplier_status_id, filter);
  }
  $('#select_all').click(function() {
      $('#suppliers option').prop('selected', true);
  });
  $('#select_no').click(function() {
      $('#suppliers option').prop('selected', false);
  });
</script>
@endsection