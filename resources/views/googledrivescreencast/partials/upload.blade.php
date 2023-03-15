<div id="uploadeScreencastModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload Screencast to Google Drive</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('google-drive-screencast.create') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="{{ old('doc_name') }}" class="form-control input-sm" placeholder="Document Name" required>

                        @if ($errors->has('doc_name'))
                            <div class="alert alert-danger">{{$errors->first('doc_name')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Upload File</strong>
                        <input type="file" name="file" class="form-control input-sm" placeholder="Upload File">
                        @if ($errors->has('file'))
                            <div class="alert alert-danger">{{$errors->first('file')}}</div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Upload</button>
                </div>
            </form>
        </div>

    </div>
</div>
