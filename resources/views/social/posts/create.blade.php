    <form  id="create-form" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="config_id" value="{{$id}}"/>
        <div class="modal-header">
            <h4 class="modal-title">Page Posting</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <input type="file"  name="source" class="form-control-file">
                @if ($errors->has('source'))
                <p class="text-danger">{{$errors->first('source')}}</p>
                @endif
            </div>
            <!-- <div class="form-group">
                <label>Video</label>
                <input type="file"  name="video" class="form-control-file">
                @if ($errors->has('video'))
                <p class="text-danger">{{$errors->first('video')}}</p>
                @endif
            </div> -->
            
            <div class="form-group">
                <label for="">Message</label>
                <input type="text" name="caption" class="form-control" placeholder="Type your message">
                @if ($errors->has('caption'))
                <p class="text-danger">{{$errors->first('caption')}}</p>
                @endif
            </div>
            <div class="form-group">
                <label for="">Description</label>
                <textarea name="post_body" class="form-control" cols="30" rows="5"></textarea>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Post</button>
        </div>
    </form>
