<div id="emailSendModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send an Email</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="{{ route('purchase.email.send') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="supplier_id" value="{{ isset($supplier) ? $supplier->id : '' }}">

        <div class="modal-body">
          <div class="form-group">
            <select class="form-control input-sm select-multiple" name="email[]" multiple required>
              <option value="">Select Default Email</option>
              @if ($supplier->email != '')
                <option value="{{ $supplier->email }}" {{ $supplier->email == $supplier->default_email ? 'selected' : '' }}>{{ $supplier->email }} - Supplier's Email</option>
              @endif

              @if ($supplier->agents)
                @foreach ($supplier->agents as $agent)
                  <option value="{{ $agent->email }}" {{ $agent->email == $supplier->default_email ? 'selected' : '' }}>{{ $agent->email }} - {{ $agent->name }}</option>
                @endforeach
              @endif
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
