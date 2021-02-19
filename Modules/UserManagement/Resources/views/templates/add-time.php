<script type="text/x-jsrender" id="template-add-time">
    <form  method="post">
    <?php echo csrf_field(); ?>
		<div class="modal-content">
		   <div class="modal-header">
		      <h5 class="modal-title">Add Avaibility</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      <span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
           <input type="hidden" id="time_user_id" name="user_id" class="form-control">
           <div class="form-group">
                  <strong>Day</strong>
                  <select class="form-control" name="day">
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                 </select>
			</div>
         <div class="form-group">
            <strong>Available Day (eg. 6) <small> From Week </small> </strong>
            <input type="number" step=0.1 class="form-control" name="availableDay">
			</div>
         <div class="form-group">
            <strong>Available Minute (eg. 480) <small> From Day </small> </strong>
            <input type="number" step=0.1 class="form-control" name="availableMinute">
			</div>
            <div class="form-group">
                  <strong>Available From (eg. 10) <small>24 Hours format</small> </strong>
                  <input type="number" step=0.1 class="form-control" name="from">
			</div>
            <div class="form-group">
                  <strong>Available To (eg. 18) <small>24 Hours format</small></strong>
                  <input type="number" step=0.1 class="form-control" name="to">
			</div>
            <div class="form-group">
                  <strong>Status</strong>
                  <select class="form-control" name="status">
                    <option value="1">Available</option>
                    <option value="0">Not Available</option>
                 </select>
			</div>
            <div class="form-group">
                  <strong>Note</strong>
                  <textarea class="form-control" name="note" id="" rows="3"></textarea>
			</div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		      <button type="button"  class="btn btn-secondary submit-time" data-dismiss="modal">Submit</button>
		   </div>
		</div>
	</form> 
</script>