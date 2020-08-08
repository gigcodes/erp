<form  method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <input type="hidden" id="remark_email_id" name="remark_email_id" value="{{ $email['id'] }}">

        <div class="form-group">
            <textarea id="remark-message" name="message" class="form-control remark-message-textarea" rows="5" placeholder="Remarks...">
                {!! $email['remarks'] !!}
            </textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-default submit-remark">Save</button>
    </div>
</form>