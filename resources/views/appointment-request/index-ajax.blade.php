<?php 
foreach($data as $prop) {?>

	<tr>
	   	<td><?php echo $prop->id;  ?></td>

	   	<td><?php echo $prop->created_at_date;  ?></td>

	   	<td><?php echo $prop->user->name;  ?></td>

	   	<td><?php echo $prop->userrequest->name;  ?></td>
   	</tr>
<?php 
} ?>