<div id="createGoogleDocModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Google Doc</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('google-docs.create') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <strong>Document type:</strong>

                        <select class="form-control" name="type" required>
                            <option value="spreadsheet">Spreadsheet</option>
                            <option value="doc">Doc</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="{{ old('doc_name') }}" class="form-control input-sm" placeholder="Document Name" required>

                        @if ($errors->has('doc_name'))
                            <div class="alert alert-danger">{{$errors->first('doc_name')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Existing Doc Id:</strong>
                        <input type="text" name="existing_doc_id" value="{{ old('existing_doc_id') }}" class="form-control input-sm" placeholder="Existing Document ID">

                        @if ($errors->has('existing_doc_id'))
                            <div class="alert alert-danger">{{$errors->first('existing_doc_id')}}</div>
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