    <form  id="create-form" action="{{ route('social.post.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="config_id" value="{{$id}}"/>
        <div class="modal-header">
            <h4 class="modal-title">Page Posting</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Picture <small class="text-danger">* You can select multiple images only </small></label>
                <input type="file" multiple  name="source[]" class="form-control-file">
                @if ($errors->has('source.*'))
                <p class="text-danger">{{$errors->first('source.*')}}</p>
                @endif
            </div>
            <div class="form-group">
                <label>Video</label>
                <input type="file"  name="video1" class="form-control-file">
                @if ($errors->has('video'))
                <p class="text-danger">{{$errors->first('video')}}</p>
                @endif
            </div>
            
            <div class="form-group">
                <label for="">Message</label>
                <input type="text" name="message" class="form-control" placeholder="Type your message">
                @if ($errors->has('message'))
                <p class="text-danger">{{$errors->first('message')}}</p>
                @endif
            </div>
            <div class="form-group">
                <label for="">Description</label>
                <textarea name="description" class="form-control" cols="30" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="">Post on
                    <small class="text-danger">
                    * Can be Scheduled too </small>
                </label>
                <input  type="date"  name="date" class="form-control">
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Post</button>
        </div>
    </form>
