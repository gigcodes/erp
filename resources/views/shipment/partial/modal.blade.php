<div id="send_email_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Send an Email</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
  
        <form action="{{ route('shipment/send/email') }}" method="POST" enctype="multipart/form-data">
          @csrf
			<input type="hidden" name="order_id" id="order_id" value="" >
          <div class="modal-body">
            <div class="form-group">
                <label>Select Name</label>
                <select class="form-control" name="email_name" id="email_name" required>
                    <option value="">Select Template</option>
                    @if($template_names)
                        @foreach($template_names as $name)
                            <option value="{{ $name->name }}">{{ $name->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
              <div class="form-group">
                  <label>Select Template</label>
                  <select class="form-control" name="template" id="templates" required>

                  </select>
              </div>

            <div class="form-group">
				<label>To</label>
				<select class="form-control to-email" name="to[]" multiple="multiple" required style="width: 100%;">
				</select>
            </div>

            <div id="cc-label" class="form-group">
              <strong class="mr-3">Cc</strong>
				<select class="form-control cc-email" name="cc[]" multiple style="width: 100%;">
				</select>
            </div>
    
            <div id="bcc-label" class="form-group">
              	<strong class="mr-3">Bcc</strong>
              	<select class="form-control bcc-email" name="bcc[]" multiple style="width: 100%;">
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



<div id="view_sent_email_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
    
            <div class="modal-body" id="view_email_body">

			</div>
		</div>
	</div>
</div>


  