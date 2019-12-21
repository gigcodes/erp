<div id="groupModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('dubbizle.bulk.whatsapp') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Send Message in Bulk</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>HsCode:</strong>
            <select class="form-control selectpicker" name="group" data-live-search="true" required id="hscode">
              <option value="">Select HsCode</option>
              @foreach ($hscodes as $hscode)
                <option value="{{ $hscode->id }}">{{ $hscode->code }}</option>
              @endforeach
            </select>

          </div>

          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" id="name">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-secondary" onclick="submitGroup()">Send</button>
        </div>
      </form>
    </div>

  </div>
</div>