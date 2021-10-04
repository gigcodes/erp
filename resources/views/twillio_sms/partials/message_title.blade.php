
                        <div class="form-group">
                            <label >Title</label>
							{{Form::text('title', $details['title'], array('class'=>'form-control', 'placeholder'=>'Enter title'))}}
                        </div>
				      {{Form::hidden('message_group_id', $details['message_group_id'])}}
				      {{Form::hidden('id', $details['id'])}}
					<div class="form-group"><label >Scheduled Time</label>
						{{Form::datetime('scheduled_at', $details['scheduled_at'], array('class'=>'form-control scheduled_at', 'placeholder'=>'Enter title'))}}
					</div>
						
                    <button type="submit" class="btn btn-primary">Submit</button>
                