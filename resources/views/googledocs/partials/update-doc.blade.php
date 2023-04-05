<div id="updateGoogleDocModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Google Doc Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('google-docs.update') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="id" id = "id">
                    <div class="form-group custom-select2">
                        <label>Category:
                        </label>
                        <input type="text" name="doc_category" value="" class="form-control input-sm" placeholder="Document Category" required>
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
