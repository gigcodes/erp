<?php 
foreach($data as $prop) {?>

	<tr>
	   	<td><?php echo $prop->id;  ?></td>

	   	<td><?php echo $prop->created_at_date;  ?></td>

	   	<td><?php echo $prop->user->name;  ?></td>

	   	<td><?php echo $prop->userrequest->name;  ?></td>

	   	<td>
	   		<?php echo $prop->requested_time;  ?>

	   		@if(!empty($prop->remarks))
		   		<button type="button" data-id="<?php echo $prop->id;  ?>" class="btn requested-remarks-view" style="padding:1px 0px;">
	    			<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
	    		</button>
    		@endif
	   	</td>

	   	<td>
	   		@if($prop->request_status==0)
	   			{{'Requested'}}
	   		@elseif($prop->request_status==1)
	   			{{'Accepeted'}}
	   		@elseif($prop->request_status==2)
	   			{{'Decline'}}

	   			@if(!empty($prop->decline_remarks))
			   		<button type="button" data-id="<?php echo $prop->id;  ?>" class="btn decline_remarks-view" style="padding:1px 0px;">
		    			<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
		    		</button>
	    		@endif
	   		@endif
      	</td>
   	</tr>
<?php 
} ?>