<div id="IVREditModal" class="modal fade col-md-12 col-sm-12" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('twilio.ivr.create') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="twilioSid" name="twilioSid">

        <div class="modal-header">
          <h4 class="modal-title">Add IVR Mp3</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="row" style="margin-left: 2px;"><p>Kindly Upload MP3 for particular category to play</p></div>
          <div class="form-group">
            <strong>Category:</strong>
            <select class="form-control" name="category_id" required id="category_ids">
              <option value="">Select a Category</option>

              @foreach ($all_category as $category)
                <option value="{{ $category->id }}" {{ $category->id == old('category_id') ? 'selected' : '' }}>{{ $category->category_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <strong>Response Upload:</strong>
            <input type="file" class="form-control" name="upload_mp3" />
          </div>         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Submit</button>
        </div>
      </form>
    </div>

  </div>
</div>