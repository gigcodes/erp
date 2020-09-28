<script type="text/x-jsrender" id="template-task-history">
<form name="template-create-goal" method="post">
		<div class="modal-content">
		   <div class="modal-header">
		      <h5 class="modal-title">Task</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      <span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
           <table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Task</th>
		      	<th>Approximate time</th>
				<th>Action</th> 
			</tr>
		    </thead>
		    <tbody>
				{{props taskList}}
			      <tr>
			      	<td>
					  {{if prop.type == 'TASK'}}
					  #TASK-{{:prop.task_id}} => {{:prop.subject}} : {{:prop.subject}}. {{:prop.details}}
					  {{else}}
					  #DEVTASK-{{:prop.task_id}} => {{:prop.subject}} : {{:prop.subject}}. {{:prop.details}}
					  {{/if}}

					 
					  </td>
			      	<td>
					  <div class="form-group">
							<div class='input-group estimate_minutes'>
								<input style="min-width: 30px;" placeholder="E.minutes" value="{{:prop.approximate_time}}" type="text" class="form-control estimate-time-change" name="estimate_minutes_{{:prop.task_id}}" data-id="{{:prop.task_id}}" id="estimate_minutes_{{:prop.task_id}}" data-type={{:prop.type}}>

								<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-time-history" title="Show History" data-id="{{:prop.task_id}}" data-type={{:prop.type}}><i class="fa fa-info-circle"></i></button>
							</div>
						</div>
						<label for="" style="font-size: 12px;margin-top:10px;">Due date :</label>
                        <div class="d-flex">
                            <div class="form-group" style="padding-top:5px;">
                                <div class='input-group date due-datetime'>
									<input type="text" class="form-control input-sm due_date_cls" name="due_date" value="{{:prop.due_date}}"/>
									<span class="input-group-addon">
                            		<span class="glyphicon glyphicon-calendar"></span>
                        			</span>
								</div>
							</div>
                            <button class="btn btn-sm btn-image set-due-date" title="Set due date" data-taskid="{{:prop.task_id}}"><img style="padding: 0;margin-top: -14px;" src="/images/filled-sent.png"/></button>
                        </div>
					  </td>
					  <td>
					  	<input type="text" class="form-control quick-message-field input-sm" name="message" placeholder="Message" value="">
					  	<button class="btn btn-sm btn-image send-message" data-id="{{:prop.task_id}}"><img src="/images/filled-sent.png"/></button>
					  </td>
				  </tr>
				  {{/props}}
		    </tbody>
		</table>
		   </div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		   </div>
		</div>
	</form>
	$('.due-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    }); 
</script>
<script>
	$(document).on('click', '.send-message', function () {
		var cached_suggestions = localStorage['message_suggestions'];
            var thiss = $(this);
            var data = new FormData();
            var task_id = $(this).data('id');
            var message = $(this).siblings('input').val();

            data.append("task_id", task_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: '/whatsapp/sendMessage/task',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        $(thiss).siblings('input').val('');

                        if (cached_suggestions) {
                            suggestions = JSON.parse(cached_suggestions);

                            if (suggestions.length == 10) {
                                suggestions.push(message);
                                suggestions.splice(0, 1);
                            } else {
                                suggestions.push(message);
                            }
                            localStorage['message_suggestions'] = JSON.stringify(suggestions);
                            cached_suggestions = localStorage['message_suggestions'];

                            console.log('EXISTING');
                            console.log(suggestions);
                        } else {
                            suggestions.push(message);
                            localStorage['message_suggestions'] = JSON.stringify(suggestions);
                            cached_suggestions = localStorage['message_suggestions'];

                            console.log('NOT');
                            console.log(suggestions);
                        }

                        // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                        //   .done(function( data ) {
                        //
                        //   }).fail(function(response) {
                        //     console.log(response);
                        //     alert(response.responseJSON.message);
                        //   });

                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
	</script>