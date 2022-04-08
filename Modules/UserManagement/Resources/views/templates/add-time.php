<script type="text/x-jsrender" id="template-add-time">
    <form  method="post">
    <?php echo csrf_field(); ?>
		<div class="modal-content">
		   <div class="modal-header">
		      <h5 class="modal-title">{{if data.id}} Edit Avaibility {{else}}Add Avaibility{{/if}}</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      <span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
           
           <input type="text" id="time_user_id" name="user_id" class="form-control" 
           value="{{if data.user_id}}{{:data.user_id}}{{/if}}">

           <div class="form-group">
                  <strong>Day</strong>
                  
                 <select class="form-control" name="day[]" multiple="true">
                    <option value='monday' {{if data.weekday_0 }} selected {{/if}}>Monday</option>

                    <option value='tuesday' {{if data.weekday_1 }} selected {{/if}}>Tuesday</option>

                    <option value='wednesday' {{if data.weekday_2 }} selected {{/if}}>Wednesday</option>

                    <option value='thursday' {{if data.weekday_3  }} selected {{/if}}>Thursday</option>

                    <option value='friday' {{if data.weekday_4 }} selected {{/if}}>Friday</option>

                    <option value='saturday' {{if data.weekday_5 }} selected {{/if}}>Saturday</option>

                 </select>
			</div>
         <div class="form-group">
            <strong>Available Day (eg. 6) <small> From Week </small> </strong>
            <input type="number" step=0.1 class="form-control" name="availableDay" 
            value={{if data.day}}{{:data.day }}{{/if}}>
			</div>
         <div class="form-group">
            <strong>Available Hour (eg. 2) <small> From Day </small> {{if data.minute}}{{:data.minute}}{{/if}}</strong>
            <input type="time" step=0.1 class="form-control" name="availableHour" 
            value={{if data.minute}}{{:data.minute}}{{/if}}>
			</div>
            <div class="form-group">
                  <strong>Available From (eg. 10) <small>24 Hours format</small> </strong>
                  <input type="date" step=0.1 class="form-control" name="from" value={{if data.from}}{{:data.from}}{{/if}}>
			</div>
            <div class="form-group">
                  <strong>Available To (eg. 18) <small>24 Hours format</small></strong>
                  <input type="date" step=0.1 class="form-control" name="to" value={{if data.to}}{{:data.to}}{{/if}}>
			</div>
            <div class="form-group">
                  <strong>Status</strong>
                  <select class="form-control" name="status">
                    <option value="1" {{if data.status == 1}} selected {{/if}}>Available</option>
                    <option value="0" {{if data.status == 0}} selected {{/if}}>Not Available</option>
                 </select>
			</div>
            <div class="form-group">
                  <strong>Note</strong>
                  <textarea class="form-control" name="note" id="" rows="3">{{if data.note}}{{:data.note}}{{/if}}</textarea>
			</div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		      <button type="button"  class="btn btn-secondary submit-time" data-dismiss="modal">Submit</button>
		   </div>
		</div>
	</form> 
   
</script>
<script type="text/x-jsrender" id="template-view-time">
   <style>
   .display {
      display: flex;
      justify-content: space-evenly;
   }

   </style>
   <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title">View Avaibility</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body">
            <div class="form-group display">
               {{if data.user_id }}
                  <div>
                     <strong>User Id:</strong>
                     {{:data.user_id}}
                  </div>
               {{/if}}
               {{if data.weekday }}
                  <div>
                     <strong>Day:</strong>
                     {{:data.weekday}}
                  </div>
               {{/if}}
            </div>
            <div class="form-group display">
               {{if data.day }}
                  <div>
                     <strong>Available Day:</strong>
                     {{:data.day}}
                  </div>
               {{/if}}
               {{if data.minute }}
               <div>
                  <strong>Available Hour:</strong>
                  {{:data.minute}}
               </div>
               {{/if}}
            </div>
            
            <div class="form-group display">
               {{if data.date_from }}
                  <div>
                     <strong>Available From: </strong>
                     {{:data.date_from}}
                  </div>
               {{/if}}
               {{if data.date_to }}
                  <div>
                     <strong>Available To: </strong>
                     {{:data.date_to}}
                  </div>
               {{/if}}
            </div>
            
            <div class="form-group display">
               {{if data.status }}
                  <div>
                     <strong>Status: </strong>
                     {{if data.status == 1}}
                        Available
                     {{/if}}
                     {{if data.status == 0}}
                        Not Available
                     {{/if}}
                  </div>
               {{/if}}
               {{if data.note }}
                  <div>
                     <strong>Notes: </strong>
                     {{:data.note}}
                  </div>
               {{/if}}
            </div>
            <div class="form-group display">
               {{if data.userhubstafftotal }}
               {{/if}}
               <div>
                  <strong>HubStaff Total Minute: </strong>
                  {{:data.userhubstafftotal}}
               </div>
               
            </div>
      </div>
	</div>
</script>