<div id="autoReplyCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('autoreply.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Create Auto Reply</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Keyword:</strong>
            <input type="text" name="keyword" class="form-control" value="{{ old('keyword') }}" placeholder="Enter Comma Separated Values" required>

            @if ($errors->has('keyword'))
              <div class="alert alert-danger">{{$errors->first('keyword')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Reply:</strong>
            <textarea name="reply" class="form-control" rows="8" cols="80" required>{{ old('reply') }}</textarea>

            @if ($errors->has('reply'))
              <div class="alert alert-danger">{{$errors->first('reply')}}</div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Create</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="autoReplyEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update Auto Reply</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Keyword:</strong>
            <input type="text" name="keyword" class="form-control" value="{{ old('keyword') }}" id="autoreply_keyword" required>

            @if ($errors->has('keyword'))
              <div class="alert alert-danger">{{$errors->first('keyword')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Reply:</strong>
            <textarea name="reply" class="form-control" rows="8" cols="80" required id="autoreply_reply">{{ old('reply') }}</textarea>

            @if ($errors->has('reply'))
              <div class="alert alert-danger">{{$errors->first('reply')}}</div>
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
