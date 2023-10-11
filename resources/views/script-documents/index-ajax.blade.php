<?php 
foreach($data as $prop) {?>

	<tr>
	   	<td><?php echo $prop->id;  ?></td>

	   	<td><?php echo $prop->created_at_date;  ?></td>

	   	<td><?php echo $prop->script_type;  ?></td>

	   	<td><?php echo $prop->file;  ?></td>

	   	<!-- <td class='break expand-row-msg' data-name="url" data-id="<?php echo $prop->id;  ?>">
			<button class="btn btn-sm upload-script_documents-files-button" type="button" title="Uploaded Files" data-script_document_id="<?php echo $prop->id;  ?>">
				<i class="fa fa-cloud-upload" aria-hidden="true"></i>
			</button>
			<button class="btn btn-sm view-script_documents-files-button" type="button" title="View Uploaded Files" data-script_document_id="<?php echo $prop->id;  ?>">
				<img src="/images/google-drive.png" style="cursor: nwse-resize; width: 12px;">
			</button>
	 	</td> -->

	 	<td><?php echo $prop->description; ?></td>

	   	<td><?php echo $prop->usage_parameter; ?></td>

	   	<td><?php echo $prop->category; ?></td>

	   	<td><?php echo $prop->comments; ?></td>

	   	<td><?php echo $prop->author; ?></td>

	   	<td><?php echo $prop->location; ?></td>

	   	<td><?php echo $prop->last_run; ?></td>

	   	<td><?php echo $prop->status; ?></td>

	     <td>		
		 	<div  class="d-flex">
				<button type="button" title="Edit" data-id="<?php echo $prop->id;  ?>" class="btn btn-edit-template">
	     			<i class="fa fa-edit" aria-hidden="true"></i>
				</button>
				
				<button type="button" title="Delete" data-id="<?php echo $prop->id;  ?>" class="btn btn-delete-template">
					<i class="fa fa-trash" aria-hidden="true"></i>
				</button>
		 	</div>
	     </td>
	</tr>
<?php 
} ?>