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
            <strong>Suppliers</strong>
            <select class="form-control" name="suppliers[]" required multiple>
              <option value="">Select Suppliers</option>

              @foreach ($suppliers_all as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->supplier }} - {{ $supplier->default_email }} / {{ $supplier->email }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <strong>Subject</strong>
            <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required>
          </div>

          <div class="form-group">
            <strong>Message</strong>
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
